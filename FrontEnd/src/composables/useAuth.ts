import { ref, computed } from 'vue'

interface User {
  id?: string
  nome: string
  email: string
  dataLogin?: string
}

const usuarioLogado = ref<User | null>(null)
const carregando = ref(false)

// Carregar dados do localStorage ao inicializar
const loadStoredUser = () => {
  const stored = localStorage.getItem('usuarioLogado')
  if (stored) {
    usuarioLogado.value = JSON.parse(stored)
  }
}

export const useAuth = () => {
  const isAutenticado = computed(() => !!usuarioLogado.value)

  const register = async (nome: string, email: string, senha: string) => {
    try {
      carregando.value = true
      // TODO: Integrar com API real
      console.log('Registrando usuário:', { nome, email })
      
      // Simulação de requisição
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Salvar dados do usuário (sem senha!)
      const novoUsuario: User = {
        id: Math.random().toString(36).substr(2, 9),
        nome,
        email
      }
      
      localStorage.setItem('usuarioCadastrado', JSON.stringify(novoUsuario))
      
      return { sucesso: true, mensagem: 'Cadastro realizado com sucesso!' }
    } catch (erro) {
      console.error('Erro ao registrar:', erro)
      return { sucesso: false, mensagem: 'Erro ao registrar. Tente novamente.' }
    } finally {
      carregando.value = false
    }
  }

  const login = async (email: string, senha: string) => {
    try {
      carregando.value = true
      // TODO: Integrar com API real
      console.log('Fazendo login com:', { email })
      
      // Verificar se o usuário foi cadastrado
      const usuarioCadastrado = localStorage.getItem('usuarioCadastrado')
      if (!usuarioCadastrado) {
        return { sucesso: false, mensagem: 'Usuário não encontrado. Cadastre-se primeiro.' }
      }
      
      const userData = JSON.parse(usuarioCadastrado)
      
      // Simulação de requisição
      await new Promise(resolve => setTimeout(resolve, 1000))
      
      // Validação simples (remover após integração com API)
      if (email !== userData.email) {
        return { sucesso: false, mensagem: 'Email ou senha inválidos.' }
      }
      
      // Login bem-sucedido
      const usuarioAutenticado: User = {
        ...userData,
        dataLogin: new Date().toISOString()
      }
      
      usuarioLogado.value = usuarioAutenticado
      localStorage.setItem('usuarioLogado', JSON.stringify(usuarioAutenticado))
      
      return { sucesso: true, mensagem: 'Login realizado com sucesso!' }
    } catch (erro) {
      console.error('Erro ao fazer login:', erro)
      return { sucesso: false, mensagem: 'Erro ao fazer login. Tente novamente.' }
    } finally {
      carregando.value = false
    }
  }

  const logout = () => {
    usuarioLogado.value = null
    localStorage.removeItem('usuarioLogado')
    localStorage.removeItem('usuarioCadastrado')
  }

  // Carregar dados do localStorage quando o composable é inicializado
  loadStoredUser()

  return {
    usuarioLogado: computed(() => usuarioLogado.value),
    isAutenticado,
    carregando: computed(() => carregando.value),
    register,
    login,
    logout
  }
}
