<!-- src/components/landing/FooterSection.vue -->
<template>
  <footer class="footer-section">
    <div class="footer-accent"></div>

    <div class="footer-top">
      <div class="container">
        <div class="footer-grid">
          <!-- Column 1: Brand & Contact -->
          <div class="footer-col brand-col">
            <div class="footer-brand">
              <span class="dot"></span>
              <span class="brand-name">{{ footerData?.title || 'US2PK' }}</span>
            </div>
            <p class="brand-description">
              {{ footerData?.newsletter_text || 'Connecting Pakistan to the world\'s best products through trusted shopping, shipping, and delivery.' }}
            </p>
            <div class="contact-info">
              <div v-if="footerData?.address" class="contact-item">
                <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                  <circle cx="12" cy="10" r="3" />
                </svg>
                <span>{{ footerData.address }}</span>
              </div>
              <div v-if="footerData?.phone" class="contact-item">
                <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path
                    d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                </svg>
                <a :href="'tel:' + footerData.phone">{{ footerData.phone }}</a>
              </div>
              <div v-if="footerData?.email" class="contact-item">
                <svg class="contact-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z" />
                  <polyline points="22,6 12,13 2,6" />
                </svg>
                <a :href="'mailto:' + footerData.email">{{ footerData.email }}</a>
              </div>
            </div>
            <div class="social-icons" v-if="socialIcons.length">
              <a v-for="social in socialIcons" :key="social.platform" :href="social.url" target="_blank"
                rel="noopener noreferrer" class="social-link" :title="social.platform">
                <i :class="social.icon || 'fab fa-' + social.platform"></i>
              </a>
            </div>
          </div>

          <!-- Column 2: Services -->
          <div v-if="footerData?.service_links?.length" class="footer-col links-col">
            <h4 class="footer-heading">Services</h4>
            <ul class="footer-links">
              <li v-for="link in footerData.service_links" :key="link.title">
                <a :href="link.url">{{ link.title }}</a>
              </li>
            </ul>
          </div>

          <!-- Column 3: Company -->
          <div v-if="footerData?.company_links?.length" class="footer-col links-col">
            <h4 class="footer-heading">Company</h4>
            <ul class="footer-links">
              <li v-for="link in footerData.company_links" :key="link.title">
                <a :href="link.url">{{ link.title }}</a>
              </li>
            </ul>
          </div>

          <!-- Column 4: Quick Links & Newsletter -->
          <div class="footer-col newsletter-col">
            <div v-if="footerData?.quick_links?.length" class="quick-links">
              <h4 class="footer-heading">Quick Links</h4>
              <ul class="footer-links">
                <li v-for="link in footerData.quick_links" :key="link.title">
                  <a :href="link.url">{{ link.title }}</a>
                </li>
              </ul>
            </div>
            <!-- <div class="newsletter-section">
              <h4 class="footer-heading">Newsletter</h4>
              <p class="newsletter-text">Subscribe for updates and special offers.</p>
              <div class="newsletter-form">
                <input type="email" placeholder="Your email address" class="newsletter-input">
                <button type="button" class="newsletter-btn">Subscribe</button>
              </div>
            </div> -->
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="footer-bottom">
      <div class="container">
        <div class="footer-bottom-content">
          <div class="footer-copyright">
            &copy; {{ currentYear }}
            <a href="#" class="copyright-link">{{ footerData?.copyright || 'US2PK' }}</a>.
            All Rights Reserved.
          </div>
          <div class="footer-credit">
            Designed with <span class="heart">♥</span> by
            <a href="#" class="credit-link">{{ footerData?.title || 'US2PK' }} Team</a>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useLandingStore } from '@/stores/landingStore'

const landingStore = useLandingStore()
const footerData = computed(() => landingStore.getPublicFooter)

const socialIcons = computed(() => {
  return footerData.value?.social_icons || []
})

const currentYear = computed(() => new Date().getFullYear())
</script>

<style scoped>
/* ============================================
   FOOTER SECTION — owns all of its own styling.
   Flows directly beneath the previous section with
   no gap, matching how every other section on the
   page stacks (see Landing.vue / PublicLayout.vue).
   ============================================ */

.footer-section {
  position: relative;
  background: var(--navy-950, #0A1330);
  color: rgba(255, 255, 255, 0.7);
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
}

/* hairline accent at the seam — echoes the caution-stripe
   device used on the Prohibited Items section */
.footer-accent {
  height: 3px;
  background: linear-gradient(90deg, var(--amber, #E0A93A), var(--teal, #2C8C86) 55%, transparent);
}

.container {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
}

.footer-top {
  padding: 80px 0 50px;
  border-bottom: 1px solid var(--line-dark, rgba(255, 255, 255, 0.08));
}

.footer-grid {
  display: grid;
  grid-template-columns: 1.4fr 1fr 1fr 1.2fr;
  gap: 48px;
}

/* ===== Brand Column ===== */
.brand-col {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.footer-brand {
  display: flex;
  align-items: center;
  gap: 10px;
}

.footer-brand .dot {
  width: 8px;
  height: 8px;
  background: var(--amber, #E0A93A);
  border-radius: 50%;
  display: inline-block;
  box-shadow: 0 0 12px rgba(224, 169, 58, .4);
}

.brand-name {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 22px;
  font-weight: 700;
  color: #ffffff;
  letter-spacing: -0.02em;
}

.brand-description {
  font-size: 14px;
  line-height: 1.7;
  color: rgba(255, 255, 255, 0.6);
  max-width: 320px;
  margin: 0;
}

/* ===== Contact Info ===== */
.contact-info {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 4px;
}

.contact-item {
  display: flex;
  align-items: center;
  gap: 12px;
  font-size: 14px;
  color: rgba(255, 255, 255, 0.7);
}

.contact-icon {
  width: 18px;
  height: 18px;
  flex-shrink: 0;
  color: var(--amber, #E0A93A);
}

.contact-item a {
  color: rgba(255, 255, 255, 0.7);
  text-decoration: none;
  transition: color 0.2s ease;
}

.contact-item a:hover {
  color: var(--amber, #E0A93A);
}

/* ===== Social Icons ===== */
.social-icons {
  display: flex;
  gap: 10px;
  margin-top: 6px;
  flex-wrap: wrap;
}

.social-link {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 38px;
  height: 38px;
  border-radius: 50%;
  border: 1px solid var(--line-dark, rgba(255, 255, 255, 0.1));
  color: rgba(255, 255, 255, 0.5);
  font-size: 15px;
  transition: all 0.3s ease;
  text-decoration: none;
}

.social-link:hover {
  border-color: var(--amber, #E0A93A);
  color: var(--amber, #E0A93A);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(224, 169, 58, 0.2);
}

/* ===== Links Columns ===== */
.footer-col {
  display: flex;
  flex-direction: column;
}

.footer-heading {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 13.5px;
  font-weight: 600;
  color: #ffffff;
  text-transform: uppercase;
  letter-spacing: 0.08em;
  margin-bottom: 20px;
  position: relative;
  padding-bottom: 10px;
}

.footer-heading::after {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  width: 24px;
  height: 2px;
  background: var(--amber, #E0A93A);
  border-radius: 2px;
}

.footer-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 11px;
}

.footer-links li a {
  font-size: 14px;
  color: rgba(255, 255, 255, 0.6);
  text-decoration: none;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  gap: 6px;
}

.footer-links li a::before {
  content: '→';
  opacity: 0;
  transform: translateX(-6px);
  transition: all 0.2s ease;
  color: var(--amber, #E0A93A);
  font-size: 12px;
}

.footer-links li a:hover {
  color: var(--amber, #E0A93A);
}

.footer-links li a:hover::before {
  opacity: 1;
  transform: translateX(0);
}

/* ===== Newsletter Column ===== */
.newsletter-col {
  display: flex;
  flex-direction: column;
  gap: 28px;
}

.newsletter-text {
  font-size: 13.5px;
  color: rgba(255, 255, 255, 0.5);
  margin-bottom: 14px;
  line-height: 1.6;
}

.newsletter-form {
  display: flex;
  gap: 8px;
  max-width: 100%;
}

.newsletter-input {
  flex: 1;
  padding: 11px 16px;
  border: 1px solid var(--line-dark, rgba(255, 255, 255, 0.1));
  border-radius: 2px;
  background: rgba(255, 255, 255, 0.04);
  color: #ffffff;
  font-size: 14px;
  font-family: 'Inter', sans-serif;
  outline: none;
  transition: all 0.2s ease;
  min-width: 0;
}

.newsletter-input::placeholder {
  color: rgba(255, 255, 255, 0.3);
}

.newsletter-input:focus {
  border-color: var(--amber, #E0A93A);
  background: rgba(255, 255, 255, 0.06);
}

.newsletter-btn {
  padding: 11px 20px;
  background: var(--amber, #E0A93A);
  color: var(--navy-950, #0A1330);
  border: none;
  border-radius: 2px;
  font-weight: 600;
  font-size: 13px;
  font-family: 'Space Grotesk', sans-serif;
  cursor: pointer;
  transition: all 0.25s ease;
  white-space: nowrap;
}

.newsletter-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(224, 169, 58, 0.35);
}

/* ===== Footer Bottom ===== */
.footer-bottom {
  padding: 24px 0;
  background: rgba(0, 0, 0, 0.2);
}

.footer-bottom-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
}

.footer-copyright {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.4);
}

.copyright-link {
  color: rgba(255, 255, 255, 0.5);
  text-decoration: none;
  transition: color 0.2s ease;
}

.copyright-link:hover {
  color: var(--amber, #E0A93A);
}

.footer-credit {
  font-size: 13px;
  color: rgba(255, 255, 255, 0.3);
}

.heart {
  color: #e74c3c;
  display: inline-block;
  animation: heartbeat 1.5s ease-in-out infinite;
}

.credit-link {
  color: rgba(255, 255, 255, 0.4);
  text-decoration: none;
  transition: color 0.2s ease;
}

.credit-link:hover {
  color: var(--amber, #E0A93A);
}

@keyframes heartbeat {

  0%,
  100% {
    transform: scale(1);
  }

  50% {
    transform: scale(1.15);
  }
}

/* ===== Responsive ===== */
@media (max-width: 992px) {
  .footer-grid {
    grid-template-columns: 1fr 1fr;
    gap: 40px;
  }

  .brand-col {
    grid-column: 1 / -1;
  }

  .brand-description {
    max-width: 100%;
  }
}

@media (max-width: 640px) {
  .footer-grid {
    grid-template-columns: 1fr;
    gap: 32px;
  }

  .footer-top {
    padding: 56px 0 32px;
  }

  .container {
    padding: 0 16px;
  }

  .newsletter-form {
    flex-direction: column;
  }

  .newsletter-btn {
    width: 100%;
    justify-content: center;
  }

  .footer-bottom-content {
    flex-direction: column;
    text-align: center;
    gap: 8px;
  }

  .social-icons {
    justify-content: flex-start;
  }
}

@media (max-width: 480px) {
  .brand-name {
    font-size: 20px;
  }

  .footer-links li a {
    font-size: 13px;
  }

  .footer-copyright,
  .footer-credit {
    font-size: 12px;
  }
}
</style>
