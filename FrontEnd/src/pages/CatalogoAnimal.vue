<template>
  <div>
    <NavBar />

    <main class="container py-5">
      
      <section v-if="!categoriaSelecionada">
        <header class="d-flex justify-content-between align-items-center mb-4">
          <h1 class="h3 fw-bold text-dark m-0">Catálogo de Categorias</h1>
          <button class="btn btn-success fw-medium px-4" @click="modalNovaCategoria = true">
            + Nova Categoria
          </button>
        </header>

        <div class="row row-cols-2 row-cols-md-4 g-4 justify-content-center">
          <div class="col" v-for="cat in listaCategorias" :key="cat.id_categoria">
            <article 
              class="card p-4 text-center shadow-sm h-100 border-0 rounded-3"
              style="cursor: pointer;"
              @click="selecionarCategoria(cat)"
            >
              <figure class="m-0">
                <img 
                  :src="cat.foto ? `http://localhost:8000/storage/${cat.foto}` : 'https://cdn-icons-png.flaticon.com/512/616/616408.png'" 
                  :alt="cat.nome_popular"
                  class="img-fluid mb-2" 
                  style="max-height: 80px;"
                >
                <figcaption class="h5 fw-bold text-dark m-0">{{ cat.nome_popular }}</figcaption>
                <small class="text-muted fst-italic">{{ cat.nome_cientifico }}</small>
              </figure>
            </article>
          </div>
        </div>
      </section>

      <section v-else>
        <header class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <button class="btn btn-sm btn-light border px-3 mb-2" @click="categoriaSelecionada = null">
              ← Voltar
            </button>
            <h1 class="h3 fw-bold text-success m-0">Espécies em {{ categoriaSelecionada.nome_popular }}</h1>
          </div>
          
          <button class="btn btn-primary fw-medium px-4" @click="modalNovaEspecie = true">
            + Nova Espécie
          </button>
        </header>

        <div class="row row-cols-1 row-cols-md-3 g-4">
          <div class="col" v-for="especie in (categoriaSelecionada?.especies || [])" :key="especie.id_especie">
            <article class="card h-100 shadow-sm border-0 overflow-hidden rounded-3" style="cursor: pointer;" @click="IrParaDetalhes(especie.id_especie)">
              <img 
                :src="especie.foto ? `http://localhost:8000/storage/${especie.foto}` : 'https://picsum.photos/seed/fauna/300/200'" 
                :alt="especie.nome_popular" 
                class="card-img-top object-fit-cover"
                style="height: 180px;"
              >
              <div class="card-body p-3">
                <h2 class="h5 fw-bold text-dark mb-1">{{ especie.nome_popular }}</h2>
                <small class="text-muted fst-italic d-block mb-2">{{ especie.nome_cientifico }}</small>
                <p class="text-secondary small m-0">{{ especie.descricao }}</p>
              </div>
            </article>
          </div>
        </div>

        <div v-if="(categoriaSelecionada?.especies || []).length === 0" class="alert alert-light text-center border mt-4">
          Nenhuma espécie cadastrada em {{ categoriaSelecionada?.nome_popular }} ainda.
        </div>
      </section>

    </main>

    <!-- Modal Nova Categoria -->
    <div class="modal" :class="{ show: modalNovaCategoria }" :style="{ display: modalNovaCategoria ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nova Categoria</h5>
            <button type="button" class="btn-close" @click="modalNovaCategoria = false"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nome Científico</label>
              <input type="text" class="form-control" v-model="formCategoria.nome_cientifico" placeholder="Ex: Felidae">
            </div>
            <div class="mb-3">
              <label class="form-label">Nome Popular</label>
              <input type="text" class="form-control" v-model="formCategoria.nome_popular" placeholder="Ex: Felinos">
            </div>
            <div class="mb-3">
              <label class="form-label">Foto</label>
              <input type="file" class="form-control" @change="handleFotoCategoriaChange" accept="image/*">
              <small class="text-muted">Formatos aceitos: JPG, PNG, GIF, WebP</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="modalNovaCategoria = false">Cancelar</button>
            <button type="button" class="btn btn-success" @click="criarCategoria">Salvar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal Nova Espécie -->
    <div class="modal" :class="{ show: modalNovaEspecie }" :style="{ display: modalNovaEspecie ? 'block' : 'none' }" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Nova Espécie</h5>
            <button type="button" class="btn-close" @click="modalNovaEspecie = false"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nome Científico</label>
              <input type="text" class="form-control" v-model="formEspecie.nome_cientifico" placeholder="Ex: Panthera leo">
            </div>
            <div class="mb-3">
              <label class="form-label">Nome Popular</label>
              <input type="text" class="form-control" v-model="formEspecie.nome_popular" placeholder="Ex: Leão">
            </div>
            <div class="mb-3">
              <label class="form-label">Foto</label>
              <input type="file" class="form-control" @change="handleFotoEspecieChange" accept="image/*">
              <small class="text-muted">Formatos aceitos: JPG, PNG, GIF, WebP</small>
            </div>
            <div class="mb-3">
              <label class="form-label">Descrição</label>
              <textarea class="form-control" v-model="formEspecie.descricao" rows="3" placeholder="Ex: Descrição da espécie..."></textarea>
            </div>

            <hr>

            <div class="mb-2">
              <label class="form-label fw-semibold">Atribuir ocorrências publicadas</label>
              <p class="small text-muted mb-2">Selecione as ocorrências já publicadas no mapa que pertencem a esta espécie.</p>

              <div v-if="carregandoOcorrencias" class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-success" role="status"></div>
                <small class="text-muted ms-2">Carregando ocorrências publicadas...</small>
              </div>

              <div v-else-if="ocorrenciasPublicadas.length > 0" class="border rounded p-2" style="max-height: 220px; overflow-y: auto;">
                <label
                  v-for="ocorrencia in ocorrenciasPublicadas"
                  :key="ocorrencia.id"
                  class="d-block border rounded p-2 mb-2"
                >
                  <input
                    type="checkbox"
                    class="form-check-input me-2"
                    :value="ocorrencia.id"
                    v-model="ocorrenciasSelecionadas"
                  >
                  <span class="fw-semibold">{{ ocorrencia.tipo_animal || 'Animal não informado' }}</span>
                  <small class="d-block text-muted">{{ ocorrencia.ponto_referencia || 'Local não informado' }}</small>
                  <small class="d-block text-muted">{{ new Date(ocorrencia.created_at).toLocaleDateString('pt-BR') }}</small>
                </label>
              </div>

              <div v-else class="alert alert-light border text-center py-2 mb-0">
                Nenhuma ocorrência publicada disponível para vínculo.
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="modalNovaEspecie = false">Cancelar</button>
            <button type="button" class="btn btn-primary" @click="criarEspecie">Salvar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Backdrop -->
    <div class="modal-backdrop fade" :class="{ show: modalNovaCategoria || modalNovaEspecie }" v-if="modalNovaCategoria || modalNovaEspecie"></div>
  </div>
</template>

<script>
import NavBar from '@/components/NavBar.vue';
import axios from 'axios';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

export default {
  components: {
    NavBar
  },
  data() {
    return {
      // Estado que dita qual tela está ativa (null = Categorias, objeto preenchido = Espécies)
      categoriaSelecionada: null,
      
      // Recebe o JSON completo estruturado com suas espécies
      listaCategorias: [],
      
      // Modais
      modalNovaCategoria: false,
      modalNovaEspecie: false,

      // Ocorrências publicadas para vincular na criação de espécie
      carregandoOcorrencias: false,
      ocorrenciasPublicadas: [],
      ocorrenciasSelecionadas: [],
      
      // Formulários
      formCategoria: {
        nome_cientifico: '',
        nome_popular: '',
        foto: null
      },
      formEspecie: {
        nome_cientifico: '',
        nome_popular: '',
        foto: null,
        descricao: ''
      }
    }
  },
  methods: {
    async buscarDadosDoCatalogo() {
      try {
        const response = await axios.get('http://localhost:8000/api/categorias');
        this.listaCategorias = response.data;
      } catch (error) {
        console.error('Erro ao conectar com a API:', error);
      }
    },
    
    async selecionarCategoria(categoria) {
      try {
        // Busca a categoria com suas espécies
        const response = await axios.get(`http://localhost:8000/api/categorias/${categoria.id_categoria || categoria.id}`);
        let dados = response.data;
        
        // Se a resposta for um array, pega o primeiro elemento
        if (Array.isArray(dados)) {
          dados = dados[0] || {};
        }
        
        // Garante que sempre há um array de espécies, mesmo que vazio
        if (!dados.especies) {
          dados.especies = [];
        }
        
        this.categoriaSelecionada = dados;
      } catch (error) {
        console.error('Erro ao carregar espécies:', error);
        // Mesmo com erro, entra na categoria com espécies vazias
        this.categoriaSelecionada = {
          ...categoria,
          especies: []
        };
      }
    },
    
    handleFotoCategoriaChange(event) {
      this.formCategoria.foto = event.target.files[0] || null;
    },
    
    handleFotoEspecieChange(event) {
      this.formEspecie.foto = event.target.files[0] || null;
    },
    
    async criarCategoria() {
      try {
        if (!this.formCategoria.nome_cientifico || !this.formCategoria.nome_popular) {
          alert('Preencha todos os campos obrigatórios!');
          return;
        }
        
        const formData = new FormData();
        formData.append('nome_cientifico', this.formCategoria.nome_cientifico);
        formData.append('nome_popular', this.formCategoria.nome_popular);
        if (this.formCategoria.foto) {
          formData.append('foto', this.formCategoria.foto);
        }
        
        await axios.post('http://localhost:8000/api/categorias', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        
        // Limpar formulário e fechar modal
        this.formCategoria = { nome_cientifico: '', nome_popular: '', foto: null };
        this.modalNovaCategoria = false;
        
        // Recarregar dados
        await this.buscarDadosDoCatalogo();
        alert('Categoria criada com sucesso!');
      } catch (error) {
        console.error('Erro ao criar categoria:', error);
        alert('Erro ao criar categoria. Verifique o console para mais detalhes.');
      }
    },
    
    async criarEspecie() {
      try {
        if (!this.categoriaSelecionada) {
          alert('Selecione uma categoria primeiro!');
          return;
        }

        const categoriaId = this.categoriaSelecionada.id_categoria || this.categoriaSelecionada.id;

        if (!categoriaId) {
          alert('Categoria inválida. Reabra a categoria e tente novamente.');
          return;
        }
        
        if (!this.formEspecie.nome_cientifico || !this.formEspecie.nome_popular) {
          alert('Preencha todos os campos obrigatórios!');
          return;
        }
        
        const formData = new FormData();
        formData.append('id_categoria', String(categoriaId));
        formData.append('nome_cientifico', this.formEspecie.nome_cientifico);
        formData.append('nome_popular', this.formEspecie.nome_popular);
        formData.append('descricao', this.formEspecie.descricao);
        if (this.formEspecie.foto) {
          formData.append('foto', this.formEspecie.foto);
        }
        
        const responseCriacao = await axios.post('http://localhost:8000/api/especies', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });

        const novaEspecieId = responseCriacao.data?.id_especie || responseCriacao.data?.id;

        if (novaEspecieId && this.ocorrenciasSelecionadas.length > 0) {
          await axios.post(`http://localhost:8000/api/especies/${novaEspecieId}/vincular-ocorrencias`, {
            ocorrencias_ids: this.ocorrenciasSelecionadas
          });
        }
        
        // Limpar formulário e fechar modal
        this.formEspecie = { nome_cientifico: '', nome_popular: '', foto: null, descricao: '' };
        this.ocorrenciasSelecionadas = [];
        this.modalNovaEspecie = false;
        
        // Recarregar categoria com suas espécies
        const response = await axios.get(`http://localhost:8000/api/categorias/${categoriaId}`);
        const dados = response.data;
        if (!dados.especies) {
          dados.especies = [];
        }
        this.categoriaSelecionada = dados;
        
        alert('Espécie criada com sucesso!');
      } catch (error) {
        console.error('Erro ao criar espécie:', error);
        const errosValidacao = error?.response?.data?.errors;
        if (errosValidacao) {
          const primeiraChave = Object.keys(errosValidacao)[0];
          const primeiraMensagem = primeiraChave ? errosValidacao[primeiraChave][0] : null;
          alert(primeiraMensagem || 'Dados inválidos para criar espécie.');
          return;
        }

        const mensagem = error?.response?.data?.message;
        alert(mensagem || 'Erro ao criar espécie. Verifique o console para mais detalhes.');
      }
    },
    async buscarOcorrenciasPublicadas() {
      try {
        this.carregandoOcorrencias = true;
        const response = await axios.get('http://localhost:8000/api/ocorrencias/publicados');
        this.ocorrenciasPublicadas = response.data || [];
      } catch (error) {
        console.error('Erro ao carregar ocorrências publicadas:', error);
        this.ocorrenciasPublicadas = [];
      } finally {
        this.carregandoOcorrencias = false;
      }
    },
    IrParaDetalhes(id_especie) {
      // Navega para a rota de detalhe do catálogo (AnimalInfo)
      this.$router.push({ name: 'catalogo-detalhe', params: { id: id_especie } });
    }
  },
  watch: {
    modalNovaEspecie(valor) {
      if (valor) {
        this.ocorrenciasSelecionadas = [];
        this.buscarOcorrenciasPublicadas();
      }
    }
  },
  mounted() {
    this.buscarDadosDoCatalogo();
  }
}
</script>

<style scoped>
/* Estilos mínimos mantidos apenas para caprichar na apresentação dos cards */
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 15px rgba(0,0,0,0.08) !important;
}

/* Modal styles */
.modal.show {
  display: block !important;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1040;
  width: 100vw;
  height: 100vh;
  background-color: #000;
}

.modal-backdrop.show {
  opacity: 0.5;
}
</style>