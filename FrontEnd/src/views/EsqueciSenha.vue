<template>
  <div class="login-page d-flex align-items-center justify-content-center">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="auth-card shadow-lg p-5">
            <h2 class="auth-title mb-4">Esqueci Minha Senha</h2>
            <p class="text-dark mb-4">Informe o e-mail cadastrado e enviaremos um link para você redefinir sua senha.</p>
            
            <div v-if="mensagem" class="alert alert-success small py-2" role="alert">
              {{ mensagem }}
            </div>
            
            <div v-if="erro" class="alert alert-danger small py-2" role="alert">
              {{ erro }}
            </div>
            
            <form @submit.prevent="enviarLink">
              <div class="mb-4">
                <label class="form-label">E-mail</label>
                <input 
                  type="email" 
                  v-model="email" 
                  class="form-control custom-input" 
                  placeholder="seu@email.com" 
                  :disabled="carregando"
                  required
                >
              </div>
              
              <button 
                type="submit" 
                :disabled="carregando || mensagem"
                class="btn btn-dark w-100 py-3 fw-bold mb-3"
              >
                <span v-if="carregando" class="spinner-border spinner-border-sm me-2"></span>
                {{ carregando ? 'Enviando...' : 'Enviar Link de Recuperação' }}
              </button>
              
              <div class="text-center mt-3">
                <RouterLink to="/login" class="text-decoration-none text-dark opacity-75 small">
                  Voltar para o Login
                </RouterLink>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import axios from 'axios'

const email = ref('')
const carregando = ref(false)
const mensagem = ref('')
const erro = ref('')

const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
const API_BASE_URL = isLocal ? 'http://localhost:8000/api' : 'https://conviva-labev.onrender.com/api'

const enviarLink = async () => {
  try {
    erro.value = ''
    mensagem.value = ''
    carregando.value = true

    const response = await axios.post(`${API_BASE_URL}/esqueci-senha`, {
      email: email.value
    })

    mensagem.value = response.data.message || 'Link enviado com sucesso!'
  } catch (err) {
    erro.value = err.response?.data?.message || 'Não foi possível processar a solicitação.'
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
