<template>
  <section class="row g-4">
    <article class="col-12">
      <div class="map-card p-4 shadow-sm">
        
        <header class="mb-4">
          <h4 class="fw-bold m-0 text-dark">📍 Mapa de Distribuição da Fauna</h4>
          <small class="text-muted">Abaixo estão mapeadas todas as ocorrências validadas e publicadas no campus</small>
        </header>

        <div id="mapa-fauna" class="rounded-4 shadow-sm mb-4" style="height: 400px; width: 100%;"></div>

        <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Animal</th>
                <th scope="col">Ponto de Referência</th>
                <th scope="col">Data Publicação</th>
                <th scope="col">Coordenadas</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="item in publicados" :key="item.id" class="clickable-row" @click="focarNoAnimal(item)">
                <td class="fw-bold">🐾 {{ item.animal }}</td>
                <td>{{ item.local }}</td>
                <td>{{ item.data }}</td>
                <td>
                  <small class="text-mono text-muted">
                    {{ item.coordenadas && item.coordenadas.lat != null ? item.coordenadas.lat.toFixed(4) : '-' }},
                    {{ item.coordenadas && item.coordenadas.lng != null ? item.coordenadas.lng.toFixed(4) : '-' }}
                  </small>
                </td>
              </tr>
              <tr v-if="publicados.length === 0">
                <td colspan="4" class="text-center py-4 text-muted">Nenhum animal publicado no mapa ainda.</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </article>
  </section>
</template>

<script setup>
import { onMounted, watch, ref } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
  publicados: { type: Array, required: true, default: () => [] }
});

let map = null;
const markerGroup = L.layerGroup();

// Configuração das coordenadas centrais da UFPA - Campus Guamá
const LAT_UFPA = -1.4748;
const LNG_UFPA = -48.4456;

const inicializarMapa = () => {
  // Inicializa o mapa focado na UFPA
  map = L.map('mapa-fauna').setView([LAT_UFPA, LNG_UFPA], 15);

  // Adiciona a camada visual gratuita do OpenStreetMap
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
  }).addTo(map);

  markerGroup.addTo(map);
  renderizarMarcadores();
};

const renderizarMarcadores = () => {
  if (!map) return;
  markerGroup.clearLayers(); // Limpa os pins antigos antes de redesenhar

  props.publicados.forEach(animal => {
    if (animal.coordenadas && animal.coordenadas.lat && animal.coordenadas.lng) {
      
      // Cria o Pin no mapa
      const marker = L.marker([animal.coordenadas.lat, animal.coordenadas.lng]);
      
      // Cria a janelinha (Popup) que abre ao clicar no Pin
      const popupContent = `
        <div style="font-family: sans-serif; padding: 5px;">
          <h6 style="margin: 0 0 5px 0; font-weight: bold; color: #198754;">${animal.animal}</h6>
          <p style="margin: 0 0 5px 0; font-size: 12px;"><strong>Local:</strong> ${animal.local}</p>
          <p style="margin: 0; font-size: 11px; color: #6c757d;">Registrado em: ${animal.data}</p>
        </div>
      `;
      
      marker.bindPopup(popupContent);
      markerGroup.addLayer(marker);
    }
  });
};

// Permite que ao clicar na linha da tabela, o mapa dê um "zoom" suave direto no animal selecionado
const focarNoAnimal = (animal) => {
  if (map && animal.coordenadas) {
    map.setView([animal.coordenadas.lat, animal.coordenadas.lng], 18);
  }
};

// Monitora se a lista mudou (caso chegue dado novo da API) para atualizar os pins automaticamente
watch(() => props.publicados, () => {
  renderizarMarcadores();
}, { deep: true });

onMounted(() => {
  inicializarMapa();
});
</script>

<style scoped>
.map-card { background: #dfe6df; border-radius: 18px; color: #2f3a33; border: 1px solid #c8d0c9; }
.table { --bs-table-bg: transparent; }
.rounded-4 { border-radius: 0.9rem; }
.clickable-row { cursor: pointer; }
.clickable-row:hover { background: #f4f7f4; }
.text-mono { font-family: monospace; }
</style>