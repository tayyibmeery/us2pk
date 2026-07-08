<template>
  <div class="landing-page">
    <!-- Spinner -->
    <div id="spinner"
      class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
      <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white navbar-light shadow border-top border-5 border-primary sticky-top p-0">
      <router-link to="/" class="navbar-brand bg-primary d-flex align-items-center px-4 px-lg-5">
        <h2 class="mb-2 text-white">US2PK</h2>
      </router-link>
      <button type="button" class="navbar-toggler me-4" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarCollapse">
        <div class="navbar-nav ms-auto p-4 p-lg-0">
          <router-link to="/" class="nav-item nav-link active">Home</router-link>
          <router-link to="/about" class="nav-item nav-link">About</router-link>
          <router-link to="/services" class="nav-item nav-link">Services</router-link>
          <div class="nav-item dropdown">
            <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">Pages</a>
            <div class="dropdown-menu fade-up m-0">
              <router-link to="/pricing" class="dropdown-item">Pricing</router-link>
              <router-link to="/features" class="dropdown-item">Features</router-link>
              <router-link to="/quote" class="dropdown-item">Free Quote</router-link>
              <router-link to="/team" class="dropdown-item">Our Team</router-link>
              <router-link to="/testimonials" class="dropdown-item">Testimonials</router-link>
            </div>
          </div>
          <router-link to="/contact" class="nav-item nav-link">Contact</router-link>
        </div>
        <div class="d-flex align-items-center gap-3">
          <router-link to="/signin" class="btn btn-outline-primary btn-sm">Sign In</router-link>
          <router-link to="/signup" class="btn btn-primary btn-sm">Sign Up</router-link>
        </div>
      </div>
    </nav>

    <!-- Carousel -->
    <div class="container-fluid p-0 pb-5">
      <div class="owl-carousel header-carousel position-relative mb-5">
        <div class="owl-carousel-item position-relative">
          <img class="img-fluid" src="/landing/img/carousel-1.jpg" alt="">
          <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center"
            style="background: rgba(6, 3, 21, .5);">
            <div class="container">
              <div class="row justify-content-start">
                <div class="col-10 col-lg-8">
                  <h5 class="text-white text-uppercase mb-3 animated slideInDown">Shipping & Logistics Solution</h5>
                  <h1 class="display-3 text-white animated slideInDown mb-4">#1 Place For Your <span
                      class="text-primary">US2PK</span> Shipments</h1>
                  <p class="fs-5 fw-medium text-white mb-4 pb-2">Trusted shopping, shipping, and delivery from USA to
                    Pakistan.</p>
                  <router-link to="/signup" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Get
                    Started</router-link>
                  <router-link to="/quote" class="btn btn-secondary py-md-3 px-md-5 animated slideInRight">Free
                    Quote</router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="owl-carousel-item position-relative">
          <img class="img-fluid" src="/landing/img/carousel-2.jpg" alt="">
          <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center"
            style="background: rgba(6, 3, 21, .5);">
            <div class="container">
              <div class="row justify-content-start">
                <div class="col-10 col-lg-8">
                  <h5 class="text-white text-uppercase mb-3 animated slideInDown">Fast & Reliable</h5>
                  <h1 class="display-3 text-white animated slideInDown mb-4">International <span
                      class="text-primary">Shipping</span> Made Easy</h1>
                  <p class="fs-5 fw-medium text-white mb-4 pb-2">We handle customs, consolidation, and delivery – you
                    just shop.</p>
                  <router-link to="/signup" class="btn btn-primary py-md-3 px-md-5 me-3 animated slideInLeft">Join
                    Now</router-link>
                  <router-link to="/contact" class="btn btn-secondary py-md-3 px-md-5 animated slideInRight">Contact
                    Us</router-link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- About Section -->
    <div class="container-fluid overflow-hidden py-5 px-lg-0">
      <div class="container about py-5 px-lg-0">
        <div class="row g-5 mx-lg-0">
          <div class="col-lg-6 ps-lg-0 wow fadeInLeft" style="min-height: 400px;">
            <div class="position-relative h-100">
              <img class="position-absolute img-fluid w-100 h-100" src="/landing/img/about.jpg"
                style="object-fit: cover;" alt="">
            </div>
          </div>
          <div class="col-lg-6 about-text wow fadeInUp">
            <h6 class="text-secondary text-uppercase mb-3">About Us</h6>
            <h1 class="mb-5">Quick Transport and Logistics Solutions</h1>
            <p class="mb-5">US2PK connects Pakistan to the world’s best products through trusted shopping, shipping, and
              delivery solutions. We handle everything from purchase to doorstep.</p>
            <div class="row g-4 mb-5">
              <div class="col-sm-6 wow fadeIn">
                <i class="fa fa-globe fa-3x text-primary mb-3"></i>
                <h5>Global Coverage</h5>
                <p class="m-0">Ship from anywhere in the USA to any city in Pakistan.</p>
              </div>
              <div class="col-sm-6 wow fadeIn">
                <i class="fa fa-shipping-fast fa-3x text-primary mb-3"></i>
                <h5>On Time Delivery</h5>
                <p class="m-0">We guarantee your packages arrive safely and on schedule.</p>
              </div>
            </div>
            <router-link to="/about" class="btn btn-primary py-3 px-5">Explore More</router-link>
          </div>
        </div>
      </div>
    </div>

    <!-- Fact Section -->
    <div class="container-xxl py-5">
      <div class="container py-5">
        <div class="row g-5">
          <div class="col-lg-6 wow fadeInUp">
            <h6 class="text-secondary text-uppercase mb-3">Some Facts</h6>
            <h1 class="mb-5">#1 Place To Manage All Of Your Shipments</h1>
            <p class="mb-5">Track your orders, consolidate packages, and save on shipping – all in one platform.</p>
            <div class="d-flex align-items-center">
              <i class="fa fa-headphones fa-2x flex-shrink-0 bg-primary p-3 text-white"></i>
              <div class="ps-4">
                <h6>Call for any query!</h6>
                <h3 class="text-primary m-0">+92 123 4567890</h3>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="row g-4 align-items-center">
              <div class="col-sm-6">
                <div class="bg-primary p-4 mb-4 wow fadeIn">
                  <i class="fa fa-users fa-2x text-white mb-3"></i>
                  <h2 class="text-white mb-2" data-toggle="counter-up">1234</h2>
                  <p class="text-white mb-0">Happy Clients</p>
                </div>
                <div class="bg-secondary p-4 wow fadeIn">
                  <i class="fa fa-ship fa-2x text-white mb-3"></i>
                  <h2 class="text-white mb-2" data-toggle="counter-up">5678</h2>
                  <p class="text-white mb-0">Complete Shipments</p>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="bg-success p-4 wow fadeIn">
                  <i class="fa fa-star fa-2x text-white mb-3"></i>
                  <h2 class="text-white mb-2" data-toggle="counter-up">910</h2>
                  <p class="text-white mb-0">Customer Reviews</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Services Section -->
    <div class="container-xxl py-5">
      <div class="container py-5">
        <div class="text-center wow fadeInUp">
          <h6 class="text-secondary text-uppercase">Our Services</h6>
          <h1 class="mb-5">Explore Our Services</h1>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-4 wow fadeInUp">
            <div class="service-item p-4">
              <div class="overflow-hidden mb-4">
                <img class="img-fluid" src="/landing/img/service-1.jpg" alt="">
              </div>
              <h4 class="mb-3">Air Freight</h4>
              <p>Fast air shipping for urgent packages from US to PK.</p>
              <a class="btn-slide mt-2" href="#"><i class="fa fa-arrow-right"></i><span>Read More</span></a>
            </div>
          </div>
          <div class="col-md-6 col-lg-4 wow fadeInUp">
            <div class="service-item p-4">
              <div class="overflow-hidden mb-4">
                <img class="img-fluid" src="/landing/img/service-2.jpg" alt="">
              </div>
              <h4 class="mb-3">Ocean Freight</h4>
              <p>Cost‑effective sea freight for bulk shipments.</p>
              <a class="btn-slide mt-2" href="#"><i class="fa fa-arrow-right"></i><span>Read More</span></a>
            </div>
          </div>
          <div class="col-md-6 col-lg-4 wow fadeInUp">
            <div class="service-item p-4">
              <div class="overflow-hidden mb-4">
                <img class="img-fluid" src="/landing/img/service-3.jpg" alt="">
              </div>
              <h4 class="mb-3">Road Freight</h4>
              <p>Reliable overland delivery across Pakistan.</p>
              <a class="btn-slide mt-2" href="#"><i class="fa fa-arrow-right"></i><span>Read More</span></a>
            </div>
          </div>
          <div class="col-md-6 col-lg-4 wow fadeInUp">
            <div class="service-item p-4">
              <div class="overflow-hidden mb-4">
                <img class="img-fluid" src="/landing/img/service-4.jpg" alt="">
              </div>
              <h4 class="mb-3">Train Freight</h4>
              <p>Rail transport for heavy and bulky goods.</p>
              <a class="btn-slide mt-2" href="#"><i class="fa fa-arrow-right"></i><span>Read More</span></a>
            </div>
          </div>
          <div class="col-md-6 col-lg-4 wow fadeInUp">
            <div class="service-item p-4">
              <div class="overflow-hidden mb-4">
                <img class="img-fluid" src="/landing/img/service-5.jpg" alt="">
              </div>
              <h4 class="mb-3">Customs Clearance</h4>
              <p>We handle all customs paperwork and duties.</p>
              <a class="btn-slide mt-2" href="#"><i class="fa fa-arrow-right"></i><span>Read More</span></a>
            </div>
          </div>
          <div class="col-md-6 col-lg-4 wow fadeInUp">
            <div class="service-item p-4">
              <div class="overflow-hidden mb-4">
                <img class="img-fluid" src="/landing/img/service-6.jpg" alt="">
              </div>
              <h4 class="mb-3">Warehouse Solutions</h4>
              <p>Safe storage and consolidation at our US warehouse.</p>
              <a class="btn-slide mt-2" href="#"><i class="fa fa-arrow-right"></i><span>Read More</span></a>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quote & Features (combined) -->
    <div class="container-xxl py-5">
      <div class="container py-5">
        <div class="row g-5 align-items-center">
          <div class="col-lg-5 wow fadeInUp">
            <h6 class="text-secondary text-uppercase mb-3">Get A Quote</h6>
            <h1 class="mb-5">Request A Free Quote!</h1>
            <p class="mb-5">Tell us what you need and we'll give you a competitive price – no obligation.</p>
            <div class="d-flex align-items-center">
              <i class="fa fa-headphones fa-2x flex-shrink-0 bg-primary p-3 text-white"></i>
              <div class="ps-4">
                <h6>Call for any query!</h6>
                <h3 class="text-primary m-0">+92 123 4567890</h3>
              </div>
            </div>
          </div>
          <div class="col-lg-7">
            <div class="bg-light text-center p-5 wow fadeIn">
              <form>
                <div class="row g-3">
                  <div class="col-12 col-sm-6">
                    <input type="text" class="form-control border-0" placeholder="Your Name" style="height: 55px;">
                  </div>
                  <div class="col-12 col-sm-6">
                    <input type="email" class="form-control border-0" placeholder="Your Email" style="height: 55px;">
                  </div>
                  <div class="col-12 col-sm-6">
                    <input type="text" class="form-control border-0" placeholder="Your Mobile" style="height: 55px;">
                  </div>
                  <div class="col-12 col-sm-6">
                    <select class="form-select border-0" style="height: 55px;">
                      <option selected>Select A Service</option>
                      <option value="1">Air Freight</option>
                      <option value="2">Ocean Freight</option>
                      <option value="3">Road Freight</option>
                    </select>
                  </div>
                  <div class="col-12">
                    <textarea class="form-control border-0" placeholder="Special Note" rows="3"></textarea>
                  </div>
                  <div class="col-12">
                    <button class="btn btn-primary w-100 py-3" type="submit">Submit</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer -->
    <div class="container-fluid bg-dark text-light footer pt-5 wow fadeIn" style="margin-top: 6rem;">
      <div class="container py-5">
        <div class="row g-5">
          <div class="col-lg-3 col-md-6">
            <h4 class="text-light mb-4">Address</h4>
            <p class="mb-2"><i class="fa fa-map-marker-alt me-3"></i>Lahore, Pakistan</p>
            <p class="mb-2"><i class="fa fa-phone-alt me-3"></i>+92 123 4567890</p>
            <p class="mb-2"><i class="fa fa-envelope me-3"></i>info@us2pk.com</p>
            <div class="d-flex pt-2">
              <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-twitter"></i></a>
              <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-facebook-f"></i></a>
              <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-youtube"></i></a>
              <a class="btn btn-outline-light btn-social" href="#"><i class="fab fa-linkedin-in"></i></a>
            </div>
          </div>
          <div class="col-lg-3 col-md-6">
            <h4 class="text-light mb-4">Services</h4>
            <a class="btn btn-link" href="#">Air Freight</a>
            <a class="btn btn-link" href="#">Sea Freight</a>
            <a class="btn btn-link" href="#">Road Freight</a>
            <a class="btn btn-link" href="#">Customs Clearance</a>
            <a class="btn btn-link" href="#">Warehousing</a>
          </div>
          <div class="col-lg-3 col-md-6">
            <h4 class="text-light mb-4">Quick Links</h4>
            <router-link class="btn btn-link" to="/about">About Us</router-link>
            <router-link class="btn btn-link" to="/contact">Contact Us</router-link>
            <router-link class="btn btn-link" to="/services">Our Services</router-link>
            <a class="btn btn-link" href="#">Terms & Condition</a>
            <a class="btn btn-link" href="#">Support</a>
          </div>
          <div class="col-lg-3 col-md-6">
            <h4 class="text-light mb-4">Newsletter</h4>
            <p>Subscribe for updates and special offers.</p>
            <div class="position-relative mx-auto" style="max-width: 400px;">
              <input class="form-control border-0 w-100 py-3 ps-4 pe-5" type="text" placeholder="Your email">
              <button type="button" class="btn btn-primary py-2 position-absolute top-0 end-0 mt-2 me-2">SignUp</button>
            </div>
          </div>
        </div>
      </div>
      <div class="container">
        <div class="copyright">
          <div class="row">
            <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
              &copy; <a class="border-bottom" href="#">US2PK</a>, All Rights Reserved.
            </div>
            <div class="col-md-6 text-center text-md-end">
              Designed with <i class="fa fa-heart text-danger"></i> by US2PK Team
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Back to Top -->
    <a href="#" class="btn btn-lg btn-primary btn-lg-square rounded-0 back-to-top"><i class="bi bi-arrow-up"></i></a>
  </div>
</template>

<script setup>
import { onMounted } from 'vue';

onMounted(() => {
  // ---- Load external JS libraries (if not already loaded globally) ----
  // The CSS and JS files should be loaded in index.html.
  // If you prefer dynamic loading, you can use:
  // const script = document.createElement('script');
  // script.src = '/landing/js/main.js';
  // document.body.appendChild(script);

  // ---- Initialise all plugins (same as template's main.js) ----
  if (typeof window !== 'undefined') {
    // Spinner
    const spinner = document.getElementById('spinner');
    if (spinner) {
      setTimeout(() => {
        spinner.classList.remove('show');
        spinner.classList.add('d-none');
      }, 500);
    }

    // Sticky Navbar
    const navbar = document.querySelector('.navbar');
    if (navbar) {
      window.addEventListener('scroll', () => {
        if (window.scrollY > 45) {
          navbar.classList.add('sticky-top', 'shadow-sm');
        } else {
          navbar.classList.remove('sticky-top', 'shadow-sm');
        }
      });
    }

    // Dropdown hover (optional)
    // Not needed with Bootstrap 5 dropdown toggle.

    // Back to Top button
    const backToTop = document.querySelector('.back-to-top');
    if (backToTop) {
      window.addEventListener('scroll', () => {
        if (window.scrollY > 300) {
          backToTop.style.display = 'block';
        } else {
          backToTop.style.display = 'none';
        }
      });
      backToTop.addEventListener('click', (e) => {
        e.preventDefault();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    }

    // Wow.js
    if (window.WOW) {
      new window.WOW().init();
    }

    // Counter Up
    const counters = document.querySelectorAll('[data-toggle="counter-up"]');
    if (window.counterUp && counters.length) {
      counters.forEach((counter) => {
        const target = parseInt(counter.innerText, 10);
        window.counterUp(counter, {
          duration: 2000,
          delay: 16,
        });
      });
    }

    // Owl Carousel
    if (window.$ && window.$.fn.owlCarousel) {
      // Header Carousel
      $('.header-carousel').owlCarousel({
        animateOut: 'fadeOut',
        items: 1,
        autoplay: true,
        smartSpeed: 1000,
        autoplayTimeout: 4000,
        loop: true,
        dots: false,
        nav: true,
        navText: [
          '<i class="bi bi-chevron-left"></i>',
          '<i class="bi bi-chevron-right"></i>'
        ]
      });

      // Testimonial Carousel
      $('.testimonial-carousel').owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        autoplayTimeout: 4000,
        loop: true,
        center: true,
        dots: true,
        nav: false,
        margin: 24,
        responsive: {
          0: { items: 1 },
          768: { items: 2 },
          992: { items: 3 }
        }
      });
    }
  }
});
</script>

<style scoped>
/* You can import the required CSS files in main.ts or in this component */
/* To keep them scoped, we'll import them globally in main.ts or index.html */
</style>
