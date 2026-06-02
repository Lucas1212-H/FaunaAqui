<template>
  <div class="home-page">
    <NavBarPublic />
    
    <header class="hero-banner position-relative d-flex align-items-end">
      <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
      <div class="container-fluid px-5 pb-5 position-relative z-index-2 text-white">
        <h1 class="display-4 fw-bold m-0 hero-title">Home</h1>
      </div>
      <aside class="banner-text-right position-absolute end-0 top-50 translate-middle-y pe-5 text-white" style="max-width: 450px;">
        <p class="fs-5 fw-light lh-lg">
          Entre macacos, sapos, cobras e lagartos, SALVAM-SE TODOS! Projeto de coexistência e conservação de animais silvestres no campus da UFPA
        </p>
      </aside>
    </header>

    <main class="container position-relative text-white">
      <section class="row align-items-center">
        <div class="col-lg-6 px-10">
          <article class="hero-card shadow-lg">
            <h2 class="display-10 fw-bold text-dark">Encontrou um animal silvestre na região?</h2>
            <p class="lead text-dark">Ajude a ciência e a preservação local com apenas alguns cliques.</p>
            <button @click="irParaDenuncia" class="btn btn-warning btn-lg fw-bold mt-3 px-5 py-3">Denuncie Aqui</button>
          </article>
        </div>
        <div class="col-lg-6 p-5">
          <div id="mapa-fauna" class="rounded-4 shadow-sm" style="height: 350px;"></div>
        </div>
      </section>
      <section class="py-5 catalog-section">
        <div class="container position-relative px-md-5">
          <h2 class="fw-bold mb-2 pb-3 text-center section-title">Animais Catalogados</h2>
          
          <template v-if="carregandoAnimaisCatalogados">
            <div class="text-center py-5">
              <div class="spinner-border text-success" role="status" aria-label="Carregando animais catalogados"></div>
              <p class="text-muted mt-3 mb-0">Carregando animais catalogados...</p>
            </div>
          </template>

          <template v-else-if="erroAnimaisCatalogados">
            <div class="alert alert-warning border-0 shadow-sm" role="alert">
              {{ erroAnimaisCatalogados }}
            </div>
          </template>

          <template v-else-if="animaisExibidos.length === 0">
            <div class="alert alert-light border shadow-sm text-center py-4" role="status">
              Nenhuma espécie catalogada.
            </div>
          </template>

          <template v-else>
            <div id="catalogoAnimaisCarousel" class="carousel slide catalog-carousel" data-bs-ride="carousel">
              <div class="carousel-indicators">
                <button
                  v-for="(grupo, index) in animaisAgrupados"
                  :key="index"
                  type="button"
                  data-bs-target="#catalogoAnimaisCarousel"
                  :data-bs-slide-to="index"
                  :class="{ active: index === 0 }"
                  :aria-current="index === 0 ? 'true' : 'false'"
                  :aria-label="`Slide ${index + 1}`"
                ></button>
              </div>

              <div class="carousel-inner">
                <section
                  v-for="(grupo, index) in animaisAgrupados"
                  :key="index"
                  class="carousel-item"
                  :class="{ active: index === 0 }"
                >
                  <div class="row g-4 justify-content-center px-2">
                    <article 
                      v-for="animal in grupo" 
                      :key="animal.id" 
                      class="col-12 col-md-6 col-lg-4 catalog-card shadow-sm h-100"
                    >
                      <figure class="m-0 overflow-hidden rounded-top">
                        <img :src="animal.imagem" :alt="animal.nome" class="catalog-image w-100 h-100" />
                      </figure>
                      <div class="p-4 d-flex flex-column justify-content-between h-100">
                        <div>
                          <span class="badge rounded-pill text-bg-success mb-2">{{ animal.categoria }}</span>
                          <h3 class="h4 fw-bold mb-1 text-dark text-truncate">{{ animal.nome }}</h3>
                          <p class="text-secondary small fst-italic mb-3 text-truncate">{{ animal.nomeCientifico }}</p>
                          <p class="text-body-secondary small mb-0 linha-limitada">{{ animal.descricao }}</p>
                        </div>
                      </div>
                    </article>
                  </div>
                </section>
              </div>

              <template v-if="animaisAgrupados.length > 1">
                <button class="carousel-control-prev" type="button" data-bs-target="#catalogoAnimaisCarousel" data-bs-slide="prev">
                  <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Anterior</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#catalogoAnimaisCarousel" data-bs-slide="next">
                  <span class="carousel-control-next-icon" aria-hidden="true"></span>
                  <span class="visually-hidden">Próximo</span>
                </button>
              </template>
            </div>
          </template>
        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>

<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import axios from 'axios'
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'
import NavBarPublic from '@/components/NavBarPublic.vue'
import Footer from '@/components/Footer.vue'

type Publicado = {
  coordenadas?: {
    lat?: number
    lng?: number
  }
  animal?: string
  local?: string
  data?: string
}

type CategoriaApi = {
  id_categoria: number
  nome_popular?: string | null
  nome_cientifico?: string | null
  foto?: string | null
}

type EspecieApi = {
  id_especie: number
  nome_popular?: string | null
  nome_cientifico?: string | null
  descricao?: string | null
  foto?: string | null
  categoria?: CategoriaApi | null
}

type AnimalCatalogado = {
  id: number
  nome: string
  nomeCientifico: string
  categoria: string
  descricao: string
  imagem: string
}
const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'

const router = useRouter()
const API_BASE = isLocal 
  ? 'http://localhost:8000/api' 
  : 'https://conviva-labev.onrender.com/api'

const STORAGE_BASE = isLocal 
  ? 'http://localhost:8000/storage' 
  : 'https://conviva-labev.onrender.com/storage'
const FALLBACK_IMAGE = 'https://cdn-icons-png.flaticon.com/512/616/616408.png'
const MAX_ANIMAIS_CARROSSEL = 9

const carregandoAnimaisCatalogados = ref(true)
const erroAnimaisCatalogados = ref('')
const animaisCatalogados = ref<AnimalCatalogado[]>([])

const props = defineProps({
  publicados: {
    type: Array,
    default: () => [],
  },
})

const animaisExibidos = computed(() => animaisCatalogados.value.slice(0, MAX_ANIMAIS_CARROSSEL))

const animaisAgrupados = computed(() => {
  const grupos = []
  const tamanhoGrupo = 3
  for (let i = 0; i < animaisExibidos.value.length; i += tamanhoGrupo) {
    grupos.push(animaisExibidos.value.slice(i, i + tamanhoGrupo))
  }
  return grupos
})

let map: L.Map | null = null
const markerGroup = L.layerGroup()

const LAT_UFPA = -1.4748
const LNG_UFPA = -48.4542

const irParaDenuncia = () => {
  router.push('/denuncia')
}

const irParaCatalogo = () => {
  router.push('/catalogo')
}

const normalizarTexto = (valor: unknown) => (valor ?? '').toString().trim()

const carregarAnimaisCatalogados = async () => {
  try {
    carregandoAnimaisCatalogados.value = true
    erroAnimaisCatalogados.value = ''

    const response = await axios.get<EspecieApi[]>(`${API_BASE}/especies`)

    animaisCatalogados.value = response.data.map((especie) => ({
      id: especie.id_especie,
      nome: normalizarTexto(especie.nome_popular) || 'Espécie sem nome popular',
      nomeCientifico: normalizarTexto(especie.nome_cientifico) || 'Nome científico indisponível',
      categoria:
        normalizarTexto(especie.categoria?.nome_popular) ||
        normalizarTexto(especie.categoria?.nome_cientifico) ||
        'Categoria não informada',
      descricao: normalizarTexto(especie.descricao) || 'Sem descrição cadastrada para esta espécie.',
      imagem: especie.foto ? `${STORAGE_BASE}/${especie.foto}` : FALLBACK_IMAGE,
    }))
  } catch (error) {
    console.error('Erro ao carregar animais catalogados:', error)
    erroAnimaisCatalogados.value = 'Não foi possível carregar os animais catalogados no momento.'
    animaisCatalogados.value = []
  } finally {
    carregandoAnimaisCatalogados.value = false
  }
}

const renderizarMarcadores = () => {
  if (!map) return

  markerGroup.clearLayers()

  ;(props.publicados as Publicado[]).forEach((animal) => {
    const lat = animal?.coordenadas?.lat
    const lng = animal?.coordenadas?.lng

    if (typeof lat === 'number' && typeof lng === 'number') {
      const marker = L.marker([lat, lng])

      const popupContent = `
        <div style="font-family: sans-serif; padding: 5px;">
          <h6 style="margin: 0 0 5px 0; font-weight: bold; color: #198754;">${animal.animal ?? 'Animal'}</h6>
          <p style="margin: 0 0 5px 0; font-size: 12px;"><strong>Local:</strong> ${animal.local ?? 'Não informado'}</p>
          <p style="margin: 0; font-size: 11px; color: #6c757d;">Registrado em: ${animal.data ?? '-'}</p>
        </div>
      `

      marker.bindPopup(popupContent)
      markerGroup.addLayer(marker)
    }
  })
}

const inicializarMapa = () => {
  map = L.map('mapa-fauna').setView([LAT_UFPA, LNG_UFPA], 15)

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors',
  }).addTo(map)

  markerGroup.addTo(map)
  renderizarMarcadores()
}

watch(
  () => props.publicados,
  () => {
    renderizarMarcadores()
  },
  { deep: true }
)

onMounted(() => {
  inicializarMapa()
  carregarAnimaisCatalogados()
})
</script>

<style scoped>
.hero-banner {
  min-height: 400px;
  background: url('@/assets/images/banner_macaco.jpg') center/cover no-repeat;
}

.hero-overlay {
  background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.1) 100%);
}


.hero-card { 
  background: color#eef6ec; 
  padding: 3rem; border: ; 
  backdrop-filter: blur(8px); 
}

.banner-text-right {
  max-width: 450px;
}

.catalog-section {
  background: linear-gradient(180deg, #f7fbf8 0%, #eef6ef 100%);
}

.section-title {
  color: #14532d;
  font-size: 2.5rem;
}

.catalog-carousel {
  position: relative;
  padding-bottom: 2.5rem;
}

.catalog-card {
  border-radius: 20px;
  background: #ffffff;
  border: 1px solid rgba(20, 83, 45, 0.08);
  transition: transform 0.2s ease;
  overflow: hidden;
}

.catalog-card:hover {
  transform: translateY(-4px);
}

figure {
  height: 200px;
  width: 100%;
}

.catalog-image {
  object-fit: cover;
  width: 100%;
  height: 100%;
}

.linha-limitada {
  display: -webkit-box;
  line-clamp: 3;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.carousel-indicators [data-bs-target] {
  background-color: #2d6a4f;
}

.carousel-control-prev {
  left: -3.5rem;
}

.carousel-control-next {
  right: -3.5rem;
}

.carousel-control-prev-icon,
.carousel-control-next-icon {
  background-color: #2d6a4f;
  border-radius: 50%;
  padding: 1.25rem;
}

@media (max-width: 1200px) {
  .carousel-control-prev {
    left: -1.5rem;
  }

  .carousel-control-next {
    right: -1.5rem;
  }
}

@media (max-width: 767.98px) {
  #mapa-fauna {
    height: 350px;
  }

  .section-title {
    font-size: 2rem;
  }
}


</style>