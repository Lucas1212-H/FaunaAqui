<template>
  <div class="navibar">    
    <div class="top-bar py-2 px-4 d-flex justify-content-between align-items-center text-white small">
      <div class="d-flex align-items-center">
        <i class="bi bi-envelope-fill me-2"></i>
        <span>contato@conviva.com</span>
      </div>
      <div class="social-icons d-flex gap-3">
        <a href="https://www.instagram.com/conviva.labev/" target="_blank" class="text-white"><i class="bi bi-instagram"></i></a>
        <a href="mailto:contato@conviva.com" class="text-white"><i class="bi bi-envelope"></i></a>
      </div>
    </div>

    <header class="navbar navbar-expand-xl navbar-light bg-white py-3 px-4 shadow-sm">
      <div class="container-fluid d-flex justify-content-between align-items-center">
        
        <RouterLink class="navbar-brand d-flex align-items-center" to="/">
          <div class="logo-text fw-bold lh-1">
            <img src="../assets/images/logo_sf.png" height="75px" max-height="75px" width="auto" alt="conviva">
          </div>
        </RouterLink>

        <nav class="collapse navbar-collapse justify-content-center" id="navbarNav">
          <ul class="navbar-nav gap-3 fw-semibold">
            <li class="nav-item"><RouterLink class="nav-link text-dark-custom" to="/">Home</RouterLink></li>
            <li class="nav-item"><RouterLink class="nav-link text-dark-custom" to="/sobre">Quem Somos</RouterLink></li>
            <li class="nav-item"><RouterLink class="nav-link text-dark-custom" to="/blog">Blog</RouterLink></li>
            <li class="nav-item"><RouterLink class="nav-link text-dark-custom" to="/contato">Contato</RouterLink></li>
          </ul>
        </nav>
        
        <div class="d-flex align-items-center gap-4 hidden-md-down">
          <a href="https://wa.me/5591998387277" target="_blank" class="nav-link d-flex align-items-center text-decoration-none">
            <!-- Ícone do WhatsApp (cor verde nativa do Bootstrap ou use text-success) -->
            <i class="bi bi-whatsapp text-success fs-2 me-2"></i>

            <div class="d-flex flex-column lh-sm">
              <span class="fw-bold text-dark" style="font-size: 0.85rem;">Fale Conosco</span>
              <span class="text-muted" style="font-size: 0.8rem;">(91) 99838-7277</span>
            </div>
          </a>

          <button @click="irParaEspecialista" class="btn nav-btn bg-info">
            {{ isAutenticado ? 'Minha Área' : 'Área do Especialista' }}
          </button>

            <img src="../assets/images/UFPA_logo.png" height="60px" max-height="60px" width="auto" alt="UFPA">

        </div>

        <!-- manter este botao para o modo mobile -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>

      </div>
    </header>
    </div>
  </template>



<script setup>
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

const router = useRouter()
const { isAutenticado, logout } = useAuth()

const irParaEspecialista = () => {
  if (isAutenticado.value) router.push('/especialista')
  else router.push('/login')
}

const fazerLogout = () => {
  logout()
  router.push('/')
}
</script>

<style scoped>
/* --- CONFIGURAÇÕES DE PALETA E TIPOGRAFIA --- */
.text-sans-serif {
  font-family: 'Inter', 'Montserrat', sans-serif;
  -webkit-font-smoothing: antialiased;
}

/* Cores Customizadas */
.top-bar {
  background-color: #1f6b6d; /* Verde Petróleo Escuro */
}
.text-petroleo { color: #1f6b6d; }
.text-oliva { color: #556b2f; }      /* Verde Oliva Corporativo */
.text-laranja-oliva { color: #d4a373; } /* Tom terroso secundário */
.text-dark-custom { color: #333333; }

/* Menu Ativo */
.active-green {
  color: #70a1ff !important; /* Ajuste para a cor ativa destacada */
  color: #7ba986 !important; /* Exemplo de verde claro institucional ativo */
}

/* Ajustes de Fontes Finas/Especiais */
.tiny-text {
  font-size: 0.7rem;
  font-weight: 600;
}
.tracking-wide { letter-spacing: 0.1em; }
.tracking-wider { letter-spacing: 0.15em; }
.tracking-widest { letter-spacing: 0.2em; }
.tracking-tight { letter-spacing: -0.05em; }

.phone-number {
  font-size: 0.95rem;
  color: #212529;
}



.hero-overlay {
  background: linear-gradient(180deg, rgba(0,0,0,0.2) 0%, rgba(0,0,0,0.65) 100%);
  z-index: 1;
}

.z-index-2 {
  z-index: 2;
}

.hero-title {
  font-size: 3.5rem;
  letter-spacing: -0.02em;
}

/* Customização leve do Breadcrumb para aceitar setas separadoras brancas */
.breadcrumb-item + .breadcrumb-item::before {
  content: ">" !important;
  color: rgba(255, 255, 255, 0.5) !important;
}

/* Responsividade de Mídia */
@media (max-width: 1200px) {
  .hidden-md-down {
    display: none !important;
  }
}
</style>