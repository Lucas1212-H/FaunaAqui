<template>
    <div>
        <NavBar />
        <main>
            <h1>Cadastrar Espécie</h1>
            <form action="" @submit.prevent="salvarEspecieCompleta">
                <fieldset>
                    <legend>Dados da Espécie</legend>

                    <label for="nome_popular">Nome Popular</label>
                    <input type="text" name="NomePopular" id="nome_popular" placeholder="Ex: Sabiá-laranjeira" v-model="especie.nome_popular" required>
                    <label for="nome_cientifico">Nome Cientifico</label>
                    <input type="text" name="NomeCientifico" id="nome_cientifico" placeholder="Ex: Sturnus vulgaris" v-model="especie.nome_cientifico" required>
                    <label for="descricao">Descrição da Espécie</label>
                    <textarea name="Descricao" id="descricao" placeholder="Ex: Espécie de pássaro encontrada em áreas urbanas" v-model="especie.descricao"></textarea>
                </fieldset>

                <fieldset>
                    <legend>Atribuir Ocorrências Publicadas</legend>
                    <p>Selecione abaixo as ocorrências publicadas no mapa que pertencem a esta espécie.</p>
                    <div v-if="carregandoOcorrencias" class="text-center py-3">
                        <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                        <small class="text-muted ms-2">Buscando ocorrências publicadas...</small>
                    </div>
                    <div v-else-if="ocorrenciasPublicadas.length > 0">
                        <label
                          v-for="ocorrencia in ocorrenciasPublicadas"
                          :key="ocorrencia.id"
                          class="d-block border rounded p-3 mb-3"
                        >
                          <input
                            v-model="ocorrenciasSelecionadas"
                            :value="ocorrencia.id"
                            type="checkbox"
                            class="me-2"
                          >
                          <div class="d-inline-block w-100 align-middle">
                            <div class="d-flex justify-content-between align-items-center">
                              <span class="badge rounded-pill px-3 py-1 text-uppercase bg-success">
                                {{ ocorrencia.status || 'Publicado' }}
                              </span>
                              <small class="text-secondary">{{ formatarData(ocorrencia.created_at) }}</small>
                            </div>
                            <p class="mb-0 mt-2 text-dark fw-medium small">
                              Animal: <span class="text-muted font-monospace">{{ ocorrencia.tipo_animal }}</span>
                            </p>
                            <p class="mb-0 mt-1 text-dark fw-medium small">
                              Localização: <span class="text-muted font-monospace">{{ ocorrencia.ponto_referencia || '-' }}</span>
                            </p>
                          </div>
                        </label>
                    </div>
                    <div v-else class="alert alert-light text-center border p-4 m-0">
                     Nenhuma ocorrência publicada disponível para vinculação no momento.
                    </div>
                    <button type="submit" :disabled="salvando">
                        <span v-if="salvando" class="spinner-border spinner-border-sm me-2"></span>
                        {{ salvando ? 'salvando...' : 'Publicar Espécie e Atribuir Casos' }}
                    </button>
                </fieldset>
                

            </form>
        </main>
    </div>
</template>
<script>
import NavBar from '@/components/NavBar.vue'
import axios from 'axios'

export default {
  components: {
    NavBar
  },
  data() {
    return {
      especie: {
        nome_popular: '',
        nome_cientifico: '',
        descricao: '',
        id_categoria: 1
      },
      
      // Estados de carregamento
      carregandoOcorrencias: true,
      salvando: false,

      // Dados vindo da API
      ocorrenciasPublicadas: [],

      // Caixinha (Array) que guarda automaticamente os IDs dos checkboxes marcados
      ocorrenciasSelecionadas: []
    }
  },
  methods: {
    async buscarOcorrenciasPublicadas() {
      try {
        this.carregandoOcorrencias = true;
        const response = await axios.get('http://localhost:8000/api/ocorrencias/publicados');
        this.ocorrenciasPublicadas = response.data || [];
      } catch (error) {
        console.error('Erro ao buscar ocorrências publicadas:', error);
        this.ocorrenciasPublicadas = [];
      } finally {
        this.carregandoOcorrencias = false;
      }
    },

    async salvarEspecieCompleta() {
      try {
        this.salvando = true;

        const formData = new FormData();
        formData.append('nome_popular', this.especie.nome_popular);
        formData.append('nome_cientifico', this.especie.nome_cientifico);
        formData.append('descricao', this.especie.descricao || '');
        formData.append('id_categoria', this.especie.id_categoria);

        const responseEspecie = await axios.post('http://localhost:8000/api/especies', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        });

        const novaEspecieId = responseEspecie.data.id_especie || responseEspecie.data.id;

        if (this.ocorrenciasSelecionadas.length > 0) {
          await axios.post(`http://localhost:8000/api/especies/${novaEspecieId}/vincular-ocorrencias`, {
            ocorrencias_ids: this.ocorrenciasSelecionadas
          });
        }

        alert('Espécie cadastrada e ocorrências vinculadas com sucesso!');

        this.especie = {
          nome_popular: '',
          nome_cientifico: '',
          descricao: '',
          id_categoria: 1
        };
        this.ocorrenciasSelecionadas = [];
        this.$router.push('/catalogo');

      } catch (error) {
        console.error('Erro no fluxo de cadastro/vinculação:', error);
        alert('Ocorreu um erro ao salvar o registro.');
      } finally {
        this.salvando = false;
      }
    },

    // Auxiliar apenas para formatar a data do banco (ISO) para o padrão brasileiro
    formatarData(stringData) {
      const data = new Date(stringData);
      return data.toLocaleDateString('pt-BR', { day: '2-digit', month: '2-digit', year: 'numeric' });
    }
  },
  mounted() {
    this.buscarOcorrenciasPublicadas();
  }
}
</script>