import { createRouter, createWebHistory } from 'vue-router'
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
    path: '/contato',
    name: 'contato',
    component: ContatoPage
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach((to, from, next) => {
  const { isAutenticado } = useAuth()

  if (to.meta.requiresAuth) {
    if (isAutenticado.value) {
      next()
    } else {
      next({ name: 'login', query: { redirect: to.fullPath } })
    }
  } else if (to.meta.requiresGuest) {
    if (!isAutenticado.value) {
      next()
    } else {
      next({ name: 'specialist-area' })
    }
  } else {
    next()
  }
})

export default router
