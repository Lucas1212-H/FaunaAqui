<template>
  <nav class="navbar-especialista d-flex align-items-center justify-content-between px-4">
    <div class="navbar-brand-container">
      <h1 class="m-0 brand-text">Fauna<span>aqui!</span></h1>
    </div>

    <div class="nav-pill-container">
      <div class="nav-pill-bg d-flex align-items-center justify-content-around">
        <a href="#" class="nav-item-link" :class="{ active: abaAtiva === 'triagem' }" @click.prevent="$emit('mudarAba', 'triagem')">Início</a>
        <a href="#" class="nav-item-link" :class="{ active: abaAtiva === 'arquivadas' }" @click.prevent="$emit('mudarAba', 'arquivadas')">Denuncias Arquivadas</a>
        <a href="#" class="nav-item-link" :class="{ active: abaAtiva === 'publicados' }" @click.prevent="$emit('mudarAba', 'publicados')">Publicados</a>
        <a href="#" class="nav-item-link">Sobre nós</a>
      </div>
    </div>

    <div class="user-section">
      <div class="user-info">
        <small class="user-name">{{ usuarioLogado?.nome || 'Usuário' }}</small>
      </div>
      <div class="dropdown">
        <button 
          class="avatar-circle shadow-sm dropdown-toggle" 
          type="button" 
          id="userDropdown" 
          data-bs-toggle="dropdown" 
          aria-expanded="false"
        >
          {{ inicialNome }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
          <li><a class="dropdown-item" href="#">Perfil</a></li>
          <li><hr class="dropdown-divider"></li>
          <li><a class="dropdown-item" href="#" @click.prevent="fazerLogout">Sair</a></li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '@/composables/useAuth'

defineProps({
  abaAtiva: {
    type: String,
    default: 'triagem'
  }
})

defineEmits(['mudarAba'])

const router = useRouter()
const { usuarioLogado, logout } = useAuth()

const inicialNome = computed(() => {
  const nome = usuarioLogado.value?.nome || 'U'
  return nome.charAt(0).toUpperCase()
})

const fazerLogout = () => {
  logout()
  router.push('/')
}
</script>

<style scoped>
.navbar-especialista {
  background-color: #58d68d; /* Verde principal do seu mockup */
  height: 80px;
  width: 100%;
}

.brand-text {
  color: white;
  font-weight: 800;
  font-size: 2.2rem;
  letter-spacing: -1px;
}

.brand-text span {
  font-weight: 400;
}

/* Container da Pílula Central */
.nav-pill-container {
  flex-grow: 1;
  display: flex;
  justify-content: center;
}

.nav-pill-bg {
  background-color: white;
  border-radius: 50px;
  padding: 8px 15px;
  min-width: 450px;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.nav-item-link {
  text-decoration: none;
  color: #9cdb81; /* Verde claro dos links */
  font-weight: 600;
  padding: 8px 20px;
  border-radius: 30px;
  transition: all 0.3s ease;
  font-size: 0.95rem;
}

.nav-item-link:hover {
  color: #45a049;
}

.nav-item-link.active {
  background-color: #9cdb81;
  color: white !important;
}

/* User Section */
.user-section {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-info {
  text-align: right;
}

.user-name {
  color: white;
  display: block;
  font-weight: 500;
  white-space: nowrap;
}

/* Avatar Circular */
.avatar-circle {
  width: 50px;
  height: 50px;
  background-color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #333;
  font-size: 1.2rem;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
}

.avatar-circle:hover {
  background-color: #f0f0f0;
}

.dropdown-menu {
  border: none;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  border-radius: 10px;
}

.dropdown-item {
  color: #333;
  transition: all 0.3s ease;
}

.dropdown-item:hover {
  background-color: #9cdb81;
  color: white;
}

/* Responsividade básica */
@media (max-width: 992px) {
  .nav-pill-bg {
    min-width: auto;
    padding: 5px;
  }
  .nav-item-link {
    padding: 5px 10px;
    font-size: 0.8rem;
  }
  .user-info {
    display: none;
  }
}
</style>