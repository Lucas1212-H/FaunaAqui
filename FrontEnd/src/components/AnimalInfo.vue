<template>
  <div class="animal-info-page min-vh-100">
    <NavBar />

    <main class="container py-5">
      <article class="info-container p-4 p-md-5 shadow-sm text-white row g-4 position-relative overflow-hidden">
        <div v-if="carregando" class="col-12 text-center py-5">
          <div class="spinner-border text-light" role="status" aria-label="Carregando"></div>
        </div>

        <template v-else>
          <section class="col-lg-7 d-flex flex-column justify-content-between">
            <div class="mb-4">
              <header class="d-flex justify-content-between align-items-start mb-3 gap-3">
                <div>
                  <h2 class="display-6 fw-bold m-0 text-white">{{ animal.nome }}</h2>
                  <small class="animal-subtitle d-block mt-1">{{ animal.nomeCientifico }}</small>
                </div>
                <button class="btn btn-edit fw-medium px-4 py-2 d-lg-none">Editar</button>
              </header>
              <p class="description-text m-0">{{ animal.descricao }}</p>
            </div>

            <footer class="mt-auto">
              <div class="row g-3 mb-3">
                <div class="col-4">
                  <div class="metric-card bg-avistamentos p-3 text-center">
                    <strong class="d-block fs-3 fw-bold m-0">{{ metricas.avistamentos }}</strong>
                    <span class="fw-medium d-block text-truncate">Avistamentos</span>
                  </div>
                </div>
                <div class="col-4">
                  <div class="metric-card bg-feridos p-3 text-center">
                    <strong class="d-block fs-3 fw-bold m-0">{{ metricas.feridos }}</strong>
                    <span class="fw-medium d-block text-truncate">Feridos</span>
                  </div>
                </div>
                <div class="col-4">
                  <div class="metric-card bg-mortos p-3 text-center">
                    <strong class="d-block fs-3 fw-bold m-0">{{ metricas.mortos }}</strong>
                    <span class="fw-medium d-block text-truncate">Mortos</span>
                  </div>
                </div>
              </div>

              <figure class="map-wrapper rounded-3 overflow-hidden border border-white border-opacity-10 m-0">
                <img
                  :src="animal.imagemMapa"
                  :alt="animal.nome"
                  class="w-100 object-fit-cover"
                  style="height: 160px;"
                >
              </figure>
            </footer>
          </section>

          <section class="col-lg-5 d-flex flex-column align-items-end justify-content-between position-relative">
            <button class="btn btn-edit fw-medium px-4 py-2 d-none d-lg-block mb-3">Editar</button>

            <figure class="w-100 my-auto d-flex justify-content-center justify-content-lg-end m-0">
              <img
                :src="animal.imagem"
                :alt="animal.nome"
                class="img-fluid rounded-3 shadow-sm object-fit-cover w-100"
                style="max-height: 340px; max-width: 440px;"
              >
            </figure>
          </section>
        </template>
      </article>
    </main>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import axios from 'axios'
import NavBar from '@/components/NavBar.vue'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

const route = useRoute()
const carregando = ref(true)
const ocorrencias = ref([])
const animalSelecionado = ref(null)

const API_BASE = 'http://localhost:8000/api/ocorrencias'
const STORAGE_BASE = 'http://localhost:8000/storage'
const FALLBACK_IMAGE = 'https://cdn-icons-png.flaticon.com/512/616/616408.png'

const normalizar = (valor) => (valor ?? '').toString().trim()

const tituloAnimal = (valor) => {
  const texto = normalizar(valor)
  return texto ? texto.charAt(0).toUpperCase() + texto.slice(1) : 'Animal'
}

const animal = computed(() => {
  if (!animalSelecionado.value) {
    return {
      nome: 'Animal não encontrado',
      nomeCientifico: 'Dados indisponíveis',
      descricao: 'Não foi possível localizar os dados desta ocorrência.',
      imagem: FALLBACK_IMAGE,
      imagemMapa: FALLBACK_IMAGE,
      tipoAnimal: '',
      categoria: 'Categoria não informada',
      status: 'Indefinido',
      localOcorrencia: 'Indefinido',
      pontoReferencia: 'Indefinido',
      latitude: '-',
      longitude: '-',
    }
  }

  return animalSelecionado.value
})

const metricas = computed(() => {
  const tipo = normalizar(animal.value.tipoAnimal)
  const listaRelacionada = ocorrencias.value.filter((item) => item.tipoAnimal === tipo)

  return {
    avistamentos: listaRelacionada.length,
    feridos: listaRelacionada.filter((item) => normalizar(item.localOcorrencia).toLowerCase().includes('ferid')).length,
    mortos: listaRelacionada.filter((item) => normalizar(item.localOcorrencia).toLowerCase().includes('mort')).length,
  }
})

const carregarDados = async () => {
  try {
    carregando.value = true

    const [listaResponse, detalheResponse] = await Promise.all([
      axios.get(`${API_BASE}/publicados`),
      axios.get(`${API_BASE}/${route.params.id}`),
    ])

    ocorrencias.value = listaResponse.data.map((item) => ({
      id: item.id,
      tipoAnimal: normalizar(item.tipo_animal),
      localOcorrencia: item.local_ocorrencia || item.situacao_animal || 'Não informado',
    }))

    const data = detalheResponse.data?.data ?? detalheResponse.data

    animalSelecionado.value = {
      nome: tituloAnimal(data.tipo_animal),
      nomeCientifico: data.categoria_ocorrencia || 'Categoria não informada',
      descricao: data.descricao_ocorrencia || data.descricao || 'Sem descrição',
      imagem: data.foto_path ? `${STORAGE_BASE}/${data.foto_path}` : FALLBACK_IMAGE,
      imagemMapa: data.foto_path ? `${STORAGE_BASE}/${data.foto_path}` : FALLBACK_IMAGE,
      tipoAnimal: normalizar(data.tipo_animal),
      categoria: data.categoria_ocorrencia || 'Categoria não informada',
      status: data.status || 'Publicado',
      localOcorrencia: data.local_ocorrencia || data.situacao_animal || 'Não informado',
      pontoReferencia: data.ponto_referencia || 'Não informado',
      latitude: data.latitude || '-',
      longitude: data.longitude || '-',
    }
  } catch (error) {
    console.error('Erro ao carregar dados do animal:', error)
    animalSelecionado.value = null
  } finally {
    carregando.value = false
  }
}

onMounted(() => {
  carregarDados()
})
</script>

<style scoped>
.info-container {
  background-color: #3fd394;
  border-radius: 20px;
}

.animal-subtitle {
  color: rgba(255, 255, 255, 0.85);
  font-size: 1rem;
}

.btn-edit {
  background-color: #a3e681;
  color: #1e4620;
  border: none;
  border-radius: 10px;
  transition: background-color 0.2s ease;
}

.btn-edit:hover {
  background-color: #91d271;
  color: #1e4620;
}

.description-text {
  font-size: 0.95rem;
  line-height: 1.6;
  opacity: 0.95;
  text-align: justify;
}

.metric-card {
  border-radius: 10px;
  font-size: 0.9rem;
}

.bg-avistamentos {
  background-color: #a3e681;
  color: #1e4620;
}

.bg-feridos {
  background-color: #e9cc6d;
  color: #554411;
}

.bg-mortos {
  background-color: #ee5b6c;
  color: #ffffff;
}

.map-wrapper {
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}
</style>
  <NavBar />
  <div v-if="carregando" class="container my-5 text-center py-5">
      <div class="spinner-border text-success" role="status"></div>
      <p class="text-muted mt-2">Carregando ficha do animal...</p>
  </div>
  <div v-else-if="animal" class="container my-5 d-flex flex-column align-items-start border rounded p-5 
  border-secondary bg-[#AAF59D]">

  <header class="d-flex justify-content-between align-items-center mb-2 w-100 px-3">
    <div class="flex-grow-1 pe-5">
      <h1 class="mb-3 text-nowrap">{{ animal.nome_popular }}</h1>
      <h2 class="mb-3 text-nowrap">{{ animal.nome_cientifico }}</h2>
    </div>

    <button class="btn btn-primary" @click="editarInformacoes">Editar</button>
  </header>

  <main class="w-100 mt-4">
    <section class="row align-items-start mb-5 px-3">
      <div class="col-md-7 pe-5">
        <p>{{ animal.descricao }}</p>
      </div>

      <div class="col-md-5 ps-5 text-end">
          <img 
              class="img-fluid d-block ms-auto rounded shadow-sm object-fit-cover" 
              style="max-height: 300px; width: 100%;"
              :src="animal.foto ? `http://localhost:8000/storage/${animal.foto}` : 'https://picsum.photos/seed/fauna/400/300'" 
              :alt="`Imagem de ${animal.nome_popular}`" 
            />      
      </div>
    </section>

    <section class="row align-items-start mb-5 px-3">
      <h3>Dados de Ocorrências Vinculadas</h3>
      <div>
        <strong> {{ totalAvistados }}</strong>
        <span>Avistados</span>
      </div>
      <div>
        <strong>{{ totalFeridos }}</strong>
        <span>Feridos</span>
      </div>
      <div>
        <strong>{{ totalMortos }}</strong>
        <span>Mortos</span>
      </div>
      <div>
        <strong>{{ totalAreadeRisco }}</strong>
        <span>Área de Risco</span>
      </div>
      <div>
        <strong>{{ totalPreso }}</strong>
        <span>Presos</span>
      </div>
      <div>
        <strong>{{ totalOcorrencias }}</strong>
        <span>Total de Ocorrências</span>
      </div>
    </section>

    <article>
      <h3>Mapa de Ocorrências</h3>
        <div id="mapaEspecie" style="height:400px; width:100%;"></div>
    </article>
  </main>
</div>
  <div v-else class="container my-5 text-center">
    <p class="text-muted">Não foi possível carregar a ficha do animal.</p>
    <p v-if="erro" class="text-danger">{{ erro }}</p>
    <button class="btn btn-secondary" @click="retryCarregar">Tentar novamente</button>
  </div>
</template>
<script>
import NavBar from './NavBar.vue';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

export default {
  components: {
    NavBar
  },
  data() {
    return {
        carregando: true, // começa verdadeira para esperar o banco responder
        animal: null, // aqui guardará os dados do animal
    mapaInstance: null,
    erro: null
    }
  },
  computed: {
    totalAvistados() {
      if (!this.animal || !this.animal.ocorrencias) return 0;
      return this.animal.ocorrencias.filter(o => o.situacao_animal === 'Avistado').length;
    },
    totalFeridos() {
      if (!this.animal || !this.animal.ocorrencias) return 0;
      return this.animal.ocorrencias.filter(o => o.situacao_animal === 'Ferido').length;
    },
    totalMortos() {
      if (!this.animal || !this.animal.ocorrencias) return 0;
      return this.animal.ocorrencias.filter(o => o.situacao_animal === 'Morto').length;
    },
    totalAreadeRisco() {
      if (!this.animal || !this.animal.ocorrencias) return 0;
      return this.animal.ocorrencias.filter(o => o.situacao_animal === 'Área de Risco').length;
    },
    totalPreso() {
      if (!this.animal || !this.animal.ocorrencias) return 0;
      return this.animal.ocorrencias.filter(o => o.situacao_animal === 'Preso').length;
    }
    ,
    totalOcorrencias() {
      if (!this.animal || !this.animal.ocorrencias) return 0;
      return this.animal.ocorrencias.length;
    }
  },
  methods: {
    async carregarDadosDaEspecie() {
        try {
          // pegamos o ID do animal diretamente da URL
          // se não usar router, usar um ID fixo para testar
          this.carregando = true;
          this.erro = null;
          const id_especie = this.$route.params.id;
          // Requisição Axios para a API do Laravel
          const response = await axios.get(`http://localhost:8000/api/especies/${id_especie}`);
          // Injeta os dados retornados no nosso objeto local
          this.animal = response.data;

          this.$nextTick(() =>
            this.inicializarMapa()
          );
        } catch (error) {
          console.error('Erro ao carregar dados do animal:', error);
          this.erro = (error.response && error.response.data && error.response.data.message) ? error.response.data.message : error.message;
        }
        finally {
          this.carregando = false;
        }
    },

    inicializarMapa() {
      if (this.mapaInstance) {
        this.mapaInstance.remove();
        this.mapaInstance = null;
      }
      delete L.Icon.Default.prototype._getIconUrl;
      L.icon.Default.mergeOptions({
        iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
        iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png'
      });
      let centro= [0, 0];
      if (this.animal.ocorrencias && this.animal.ocorrencias.length > 0) {
        const primeiraOcorrencia = this.animal.ocorrencias[0];
        centro = [primeiraOcorrencia.latitude, primeiraOcorrencia.longitude];
      }
      this.mapaInstance = L.map('mapaEspecie').setView(centro, 5);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(this.mapaInstance);

      if (this.animal.ocorrencias) {
        this.animal.ocorrencias.forEach(ocorrencia => {
          // Define a mensagem do balãozinho (Popup)
          const textoPopup = `
            <strong>Ocorrência #${ocorrencia.id}</strong><br/>
            Status: <span class="badge ${ocorrencia.situacao_animal === 'Morto' ? 'bg-danger' : 'bg-warning text-dark'}">${ocorrencia.situacao_animal}</span><br/>
            Ref: ${ocorrencia.ponto_referencia || 'Não informado'}
          `;
          L.marker([ocorrencia.latitude, ocorrencia.longitude])
            .addTo(this.mapaInstance)
            .bindPopup(textoPopup);
        });
      }
    },
    async editarInformacoes() {
      try {
        const response = await axios.put(`http://localhost:8000/api/especies/${this.animal.id_especie}`, this.animal);
        // Atualiza os dados locais com a resposta do servidor
        this.animal = response.data;
        alert('Informações atualizadas com sucesso!');
      } catch (error) {
        console.error('Erro ao atualizar informações do animal:', error);
        alert('Erro ao atualizar informações do animal.');
      }
    }
    ,
    retryCarregar() {
      this.carregarDadosDaEspecie();
    }
  },
  mounted() {
    this.carregarDadosDaEspecie();
  }
}

</script>
