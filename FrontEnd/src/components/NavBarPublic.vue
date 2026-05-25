<template>
  <nav class="navbar navbar-expand-lg custom-navbar px-4 py-3 shadow-sm ">
    
    <div class="container-fluid">
      <img src="../assets/images/conviva.png" height="75px" max-height="75px" width="auto" alt="conviva">
      
      <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
        <div class="d-flex gap-3 mt-3 mt-lg-0 ">
          <button @click="irParaEspecialista" class="btn nav-btn">
            {{ isAutenticado ? 'Minha Área' : 'Área do Especialista' }}
          </button>
          <RouterLink class="btn nav-btn" style="display:flex; align-items: center;" to="/">Home</RouterLink>
          <RouterLink class="btn nav-btn" style="display:flex; align-items: center;" to="/sobre">Quem somos</RouterLink>
          <RouterLink class="btn nav-btn" style="display:flex; align-items: center;" to="/">Blog</RouterLink>
          <RouterLink class="btn nav-btn" style="display:flex; align-items: center;" to="/contato">Contato</RouterLink>
          <button v-if="isAutenticado" @click="fazerLogout" class="btn nav-btn btn-logout">
            Sair
          </button>
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

          <a href="https://wa.me/5591998387277" target="_blank" class="nav-link d-flex align-items-center text-decoration-none">
            <!-- Ícone do WhatsApp (cor verde nativa do Bootstrap ou use text-success) -->
            <i class="bi bi-whatsapp text-success fs-2 me-2"></i>

            <div class="d-flex flex-column lh-sm">
              <span class="fw-bold text-dark" style="font-size: 0.85rem;">Fale Conosco</span>
              <span class="text-muted" style="font-size: 0.8rem;">(91) 99838-7277</span>
            </div>
          </a>

           <img src="../assets/images/UFPA.png" height="60px" max-height="60px" width="auto" alt="UFPA">
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
.custom-navbar { background:white;}
.logo { color: white; font-weight: 800; font-size: 2rem; text-decoration: none; }
.nav-btn { background: rgba(0,0,0,0.2); color: white; border-radius: 10px; border: none; transition: 0.3s; }
.nav-btn:hover { background: rgba(0,0,0,0.4); color: white; }
.btn-logout { background: rgba(255,0,0,0.3); }
.btn-logout:hover { background: rgba(255,0,0,0.5); }
</style>