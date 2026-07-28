<template>
  <section id="contact-section" style="background:#EDEAE0;">
    <div class="wrap">
      <!-- Centered Header -->
      <div class="section-header">
        <div class="eyebrow">Get A Quote</div>
        <h2>{{ sectionTitle || 'Ready to ship? Let\'s talk.' }}</h2>
        <p>
          {{ sectionSubtitle || 'Tell us what you\'re shipping and where, and we\'ll send a competitive quote within one business day. No obligation.' }}
        </p>
      </div>

      <!-- Two Column Layout: Form (Left) | Contact Info (Right) -->
      <div class="contact-grid">
        <!-- Left: Quote Form -->
        <div class="quote-form" ref="formRef">
          <form @submit.prevent="submitQuote">
            <div class="form-row">
              <div class="field">
                <label>Your Name</label>
                <input type="text" v-model="form.name" placeholder="Full name" required />
              </div>
              <div class="field">
                <label>Your Email</label>
                <input type="email" v-model="form.email" placeholder="you@example.com" required />
              </div>
            </div>
            <div class="form-row">
              <div class="field">
                <label>Mobile Number</label>
                <input type="text" v-model="form.mobile" placeholder="+92 3XX XXXXXXX" />
              </div>
              <div class="field">
                <label>Service</label>
                <select v-model="form.service">
                  <option value="">Select a service</option>
                  <option value="Air Freight">Air Freight</option>
                  <option value="Ocean Freight">Ocean Freight</option>
                  <option value="Road Freight">Road Freight</option>
                  <option value="Customs Clearance">Customs Clearance</option>
                  <option value="Warehouse Solutions">Warehouse Solutions</option>
                </select>
              </div>
            </div>
            <div class="field full">
              <label>Special Note</label>
              <textarea v-model="form.note" rows="3" placeholder="Tell us about your shipment..."></textarea>
            </div>
            <button type="submit" class="btn btn-amber" style="width:100%;justify-content:center;">
              {{ submitting ? 'Submitting...' : 'Submit Request' }}
            </button>
          </form>
        </div>

        <!-- Right: Contact Information (height-synced to the form, scrolls internally if it overflows) -->
        <div class="contact-panel">
          <div class="panel-head">
            <span class="panel-eyebrow">Reach Us Directly</span>
          </div>
          <div class="contact-list" ref="listRef" :style="listStyle">
            <div v-for="(contact, i) in activeContacts" :key="contact.id || i" class="contact-entry">
              <h3 v-if="contact.title">{{ contact.title }}</h3>
              <p class="contact-subtitle" v-if="contact.subtitle">{{ contact.subtitle }}</p>
              <p class="contact-content" v-if="contact.content">{{ contact.content }}</p>

              <div class="detail-row" v-if="contact.phone">
                <span class="detail-icon">☎</span>
                <div>
                  <div class="detail-label">Call for any query</div>
                  <div class="detail-value mono">{{ contact.phone }}</div>
                </div>
              </div>

              <div class="detail-row" v-if="contact.email">
                <span class="detail-icon">✉</span>
                <div class="detail-value">{{ contact.email }}</div>
              </div>

              <div class="detail-row" v-if="contact.address">
                <span class="detail-icon">📍</span>
                <div class="detail-value">{{ contact.address }}</div>
              </div>
            </div>

            <div v-if="!activeContacts.length" class="contact-entry">
              <div class="detail-row">
                <span class="detail-icon">☎</span>
                <div>
                  <div class="detail-label">Call for any query</div>
                  <div class="detail-value mono">+92 301 5579810</div>
                </div>
              </div>
            </div>
          </div>
          <!-- soft fade cue shown only when the list actually overflows -->
          <div class="scroll-fade" v-show="hasOverflow"></div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { reactive, computed, ref, onMounted, onBeforeUnmount, watch, nextTick } from 'vue';
import { useLandingStore } from '@/stores/landingStore';
import { useToast } from '@/composables/useToast';
import axios from 'axios';

const props = defineProps({
  sectionTitle: { type: String, default: '' },
  sectionSubtitle: { type: String, default: '' }
});

const landingStore = useLandingStore();
const toast = useToast();
const allContacts = computed(() => landingStore.getContact || []);
const submitting = ref(false);

// Only active contacts (status: true / undefined)
const activeContacts = computed(() => {
  if (Array.isArray(allContacts.value)) {
    return allContacts.value.filter(contact => contact.status !== false);
  }
  return allContacts.value ? [allContacts.value] : [];
});

const form = reactive({
  name: '',
  email: '',
  mobile: '',
  service: '',
  note: ''
});

const submitQuote = async () => {
  // Validate required fields
  if (!form.name || !form.email) {
    toast.warning('⚠️ Please fill in your name and email.');
    return;
  }

  submitting.value = true;
  try {
    const apiUrl = import.meta.env.VITE_API_BASE_URL || 'https://us2pk.com/api';
    await axios.post(`${apiUrl}/quotes`, form);

    // Success toast
    toast.success(
      '✅ Thank you! Your request has been received. Our team will contact you soon via your provided email or phone number.'
    );

    // Reset form
    Object.assign(form, { name: '', email: '', mobile: '', service: '', note: '' });
  } catch (error) {
    // Error toast
    const message = error.response?.data?.message || 'Something went wrong. Please try again.';
    toast.error('❌ ' + message);
  } finally {
    submitting.value = false;
  }
};

// ============================================================
// HEIGHT SYNC — keep the contact panel from growing taller
// than the quote form. Beyond that height it scrolls internally
// instead of pushing the layout out of balance.
// ============================================================
const formRef = ref(null);
const listRef = ref(null);
const formHeight = ref(0);
const hasOverflow = ref(false);
const isDesktop = ref(true);

const PANEL_CHROME = 64; // approx. space taken by panel head + padding, subtracted from the synced height

const listStyle = computed(() => {
  if (!isDesktop.value || !formHeight.value) return {};
  return {
    maxHeight: `${Math.max(formHeight.value - PANEL_CHROME, 160)}px`,
    overflowY: 'auto'
  };
});

const checkOverflow = () => {
  const el = listRef.value;
  hasOverflow.value = !!el && el.scrollHeight > el.clientHeight + 2;
};

let resizeObserver = null;

const measure = () => {
  isDesktop.value = window.innerWidth > 960;
  if (formRef.value) formHeight.value = formRef.value.offsetHeight;
  nextTick(checkOverflow);
};

onMounted(() => {
  landingStore.fetchLandingData();
  measure();
  window.addEventListener('resize', measure);
  if (window.ResizeObserver && formRef.value) {
    resizeObserver = new ResizeObserver(measure);
    resizeObserver.observe(formRef.value);
  }
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', measure);
  if (resizeObserver) resizeObserver.disconnect();
});

watch(activeContacts, () => nextTick(checkOverflow));
</script>

<style scoped>
section {
  padding: 96px 0;
}

.wrap {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
}

.section-header {
  text-align: center;
  margin-bottom: 48px;
}

.section-header .eyebrow {
  display: inline-block;
  font-size: 13px;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: #E0A93A;
  margin-bottom: 12px;
  font-family: 'IBM Plex Mono', monospace;
}

.section-header h2 {
  font-size: 38px;
  margin-bottom: 16px;
  font-family: 'Space Grotesk', sans-serif;
  color: #0F1B3D;
}

.section-header p {
  font-size: 16px;
  color: #4B4F5E;
  max-width: 640px;
  margin: 0 auto;
  line-height: 1.6;
}

.contact-grid {
  display: grid;
  grid-template-columns: 1fr 0.9fr;
  gap: 48px;
  align-items: start;
}

/* QUOTE FORM — left column, unchanged behavior */
.quote-form {
  background: #fff;
  border: 1px solid #DEDACB;
  border-radius: 4px;
  padding: 36px;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 16px;
}

.field label {
  display: block;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .05em;
  color: #4B4F5E;
  margin-bottom: 7px;
  font-family: 'IBM Plex Mono', monospace;
}

.field input,
.field select,
.field textarea {
  width: 100%;
  border: 1px solid #DEDACB;
  background: #F6F4EE;
  padding: 13px 14px;
  font-family: 'Inter', sans-serif;
  font-size: 14.5px;
  border-radius: 2px;
  color: #12141C;
}

.field input:focus,
.field select:focus,
.field textarea:focus {
  outline: 2px solid #2C8C86;
  outline-offset: 1px;
}

.field.full {
  grid-column: 1/-1;
  margin-bottom: 16px;
}

.btn-amber {
  background: #E0A93A;
  color: #0A1330;
  border: 1px solid transparent;
  cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease;
  padding: 14px 26px;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 15px;
  border-radius: 2px;
}

.btn-amber:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(224, 169, 58, .35);
}

/* CONTACT PANEL — right column.
   One bordered card holds the whole panel; height is capped to
   the form via inline style on .contact-list. Entries are
   separated by a dashed divider (matches the FAQ section
   language) instead of being individually boxed, so the panel
   stays visually light even with several contact entries. */
.contact-panel {
  position: relative;
  background: #fff;
  border: 1px solid #DEDACB;
  border-radius: 4px;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  height: fit-content;
}

.panel-head {
  padding: 22px 28px 0;
}

.panel-eyebrow {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 11px;
  letter-spacing: .1em;
  text-transform: uppercase;
  color: #2C8C86;
}

.contact-list {
  padding: 16px 28px 8px;
  scrollbar-width: thin;
  scrollbar-color: #DEDACB transparent;
}

.contact-list::-webkit-scrollbar {
  width: 6px;
}

.contact-list::-webkit-scrollbar-track {
  background: transparent;
}

.contact-list::-webkit-scrollbar-thumb {
  background: #DEDACB;
  border-radius: 3px;
}

.contact-list::-webkit-scrollbar-thumb:hover {
  background: #C9C4AF;
}

.contact-entry {
  padding: 18px 0;
  border-bottom: 1.5px dashed #EDEAE0;
}

.contact-entry:first-child {
  padding-top: 4px;
}

.contact-entry:last-child {
  border-bottom: none;
}

.contact-entry h3 {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 18px;
  font-weight: 600;
  margin: 0 0 4px;
  color: #0F1B3D;
}

.contact-entry .contact-subtitle {
  font-size: 12.5px;
  color: #8B8F9E;
  text-transform: uppercase;
  letter-spacing: .05em;
  margin: 0 0 8px;
}

.contact-entry .contact-content {
  font-size: 13.5px;
  color: #4B4F5E;
  line-height: 1.55;
  margin: 0 0 14px;
}

.detail-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 10px;
}

.detail-row:first-of-type {
  margin-top: 0;
}

.detail-icon {
  width: 34px;
  height: 34px;
  border-radius: 50%;
  background: #0F1B3D;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  flex-shrink: 0;
}

.detail-label {
  font-size: 10.5px;
  color: #8B8F9E;
  text-transform: uppercase;
  letter-spacing: .05em;
}

.detail-value {
  font-size: 14px;
  color: #22242E;
}

.detail-value.mono {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 15.5px;
  font-weight: 600;
  color: #0F1B3D;
}

/* fade cue at the bottom of the panel — only visible while the
   list is actually scrollable, signals there's more below */
.scroll-fade {
  position: absolute;
  left: 1px;
  right: 1px;
  bottom: 1px;
  height: 34px;
  background: linear-gradient(to bottom, rgba(255, 255, 255, 0), #fff 90%);
  pointer-events: none;
  border-radius: 0 0 4px 4px;
}

@media(max-width:960px) {
  .contact-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .contact-panel,
  .contact-list {
    height: auto !important;
    max-height: none !important;
    overflow: visible !important;
  }

  .scroll-fade {
    display: none;
  }

  .section-header h2 {
    font-size: 32px;
  }
}

@media(max-width:640px) {
  section {
    padding: 64px 0;
  }

  .wrap {
    padding: 0 16px;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .quote-form {
    padding: 20px;
  }

  .panel-head {
    padding: 18px 20px 0;
  }

  .contact-list {
    padding: 12px 20px 4px;
  }

  .section-header h2 {
    font-size: 28px;
  }

  .section-header p {
    font-size: 15px;
  }
}
</style>
