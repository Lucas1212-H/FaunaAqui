import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LoginView from '../views/LoginView.vue'
import RegistroView from '../views/RegistroView.vue'
import EspecialistaView from '../views/EspecialistaView.vue'
import DenunciaView from '../views/DenunciaView.vue'
import ContatosView from '../views/ContatosView.vue'
import QuemSomos from '../views/QuemSomos.vue'
import BlogView from '../views/BlogView.vue'
import CatalogoAnimal from '../pages/CatalogoAnimal.vue'
import AnimalInfo from '../components/AnimalInfo.vue'
import { useAuth } from '../composables/useAuth'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: { title: 'Home' } // Título definido
  },
  {
    path: '/cadastro',
    name: 'cadastro',
    component: RegistroView,
    meta: { title: 'Cadastro', requiresGuest: true }
  },
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { title: 'Login', requiresGuest: true }
  },
  {
    path: '/especialista',
    name: 'specialist-area',
    component: EspecialistaView,
    meta: { title: 'Área do Especialista', requiresAuth: true }
  },
  {
    path: '/denuncia',
    name: 'denuncia',
    component: DenunciaView,
    meta: { title: 'Fazer Denúncia' }
  },
  {
    path: '/contato',
    name: 'contato',
    component: ContatosView,
    meta: { title: 'Contatos' }
  },
  {
    path: '/sobre',
    name: 'sobre',
    component: QuemSomos,
    meta: { title: 'Quem Somos' }
  },
  {
    path: '/blog',
    name: 'blog',
    component: BlogView,
    meta: { title: 'Blog' },
  },
  {
    path: '/catalogo',
    name: 'catalogo',
    component: CatalogoAnimal,
    meta: { title: 'Catálogo Animal' },
  },
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

// Guards de rota unificados: Autenticação + Título Dinâmico
router.beforeEach((to, from, next) => {
  const { isAutenticado } = useAuth()

  // 1. Lógica de Título da Aba
  const tituloPagina = to.meta.title as string
  if (tituloPagina) {
    document.title = `${tituloPagina} | ConViva`
  } else {
    document.title = 'ConViva' // Fallback
  }

  // 2. Lógica de Autenticação
  if (to.meta.requiresAuth) {
    if (!isAutenticado.value) {
      return next({ name: 'login', query: { redirect: to.fullPath } })
    }
  } else if (to.meta.requiresGuest) {
    if (isAutenticado.value) {
      return next({ name: 'specialist-area' })
    }
  }

  next()
})

export default router
