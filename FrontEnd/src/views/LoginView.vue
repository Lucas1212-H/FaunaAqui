<template>
  <div class="login-page d-flex align-items-center justify-content-center">
    <div class="container">
      <div class="row align-items-center justify-content-center">
        <div class="col-md-5 d-none d-md-block text-center">
          <img src="@/assets/images/capivara-login.png" alt="Capivara" class="img-fluid hero-img">
        </div>
        
        <div class="col-md-6">
          <div class="auth-card shadow-lg p-5">
            <h2 class="auth-title mb-4">Login Organizacional</h2>
            
            <UiMessage
              v-if="erro"
              type="error"
              title="Falha no acesso"
              :message="erro"
              @dismiss="erro = ''"
            />
            
            <form @submit.prevent="handleLogin">
              <div class="mb-4">
                <label class="form-label">Email</label>
                <input 
                  type="email" 
                  v-model="email" 
                  class="form-control custom-input" 
                  placeholder="seu@email.com" 
                  :disabled="carregando"
                  required
                >
              </div>
              
              <div class="mb-4">
                <label class="form-label">Senha</label>
                <input 
                  type="password" 
                  v-model="senha" 
                  class="form-control custom-input" 
                  placeholder="********" 
                  :disabled="carregando"
                  required
                >
              </div>
              
              <button 
                type="submit" 
                :disabled="carregando"
                class="btn btn-dark w-100 py-3 fw-bold mb-3"
              >
                {{ carregando ? 'Entrando...' : 'Entrar' }}
              </button>
              
              <div class="text-center mt-3">
                <p class="small mb-2 text-dark">Não possui conta?</p>
                <RouterLink to="/cadastro" class="text-decoration-none text-primary fw-bold">Criar Conta</RouterLink>
              </div>
              <div class="text-center mt-2">
                <RouterLink to="/esqueci-senha" class="text-decoration-none text-dark opacity-75 small">Esqueci minha senha</RouterLink>
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
import { useRouter, RouterLink } from 'vue-router'
import { useAuth } from '@/composables/useAuth'
import UiMessage from '@/components/UiMessage.vue'

const router = useRouter()
const { login, carregando: carregandoAuth } = useAuth()
const email = ref('')
const senha = ref('')
const erro = ref('')
const carregando = ref(false)

const handleLogin = async () => {
  try {
    erro.value = ''
    
    if (!email.value || !senha.value) {
      erro.value = 'Email e senha são obrigatórios'
      return
    }
    
    carregando.value = true
    const resultado = await login({ email: email.value, senha: senha.value })
    
    if (resultado.sucesso) {
      const redirectPath = String(router.currentRoute.value.query.redirect || '/especialista')
      router.push(redirectPath)
    } else {
      erro.value = resultado.mensagem
    }
  } catch (err) {
    erro.value = 'Erro ao fazer login. Tente novamente.'
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

.hero-img { 
  max-width: 80%; 
}

.auth-footer {
  flex-wrap: wrap;
  gap: 1rem;
}
</style>