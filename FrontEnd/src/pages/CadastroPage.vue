<template>
  <div class="container-fluid min-vh-100 d-flex justify-content-center align-items-center py-4 painel-autenticacao">
    <div class="card card-custom border-0 p-4 p-sm-5 shadow w-100 text-dark">
      <div class="card-body">
        
        <h2 class="display-6 fw-normal mb-4 text-start">Cadastro</h2>

        <form @submit.prevent="lidarComCadastro">
          <div class="mb-3 text-start">
            <label for="nome" class="form-label small fw-semibold mb-1">Nome</label>
            <input 
              type="text" 
              id="nome" 
              v-model="usuario.nome" 
              class="form-control input-custom py-2 shadow-sm" 
              required
            />
          </div>

          <div class="mb-3 text-start">
            <label for="email" class="form-label small fw-semibold mb-1">Email</label>
            <input 
              type="email" 
              id="email" 
              v-model="usuario.email" 
              class="form-control input-custom py-2 shadow-sm" 
              required
            />
          </div>

          <div class="mb-3 text-start">
            <label for="senha" class="form-label small fw-semibold mb-1">Senha</label>
            <input 
              type="password" 
              id="senha" 
              v-model="usuario.senha" 
              class="form-control input-custom py-2 shadow-sm" 
              required
            />
          </div>

          <div class="d-grid mb-2">
            <button type="submit" class="btn btn-black py-2.5 rounded-pill fw-medium shadow-sm">
              Cadastrar
            </button>
          </div>
        </form>

        <p class="text-center text-muted mb-4 text-dark opacity-75" style="font-size: 10px;">
          Seu nome de perfil organizacional será compartilhado. Nunca envie senhas.
        </p>

        <div class="text-center mt-3">
          <span class="d-block text-secondary small mb-1">Já possui conta?</span>
          <a href="#" @click.prevent="irParaLogin" class="link-custom fw-semibold text-decoration-none fs-5">
            Clique Aqui
          </a>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { reactive } from 'vue'

const emit = defineEmits(['mudarTela'])

const usuario = reactive({
  nome: '',
  email: '',
  senha: ''
})

const lidarComCadastro = () => {
  console.log('Dados do novo cadastro organizacional:', usuario)
  alert('Cadastro efetuado (Simulação)!')
  emit('mudarTela', 'login')
}

const irParaLogin = () => {
  emit('mudarTela', 'login')
}
</script>

<style scoped>
/* Reaproveita exatamente as mesmas definições visuais para manter consistência */
.painel-autenticacao {
  background-color: #f4f6f0;
  font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
}

.card-custom {
  background-color: #93e27a !important;
  border-radius: 2.5rem !important;
  max-width: 500px;
}

.input-custom {
  background-color: rgba(255, 255, 255, 0.4) !important;
  border: 1px solid #5fa64a !important;
  border-radius: 8px !important;
  color: #111111 !important;
}
.input-custom:focus {
  background-color: #ffffff !important;
  border-color: #335d25 !important;
  box-shadow: 0 0 0 0.25rem rgba(51, 93, 37, 0.25) !important;
}

.btn-black {
  background-color: #000000 !important;
  color: #ffffff !important;
  border: none;
  transition: opacity 0.2s ease;
}
.btn-black:hover {
  opacity: 0.85;
}

.link-custom {
  color: #4b39ef !important;
}
.link-custom:hover {
  color: #3225b8 !important;
}
</style>