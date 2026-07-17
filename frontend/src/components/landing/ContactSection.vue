<!-- src/components/landing/ContactSection.vue -->
<template>
  <div id="contact" class="container-xxl py-5">
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
            <form @submit.prevent="submitQuote">
              <div class="row g-3">
                <div class="col-12 col-sm-6">
                  <input type="text" class="form-control border-0" placeholder="Your Name" v-model="form.name"
                    style="height: 55px;" required>
                </div>
                <div class="col-12 col-sm-6">
                  <input type="email" class="form-control border-0" placeholder="Your Email" v-model="form.email"
                    style="height: 55px;" required>
                </div>
                <div class="col-12 col-sm-6">
                  <input type="text" class="form-control border-0" placeholder="Your Mobile" v-model="form.mobile"
                    style="height: 55px;">
                </div>
                <div class="col-12 col-sm-6">
                  <select class="form-select border-0" v-model="form.service" style="height: 55px;">
                    <option value="">Select A Service</option>
                    <option value="Air Freight">Air Freight</option>
                    <option value="Ocean Freight">Ocean Freight</option>
                    <option value="Road Freight">Road Freight</option>
                    <option value="Train Freight">Train Freight</option>
                    <option value="Customs Clearance">Customs Clearance</option>
                    <option value="Warehouse Solutions">Warehouse Solutions</option>
                  </select>
                </div>
                <div class="col-12">
                  <textarea class="form-control border-0" placeholder="Special Note" v-model="form.note"
                    rows="3"></textarea>
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
</template>

<script setup>
import { reactive } from 'vue';

const form = reactive({
  name: '',
  email: '',
  mobile: '',
  service: '',
  note: ''
});

const submitQuote = () => {
  // Handle form submission
  console.log('Quote Form Submitted:', form);
  // You can send this to your API
  alert('Thank you for your request! We will contact you soon.');
  // Reset form
  Object.assign(form, { name: '', email: '', mobile: '', service: '', note: '' });
};
</script>
