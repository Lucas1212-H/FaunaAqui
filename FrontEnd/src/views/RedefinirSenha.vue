<template>
  <div class="login-page d-flex align-items-center justify-content-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="auth-card shadow-lg p-5">
            <h2 class="auth-title mb-4">Redefinir Senha</h2>
            <p class="text-dark mb-4">Digite sua nova senha abaixo.</p>
            
            <div v-if="mensagem" class="alert alert-success small py-2" role="alert">
              {{ mensagem }}
            </div>
            
            <div v-if="erro" class="alert alert-danger small py-2" role="alert">
              {{ erro }}
            </div>
            
            <form @submit.prevent="redefinirSenha" v-if="!mensagem">
              <div class="mb-4">
                <label class="form-label">E-mail</label>
                <input 
                  type="email" 
                  v-model="email" 
                  class="form-control custom-input" 
                  disabled
                >
              </div>

              <div class="mb-4">
                <label class="form-label">Nova Senha</label>
                <input 
                  type="password" 
                  v-model="password" 
                  class="form-control custom-input" 
                  placeholder="Mínimo de 6 caracteres" 
                  :disabled="carregando"
                  required
                >
              </div>

              <div class="mb-4">
                <label class="form-label">Confirmar Nova Senha</label>
                <input 
                  type="password" 
                  v-model="password_confirmation" 
                  class="form-control custom-input" 
                  placeholder="Repita a nova senha" 
                  :disabled="carregando"
                  required
                >
              </div>
              
              <button 
                type="submit" 
                :disabled="carregando"
                class="btn btn-dark w-100 py-3 fw-bold mb-3"
              >
                <span v-if="carregando" class="spinner-border spinner-border-sm me-2"></span>
                {{ carregando ? 'Redefinindo...' : 'Redefinir Senha' }}
              </button>
            </form>

            <div class="text-center mt-3" v-if="mensagem">
              <RouterLink to="/login" class="btn btn-dark fw-bold px-4">
                Ir para o Login
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import axios from 'axios'

const route = useRoute()
const router = useRouter()

const email = ref('')
const token = ref('')
const password = ref('')
const password_confirmation = ref('')

const carregando = ref(false)
const mensagem = ref('')
const erro = ref('')

const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
const API_BASE_URL = isLocal ? 'http://localhost:8000/api' : 'https://conviva-labev.onrender.com/api'

onMounted(() => {
  email.value = route.query.email || ''
  token.value = route.query.token || ''

  if (!token.value) {
    erro.value = 'Token de recuperação ausente ou inválido.'
  }
})

const redefinirSenha = async () => {
  if (password.value !== password_confirmation.value) {
    erro.value = 'As senhas não coincidem.'
    return
  }

  try {
    erro.value = ''
    mensagem.value = ''
    carregando.value = true

    const response = await axios.post(`${API_BASE_URL}/redefinir-senha`, {
      email: email.value,
      token: token.value,
      password: password.value,
      password_confirmation: password_confirmation.value
    })

    mensagem.value = response.data.message || 'Senha redefinida com sucesso!'
  } catch (err) {
    erro.value = err.response?.data?.message || 'Não foi possível redefinir a senha.'
    console.error(err)
  } finally {
    carregando.value = false
  }
}
</script>

<style scoped>
.login-page { 
  min-height: 100vh; 
  background-color: #f8fafc; 
}

.auth-card { 
  background-color: #9cdb81;
  border-radius: 40px; 
}

.auth-title { 
  font-weight: 700; 
  color: #1e293b; 
}

.custom-input { 
  background: rgba(255,255,255,0.5); 
  border: 1px solid rgba(0,0,0,0.1);
  border-radius: 10px;
  padding: 12px;
}

.custom-input:focus {
  background-color: #ffffff;
  border-color: #1e293b;
  box-shadow: 0 0 0 0.25rem rgba(30, 41, 59, 0.25);
}
</style>
