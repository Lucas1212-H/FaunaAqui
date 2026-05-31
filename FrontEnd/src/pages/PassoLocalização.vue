<template>
  <div class="card border-0 shadow-sm p-4 mx-auto" style="max-width: 360px; border-radius: 24px;">
    
    <div class="d-flex gap-1 mb-3">
      <div class="flex-grow-1 bg-success" style="height: 4px; border-radius: 2px;"></div>
      <div class="flex-grow-1 bg-success" style="height: 4px; border-radius: 2px;"></div>
      <div class="flex-grow-1 bg-secondary bg-opacity-25" style="height: 4px; border-radius: 2px;"></div>
    </div>

    <h1 class="h4 fw-bold text-dark mb-1">Local da ocorrência</h1>
    <p class="small text-muted mb-4">Passo 2 de 4 — clique no mapa</p>

    <div class="border border-light-subtle rounded-4 overflow-hidden mb-4 shadow-sm">
      
      <div id="mapa-ufpa" style="height: 220px; z-index: 1;"></div>

      <div class="bg-white p-3 border-top border-light-subtle text-dark small">
        <div v-if="coordenadas" class="text-success fw-medium">
          📍 Cadastrado: <span class="text-muted">{{ coordenadas.lat.toFixed(5) }}, {{ coordenadas.lng.toFixed(5) }}</span>
        </div>
        <div v-else class="text-danger animate-pulse">
          🌐 Toque no mapa para marcar a localização exata
        </div>
      </div>
    </div>

    <div class="mb-4">
      <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.8px;">
        Referência (Opcional)
      </div>
      <input
        v-model.trim="referencia"
        type="text"
        class="form-control shadow-none border-light-subtle rounded-3 text-dark text-opacity-75"
        style="padding: 12px; font-size: 14px;"
        placeholder="Ex.: em frente à Reitoria, perto do bloco de Computação"
        maxlength="120"
        autocomplete="off"
      />
      <div class="form-text small text-muted mt-1">
        Escreva uma descrição curta do local, se quiser ajudar no retorno.
      </div>
    </div>

    <button 
      class="btn btn-success w-100 py-3 fw-bold border-0 d-flex justify-content-center align-items-center gap-2 shadow-sm text-white"
      style="border-radius: 12px; font-size: 14px;"
      @click="avancar"
      :disabled="!coordenadas"
    >
      Concluir Registro
    </button>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import L from 'leaflet'

const emit = defineEmits(['proximo'])

// Estados que vão armazenar os dados digitados/clicados
const coordenadas = ref(null)
const referencia = ref('')

let map = null
let marker = null

// Inicializa o mapa assim que o HTML for renderizado na tela
onMounted(() => {
  // Coordenadas centrais aproximadas da UFPA Campus Guamá
  const centroUFPA = [-1.4748, -48.4452]

  // Configura o mapa
  map = L.map('mapa-ufpa', {
    zoomControl: false // Desativamos o padrão para usar a estética clean
  }).setView(centroUFPA, 16)

  // Adiciona as imagens das ruas (OpenStreetMap)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OSM'
  }).addTo(map)

  // Adiciona novamente os botões de zoom em uma posição mais discreta
  L.control.zoom({ position: 'topright' }).addTo(map)

  // ESCUTA O CLIQUE NO MAPA PARA ARMAZENAR OS DADOS
  map.on('click', (evento) => {
    const { lat, lng } = evento.latlng
    
    // 1. Armazena a informação no estado reativo do Vue
    coordenadas.value = { lat, lng }

    // 2. Controla o marcador visual (Pin) no mapa
    if (marker) {
      // Se já existia um pin antigo, só move ele para o novo lugar clicado
      marker.setLatLng([lat, lng])
    } else {
      // Se for o primeiro clique, cria o Pin vermelho na tela
      marker = L.marker([lat, lng]).addTo(map)
    }
  })
})

// Limpa a memória do navegador se o usuário mudar de página
onBeforeUnmount(() => {
  if (map) {
    map.remove()
  }
})

// Envia as informações armazenadas para o componente Pai (Wizard)
const avancar = () => {
  if (coordenadas.value) {
    emit('proximo', {
      localizacao: coordenadas.value, // Aqui vai o objeto { lat: x, lng: y } real
      referencia: referencia.value
    })
  }
}
</script>

<style scoped>
.form-control:focus {
  border-color: #198754;
  box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}

/* Efeito pulsante discreto para avisar que o mapa precisa ser clicado */
@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.6; }
}
.animate-pulse {
  animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>