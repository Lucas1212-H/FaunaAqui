<template>
  <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center py-4 painel-autenticacao">
    <div class="card card-custom border-0 p-4 p-sm-5 shadow w-100 text-dark">
      <div class="card-body">
        
        <div v-if="cadastroSolicitado" class="text-center py-4">
          <div class="fs-1 mb-3">📩</div>
          <h2 class="h4 fw-bold text-dark mb-3">Solicitação Enviada!</h2>
          <p class="small text-dark opacity-75 mb-4">
            Seus dados foram enviados para validação organizacional. Um e-mail de aviso será enviado assim que o administrador confirmar a criação da sua conta.
          </p>
          <button class="btn btn-black rounded-pill px-4" @click="irParaLogin">Voltar para o Login</button>
        </div>

        <div v-else>
          <h2 class="display-6 fw-normal mb-4 text-start">Cadastro</h2>

          <div v-if="erroMensagem" class="alert alert-danger small py-2 rounded-3 text-start" role="alert">
            {{ erroMensagem }}
          </div>

          <form @submit.prevent="lidarComCadastro">
            <div class="mb-3 text-start">
              <label for="nome" class="form-label small fw-semibold mb-1">Nome</label>
              <input type="text" id="nome" v-model="usuario.name" class="form-control input-custom py-2 shadow-sm" :disabled="carregando" required />
            </div>

            <div class="mb-3 text-start">
              <label for="email" class="form-label small fw-semibold mb-1">Email</label>
              <input type="type" id="email" v-model="usuario.email" class="form-control input-custom py-2 shadow-sm" :disabled="carregando" required />
            </div>

            <div class="mb-3 text-start">
              <label for="senha" class="form-label small fw-semibold mb-1">Senha</label>
              <input type="password" id="senha" v-model="usuario.password" class="form-control input-custom py-2 shadow-sm" placeholder="Mínimo 6 caracteres" :disabled="carregando" required />
            </div>

            <div class="d-grid mb-2">
              <button type="submit" class="btn btn-black py-2.5 rounded-pill fw-medium shadow-sm" :disabled="carregando">
                <span v-if="carregando" class="spinner-border spinner-border-sm me-2"></span>
                {{ carregando ? 'Processando...' : 'Solicitar Cadastro' }}
              </button>
            </div>
          </form>

          <div class="text-center mt-3">
            <span class="d-block text-secondary small mb-1">Já possui conta?</span>
            <a href="#" @click.prevent="irParaLogin" class="link-custom fw-semibold text-decoration-none fs-5">Clique Aqui</a>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive, ref } from 'vue'
import axios from 'axios'

const emit = defineEmits(['mudarTela'])

const carregando = ref(false)
const cadastroSolicitado = ref(false) // Alterna a tela para a mensagem de sucesso
const erroMensagem = ref('')

const usuario = reactive({
  name: '',
  email: '',
  password: ''
})

const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
const API_BASE_URL = isLocal ? 'http://localhost:8000/api' : 'https://conviva-labev.onrender.com/api'

const lidarComCadastro = async () => {
  try {
    carregando.value = true
    erroMensagem.value = ''

    await axios.post(`${API_BASE_URL}/registrar`, usuario)
    cadastroSolicitado.value = true // Exibe o aviso de e-mail enviado

  } catch (error) {
    console.error(error)
    erroMensagem.value = error.response?.data?.message || 'Erro ao processar cadastro.'
  } finally {
    carregando.value = false
  }
}

const irParaLogin = () => {
  emit('mudarTela', 'login')
}
</script>

<style scoped>
.painel-autenticacao { background-color: #f4f6f0; font-family: -apple-system, sans-serif; }
.card-custom { background-color: #93e27a !important; border-radius: 2.5rem !important; max-width: 500px; }
.input-custom { background-color: rgba(255, 255, 255, 0.4) !important; border: 1px solid #5fa64a !important; border-radius: 8px !important; color: #111111 !important; }
.input-custom:focus { background-color: #ffffff !important; border-color: #335d25 !important; box-shadow: 0 0 0 0.25rem rgba(51, 93, 37, 0.25) !important; }
.btn-black { background-color: #000000 !important; color: #ffffff !important; border: none; transition: opacity 0.2s ease; }
.btn-black:hover { opacity: 0.85; }
.link-custom { color: #4b39ef !important; }
.link-custom:hover { color: #3225b8 !important; }
</style>