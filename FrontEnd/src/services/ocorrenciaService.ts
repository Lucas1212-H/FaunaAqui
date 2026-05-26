import axios from 'axios'

const API_URL = 'http://localhost:8000/api' // Ajuste conforme necessário

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Content-Type': 'application/json'
  }
})

export interface DadosDenuncia {
  denunciante_nome: string
  denunciante_contato_tipo: string
  denunciante_contato_valor: string
  categoria_ocorrencia: string
  tipo_animal: string
  situacao_animal: string
  descricao: string
  latitude: number
  longitude: number
  ponto_referencia: string
  foto: File
}

export const ocorrenciaService = {
  /**
   * Cria uma nova ocorrência (denuncia) no backend
   * @param dados Dados do formulário da denuncia
    * @returns Response com confirmação da ocorrência
   */
  async criarDenuncia(dados: DadosDenuncia) {
    const formData = new FormData()
    
    // Adiciona todos os campos de texto
    formData.append('denunciante_nome', dados.denunciante_nome)
    formData.append('denunciante_contato_tipo', dados.denunciante_contato_tipo)
    formData.append('denunciante_contato_valor', dados.denunciante_contato_valor)
    formData.append('categoria_ocorrencia', dados.categoria_ocorrencia)
    formData.append('tipo_animal', dados.tipo_animal)
    formData.append('situacao_animal', dados.situacao_animal)
    formData.append('descricao', dados.descricao)
    formData.append('latitude', dados.latitude.toString())
    formData.append('longitude', dados.longitude.toString())
    formData.append('ponto_referencia', dados.ponto_referencia)
    
    // Adiciona a foto
    formData.append('foto', dados.foto)

    try {
      const response = await api.post('/ocorrencias', formData, {
        headers: {
          'Content-Type': 'multipart/form-data'
        }
      })
      return response.data
    } catch (error) {
      if (axios.isAxiosError(error)) {
        throw new Error(error.response?.data?.message || 'Erro ao criar denuncia')
      }
      throw error
    }
  },

  /**
   * Busca os dados de uma ocorrência pelo id
   * @param id Identificador da ocorrência
   * @returns Dados da ocorrência
   */
  async obterDenunciaPorId(id: number | string) {
    try {
      const response = await api.get(`/ocorrencias/${id}`)
      return response.data
    } catch (error) {
      if (axios.isAxiosError(error)) {
        throw new Error(error.response?.data?.message || 'Denuncia não encontrada')
      }
      throw error
    }
  }
}
