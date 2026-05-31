import { createRouter, createWebHistory, RouteRecordRaw } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegistroView from '../views/RegistroView.vue'
import EspecialistaView from '../views/EspecialistaView.vue'
import DenunciaView from '../views/DenunciaView.vue'
import CatalogoAnimal from '../pages/CatalogoAnimal.vue'
import AnimalInfo from '../components/AnimalInfo.vue'
import ContatoPage from '../pages/ContatoPage.vue'
import { useAuth } from '../composables/useAuth'
const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView
  },
  {
    path: '/cadastro',
    name: 'cadastro',
    component: RegistroView,
    meta: { requiresGuest: true }
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { requiresGuest: true }
  },
  {
    path: '/especialista',
    name: 'specialist-area',
    component: EspecialistaView,
    meta: { requiresAuth: true }
  },
  {
    path: '/denuncia',
    name: 'denuncia',
    component: DenunciaView
  },
  {
    path: '/catalogo',
    name: 'catalogo',
    component: CatalogoAnimal
  }
  ,
  {
    path: '/catalogo/:id',
    name: 'catalogo-detalhe',
    component: AnimalInfo,
    props: true
  },

]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Guards de rota para autenticação
router.beforeEach((to, from) => {
  const { isAutenticado } = useAuth()

  // Se a rota requer autenticação
  if (to.meta.requiresAuth) {
    if (isAutenticado.value) {
      return true
    } else {
      // Redirecionar para login se não estiver autenticado
      return { name: 'login', query: { redirect: to.fullPath } }
    }
  }
  // Se a rota requer estar desautenticado (como login/cadastro)
  else if (to.meta.requiresGuest) {
    if (!isAutenticado.value) {
      return true
    } else {
      // Se já está autenticado e tenta acessar login/cadastro, redireciona para dashboard
      return { name: 'specialist-area' }
    }
  }
})

export default router
