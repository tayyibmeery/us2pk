<template>
  <section class="hero-slider" id="home-section">
    <div class="slider-track" :style="trackStyle">
      <div v-for="(slide, index) in displaySlides" :key="slide.id || index" class="slide"
        :style="getSlideBackground(slide)">
        <div class="slide-overlay"></div>
        <div class="slide-content-wrap">
          <div class="slide-content" :class="{ active: index === currentIndex }">
            <div class="eyebrow on-dark">{{ slide.subtitle || 'Shop USA — Ship Pakistan' }}</div>
            <h1 v-html="slide.content || defaultTitle"></h1>
            <p class="lead">{{ slide.description || defaultLead }}</p>
            <div class="hero-actions">
              <router-link :to="slide.button1_link || '/signup'" class="btn btn-amber">
                {{ slide.button1_text || 'Get Your Free US Address' }} →
              </router-link>
              <a :href="slide.button2_link || '#contact-section'" class="btn btn-outline">
                {{ slide.button2_text || 'Request a Quote' }}
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Prev / Next arrows -->
    <button v-if="displaySlides.length > 1" class="slider-arrow slider-arrow-prev" aria-label="Previous slide"
      @click="prevSlide">
      ‹
    </button>
    <button v-if="displaySlides.length > 1" class="slider-arrow slider-arrow-next" aria-label="Next slide"
      @click="nextSlide">
      ›
    </button>

    <!-- Dots -->
    <div v-if="displaySlides.length > 1" class="slide-dots">
      <button v-for="(slide, index) in displaySlides" :key="`dot-${slide.id || index}`" class="dot"
        :class="{ active: index === currentIndex }" :aria-label="`Go to slide ${index + 1}`"
        @click="goToSlide(index)"></button>
    </div>
  </section>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from 'vue';
import { useLandingStore } from '@/stores/landingStore';

defineProps({
  sectionTitle: { type: String, default: 'Hero Section' },
  sectionSubtitle: { type: String, default: '' }
});

const landingStore = useLandingStore();

const heroSlides = computed(() => landingStore.getHero || []);

const defaultTitle = '#1 Place For Your <span class="text-primary">US2PK</span> Shipments';
const defaultLead = 'Trusted shopping, shipping, and delivery from USA to Pakistan.';

const fallbackSlide = {
  id: 'fallback',
  subtitle: 'Shipping & Logistics Solution',
  content: defaultTitle,
  description: defaultLead,
  image: null,
  button1_text: 'Get Started',
  button1_link: '/signup',
  button2_text: 'Free Quote',
  button2_link: '#contact-section',
};

const displaySlides = computed(() => {
  return heroSlides.value.length > 0 ? heroSlides.value : [fallbackSlide];
});

const currentIndex = ref(0);

const trackStyle = computed(() => ({
  transform: `translateX(-${currentIndex.value * 100}%)`,
}));

const getImageUrl = (imagePath) => {
  if (!imagePath) return '';
  if (imagePath.startsWith('http://') || imagePath.startsWith('https://')) return imagePath;
  const baseUrl = import.meta.env.VITE_BASE_URL || 'https://us2pk.com';
  if (imagePath.startsWith('/storage/')) return `${baseUrl}${imagePath}`;
  if (imagePath.startsWith('storage/')) return `${baseUrl}/${imagePath}`;
  if (imagePath.startsWith('hero/')) return `${baseUrl}/storage/${imagePath}`;
  return `${baseUrl}/storage/${imagePath}`;
};

const getSlideBackground = (slide) => {
  if (!slide.image) {
    return { background: 'linear-gradient(135deg, #0A1330 0%, #16265A 100%)' };
  }
  return {
    backgroundImage: `url(${getImageUrl(slide.image)})`,
    backgroundSize: 'cover',
    backgroundPosition: 'center',
  };
};

let rotationTimer = null;

const goToSlide = (index) => {
  currentIndex.value = index;
  restartRotation();
};

const nextSlide = () => {
  currentIndex.value = (currentIndex.value + 1) % displaySlides.value.length;
  restartRotation();
};

const prevSlide = () => {
  currentIndex.value = (currentIndex.value - 1 + displaySlides.value.length) % displaySlides.value.length;
  restartRotation();
};

const startRotation = () => {
  if (displaySlides.value.length <= 1) return;
  rotationTimer = setInterval(() => {
    currentIndex.value = (currentIndex.value + 1) % displaySlides.value.length;
  }, 6000);
};

const restartRotation = () => {
  if (rotationTimer) clearInterval(rotationTimer);
  startRotation();
};

watch(displaySlides, () => {
  if (currentIndex.value >= displaySlides.value.length) {
    currentIndex.value = 0;
  }
});

onMounted(() => {
  startRotation();
});

onBeforeUnmount(() => {
  if (rotationTimer) clearInterval(rotationTimer);
});
</script>

<style scoped>
.hero-slider {
  position: relative;
  width: 100%;
  height: 100vh;
  min-height: 560px;
  max-height: 900px;
  overflow: hidden;
  margin: 0;
}

.slider-track {
  display: flex;
  width: 100%;
  height: 100%;
  transition: transform 0.7s cubic-bezier(0.65, 0, 0.35, 1);
}

.slide {
  flex: 0 0 100%;
  width: 100%;
  height: 100%;
  position: relative;
  display: flex;
  align-items: center;
}

.slide-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg, rgba(10, 19, 48, .88) 0%, rgba(10, 19, 48, .55) 55%, rgba(10, 19, 48, .3) 100%);
  z-index: 1;
}

.slide-content-wrap {
  position: relative;
  z-index: 2;
  width: 100%;
  max-width: 1180px;
  margin: 0 auto;
  padding: 0 32px;
}

.slide-content {
  max-width: 620px;
  opacity: 0;
  transform: translateY(24px);
  transition: opacity 0.6s ease 0.15s, transform 0.6s ease 0.15s;
}

.slide-content.active {
  opacity: 1;
  transform: translateY(0);
}

.eyebrow.on-dark {
  font-family: 'IBM Plex Mono', monospace;
  font-size: 12px;
  letter-spacing: .16em;
  text-transform: uppercase;
  color: #E0A93A;
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
}

.eyebrow.on-dark::before {
  content: "";
  width: 22px;
  height: 1px;
  background: #E0A93A;
  display: inline-block;
}

.slide-content h1 {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 52px;
  line-height: 1.08;
  font-weight: 700;
  color: #fff;
  margin: 0 0 22px;
}

.slide-content h1 :deep(.text-primary) {
  color: #E0A93A;
}

.slide-content .lead {
  font-size: 17.5px;
  color: rgba(255, 255, 255, .78);
  line-height: 1.65;
  margin: 0 0 32px;
  max-width: 520px;
}

.hero-actions {
  display: flex;
  gap: 14px;
  flex-wrap: wrap;
}

.btn {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 26px;
  font-family: 'Space Grotesk', sans-serif;
  font-weight: 600;
  font-size: 15px;
  border-radius: 2px;
  border: 1px solid transparent;
  cursor: pointer;
  transition: transform .15s ease, box-shadow .15s ease, background .15s ease;
  text-decoration: none;
}

.btn-amber {
  background: #E0A93A;
  color: #0A1330;
}

.btn-amber:hover {
  transform: translateY(-2px);
  box-shadow: 0 10px 24px rgba(224, 169, 58, .35);
}

.btn-outline {
  border-color: rgba(255, 255, 255, .4);
  color: #fff;
  background: transparent;
}

.btn-outline:hover {
  border-color: #fff;
  background: rgba(255, 255, 255, .08);
}

.slider-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  z-index: 3;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, .3);
  background: rgba(10, 19, 48, .4);
  backdrop-filter: blur(4px);
  color: #fff;
  font-size: 26px;
  line-height: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: background 0.2s ease, border-color 0.2s ease, transform 0.2s ease;
}

.slider-arrow:hover {
  background: rgba(224, 169, 58, .9);
  border-color: #E0A93A;
  color: #0A1330;
}

.slider-arrow-prev {
  left: 24px;
}

.slider-arrow-next {
  right: 24px;
}

.slide-dots {
  position: absolute;
  bottom: 28px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 3;
  display: flex;
  gap: 10px;
}

.slide-dots .dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  border: 1px solid rgba(255, 255, 255, .5);
  background: transparent;
  cursor: pointer;
  padding: 0;
  transition: background 0.2s ease, transform 0.2s ease, border-color 0.2s ease;
}

.slide-dots .dot.active {
  background: #E0A93A;
  border-color: #E0A93A;
  transform: scale(1.25);
}

@media (max-width: 960px) {
  .hero-slider {
    height: 80vh;
    min-height: 480px;
  }

  .slide-content h1 {
    font-size: 38px;
  }
}

@media (max-width: 640px) {
  .hero-slider {
    height: 70vh;
    min-height: 420px;
  }

  .slide-content h1 {
    font-size: 28px;
    line-height: 1.15;
  }

  .slide-content .lead {
    font-size: 14.5px;
  }

  .slide-content-wrap {
    padding: 0 16px;
  }

  .hero-actions {
    flex-direction: column;
    gap: 10px;
  }

  .btn {
    width: 100%;
    justify-content: center;
  }

  .slider-arrow {
    width: 38px;
    height: 38px;
    font-size: 20px;
  }

  .slider-arrow-prev {
    left: 10px;
  }

  .slider-arrow-next {
    right: 10px;
  }

  .slide-dots {
    bottom: 16px;
  }
}
</style>
