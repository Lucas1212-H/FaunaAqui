<template>
  <div class="data-card p-4 h-100 text-center d-flex flex-column shadow-sm">
    <h4 class="mb-4 fw-bold">Análise de Campo</h4>
    
    <div class="pie-chart-sim mx-auto mb-4"></div>
    
    <div class="status-legend d-flex justify-content-center text-start small mb-4 flex-wrap gap-3">
      <span><i class="fas fa-circle text-danger me-1"></i> Mortos</span>
      <span><i class="fas fa-circle text-primary me-1"></i> Feridos</span>
      <span><i class="fas fa-circle text-warning me-1"></i> Avistados</span>
    </div>

    <div class="alert-box text-start mb-4">
      <h6 class="fw-bold mb-2 small text-uppercase">Comunicados Equipe</h6>
      <div class="alert alert-light border-0 shadow-sm py-2 mb-2">
        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
        <small>Alta incidência de Saruês no Setor Norte.</small>
      </div>
    </div>

    <RouterLink class="btn btn-purple w-100 py-3 fw-bold text-white mt-auto" to="/catalogo">
      Base de Dados Taxonômica
    </RouterLink>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()
defineEmits(['gerenciarEspecies'])

</script>

<style scoped>
.data-card {
  background: #58d68d;
  border-radius: 25px;
  color: #1e293b;
}
.pie-chart-sim {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  background: conic-gradient(#e74c3c 0% 57%, #3498db 57% 83%, #f1c40f 83% 100%);
  border: 10px solid white;
}
.btn-purple { background-color: #a569bd; border: none; border-radius: 15px; }
</style>