<template>
  <div class="card border-0 shadow-sm p-4 mx-auto" style="max-width: 360px; border-radius: 24px;">
    
    <h1 class="h4 fw-bold text-dark mb-1">Olá, denunciante</h1>
    <p class="small text-muted mb-4">Campus UFPA — O quê você observou?</p>

    <div class="text-uppercase text-muted fw-bold mb-3" style="font-size: 0.7rem; letter-spacing: 0.8px;">
      Registrar nova ocorrência
    </div>
    
    <div class="d-flex flex-column gap-2 mb-4">
      
      <div 
        @click="selecionar('silvestre')" 
        class="d-flex align-items-center p-3 border rounded-4 cursor-pointer hover-shadow transition-all"
        role="button"
      >
        <div class="d-flex align-items-center justify-content-center rounded-3 fs-5 me-3 bg-success bg-opacity-10 text-success" style="width: 40px; height: 40px;">
          🐾
        </div>
        <div class="flex-grow-1">
          <h3 class="m-0 fs-6 fw-semibold text-dark">Animal silvestre</h3>
          <p class="m-0 small text-muted" style="font-size: 12px;">Ave, réptil, mamífero, anfíbio...</p>
        </div>
        <span class="text-muted small">➔</span>
      </div>

      <div 
        @click="selecionar('peconhento')" 
        class="d-flex align-items-center p-3 border rounded-4 cursor-pointer hover-shadow transition-all"
        role="button"
      >
        <div class="d-flex align-items-center justify-content-center rounded-3 fs-5 me-3 bg-danger bg-opacity-10" style="width: 40px; height: 40px;">
          ⚠️
        </div>
        <div class="flex-grow-1">
          <h3 class="m-0 fs-6 fw-semibold text-dark">Animal peçonhento</h3>
          <p class="m-0 small fw-medium text-danger" style="font-size: 12px;">Serpente, escorpião, aranha</p>
        </div>
        <span class="text-muted small">➔</span>
      </div>

    </div>

    <div class="text-uppercase text-muted fw-bold mt-2 mb-2" style="font-size: 0.7rem; letter-spacing: 0.8px;">
      Últimas ocorrências
    </div>
    
    <div class="d-flex flex-column">
      
      <div class="d-flex align-items-center py-3 border-bottom border-light">
        <div class="d-flex align-items-center justify-content-center rounded-circle me-3 bg-danger bg-opacity-10" style="width: 32px; height: 32px;">
          📍
        </div>
        <div class="flex-grow-1">
          <h4 class="m-0 small fw-medium text-dark" style="font-size: 14px;">Serpente — Bloco A</h4>
          <p class="m-0 text-muted" style="font-size: 11px;">há 12 min · Não resolvido</p>
        </div>
        <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger fw-bold" style="font-size: 10px; padding: 4px 8px;">Urgente</span>
      </div>

      <div class="d-flex align-items-center py-3">
        <div class="d-flex align-items-center justify-content-center rounded-circle me-3 bg-success bg-opacity-10" style="width: 32px; height: 32px;">
          📍
        </div>
        <div class="flex-grow-1">
          <h4 class="m-0 small fw-medium text-dark" style="font-size: 14px;">Ave ferida — Estacionamento</h4>
          <p class="m-0 text-muted" style="font-size: 11px;">há 2h · Em atendimento</p>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
const emit = defineEmits(['proximo'])

const selecionar = (categoria) => {
  emit('proximo', categoria)
}
</script>

<style scoped>
/* Opcional: O Bootstrap cuida de 99% das coisas, mas adicionei este pequeno bloco apenas 
  para manter aquele efeito suave de "subir levemente" e sombra ao passar o mouse nos cards.
*/
.hover-shadow {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-shadow:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05) !important;
}
</style>