<template>
  <NavBarPublic />

  <main v-if="!carregando && post" class="container my-5 text-start" style="max-width: 800px;">
    
    <header class="mb-4">
      <span class="badge rounded-0 text-uppercase tracking-wider px-3 py-2 mb-2" :class="corBadge(post.tipo)">
        {{ post.tipo }}
      </span>
      <h1 class="display-5 fw-bold text-dark lh-sm">{{ post.titulo }}</h1>
      
      <div class="text-muted small border-top border-bottom py-2 my-3 d-flex justify-content-between">
        <time>{{ formatarData(post.created_at) }}</time>
      </div>
    </header>

    <img 
      :src="post.imagem_url || 'https://images.unsplash.com/photo-1546182990-dffeafbe841d?auto=format&fit=crop&w=1200&q=80'" 
      class="img-fluid border mb-4 w-100" 
      style="max-height: 450px; object-fit: cover;"
      alt="Banner da publicação"
    />

    <article class="lh-lg fs-5 mb-5" style="white-space: pre-line;">
      {{ post.conteudo }}
    </article>

    <footer v-if="post.link_externo" class="border-top pt-4 text-center">
      <a 
        :href="post.link_externo" 
        target="_blank" 
        class="btn btn-dark rounded-0 px-5 py-3 text-uppercase fw-bold tracking-wider w-100"
      >
        {{ post.tipo === 'Anuncio' ? 'Ir para Loja / Comprar' : 'Acessar Link' }}
      </a>
    </footer>
  </main>

  <div v-if="!carregando && !post" class="text-center my-5 p-5">
    <p class="text-muted">Publicação não encontrada.</p>
    <button class="btn btn-dark rounded-0" @click="$router.push('/blog')">Voltar para notícias</button>
  </div>

  <div v-if="carregando" class="text-center my-5 p-5">
    <div class="spinner-border text-dark rounded-0" role="status"></div>
  </div>

  <Footer />
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import NavBarPublic from '@/components/NavBarPublic.vue'
import Footer from '@/components/Footer.vue'

const route = useRoute()
const post = ref(null)
const discouraged = ref(false)
const carregando = ref(true)

const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
const API_BASE_URL = isLocal ? 'http://localhost:8000' : 'https://conviva-labev.onrender.com'

const carregarDetalhes = async () => {
  try {
    carregando.value = true
    const id = route.params.id // Captura o ID da URL externa
    
    const response = await axios.get(`${API_BASE_URL}/api/posts`)
    
    // Certifica-se de extrair o array de forma segura, não importa como venha do Laravel
    const todosPosts = Array.isArray(response.data) ? response.data : response.data.data || []
    
    // 🟢 Usamos dois sinais de igual (==) para ignorar diferenças de string/número
    post.value = todosPosts.find(p => p.id == id)
    
    console.log('Post encontrado:', post.value) // Monitoramento no console F12
  } catch (error) {
    console.error('Erro ao abrir detalhes:', error)
  } finally {
    carregando.value = false
  }
}

const corBadge = (tipo) => {
  if (tipo === 'Noticia') return 'bg-success text-white'
  if (tipo === 'Conferencia') return 'bg-warning text-dark'
  return 'bg-secondary text-white'
}

const formatarData = (dataString) => {
  return new Date(dataString).toLocaleDateString('pt-BR', { day: '2-digit', month: 'long', year: 'numeric' })
}

onMounted(() => {
  carregarDetalhes()
})
</script>

<style scoped>
.rounded-0 { border-radius: 0 !important; }
.tracking-wider { letter-spacing: 0.08em; }
</style>