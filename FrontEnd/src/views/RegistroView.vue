<template>
  <div class="register-page d-flex align-items-center justify-content-center">
    <div class="container">
      <div class="row align-items-center flex-row-reverse">
        <div class="col-md-5 d-none d-md-block text-center">
          <img src="@/assets/images/cobra-cadastro.png" alt="Cobra" class="img-fluid hero-img">
        </div>
        
        <div class="col-md-6">
          <div class="auth-card shadow-lg p-5">
            <h2 class="auth-title mb-4">Cadastro</h2>
            
            <div v-if="erro" class="alert alert-danger alert-dismissible fade show" role="alert">
              {{ erro }}
              <button type="button" class="btn-close" @click="erro = ''"></button>
            </div>
            
            <form @submit.prevent="handleRegister">
              <div class="mb-3">
                <label class="form-label">Nome Completo</label>
                <input 
                  type="text" 
                  v-model="nomeCompleto" 
                  class="form-control custom-input" 
                  :disabled="carregando.value"
                  required
                >
              </div>
              
              <div class="mb-3">
                <label class="form-label">Email Institucional</label>
                <input 
                  type="email" 
                  v-model="email" 
                  class="form-control custom-input" 
                  :disabled="carregando.value"
                  required
                >
              </div>
              
              <div class="mb-4">
                <label class="form-label">Senha</label>
                <input 
                  type="password" 
                  v-model="senha" 
                  class="form-control custom-input" 
                  :disabled="carregando.value"
                  required
                >
              </div>
              
              <button 
                type="submit" 
                :disabled="carregando.value" 
                class="btn btn-dark w-100 py-3 fw-bold mb-3"
              >
                {{ carregando.value ? 'Cadastrando...' : 'Cadastrar' }}
              </button>
              
              <div class="text-center auth-footer">
                <p class="small opacity-75 mb-3">Seu nome de perfil não poderá ser alterado depois, nunca envie senhas.</p>
                <p class="small">Já possui conta? 
                  <RouterLink to="/login" class="text-decoration-none text-dark fw-bold">
                    Volte para Login
                  </RouterLink>
                </p>
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

const router = useRouter()
const { register, carregando } = useAuth()
const nomeCompleto = ref('')
const email = ref('')
const senha = ref('')
const erro = ref('')

const handleRegister = async () => {
  try {
    erro.value = ''
    
    if (!nomeCompleto.value || !email.value || !senha.value) {
      erro.value = 'Todos os campos são obrigatórios'
      return
    }
    
    if (senha.value.length < 6) {
      erro.value = 'Senha deve ter no mínimo 6 caracteres'
      return
    }
    
    const resultado = await register(nomeCompleto.value, email.value, senha.value)
    
    if (resultado.sucesso) {
      // Redirecionar para login após cadastro bem-sucedido
      router.push('/login')
    } else {
      erro.value = resultado.mensagem
    }
  } catch (err) {
    erro.value = 'Erro ao cadastrar. Tente novamente.'
    console.error(err)
  }
}
</script>

<style scoped>
.register-page { min-height: 100vh; background-color: #f8fafc; }
.auth-card { background-color: #9cdb81; border-radius: 40px; }
.auth-title { font-weight: 700; font-size: 2.5rem; }
.custom-input { background: rgba(255,255,255,0.5); border: none; border-radius: 15px; }
</style>