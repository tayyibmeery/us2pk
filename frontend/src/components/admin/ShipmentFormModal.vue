<template>
  <FormModal :isOpen="isOpen" :title="formData.id ? 'Edit Shipment' : 'Add Shipment'"
    :subtitle="formData.id ? 'Update the shipment details below.' : 'Fill in the details to add a new shipment.'"
    :saveLabel="formData.id ? 'Update' : 'Create'" @close="close" @save="save">
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

        <!-- PCode (read-only) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            PCode
          </label>
          <input v-model="formData.pcode" type="text" readonly
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

        <!-- Site Name -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Site Name
          </label>
          <select v-model="formData.site_name"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">Select site</option>
            <option value="Amazon">Amazon</option>
            <option value="eBay">eBay</option>
            <option value="AliExpress">AliExpress</option>
            <option value="Walmart">Walmart</option>
            <option value="Other">Other</option>
          </select>
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

        <!-- Status -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Status
          </label>
          <select v-model="formData.status"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            required>
            <option value="Bought by Company">Bought by Company</option>
            <option value="Bought by Customer">Bought by Customer</option>
            <option value="Reached Shipment in USA facility">Reached Shipment in USA facility</option>
            <option value="Departed Operations Facility - In Transit">Departed Operations Facility - In Transit</option>
            <option value="Custom Office at Lahore Airport">Custom Office at Lahore Airport</option>
            <option value="Reached Lahore Company Office">Reached Lahore Company Office</option>
            <option value="Out for Delivery">Out for Delivery</option>
            <option value="Delivered">Delivered</option>
          </select>
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

        <!-- Item Value & Company Charges -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Item Value (PKR)
          </label>
          <input v-model.number="formData.item_value_pkr" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            required @input="calculateTotalAndDue" />
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

        <!-- Received Amount -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Received Amount (PKR)
          </label>
          <input v-model.number="formData.received_amount" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            @input="calculateTotalAndDue" />
        </div>

        <!-- Receivable COD (auto) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Receivable COD (PKR)
          </label>
          <input :value="receivableCod" type="text" readonly
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-gray-100 px-4 py-2.5 text-sm text-gray-600 cursor-not-allowed dark:bg-gray-800 dark:text-gray-400" />
        </div>

        <!-- Paid By -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Paid By
          </label>
          <select v-model="formData.paid_by"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
            required>
            <option value="By Company">By Company</option>
            <option value="By Customer">By Customer</option>
          </select>
        </div>

        <!-- Payment Method (dropdown) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Payment Method
          </label>
          <select v-model="formData.payment_method"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">Select method</option>
            <option value="Cash">Cash</option>
            <option value="ABL">ABL</option>
            <option value="SCB">SCB</option>
            <option value="Faisal Bank">Faisal Bank</option>
            <option value="BAFL">BAFL</option>
            <option value="Jazz Cash">Jazz Cash</option>
            <option value="Easy Paisa">Easy Paisa</option>
            <option value="Meezan Bank">Meezan Bank</option>
          </select>
        </div>

        <!-- Local Delivery By (dropdown) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Local Delivery By
          </label>
          <select v-model="formData.local_delivery_by"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
            <option value="">Select</option>
            <option value="By BlueEx">By BlueEx</option>
            <option value="By Customer Pickup">By Customer Pickup</option>
            <option value="By TCS">By TCS</option>
            <option value="By Leopards">By Leopards</option>
          </select>
        </div>

        <!-- Delivery Charges (renamed) -->
        <div>
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Delivery Charges (PKR)
          </label>
          <input v-model.number="formData.delivery_charges" type="number" step="0.01" placeholder="0.00"
            class="dark:bg-dark-900 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30" />
        </div>

        <!-- Image Upload -->
        <div class="sm:col-span-2">
          <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            Product Images
          </label>
          <input type="file" multiple accept="image/*" @change="handleImageUpload"
            class="dark:bg-dark-900 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs dark:border-gray-700 dark:bg-gray-900 dark:text-white/90" />
          <div v-if="imagePreviews.length" class="flex flex-wrap gap-2 mt-2">
            <div v-for="(preview, index) in imagePreviews" :key="index" class="relative w-20 h-20">
              <img :src="preview" class="w-full h-full object-cover rounded border" />
              <button type="button" @click="removeImage(index)"
                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">×</button>
            </div>
          </div>
        </div>
      </div>
    </template>
  </FormModal>
</template>

<script setup lang="ts">
import { ref, watch, onMounted, nextTick, computed } from 'vue'
import flatpickr from 'flatpickr'
import 'flatpickr/dist/flatpickr.css'
import FormModal from '@/components/common/FormModal.vue'
import api from '@/services/api'
import type { Shipment } from '@/types'

const props = defineProps<{
  isOpen: boolean
  initialData?: Shipment | null
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'save', data: Partial<Shipment>, images: File[]): void
}>()

const users = ref<any[]>([])
const usersLoading = ref(false)
const imagePreviews = ref<string[]>([])
const imageFiles = ref<File[]>([])
const datePickerRefs = ref<Record<string, any>>({})
const flatpickrInstances: Record<string, any> = {}

const formData = ref<Partial<Shipment>>({
  user_id: undefined,
  pcode: '',
  description: '',
  weight: 0,
  weight_unit: 'kg',
  seller_tracker_id: '',
  site_name: '',
  purchase_date: '',
  status: 'Bought by Company',
  arrival_date: '',
  expected_delivery_date: '',
  date_delivered: '',
  item_value_pkr: 0,
  company_charges: 0,
  received_amount: 0,
  paid_by: 'By Customer',
  payment_method: '',
  receivable_cod: 0,
  local_delivery_by: '',
  delivery_charges: 0,
  comments: '',
})

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

const calculateTotalAndDue = () => {
  // just trigger recompute
}

const resetForm = () => {
  formData.value = {
    user_id: undefined,
    pcode: '',
    description: '',
    weight: 0,
    weight_unit: 'kg',
    seller_tracker_id: '',
    site_name: '',
    purchase_date: '',
    status: 'Bought by Company',
    arrival_date: '',
    expected_delivery_date: '',
    date_delivered: '',
    item_value_pkr: 0,
    company_charges: 0,
    received_amount: 0,
    paid_by: 'By Customer',
    payment_method: '',
    receivable_cod: 0,
    local_delivery_by: '',
    delivery_charges: 0,
    comments: '',
  }
  imagePreviews.value = []
  imageFiles.value = []
}

// Initialize flatpickr when modal opens
watch(() => props.isOpen, async (open) => {
  if (open) {
    await nextTick()
    // Destroy previous instances
    Object.values(flatpickrInstances).forEach(inst => inst.destroy())
    // Initialize flatpickr on date fields
    const dateFields = ['purchase_date', 'arrival_date', 'expected_delivery_date', 'date_delivered']
    dateFields.forEach(field => {
      const el = datePickerRefs.value[field]
      if (el) {
        flatpickrInstances[field] = flatpickr(el, {
          dateFormat: 'Y-m-d',
          allowInput: true,
          onChange: (selectedDates, dateStr) => {
            formData.value[field] = dateStr
          }
        })
        // Set initial value
        if (formData.value[field]) {
          flatpickrInstances[field].setDate(formData.value[field])
        }
      }
    })
    fetchUsers()
  } else {
    Object.values(flatpickrInstances).forEach(inst => inst.destroy())
  }
})

// Auto-fetch PCode when user_id changes
watch(() => formData.value.user_id, async (newUserId) => {
  if (!newUserId) return
  const selectedUser = users.value.find(u => u.id === newUserId)
  if (selectedUser?.city?.city_code) {
    try {
      const res = await api.get('/admin/shipments/generate-pcode', {
        params: { city_code: selectedUser.city.city_code }
      })
      formData.value.pcode = res.data.pcode
    } catch (e) {
      console.error('Failed to generate pcode', e)
    }
  }
})

// Update flatpickr values when formData changes (e.g., when editing)
watch(() => formData.value, (newVal) => {
  if (!props.isOpen) return
  const dateFields = ['purchase_date', 'arrival_date', 'expected_delivery_date', 'date_delivered']
  dateFields.forEach(field => {
    const inst = flatpickrInstances[field]
    if (inst && newVal[field]) {
      inst.setDate(newVal[field])
    }
  })
}, { deep: true })

watch(
  () => props.initialData,
  (newVal) => {
    if (newVal) {
      formData.value = { ...newVal }
      if (newVal.images) {
        imagePreviews.value = newVal.images.map((img: any) => img.image_path)
      }
    } else {
      resetForm()
    }
  },
  { immediate: true }
)

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

const handleImageUpload = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    const files = Array.from(target.files)
    files.forEach(file => {
      imageFiles.value.push(file)
      const reader = new FileReader()
      reader.onload = (e) => {
        if (e.target?.result) {
          imagePreviews.value.push(e.target.result as string)
        }
      }
      reader.readAsDataURL(file)
    })
  }
  target.value = '' // reset input
}

const removeImage = (index: number) => {
  imagePreviews.value.splice(index, 1)
  imageFiles.value.splice(index, 1)
}

const close = () => emit('close')
const save = () => {
  emit('save', formData.value, imageFiles.value)
}
</script>
