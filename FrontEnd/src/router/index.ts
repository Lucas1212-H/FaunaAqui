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
import EditarConta from '@/pages/EditarConta.vue'
import AprovarUsuario from '@/pages/especialista/AprovarUsuario.vue'
import GerenciarPosts from '../pages/GerenciarPosts.vue'
import EsqueciSenha from '../views/EsqueciSenha.vue'
import RedefinirSenha from '../views/RedefinirSenha.vue'
import DetalhePostView from '../views/DetalhesPostView.vue'

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: { title: 'Home' }
  },
  {
  path: '/painel/posts',
  name: 'gerenciar-posts',
  component: GerenciarPosts,
  meta: { requiresAuth: true, role: 'Administrador' } // Caso use middleware de rotas no front
  },
  {
    path: '/noticias/:id',
    name: 'detalhe-post',
    component: DetalhePostView,
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
    path: '/esqueci-senha',
    name: 'esqueci-senha',
    component: EsqueciSenha,
    meta: { title: 'Esqueci Minha Senha', requiresGuest: true }
  },
  {
    path: '/redefinir-senha',
    name: 'redefinir-senha',
    component: RedefinirSenha,
    meta: { title: 'Redefinir Senha', requiresGuest: true }
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
    meta: { title: 'Blog' }
  },
  {
    path: '/catalogo',
    name: 'catalogo',
    component: CatalogoAnimal,
    meta: { title: 'Catálogo Animal' }
  },
  {
    path: '/catalogo/:id',
    name: 'catalogo-detalhe',
    component: AnimalInfo,
    props: true
  },
  {
    path: '/perfil',
    name: 'editar-perfil',
    component: EditarConta,
    meta: { title: 'Minha Conta', requiresAuth: true } // Protegida para logados
  },
  {
    path: '/aprovar-usuario',
    name: 'aprovar-usuario',
    component: AprovarUsuario,
    meta: { title: 'Aprovar Usuário', requiresAuth: true, requiresAdmin: true } // 🛡️ Bloqueio para admin
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

// Guards de rota unificados: Autenticação + Nível de Acesso + Título Dinâmico
router.beforeEach((to, from) => {
  const { isAutenticado } = useAuth()

  // 1. Lógica de Título da Aba
  const tituloPagina = to.meta.title
  if (typeof tituloPagina === 'string') {
    document.title = `${tituloPagina} | ConViva`
  } else {
    document.title = 'ConViva'
  }

  // 2. Lógica de Autenticação Global
  if (to.meta.requiresAuth) {
    if (!isAutenticado.value) {
      return { name: 'login', query: { redirect: to.fullPath } }
    }
    
    // 🛡️ Filtro Extra: Bloqueia colaboradores comuns em rotas de Admin
    if (to.meta.requiresAdmin) {
      const tipoConta = localStorage.getItem('user_tipo')
      if (tipoConta !== 'Administrador') {
        alert('Acesso restrito apenas para administradores do sistema!')
        return { name: 'specialist-area' }
      }
    }
  } else if (to.meta.requiresGuest) {
    if (isAutenticado.value) {
      return { name: 'specialist-area' }
    }
  }
})

export default router