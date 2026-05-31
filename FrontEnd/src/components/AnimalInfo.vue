<template>
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