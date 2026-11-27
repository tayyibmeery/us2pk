<template>
  <section id="testimonials-section">
    <div class="wrap">
      <div class="section-head center">
        <div class="eyebrow">Client Stories</div>
        <h2>{{ sectionTitle || 'Trusted by thousands of shoppers' }}</h2>
        <p v-if="sectionSubtitle" class="section-sub">{{ sectionSubtitle }}</p>
      </div>

      <!-- Carousel Container -->
      <div class="carousel-container">
        <div class="carousel-track" :style="trackStyle">
          <div v-for="t in displayTestimonials" :key="t.id" class="carousel-slide" :style="slideStyle">
            <div class="t-card">
              <div class="t-quote-mark">"</div>
              <div class="t-stars">
                <span v-for="n in 5" :key="n" class="star" :class="{ filled: n <= (t.rating || 5) }">★</span>
              </div>
              <p class="t-quote">{{ t.content || 'Amazing service!' }}</p>
              <div class="t-divider"></div>
              <div class="t-person">
                <div class="t-avatar">
                  <img v-if="t.image && !t.image.includes('/depot/')" :src="getImageUrl(t.image)"
                    :alt="t.name || t.title" @error="handleImageError" />
                  <span v-else class="initials">{{ getInitials(t.name || t.title) }}</span>
                </div>
                <div>
                  <div class="t-name">{{ t.name || t.title }}</div>
                  <div class="t-role">{{ t.title || 'Customer' }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Navigation Arrows -->
        <button v-if="canScroll" class="carousel-arrow carousel-arrow-prev" @click="prevSlide" aria-label="Previous">
          ‹
        </button>
        <button v-if="canScroll" class="carousel-arrow carousel-arrow-next" @click="nextSlide" aria-label="Next">
          ›
        </button>

        <!-- Dots Indicator -->
        <div v-if="canScroll" class="carousel-dots">
          <button v-for="(_, index) in dotCount" :key="'dot-' + index" class="dot"
            :class="{ active: index === currentIndex }" @click="goToSlide(index)"
            :aria-label="'Go to slide ' + (index + 1)"></button>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

const props = defineProps({
  sectionTitle: { type: String, default: '' },
  sectionSubtitle: { type: String, default: '' }
});

const landingStore = useLandingStore();

const displayTestimonials = computed(() => landingStore.getTestimonials || []);

const currentIndex = ref(0);
const itemsPerSlide = ref(3);
let autoPlayTimer = null;

const updateItemsPerSlide = () => {
  const width = window.innerWidth;
  if (width < 640) {
    itemsPerSlide.value = 1;
  } else if (width < 960) {
    itemsPerSlide.value = 2;
  } else {
    itemsPerSlide.value = 3;
  }
};

// Highest index we can slide to without showing empty space past the last card
const maxIndex = computed(() => {
  return Math.max(0, displayTestimonials.value.length - itemsPerSlide.value);
});

// How many "stopping points" exist — used for dot count
const dotCount = computed(() => maxIndex.value + 1);

const canScroll = computed(() => displayTestimonials.value.length > itemsPerSlide.value);

// Each card's width as a percentage of the visible window
const cardWidthPercent = computed(() => 100 / itemsPerSlide.value);

const slideStyle = computed(() => ({
  flex: `0 0 ${cardWidthPercent.value}%`,
  maxWidth: `${cardWidthPercent.value}%`,
}));

// Track shifts by exactly one card-width per index step, not by whole groups
const trackStyle = computed(() => {
  const translateX = currentIndex.value * cardWidthPercent.value * -1;
  return {
    transform: `translateX(${translateX}%)`,
    transition: 'transform 0.5s ease-in-out',
  };
});

const goToSlide = (index) => {
  currentIndex.value = Math.min(index, maxIndex.value);
  resetAutoPlay();
};

const nextSlide = () => {
  currentIndex.value = currentIndex.value >= maxIndex.value ? 0 : currentIndex.value + 1;
  resetAutoPlay();
};

const prevSlide = () => {
  currentIndex.value = currentIndex.value <= 0 ? maxIndex.value : currentIndex.value - 1;
  resetAutoPlay();
};

const startAutoPlay = () => {
  if (!canScroll.value) return;
  if (autoPlayTimer) clearInterval(autoPlayTimer);
  autoPlayTimer = setInterval(() => {
    currentIndex.value = currentIndex.value >= maxIndex.value ? 0 : currentIndex.value + 1;
  }, 4000);
};

const resetAutoPlay = () => {
  if (autoPlayTimer) {
    clearInterval(autoPlayTimer);
    autoPlayTimer = null;
  }
  startAutoPlay();
};

const getInitials = (name) => {
  if (!name) return '?';
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0, 2);
};

const getImageUrl = (imagePath) => {
  if (!imagePath) return '';
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) return imagePath;
  const baseUrl = import.meta.env.VITE_BASE_URL || 'https://us2pk.com';
  if (imagePath.startsWith('/storage/')) return `${baseUrl}${imagePath}`;
  if (imagePath.startsWith('storage/')) return `${baseUrl}/${imagePath}`;
  if (imagePath.startsWith('testimonials/')) return `${baseUrl}/storage/${imagePath}`;
  return `${baseUrl}/storage/${imagePath}`;
};

const handleImageError = (event) => {
  const img = event.target;
  img.style.display = 'none';
  const parent = img.parentElement;
  if (parent) {
    const initials = document.createElement('span');
    initials.className = 'initials';
    const card = parent.closest('.t-person');
    if (card) {
      const nameEl = card.querySelector('.t-name');
      if (nameEl) {
        initials.textContent = getInitials(nameEl.textContent);
      }
    }
    parent.appendChild(initials);
  }
};

const handleResize = () => {
  const oldItemsPerSlide = itemsPerSlide.value;
  updateItemsPerSlide();

  if (oldItemsPerSlide !== itemsPerSlide.value) {
    currentIndex.value = 0;
    resetAutoPlay();
  }
};

onMounted(() => {
  updateItemsPerSlide();
  startAutoPlay();
  window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
  if (autoPlayTimer) clearInterval(autoPlayTimer);
  window.removeEventListener('resize', handleResize);
});

watch(displayTestimonials, () => {
  currentIndex.value = 0;
  if (autoPlayTimer) clearInterval(autoPlayTimer);
  startAutoPlay();
});
</script>

<style scoped>
section {
  padding: 100px 0;
  background:
    radial-gradient(ellipse at 8% 0%, rgba(224, 169, 58, .05), transparent 45%),
    radial-gradient(ellipse at 95% 100%, rgba(44, 140, 134, .06), transparent 50%),
    #FBFAF6;
}

.wrap {
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
}

.section-head {
  max-width: 620px;
  margin: 0 auto 60px;
  text-align: center;
}

.section-head.center .eyebrow {
  justify-content: center;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 12px;
  letter-spacing: .18em;
  text-transform: uppercase;
  color: #2C8C86;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
}

.section-head.center .eyebrow::before {
  display: none;
}

.section-head h2 {
  font-size: 38px;
  font-weight: 700;
  font-family: 'Space Grotesk', sans-serif;
  color: #12141C;
  letter-spacing: -0.01em;
  margin: 0;
}

.section-sub {
  margin-top: 14px;
  font-size: 15.5px;
  color: #4B4F5E;
  line-height: 1.6;
}

/* ============================================================
   CAROUSEL — sliding window, shifts one card at a time
   ============================================================ */
.carousel-container {
  position: relative;
  overflow: hidden;
  padding: 12px 4px 68px;
}

.carousel-track {
  display: flex;
  transition: transform 0.5s ease-in-out;
}

.carousel-slide {
  box-sizing: border-box;
  padding: 0 14px;
}

/* ============================================================
   TESTIMONIAL CARD
   ============================================================ */
.t-card {
  position: relative;
  background: #ffffff;
  border: 1px solid #EDEAE0;
  padding: 36px 32px 30px;
  border-radius: 10px;
  height: 100%;
  display: flex;
  flex-direction: column;
  box-shadow: 0 2px 6px rgba(18, 20, 28, .04);
  transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.25s ease;
}

.t-card:hover {
  transform: translateY(-6px);
  box-shadow: 0 20px 40px rgba(18, 20, 28, .10);
  border-color: #E0A93A;
}

.t-quote-mark {
  position: absolute;
  top: 18px;
  right: 24px;
  font-family: Georgia, 'Times New Roman', serif;
  font-size: 64px;
  line-height: 1;
  color: #E0A93A;
  opacity: 0.14;
  pointer-events: none;
  user-select: none;
}

.t-stars {
  display: flex;
  gap: 3px;
  margin-bottom: 18px;
}

.t-stars .star {
  font-size: 14px;
  color: #E3E0D3;
  letter-spacing: 1px;
}

.t-stars .star.filled {
  color: #E0A93A;
}

.t-quote {
  position: relative;
  font-size: 15.5px;
  color: #22242E;
  line-height: 1.75;
  margin-bottom: 26px;
  flex: 1;
  font-weight: 400;
}

.t-divider {
  height: 1px;
  background: linear-gradient(90deg, #EDEAE0, transparent);
  margin-bottom: 20px;
}

.t-person {
  display: flex;
  align-items: center;
  gap: 14px;
}

.t-avatar {
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: linear-gradient(145deg, #16265A, #0A1330);
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-family: 'IBM Plex Mono', monospace;
  font-size: 13px;
  flex-shrink: 0;
  overflow: hidden;
  box-shadow: 0 0 0 3px #fff, 0 0 0 4px #EDEAE0;
}

.t-avatar img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.t-avatar .initials {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 14.5px;
  font-weight: 600;
  color: #fff;
}

.t-name {
  font-weight: 700;
  font-size: 14.5px;
  color: #12141C;
  font-family: 'Space Grotesk', sans-serif;
}

.t-role {
  font-size: 12.5px;
  color: #8A8D99;
  margin-top: 2px;
}

/* ============================================================
   CAROUSEL ARROWS
   ============================================================ */
.carousel-arrow {
  position: absolute;
  top: 42%;
  transform: translateY(-50%);
  z-index: 10;
  width: 46px;
  height: 46px;
  border-radius: 50%;
  border: 1px solid #EDEAE0;
  background: #ffffff;
  color: #12141C;
  font-size: 22px;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 8px 20px rgba(18, 20, 28, .08);
  transition: all 0.2s ease;
}

.carousel-arrow:hover {
  background: #0A1330;
  border-color: #0A1330;
  color: #E0A93A;
  transform: translateY(-50%) scale(1.06);
}

.carousel-arrow-prev {
  left: -22px;
}

.carousel-arrow-next {
  right: -22px;
}

/* ============================================================
   CAROUSEL DOTS
   ============================================================ */
.carousel-dots {
  position: absolute;
  bottom: 14px;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 10px;
  z-index: 10;
}

.carousel-dots .dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  border: none;
  background: #DEDACB;
  cursor: pointer;
  padding: 0;
  transition: all 0.25s ease;
}

.carousel-dots .dot.active {
  background: #E0A93A;
  width: 26px;
  border-radius: 5px;
}

/* ============================================================
   RESPONSIVE
   ============================================================ */
@media (max-width: 1240px) {
  .carousel-arrow-prev {
    left: 4px;
  }

  .carousel-arrow-next {
    right: 4px;
  }
}

@media (max-width: 960px) {
  .section-head h2 {
    font-size: 32px;
  }

  .carousel-arrow {
    width: 38px;
    height: 38px;
    font-size: 18px;
  }
}

@media (max-width: 640px) {
  section {
    padding: 64px 0;
  }

  .wrap {
    padding: 0 16px;
  }

  .section-head {
    margin-bottom: 40px;
  }

  .section-head h2 {
    font-size: 26px;
  }

  .carousel-container {
    padding: 8px 0 56px;
  }

  .carousel-arrow {
    width: 34px;
    height: 34px;
    font-size: 16px;
  }

  .carousel-arrow-prev {
    left: -4px;
  }

  .carousel-arrow-next {
    right: -4px;
  }

  .t-card {
    padding: 26px 22px 22px;
  }

  .t-quote-mark {
    font-size: 48px;
    top: 12px;
    right: 16px;
  }
}
</style>
