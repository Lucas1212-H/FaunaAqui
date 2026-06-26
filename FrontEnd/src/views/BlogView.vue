<template>
  <div class="noticias-page bg-light text-dark text-sans-serif">
    <NavBarPublic />

    <header class="hero-banner position-relative d-flex align-items-center justify-content-start text-start">
      <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
      
      <div class="container position-relative z-index-2 text-white px-4 px-md-5">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4 w-100">
          <div>
            <nav aria-label="breadcrumb" class="mb-3">
              <ol class="breadcrumb tiny-text tracking-widest text-uppercase m-0">
                <li class="breadcrumb-item">
                  <RouterLink class="text-white-50 text-decoration-none" to="/">Início</RouterLink>
                </li>
                <li class="breadcrumb-item active text-white fw-bold" aria-current="page">Notícias</li>
              </ol>
            </nav>
            <span class="text-uppercase tracking-widest fw-bold text-success-light mb-1 d-inline-block small">Comunicação e Mídia</span>
            <h1 class="display-3 fw-extrabold m-0 text-uppercase tracking-tight hero-title">Central de Conteúdo</h1>
          </div>

          <aside class="banner-text-right" style="max-width: 400px;">
            <p class="fs-5 fw-light lh-lg m-0">
              Fique por dentro de notícias, artigos científicos, conferências e comunicados oficiais do ecossistema <strong>ConViva</strong>.
            </p>
          </aside>
        </div>
      </div>
    </header>

    <main class="container my-5">
      
      <nav class="d-flex justify-content-center gap-2 mb-5 flex-wrap" aria-label="Filtro de publicações">
        <button 
          v-for="aba in ['Todos', 'Noticia', 'Conferencia', 'Anuncio']" 
          :key="aba"
          class="btn btn-filter text-uppercase fw-bold px-4 py-2 rounded-0 text-sans-serif"
          :class="filtroAtivo === aba ? 'btn-dark' : 'btn-outline-dark'"
          @click="filtroAtivo = aba"
        >
          {{ labelFiltro(aba) }}
        </button>
      </nav>

      <section v-if="carregando" class="text-center my-5 py-5">
        <div class="spinner-border text-dark rounded-0" role="status"></div>
        <p class="text-muted small text-uppercase tracking-widest mt-3">Sincronizando publicações...</p>
      </section>

      <section v-else-if="postsFiltrados.length === 0" class="text-center my-5 p-5 border bg-white text-muted text-uppercase small tracking-wider rounded-0 shadow-sm">
        Nenhuma publicação disponível nesta categoria no momento.
      </section>

      <section v-else class="row row-cols-1 row-cols-md-2 row-cols-lg-2 g-4 text-start">
        <article 
          class="col" 
          v-for="post in postsFiltrados" 
          :key="post.id" 
          @click="$router.push(`/noticias/${post.id}`)" 
          style="cursor: pointer;"
        >
          <div class="card h-100 border rounded-0 shadow-sm position-relative overflow-hidden card-post-link bg-white">
            
            <span class="badge position-absolute top-0 end-0 m-3 px-3 py-2 rounded-0 text-uppercase tracking-widest shadow-sm border" :class="corBadge(post.tipo)">
              {{ labelFiltro(post.tipo) }}
            </span>

            <img 
              :src="post.imagem_url || 'https://images.unsplash.com/photo-1546182990-dffeafbe841d?auto=format&fit=crop&w=600&q=80'" 
              class="card-img-top rounded-0 border-bottom" 
              style="height: 240px; object-fit: cover;"
              alt="Capa da publicação"
            />

            <div class="card-body d-flex flex-column p-4">
              <h2 class="card-title h4 fw-bold text-dark text-truncate-2 mb-3 lh-base">{{ post.titulo }}</h2>
              
              <p class="card-text text-secondary small text-truncate-4 flex-grow-1 mb-4 lh-lg">
                {{ post.conteudo }}
              </p>
              
              <footer class="pt-2 border-top d-flex justify-content-between align-items-center mt-auto">
                <div class="text-muted small">
                  Publicado em: <time :datetime="post.created_at" class="fw-semibold text-dark">{{ formatarData(post.created_at) }}</time>
                </div>
                <span class="text-uppercase fw-bold text-success-light tracking-wider small link-fake">Ler Artigo →</span>
              </footer>
            </div>
          </div>
        </article>
      </section>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { RouterLink } from 'vue-router'
import axios from 'axios'
import NavBarPublic from '@/components/NavBarPublic.vue'
import Footer from '@/components/Footer.vue'

const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
const API_BASE_URL = isLocal ? 'http://localhost:8000' : 'https://conviva-labev.onrender.com'
const posts = ref([])
const carregando = ref(true)
const filtroAtivo = ref('Todos')

const buscarPosts = async () => {
  try {
    carregando.value = true
    const response = await axios.get(`${API_BASE_URL}/api/posts`)
    
    if (Array.isArray(response.data)) {
      posts.value = response.data
    } else if (response.data && Array.isArray(response.data.data)) {
      posts.value = response.data.data
    } else {
      posts.value = []
    } 
  } catch (error) {
      console.error('Erro ao buscar publicações:', error)
      posts.value = []
    } finally {
      carregando.value = false
    }
}

const postsFiltrados = computed(() => {
  if (filtroAtivo.value === 'Todos') return posts.value
  return posts.value.filter(post => post.tipo === filtroAtivo.value)
})

const labelFiltro = (tipo) => {
  const labels = {
    'Todos': 'Ver Tudo',
    'Noticia': 'Notícias',
    'Conferencia': 'Conferências',
    'Anuncio': 'Anúncios'
  }
  return labels[tipo] || tipo
}

const corBadge = (tipo) => {
  if (tipo === 'Noticia') return 'bg-success text-white border-success'
  if (tipo === 'Conferencia') return 'bg-warning text-dark border-warning'
  return 'bg-dark text-white border-dark'
}

const formatarData = (dataString) => {
  if (!dataString) return '-'
  const data = new Date(dataString)
  return data.toLocaleDateString('pt-BR', { day: '2-digit', month: 'short', year: 'numeric' })
}

onMounted(() => {
  buscarPosts()
})
</script>

<style scoped>
.rounded-0 { border-radius: 0px !important; }
.fw-extrabold { font-weight: 800; }
.tracking-widest { letter-spacing: 0.15em; }
.tracking-wider { letter-spacing: 0.08em; }
.tracking-tight { letter-spacing: -0.02em; }
.text-success-light { color: #58d68d; }

.text-sans-serif {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  -webkit-font-smoothing: antialiased;
}

.hero-banner {
  min-height: 400px;
  background: url('@/assets/images/banner_macaco.jpg') center/cover no-repeat;
}

.hero-overlay {
  background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.5) 100%);
  z-index: 1;
}

.z-index-2 {
  z-index: 2;
}

.btn-filter {
  font-size: 0.85rem;
  letter-spacing: 0.08em;
  transition: all 0.2s ease;
  border-width: 1px;
}

.card-post-link {
  transition: transform 0.2s ease, border-color 0.2s ease;
}

.card-post-link:hover {
  transform: translateY(-4px);
  border-color: #1f2937 !important;
}

.link-fake {
  font-size: 0.85rem;
}

.text-truncate-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}

.text-truncate-4 {
  display: -webkit-box;
  -webkit-line-clamp: 4;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}

.breadcrumb-item + .breadcrumb-item::before {
  content: "→" !important;
  color: rgba(255, 255, 255, 0.4) !important;
  font-size: 0.85rem;
  padding: 0 8px;
}

.tiny-text {
  font-size: 0.75rem;
}

@media (max-width: 991.98px) {
  .hero-banner { min-height: 360px; }
  .hero-banner .d-flex { flex-direction: column !important; align-items: flex-start !important; }
  .banner-text-right { max-width: 100%; margin-top: 1rem; }
}
</style>