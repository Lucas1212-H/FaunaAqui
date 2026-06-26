<template>
  <NavBar />

  <main class="container my-5 text-start">
    <header class="border-bottom pb-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div>
        <h1 class="h4 fw-bold text-dark text-uppercase m-0">Gerenciar Publicações</h1>
        <p class="text-muted small m-0">Publique novidades, anúncios de vendas ou conferências no feed do ConViva.</p>
      </div>
      <button class="btn btn-dark rounded-0 text-uppercase fw-semibold btn-sm px-4 py-2" @click="abrirModoCriacao">
        Nova Publicação
      </button>
    </header>

    <div v-if="mensagemSucesso" class="alert alert-success rounded-0 small py-2">{{ mensagemSucesso }}</div>
    <div v-if="mensagemErro" class="alert alert-danger rounded-0 small py-2">{{ mensagemErro }}</div>

    <section v-if="exibirFormulario" class="card rounded-0 border p-4 mb-5 shadow-sm bg-light">
      <h2 class="h6 fw-bold text-uppercase border-bottom pb-2 mb-4">
        {{ emEdicao ? 'Editar Publicação' : 'Nova Publicação' }}
      </h2>

      <form @submit.prevent="salvarPost" class="row g-3">
        <div class="col-md-8">
          <label for="titulo" class="form-label small fw-semibold text-uppercase">Título</label>
          <input type="text" id="titulo" v-model="form.titulo" class="form-control rounded-0" required>
        </div>

        <div class="col-md-4">
          <label for="tipo" class="form-label small fw-semibold text-uppercase">Tipo de Post</label>
          <select id="tipo" v-model="form.tipo" class="form-select rounded-0" required>
            <option value="Noticia">Notícia</option>
            <option value="Conferencia">Conferência</option>
            <option value="Anuncio">Venda / Anúncio</option>
          </select>
        </div>

        <div class="col-112">
          <label for="conteudo" class="form-label small fw-semibold text-uppercase">Conteúdo do Post</label>
          <textarea id="conteudo" v-model="form.conteudo" class="form-control rounded-0" rows="4" required></textarea>
        </div>

        <div class="col-md-6">
          <label for="imagem" class="form-label small fw-semibold text-uppercase">Anexar Imagem Banner</label>
          <input type="file" id="imagem" @change="manipularUploadImagem" class="form-control rounded-0" accept="image/*">
        </div>

        <div class="col-md-6">
          <label for="link_externo" class="form-label small fw-semibold text-uppercase">Link de Ação Extenal (Inscrição / Compra)</label>
          <input type="url" id="link_externo" v-model="form.link_externo" class="form-control rounded-0" placeholder="https://loja.com/camisa">
        </div>

        <div class="col-12 d-flex justify-content-end gap-2 mt-4">
          <button type="button" class="btn btn-outline-secondary rounded-0 text-uppercase btn-sm px-4" @click="fecharFormulario">
            Cancelar
          </button>
          <button type="submit" class="btn btn-dark rounded-0 text-uppercase btn-sm px-4" :disabled="salvando">
            <span v-if="salvando" class="spinner-border spinner-border-sm me-2 rounded-0"></span>
            {{ emEdicao ? 'Atualizar' : 'Publicar' }}
          </button>
        </div>
      </form>
    </section>

    <section class="border rounded-0 overflow-hidden shadow-sm">
      <div v-if="carregando" class="text-center my-5">
        <div class="spinner-border text-dark rounded-0" role="status"></div>
      </div>

      <table v-else class="table table-hover m-0 align-middle small text-uppercase">
        <thead class="table-dark">
          <tr>
            <th scope="col" class="ps-3 py-3">Título</th>
            <th scope="col" class="py-3">Tipo</th>
            <th scope="col" class="py-3">Autor</th>
            <th scope="col" class="py-3">Data</th>
            <th scope="col" class="text-end pe-3 py-3">Ações</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="post in posts" :key="post.id">
            <td class="fw-bold text-dark ps-3 text-truncate" style="max-width: 250px;">{{ post.titulo }}</td>
            <td>
              <span class="badge border rounded-0 px-2 py-1" :class="classeBadge(post.tipo)">{{ post.tipo }}</span>
            </td>
            <td>{{ post.autor?.name || 'Admin' }}</td>
            <td>{{ formatarData(post.created_at) }}</td>
            <td class="text-end pe-3">
              <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-dark rounded-0 px-3" @click="prepararEdicao(post)">Editar</button>
                <button class="btn btn-outline-danger rounded-0 px-3" @click="excluirPost(post.id)">Excluir</button>
              </div>
            </td>
          </tr>
          <tr v-if="posts.length === 0">
            <td colspan="5" class="text-center text-muted py-4">Nenhuma publicação cadastrada no sistema.</td>
          </tr>
        </tbody>
      </table>
    </section>
  </main>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import axios from 'axios'
import NavBar from '@/components/NavBar.vue'

const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
const API_BASE_URL = isLocal ? 'http://localhost:8000' : 'https://conviva-labev.onrender.com'

const posts = ref([])
const carregando = ref(true)
const salvando = ref(false)
const exibirFormulario = ref(false)
const emEdicao = ref(false)
const idPostSelecionado = ref(null)
const arquivoImagem = ref(null)

const mensagemSucesso = ref('')
const mensagemErro = ref('')


const manipularUploadImagem = (event) => {
  const target = event.target
  if (target.files && target.files[0]) {
    arquivoImagem.value = target.files[0]
  }
}

const form = reactive({
  titulo: '',
  conteudo: '',
  tipo: 'Noticia',
  imagem_url: '',
  link_externo: ''
})

const buscarPosts = async () => {
  try {
    carregando.value = true
    const response = await axios.get(`${API_BASE_URL}/api/posts`)
    posts.value = response.data
  } catch (error) {
    console.error(error)
  } finally {
    carregando.value = false
  }
}

const abrirModoCriacao = () => {
  emEdicao.value = false
  idPostSelecionado.value = null
  exibirFormulario.value = true
  Object.assign(form, { titulo: '', conteudo: '', tipo: 'Noticia', imagem_url: '', link_externo: '' })
  arquivoImagem.value = null
  const input = document.getElementById('imagem')
  if (input) {
    input.value = ''
  }
}

const prepararEdicao = (post) => {
  emEdicao.value = true
  idPostSelecionado.value = post.id
  exibirFormulario.value = true
  Object.assign(form, {
    titulo: post.titulo,
    conteudo: post.conteudo,
    tipo: post.tipo,
    imagem_url: post.imagem_url || '',
    link_externo: post.link_externo || ''
  })
}

const fecharFormulario = () => {
  exibirFormulario.value = false
  emEdicao.value = false
}

// Modifique a função salvarPost para usar FormData
const salvarPost = async () => {
  try {
    salvando.value = true
    mensagemSucesso.value = ''
    mensagemErro.value = ''
    const token = localStorage.getItem('fauna_token')

    // Instancia o FormData necessário para upload de arquivos
    const formData = new FormData()
    formData.append('titulo', form.titulo)
    formData.append('conteudo', form.conteudo)
    formData.append('tipo', form.tipo)
    
    if (form.link_externo) {
      formData.append('link_externo', form.link_externo)
    }
    
    if (arquivoImagem.value) {
      formData.append('imagem', arquivoImagem.value)
    }

    let response
    if (emEdicao.value) {
      // Nota: O Laravel às vezes exige método POST com alteração de rota (_method = PUT) para processar arquivos em updates.
      formData.append('_method', 'PUT')
      response = await axios.post(`${API_BASE_URL}/api/posts/${idPostSelecionado.value}`, formData, {
        headers: { 
          Authorization: `Bearer ${token}`,
          'Content-Type': 'multipart/form-data'
        }
      })
    } else {
      response = await axios.post(`${API_BASE_URL}/api/posts`, formData, {
        headers: { 
          Authorization: `Bearer ${token}`,
          'Content-Type': 'multipart/form-data'
        }
      })
    }

    mensagemSucesso.value = response.data.message
    fecharFormulario()
    buscarPosts()
  } catch (error) {
    mensagemErro.value = error.response?.data?.message || 'Erro ao salvar publicação.'
  } finally {
    salvando.value = false
  }
}

const excluirPost = async (id) => {
  if (!confirm('Tem certeza de que deseja excluir permanentemente esta publicação?')) return

  try {
    const token = localStorage.getItem('fauna_token')
    const response = await axios.delete(`${API_BASE_URL}/api/posts/${id}`, {
      headers: { Authorization: `Bearer ${token}` }
    })
    mensagemSucesso.value = response.data.message
    buscarPosts()
  } catch (error) {
    mensagemErro.value = 'Erro ao tentar excluir a publicação.'
  }
}

const classeBadge = (tipo) => {
  if (tipo === 'Noticia') return 'text-success border-success bg-success-subtle'
  if (tipo === 'Conferencia') return 'text-warning border-warning bg-warning-subtle'
  return 'text-secondary border-secondary bg-light'
}

const formatarData = (dataString) => {
  return new Date(dataString).toLocaleDateString('pt-BR')
}

onMounted(() => {
  buscarPosts()
})
</script>

<style scoped>
.rounded-0 {
  border-radius: 0 !important;
}
</style>