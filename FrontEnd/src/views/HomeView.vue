<template>
  <div class="home-page">
    <NavBarPublic />

    <section class="hero-banner position-relative d-flex align-items-end p-5">
      <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
      <div class="position-absolute bottom-0 start-0 w-100 d-flex justify-content-start pb-5 pt-5 text-white z-index-2">
        <div class="p-0 ps-5 m-0" style="max-width: 650px; width: 100%;">
          <h1 class="fw-bold mb-4" style="font-size: 2.5rem; line-height: 1.2;">
            Entre Macacos, Sapos, Cobras e Lagartos, salvam-se todos!
          </h1>
          <h2 class="fw-medium m-0 opacity-90" style="font-size: 1.5rem; line-height: 1.4;">
            Projeto de Coexistência e Conservação de Animais Silvestres no Campus da UFPA
          </h2>
        </div>
      </div>
        
    </section>
    
    <main>
      <section class="py-5 report-map-section">
        <div class="container">
          <div class="row align-items-stretch justify-content-center g-4">
            
            <div class="col-12 col-lg-5 d-flex">
              <aside class="hero-card report-card p-4 p-md-5 w-100 h-100 d-flex flex-column justify-content-center">
                <span class="report-kicker mb-3">Reporte de forma rápida</span>
                <h2 class="report-card-title fw-bold mb-3">Encontrou um animal silvestre na região?</h2>
                <p class="report-card-text mb-4">Ajude a ciência e a preservação local informando a localização exata com apenas alguns cliques.</p>
                <button @click="irParaDenuncia" class="btn btn-success btn-lg fw-bold px-5 py-3 shadow-sm align-self-start">Denuncie Aqui</button>
              </aside>
            </div>

            <div class="col-12 col-lg-6 d-flex">
              <div class="report-map-card w-100 h-100">
                <div id="mapa-fauna" class="mapa-fauna" role="application" aria-label="Mapa de avistamentos de fauna silvestre"></div>
              </div>
            </div>

          </div>
        </div>
      </section>
      <section class="py-5 catalog-section">
        <div class="container position-relative px-md-5">
          <h2 class="fw-bold mb-2 pb-3 text-center section-title">Animais Catalogados</h2>
          
          <div v-if="carregandoAnimaisCatalogados" class="catalog-state text-center py-5">
            <div class="spinner-border text-success" role="status" aria-label="Carregando animais catalogados"></div>
            <p class="text-muted mt-3 mb-0">Carregando animais catalogados...</p>
          </div>

          <div v-else-if="erroAnimaisCatalogados" class="alert alert-warning border-0 shadow-sm" role="alert">
            {{ erroAnimaisCatalogados }}
          </div>

          <div v-else-if="animaisExibidos.length === 0" class="alert alert-light border shadow-sm text-center py-4" role="status">
            Nenhuma espécie catalogada.
          </div>

          <div v-else id="catalogoAnimaisCarousel" class="carousel slide catalog-carousel" data-bs-ride="carousel">
            
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
              <div
                v-for="(grupo, index) in animaisAgrupados"
                :key="index"
                class="carousel-item"
                :class="{ active: index === 0 }"
              >
                <div class="row g-4 justify-content-center px-2">
                  <div 
                    v-for="animal in grupo" 
                    :key="animal.id" 
                    class="col-12 col-md-6 col-lg-4"
                  >
                    <article class="catalog-card h-100 shadow-sm overflow-hidden d-flex flex-column">
                      <figure class="catalog-image-wrap m-0">
                        <img :src="animal.imagem" :alt="animal.nome" class="catalog-image w-100 h-100" />
                      </figure>
                      <div class="p-4 d-flex flex-column flex-grow-1 justify-content-between">
                        <div>
                          <span class="badge rounded-pill text-bg-success align-self-start mb-2">{{ animal.categoria }}</span>
                          <h3 class="h4 fw-bold mb-1 text-dark text-truncate">{{ animal.nome }}</h3>
                          <p class="text-secondary small fst-italic mb-3 text-truncate">{{ animal.nomeCientifico }}</p>
                          <p class="catalog-description text-body-secondary small mb-4 linha-limitada">{{ animal.descricao }}</p>
                        </div>
                      </div>
                    </article>
                  </div>
                </div>
              </div>
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

const router = useRouter()
const API_BASE = 'http://localhost:8000/api'
const STORAGE_BASE = 'http://localhost:8000/storage'
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
.hero-section {
  min-height: 40vh;
  background: url('@/assets/images/banner_macaco.jpg') center/cover no-repeat;
  position: relative;
}

.overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.6));
}

.report-map-section {
  background: linear-gradient(180deg, #f7fbf8 0%, #eef6ef 100%);
}

.hero-card {
  border: 1px solid rgba(20, 83, 45, 0.08);
  background-color: #ffffff;
  box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
  border-radius: 28px;
}

.report-kicker {
  display: inline-flex;
  align-self: flex-start;
  padding: 0.4rem 0.85rem;
  border-radius: 999px;
  background: rgba(132, 204, 22, 0.12);
  color: #14532d;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.report-card-title {
  color: #0f172a;
  font-size: clamp(1.9rem, 2vw, 2.6rem);
  line-height: 1.15;
}

.report-card-text {
  color: #475569;
  font-size: 1.05rem;
  line-height: 1.6;
  max-width: 34rem;
}

.report-map-card {
  display: flex;
  min-height: 100%;
  background: #ffffff;
  border: 1px solid rgba(20, 83, 45, 0.08);
  border-radius: 28px;
  box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
  overflow: hidden;
}

.mapa-fauna {
  flex: 1 1 auto;
  min-height: 100%;
  width: 100%;
  z-index: 1;
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
}

.catalog-card:hover {
  transform: translateY(-4px);
}

.catalog-image-wrap {
  height: 200px;
  width: 100%;
}

.catalog-image {
  object-fit: cover;
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

.carousel-control-prev { left: -3.5rem; }
.carousel-control-next { right: -3.5rem; }

.carousel-control-prev-icon,
.carousel-control-next-icon {
  background-color: #2d6a4f;
  border-radius: 50%;
  padding: 1.25rem;
}

@media (max-width: 1200px) {
  .carousel-control-prev { left: -1.5rem; }
  .carousel-control-next { right: -1.5rem; }
}

@media (max-width: 767.98px) {
  .mapa-fauna {
    height: 350px;
  }

  .section-title {
    font-size: 2rem;
  }
}

.hero-banner {
  min-height: 400px; /* Garante que o container tenha altura para o align-items-end empurrar o texto para baixo */
  background: url('@/assets/images/banner_macaco.jpg') center/cover no-repeat;
  border-bottom: 8px solid #84cc16; /* Linha verde limão na parte inferior identica à foto */
}

.hero-overlay {
  background: linear-gradient(to top, rgba(0, 0, 0, 0.85) 0%, rgba(0, 0, 0, 0.1) 100%);
}


</style>