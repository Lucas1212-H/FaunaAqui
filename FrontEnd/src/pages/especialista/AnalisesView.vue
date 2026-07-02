<template>
  <div class="container my-5 text-sans-serif text-start">
    <header class="mb-4 border-bottom pb-3 d-flex justify-content-between align-items-end">
      <div>
        <h1 class="h3 fw-bold text-uppercase m-0 text-dark">Painel Analítico ConViva</h1>
        <p class="text-muted small m-0">Inteligência geográfica e estatística aplicada à conservação da fauna.</p>
      </div>
    </header>

    <div class="row g-3 mb-4">
      <section class="col-6 col-md-3">
        <div class="card border rounded-0 p-3 shadow-sm text-center bg-white">
          <span class="text-muted tiny-text text-uppercase fw-bold">Total Registros</span>
          <h3 class="fw-extrabold m-0">{{ dadosBrutos.length }}</h3>
        </div>
      </section>
      <section class="col-6 col-md-3">
        <div class="card border rounded-0 p-3 shadow-sm text-center bg-white">
          <span class="text-muted tiny-text text-uppercase fw-bold">Espécies Únicas</span>
          <h3 class="fw-extrabold m-0 text-success">{{ totalEspecies }}</h3>
        </div>
      </section>
      <section class="col-6 col-md-3">
        <div class="card border rounded-0 p-3 shadow-sm text-center bg-white">
          <span class="text-muted tiny-text text-uppercase fw-bold">Casos Noturnos</span>
          <h3 class="fw-extrabold m-0 text-warning">{{ casosNoturnos }}</h3>
        </div>
      </section>
      <section class="col-6 col-md-3">
        <div class="card border rounded-0 p-3 shadow-sm text-center bg-white">
          <span class="text-muted tiny-text text-uppercase fw-bold">Casos Críticos</span>
          <h3 class="fw-extrabold m-0 text-danger">{{ casosCriticos }}</h3>
        </div>
      </section>
    </div>

    <section v-if="carregando" class="text-center my-5 py-5">
      <div class="spinner-border text-dark rounded-0" role="status"></div>
      <p class="small text-uppercase tracking-wider mt-2 text-muted">Sincronizando Banco de Dados...</p>
    </section>

    <section v-else class="row g-4">
      <div class="col-12">
        <div class="card border rounded-0 shadow-sm bg-white overflow-hidden">
          <div class="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <h2 class="h6 fw-bold text-uppercase m-0">Mapa de Densidade (Hotspots)</h2>
            <span class="small text-muted">Concentração geográfica de aparições</span>
          </div>
          <div ref="mapAnaliseEl" style="height: 450px; z-index: 1;"></div>
        </div>
      </div>

    <section class="col-md-6">
        <div class="card border rounded-0 shadow-sm bg-white p-4">
          <h2 class="h6 fw-bold text-uppercase text-secondary border-bottom pb-2 mb-4"> Diversidade Biológica</h2>
          <apexchart type="donut" height="300" :options="opcoesPizza" :series="seriesPizza" />
        </div>
    </section>

      <section class="col-md-6">
        <div class="card border rounded-0 shadow-sm bg-white p-4">
          <h2 class="h6 fw-bold text-uppercase text-secondary border-bottom pb-2 mb-4"> Status de Sobrevivência</h2>
          <apexchart type="bar" height="300" :options="opcoesBarras" :series="seriesBarras" />
        </div>
      </section>
    </section>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, nextTick } from 'vue'
import axios from 'axios'
import apexchart from 'vue3-apexcharts'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'leaflet.heat' // Plugin de Calor

const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
const API_BASE_URL = isLocal ? 'http://localhost:8000' : 'https://conviva-labev.onrender.com'

const dadosBrutos = ref([])
const carregando = ref(true)
const mapAnaliseEl = ref(null)
let mapInstance = null

const totalEspecies = computed(() => new Set(dadosBrutos.value.map(d => d.especie)).size)
const casosNoturnos = computed(() => dadosBrutos.value.filter(d => d.hora >= 18 || d.hora <= 5).length)
const casosCriticos = computed(() => dadosBrutos.value.filter(d => ['Morto', 'Ferido'].includes(d.situacao)).length)

const seriesPizza = computed(() => {
  const contagem = {}
  dadosBrutos.value.forEach(d => contagem[d.especie] = (contagem[d.especie] || 0) + 1)
  return Object.values(contagem)
})

const opcoesPizza = computed(() => ({
  labels: [...new Set(dadosBrutos.value.map(d => d.especie))],
  colors: ['#2D6A4F', '#40916C', '#52B788', '#74C69D', '#95D5B2'],
  legend: { position: 'bottom' }
}))

const seriesBarras = computed(() => [{
  name: 'Registros',
  data: Object.values(contarPorPropriedade('situacao'))
}])

const opcoesBarras = computed(() => ({
  chart: { toolbar: { show: false } },
  xaxis: { categories: Object.keys(contarPorPropriedade('situacao')) },
  colors: ['#1B4332'],
  plotOptions: { bar: { borderRadius: 0, columnWidth: '60%' } }
}))

function contarPorPropriedade(prop) {
  const contagem = {}
  dadosBrutos.value.forEach(d => contagem[d[prop]] = (contagem[d[prop]] || 0) + 1)
  return contagem
}

const initMapAnalise = () => {
  if (!mapAnaliseEl.value) return

  if (mapInstance) mapInstance.remove()

  mapInstance = L.map(mapAnaliseEl.value).setView([-1.45, -48.48], 12)
  L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(mapInstance)

  const heatData = dadosBrutos.value
    .filter(d => Number.isFinite(d.lat) && Number.isFinite(d.lng))
    .map(d => [d.lat, d.lng, 0.5]) // [lat, lng, intensidade]

  if (heatData.length > 0) {
    L.heatLayer(heatData, {
      radius: 25,
      blur: 15,
      maxZoom: 17,
      gradient: { 0.4: 'blue', 0.6: 'lime', 0.8: 'yellow', 1: 'red' }
    }).addTo(mapInstance)
  }
}

const carregarDadosAnalise = async () => {
  try {
    carregando.value = true
    const response = await axios.get(`${API_BASE_URL}/api/analise/ocorrencias`)
    dadosBrutos.value = response.data
  } catch (error) {
    console.error('Erro ao carregar dados:', error)
  } finally {
    carregando.value = false
  }

  await nextTick()
  initMapAnalise()
}

onMounted(() => {
  carregarDadosAnalise()
})
</script>

<style scoped>
.rounded-0 { border-radius: 0px !important; }
.fw-extrabold { font-weight: 800; }
.tiny-text { font-size: 0.65rem; letter-spacing: 0.1em; }
.text-sans-serif { font-family: 'Inter', sans-serif; }
#mapAnalise { border: 0; background: #f8f9fa; }
</style>