<template>
  <div class="row g-3 mb-4 text-white text-center">
    <div class="col-6 col-md-3">
      <div class="p-3 shadow-sm rounded-4 bg-success-gradient">
        <small class="opacity-75">Total Registros</small>
        <h2 class="fw-bold m-0">{{ total }}</h2>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="p-3 shadow-sm rounded-4 bg-warning-gradient">
        <small class="opacity-75">Pendentes</small>
        <h2 class="fw-bold m-0">{{ pendentes }}</h2>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="p-3 shadow-sm rounded-4 bg-danger-gradient">
        <small class="opacity-75">Urgentes</small>
        <h2 class="fw-bold m-0">{{ urgentes }}</h2>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="p-3 shadow-sm rounded-4 bg-purple-gradient">
        <small class="opacity-75">Espécies</small>
        <h2 class="fw-bold m-0">{{ especies }}</h2>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  total: Number,
  pendentes: Number,
  urgentes: Number,
  especies: Number
})
</script>

<style scoped>
.bg-success-gradient { background: linear-gradient(135deg, #2ecc71, #27ae60); }
.bg-warning-gradient { background: linear-gradient(135deg, #f1c40f, #f39c12); }
.bg-danger-gradient { background: linear-gradient(135deg, #e74c3c, #c0392b); }
.bg-purple-gradient { background: linear-gradient(135deg, #a569bd, #8e44ad); }
.rounded-4 { border-radius: 1.25rem; }
</style>