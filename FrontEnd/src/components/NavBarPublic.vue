<template>
  <nav class="navbar navbar-expand-lg custom-navbar px-4 py-3 shadow-sm">
    <div class="container-fluid">
      <RouterLink class="navbar-brand logo" to="/">FaunaAqui!</RouterLink>
      
      <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
        <div class="d-flex gap-3 mt-3 mt-lg-0">
          <button @click="irParaEspecialista" class="btn nav-btn">
            {{ isAutenticado ? 'Minha Área' : 'Área do Especialista' }}
          </button>
          <RouterLink class="btn nav-btn" to="/sobre">Sobre</RouterLink>
          <RouterLink class="btn nav-btn" to="/contato">Contato</RouterLink>
          <button v-if="isAutenticado" @click="fazerLogout" class="btn nav-btn btn-logout">
            Sair
          </button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { isAutenticado, logout } = useAuth()

const irParaEspecialista = () => {
  if (isAutenticado.value) {
    // Já está logado, vai direto para a área do especialista
    router.push('/especialista')
  } else {
    // Não está logado, vai para o login primeiro
    router.push('/login')
  }
}

const fazerLogout = () => {
  logout()
  router.push('/')
}
</script>

<style scoped>
.custom-navbar { background: linear-gradient(90deg, #2ecc71, #00b894); }
.logo { color: white; font-weight: 800; font-size: 2rem; text-decoration: none; }
.nav-btn { background: rgba(0,0,0,0.2); color: white; border-radius: 10px; border: none; transition: 0.3s; }
.nav-btn:hover { background: rgba(0,0,0,0.4); color: white; }
.btn-logout { background: rgba(255,0,0,0.3); }
.btn-logout:hover { background: rgba(255,0,0,0.5); }
</style>