<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Shipment' : 'Add Shipment'"
    :subtitle="formData.id ? 'Update the shipment details below.' : 'Fill in the details to add a new shipment.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" :loading="saving" @close="close" @save="save">
    <template #fields>
      <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <!-- User -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            User
          </label>
          <select v-model="formData.user_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="usersLoading" required>
            <option value="" disabled>Select a user</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.email }})
            </option>
          </select>
          <p v-if="usersLoading" class="text-xs text-gray-400 mt-1">Loading users...</p>
        </div>

        <!-- shipment_code (read-only) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Shipment Code
          </label>
          <input v-model="formData.shipment_code" type="text" readonly
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400" />
        </div>

        <!-- Description & Comments -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Description
          </label>
          <textarea v-model="formData.description" rows="2" placeholder="Brief description of the shipment"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>

        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Comments
          </label>
          <textarea v-model="formData.comments" rows="2" placeholder="Internal comments"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"></textarea>
        </div>

        <!-- Weight & Unit -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Weight
          </label>
          <input v-model.number="formData.weight" type="number" step="0.01" placeholder="e.g. 1.5"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required />
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Weight Unit
          </label>
          <select v-model="formData.weight_unit"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            required>
            <option value="ounce">Ounce</option>
            <option value="gram">Gram</option>
            <option value="kg">Kilogram</option>
            <option value="pound">Pound</option>
          </select>
        </div>

        <!-- Seller Tracker ID -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Seller Tracker ID
          </label>
          <input v-model="formData.seller_tracker_id" type="text" placeholder="e.g. 1Z9999999999999999"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Site (dynamic) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Site
          </label>
          <select v-model="formData.site_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="sitesLoading">
            <option value="">Select site</option>
            <option v-for="site in sites" :key="site.id" :value="site.id">
              {{ site.name }}
            </option>
          </select>
          <p v-if="sitesLoading" class="text-xs text-gray-400 mt-1">Loading sites...</p>
        </div>

        <!-- Purchase Date (flatpickr) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Purchase Date
          </label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.purchase_date = el }" v-model="formData.purchase_date"
              type="text"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              placeholder="Select date" readonly />
            <span
              class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400">
              <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                  fill="currentColor" />
              </svg>
            </span>
          </div>
        </div>

        <!-- Status (dynamic) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Status
          </label>
          <select v-model="formData.shipment_status_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="statusesLoading" required>
            <option value="">Select status</option>
            <option v-for="status in statuses" :key="status.id" :value="status.id">
              {{ status.name }}
            </option>
          </select>
          <p v-if="statusesLoading" class="text-xs text-gray-400 mt-1">Loading statuses...</p>
        </div>

        <!-- Arrival Date (flatpickr) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Arrival Date
          </label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.arrival_date = el }" v-model="formData.arrival_date" type="text"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              placeholder="Select date" readonly />
            <span
              class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400">
              <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                  fill="currentColor" />
              </svg>
            </span>
          </div>
        </div>

        <!-- Expected Delivery Date (flatpickr) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Expected Delivery Date
          </label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.expected_delivery_date = el }"
              v-model="formData.expected_delivery_date" type="text"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              placeholder="Select date" readonly />
            <span
              class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400">
              <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                  fill="currentColor" />
              </svg>
            </span>
          </div>
        </div>

        <!-- Date Delivered (flatpickr) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Date Delivered
          </label>
          <div class="relative">
            <input :ref="el => { if (el) datePickerRefs.date_delivered = el }" v-model="formData.date_delivered"
              type="text"
              class="dark:bg-dark-900 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent bg-none px-4 py-2.5 pl-4 pr-11 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
              placeholder="Select date" readonly />
            <span
              class="absolute text-gray-500 -translate-y-1/2 pointer-events-none right-3 top-1/2 dark:text-gray-400">
              <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path fill-rule="evenodd" clip-rule="evenodd"
                  d="M6.66659 1.5415C7.0808 1.5415 7.41658 1.87729 7.41658 2.2915V2.99984H12.5833V2.2915C12.5833 1.87729 12.919 1.5415 13.3333 1.5415C13.7475 1.5415 14.0833 1.87729 14.0833 2.2915V2.99984L15.4166 2.99984C16.5212 2.99984 17.4166 3.89527 17.4166 4.99984V7.49984V15.8332C17.4166 16.9377 16.5212 17.8332 15.4166 17.8332H4.58325C3.47868 17.8332 2.58325 16.9377 2.58325 15.8332V7.49984V4.99984C2.58325 3.89527 3.47868 2.99984 4.58325 2.99984L5.91659 2.99984V2.2915C5.91659 1.87729 6.25237 1.5415 6.66659 1.5415ZM6.66659 4.49984H4.58325C4.30711 4.49984 4.08325 4.7237 4.08325 4.99984V6.74984H15.9166V4.99984C15.9166 4.7237 15.6927 4.49984 15.4166 4.49984H13.3333H6.66659ZM15.9166 8.24984H4.08325V15.8332C4.08325 16.1093 4.30711 16.3332 4.58325 16.3332H15.4166C15.6927 16.3332 15.9166 16.1093 15.9166 15.8332V8.24984Z"
                  fill="currentColor" />
              </svg>
            </span>
          </div>
        </div>

        <!-- Payment Method (dynamic) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Payment Method
          </label>
          <select v-model="formData.payment_method_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="paymentMethodsLoading">
            <option value="">Select method</option>
            <option v-for="method in paymentMethods" :key="method.id" :value="method.id">
              {{ method.name }}
            </option>
          </select>
          <p v-if="paymentMethodsLoading" class="text-xs text-gray-400 mt-1">Loading payment methods...</p>
        </div>

        <!-- Bought By (formerly Paid By) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Bought By
          </label>
          <select v-model="formData.paid_by"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            required>
            <option value="By Company">By Company</option>
            <option value="By Customer">By Customer</option>
          </select>
        </div>

        <!-- Item Value & Company Charges -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Item Value (PKR)
          </label>
          <input v-model.number="formData.item_value_pkr" type="number" step="0.01" placeholder="0.00" :class="[
            'h-11 w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800',
            isItemValueDisabled
              ? 'bg-gray-100 text-gray-600 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400'
              : 'bg-transparent text-gray-800 dark:bg-gray-900 dark:text-white/90'
          ]" :disabled="isItemValueDisabled" required @input="calculateTotalAndDue" />
        </div>

        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Company Charges (PKR)
          </label>
          <input v-model.number="formData.company_charges" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required @input="calculateTotalAndDue" />
        </div>

        <!-- Total Amount Payable (auto) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Total Amount Payable (PKR)
          </label>
          <input :value="totalAmount" type="text" readonly
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400" />
        </div>

        <!-- Received Amount - NOW READ-ONLY ON EDIT -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Received Amount (PKR)
          </label>
          <input v-model.number="formData.received_amount" :readonly="isEdit" type="number" step="0.01"
            placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
          <p v-if="isEdit" class="text-xs text-gray-400 mt-1">Can't update it update it from payments.</p>
        </div>

        <!-- Receivable COD (auto) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Receivable COD (PKR)
          </label>
          <input :value="receivableCod" type="text" readonly
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400" />
        </div>

        <!-- Local Courier (dynamic) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Local Courier
          </label>
          <select v-model="formData.local_courier_id"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            :disabled="localCouriersLoading">
            <option value="">Select courier</option>
            <option v-for="courier in localCouriers" :key="courier.id" :value="courier.id">
              {{ courier.name }}
            </option>
          </select>
          <p v-if="localCouriersLoading" class="text-xs text-gray-400 mt-1">Loading couriers...</p>
        </div>

        <!-- Delivery Charges -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Delivery Charges (PKR)
          </label>
          <input v-model.number="formData.delivery_charges" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Image Upload Section -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Product Images
          </label>

          <div v-if="existingImages.length" class="flex flex-wrap gap-2 mb-2">
            <div v-for="img in existingImages" :key="img.id" class="relative w-20 h-20 group">
              <img :src="getImageUrl(img.image_path)"
                class="w-full h-full object-cover rounded-lg border border-gray-200 dark:border-gray-700" />
              <button type="button" @click="removeExistingImage(img.id)"
                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 transition opacity-0 group-hover:opacity-100 focus:opacity-100"
                title="Remove image">
                ×
              </button>
            </div>
          </div>

          <div v-if="newImagePreviews.length" class="flex flex-wrap gap-2 mb-2">
            <div v-for="(preview, index) in newImagePreviews" :key="index" class="relative w-20 h-20 group">
              <img :src="preview"
                class="w-full h-full object-cover rounded-lg border border-gray-200 dark:border-gray-700" />
              <button type="button" @click="removeNewImage(index)"
                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600 transition">
                ×
              </button>
            </div>
          </div>

          <input type="file" multiple accept="image/*" @change="handleNewImageUpload"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
          <p v-if="!existingImages.length && !newImagePreviews.length" class="text-xs text-gray-400 mt-1">
            No images uploaded yet.
          </p>
          <p v-else class="text-xs text-gray-400 mt-1">
            {{ existingImages.length + newImagePreviews.length }} image(s) (click × to remove)
          </p>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, nextTick, computed } from 'vue'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'
import FormModal from '@/components/common/FormModal.vue'
import api from '@/services/api'
import { usePaymentMethodStore } from '@/stores/paymentMethodStore'
import { useSiteStore } from '@/stores/siteStore'
import { useShipmentStatusStore } from '@/stores/shipmentStatusStore'
import { useLocalCourierStore } from '@/stores/localCourierStore'
import type { Shipment } from '@/types'
import { useShipmentStore } from '@/stores/shipmentStore'

const props = defineProps<{
  isOpen: boolean
  initialData?: Shipment | null
}>()

const shipmentStore = useShipmentStore()
const emit = defineEmits<{
  (e: 'close'): void
  (e: 'saved'): void
}>()

const saving = ref(false)

const existingImages = ref<{ id: number; image_path: string }[]>([])
const imagesToDelete = ref<number[]>([])
const newImages = ref<File[]>([])
const newImagePreviews = ref<string[]>([])

const getImageUrl = (path: string) => {
  if (!path) return ''
  if (path.startsWith('http://') || path.startsWith('https://')) return path
  const baseUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000'
  return `${baseUrl}/storage/${path}`
}

const paymentMethodStore = usePaymentMethodStore()
const siteStore = useSiteStore()
const shipmentStatusStore = useShipmentStatusStore()
const localCourierStore = useLocalCourierStore()

const users = ref<any[]>([])
const usersLoading = ref(false)
const paymentMethods = ref<any[]>([])
const paymentMethodsLoading = ref(false)
const sites = ref<any[]>([])
const sitesLoading = ref(false)
const statuses = ref<any[]>([])
const statusesLoading = ref(false)
const localCouriers = ref<any[]>([])
const localCouriersLoading = ref(false)

const datePickerRefs = ref<Record<string, any>>({})
const flatpickrInstances: Record<string, any> = {}

const defaultForm = (): Partial<Shipment> => ({
  user_id: undefined,
  shipment_code: '',
  description: '',
  weight: 0,
  weight_unit: 'kg',
  seller_tracker_id: '',
  site_id: null,
  purchase_date: '',
  shipment_status_id: null,
  arrival_date: '',
  expected_delivery_date: '',
  date_delivered: '',
  item_value_pkr: 0,
  company_charges: 0,
  received_amount: 0,
  paid_by: 'By Customer',
  payment_method_id: null,
  receivable_cod: 0,
  local_courier_id: null,
  delivery_charges: 0,
  comments: '',
})

const formData = ref<Partial<Shipment>>(defaultForm())

const totalAmount = computed(() => {
  const item = parseFloat(formData.value.item_value_pkr as any) || 0
  const charges = parseFloat(formData.value.company_charges as any) || 0
  return item + charges
})

const receivableCod = computed(() => {
  const total = totalAmount.value
  const received = parseFloat(formData.value.received_amount as any) || 0
  return Math.max(0, total - received)
})

const isItemValueDisabled = computed(() => formData.value.paid_by === 'By Customer')

watch(() => formData.value.paid_by, (newVal) => {
  if (newVal === 'By Customer') {
    formData.value.item_value_pkr = 0
  }
})

const calculateTotalAndDue = () => { /* triggers computed */ }

const resetForm = () => {
  formData.value = defaultForm()
  existingImages.value = []
  imagesToDelete.value = []
  newImages.value = []
  newImagePreviews.value = []
}

const isEdit = computed(() => !!props.initialData?.id)

const fetchLookupData = async () => {
  paymentMethodsLoading.value = true
  try {
    await paymentMethodStore.fetchItems(1, { per_page: 100 })
    paymentMethods.value = paymentMethodStore.items
  } catch (e) {
    console.error('Failed to load payment methods', e)
  } finally {
    paymentMethodsLoading.value = false
  }

  sitesLoading.value = true
  try {
    await siteStore.fetchItems(1, { per_page: 100 })
    sites.value = siteStore.items
  } catch (e) {
    console.error('Failed to load sites', e)
  } finally {
    sitesLoading.value = false
  }

  statusesLoading.value = true
  try {
    await shipmentStatusStore.fetchItems(1, { per_page: 100 })
    statuses.value = shipmentStatusStore.items
  } catch (e) {
    console.error('Failed to load statuses', e)
  } finally {
    statusesLoading.value = false
  }

  localCouriersLoading.value = true
  try {
    await localCourierStore.fetchItems(1, { per_page: 100 })
    localCouriers.value = localCourierStore.items
  } catch (e) {
    console.error('Failed to load local couriers', e)
  } finally {
    localCouriersLoading.value = false
  }
}

const initDatePickers = () => {
  const dateFields = ['purchase_date', 'arrival_date', 'expected_delivery_date', 'date_delivered']
  dateFields.forEach(field => {
    const el = datePickerRefs.value[field]
    if (el) {
      if (flatpickrInstances[field]) flatpickrInstances[field].destroy()
      flatpickrInstances[field] = flatpickr(el, {
        dateFormat: 'Y-m-d',
        allowInput: true,
        onChange: (_dates: any, dateStr: string) => {
          (formData.value as any)[field] = dateStr
        },
      })
      const val = (formData.value as any)[field]
      if (val) {
        const clean = typeof val === 'string' && val.includes('T') ? val.split('T')[0] : val
        flatpickrInstances[field].setDate(clean)
      }
    }
  })
}

watch(() => props.isOpen, async (open) => {
  if (open) {
    await nextTick()
    initDatePickers()
    await fetchUsers()
    await fetchLookupData()
  } else {
    Object.values(flatpickrInstances).forEach((inst: any) => inst.destroy())
  }
})

watch(() => props.initialData, (newVal) => {
  if (newVal) {
    const cleaned: Partial<Shipment> = {}
    for (const key of Object.keys(newVal)) {
      const val = (newVal as any)[key]
      if (val !== null && val !== undefined && typeof val !== 'object') {
        (cleaned as any)[key] = val
      } else if (val === null) {
        (cleaned as any)[key] = null
      }
    }
    formData.value = cleaned

    if (formData.value.paid_by === 'By Customer') {
      formData.value.item_value_pkr = 0
    }

    existingImages.value = Array.isArray(newVal.images)
      ? newVal.images.map((img: any) => ({ id: img.id, image_path: img.image_path }))
      : []

    newImages.value = []
    newImagePreviews.value = []
    imagesToDelete.value = []
  } else {
    resetForm()
  }
}, { immediate: true })

watch(() => formData.value.user_id, async (newUserId) => {
  if (!newUserId) return
  try {
    const res = await api.get('/admin/shipments/generate-shipment-code', {
      params: { user_id: newUserId }
    })
    formData.value.shipment_code = res.data.shipment_code
  } catch (e) {
    console.error('Failed to generate shipment code', e)
  }
})

const removeExistingImage = (imageId: number) => {
  imagesToDelete.value.push(imageId)
  existingImages.value = existingImages.value.filter(img => img.id !== imageId)
}

const handleNewImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    Array.from(target.files).forEach(file => {
      newImages.value.push(file)
      const reader = new FileReader()
      reader.onload = (e) => {
        if (e.target?.result) newImagePreviews.value.push(e.target.result as string)
      }
      reader.readAsDataURL(file)
    })
  }
  target.value = ''
}

const removeNewImage = (index: number) => {
  newImages.value.splice(index, 1)
  newImagePreviews.value.splice(index, 1)
}

const fetchUsers = async () => {
  if (users.value.length) return
  usersLoading.value = true
  try {
    const res = await api.get('/admin/users?per_page=1000')
    users.value = res.data.data || res.data
  } catch (e) {
    console.error('Failed to fetch users', e)
  } finally {
    usersLoading.value = false
  }
}

const close = () => {
  if (saving.value) return
  emit('close')
}

const save = async () => {
  if (saving.value) return

  const cleanData: Partial<Shipment> = {
    user_id: formData.value.user_id,
    shipment_code: formData.value.shipment_code,
    description: formData.value.description,
    comments: formData.value.comments,
    weight: formData.value.weight,
    weight_unit: formData.value.weight_unit,
    seller_tracker_id: formData.value.seller_tracker_id,
    site_id: formData.value.site_id,
    purchase_date: formData.value.purchase_date,
    shipment_status_id: formData.value.shipment_status_id,
    arrival_date: formData.value.arrival_date,
    expected_delivery_date: formData.value.expected_delivery_date,
    date_delivered: formData.value.date_delivered,
    payment_method_id: formData.value.payment_method_id,
    paid_by: formData.value.paid_by,
    item_value_pkr: formData.value.item_value_pkr,
    company_charges: formData.value.company_charges,
    received_amount: formData.value.received_amount,
    local_courier_id: formData.value.local_courier_id,
    delivery_charges: formData.value.delivery_charges,
  }

  const requiredFields = ['user_id', 'weight', 'weight_unit', 'item_value_pkr', 'company_charges', 'paid_by']
  const missing = requiredFields.filter(field => {
    const val = cleanData[field as keyof typeof cleanData]
    return val === undefined || val === null || val === ''
  })

  if (missing.length) {
    alert(`Please fill in the following required fields: ${missing.join(', ')}`)
    return
  }

  saving.value = true

  try {
    let result: any

    if (props.initialData?.id) {
      result = await shipmentStore.update(
        props.initialData.id,
        cleanData,
        newImages.value,
        imagesToDelete.value,
      )
    } else {
      result = await shipmentStore.create(cleanData, newImages.value)
    }

    existingImages.value = Array.isArray(result?.images)
      ? result.images.map((img: any) => ({ id: img.id, image_path: img.image_path }))
      : []

    newImages.value = []
    newImagePreviews.value = []
    imagesToDelete.value = []

    emit('saved')
    emit('close')

  } catch (error: any) {
    const msg = error?.response?.data?.message || error?.message || String(error)
    console.error('Save failed:', msg)
    alert('Save failed: ' + msg)
  } finally {
    saving.value = false
  }
}
</script>
