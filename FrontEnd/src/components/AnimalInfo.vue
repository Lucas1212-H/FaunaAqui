<template>
  <div class="animal-info-page">
    <NavBar />

    <main class="container py-4 py-lg-5">
      <section v-if="carregando" class="state-panel text-center py-5" aria-live="polite">
        <div class="spinner-border text-success" role="status"></div>
        <p class="text-muted mt-3 mb-0">Carregando ficha do animal...</p>
      </section>

      <section v-else-if="animal" class="animal-shell">
        <header class="animal-hero">
          <div class="animal-title-block">
            <p class="section-kicker mb-2">Ficha da espécie</p>
            <h1 class="animal-title mb-2">{{ animal.nome_popular }}</h1>
            <p class="animal-scientific mb-0">{{ animal.nome_cientifico }}</p>
          </div>

          <button class="btn btn-success fw-semibold px-4 py-2" @click="editarInformacoes">
            Editar
          </button>
        </header>

        <div class="animal-grid">
          <article class="info-card content-card">
            <h2 class="card-heading">Descrição</h2>
            <p class="mb-0 text-body-secondary">{{ animal.descricao }}</p>
          </article>

          <aside class="info-card media-card" aria-label="Imagem da espécie">
            <img
              class="animal-image"
              :src="animal.foto ? `http://localhost:8000/storage/${animal.foto}` : 'https://picsum.photos/seed/fauna/400/300'"
              :alt="`Imagem de ${animal.nome_popular}`"
            />
          </aside>
        </div>

        <section class="stats-card" aria-labelledby="stats-title">
          <header class="mb-3">
            <h2 id="stats-title" class="card-heading mb-1">Dados de Ocorrências Vinculadas</h2>
            <p class="text-body-secondary mb-0">Resumo das ocorrências registradas para esta espécie.</p>
          </header>

          <dl class="stats-grid mb-0">
            <div class="stat-item">
              <dt>Avistados</dt>
              <dd>{{ totalAvistados }}</dd>
            </div>
            <div class="stat-item">
              <dt>Feridos</dt>
              <dd>{{ totalFeridos }}</dd>
            </div>
            <div class="stat-item">
              <dt>Mortos</dt>
              <dd>{{ totalMortos }}</dd>
            </div>
            <div class="stat-item">
              <dt>Área de Risco</dt>
              <dd>{{ totalAreadeRisco }}</dd>
            </div>
            <div class="stat-item">
              <dt>Presos</dt>
              <dd>{{ totalPreso }}</dd>
            </div>
            <div class="stat-item">
              <dt>Total de Ocorrências</dt>
              <dd>{{ totalOcorrencias }}</dd>
            </div>
          </dl>
        </section>

        <article class="map-card" aria-labelledby="map-title">
          <header class="mb-3">
            <h2 id="map-title" class="card-heading mb-1">Mapa de Ocorrências</h2>
            <p class="text-body-secondary mb-0">Os pontos exibidos abaixo representam as ocorrências vinculadas à espécie.</p>
          </header>

          <div id="mapaEspecie" class="mapa-especie" role="application" aria-label="Mapa de ocorrências da espécie"></div>
        </article>
      </section>

      <section v-else class="state-panel text-center py-5">
        <p class="text-muted mb-2">Não foi possível carregar a ficha do animal.</p>
        <p v-if="erro" class="text-danger mb-3">{{ erro }}</p>
        <button class="btn btn-outline-success" @click="retryCarregar">Tentar novamente</button>
      </section>
    </main>
  </div>
</template>
<script>

import NavBar from '@/components/NavBar.vue';
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

      const mapElement = document.getElementById('mapaEspecie');
      if (!mapElement) return;

      delete L.Icon.Default.prototype._getIconUrl;
      L.Icon.Default.mergeOptions({
        iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
        iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png'
      });

      const ocorrenciasValidas = (this.animal?.ocorrencias || []).filter(
        (ocorrencia) => typeof ocorrencia.latitude === 'number' && typeof ocorrencia.longitude === 'number'
      );

      const centro = ocorrenciasValidas.length > 0
        ? [ocorrenciasValidas[0].latitude, ocorrenciasValidas[0].longitude]
        : [-3.119, -60.0217];

      this.mapaInstance = L.map(mapElement).setView(centro, ocorrenciasValidas.length > 0 ? 7 : 4);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
      }).addTo(this.mapaInstance);

      const marcadores = [];

      if (ocorrenciasValidas.length > 0) {
        ocorrenciasValidas.forEach(ocorrencia => {
          // Define a mensagem do balãozinho (Popup)
          const textoPopup = `
            <strong>Ocorrência #${ocorrencia.id}</strong><br/>
            Status: <span class="badge ${ocorrencia.situacao_animal === 'Morto' ? 'bg-danger' : 'bg-warning text-dark'}">${ocorrencia.situacao_animal}</span><br/>
            Ref: ${ocorrencia.ponto_referencia || 'Não informado'}
          `;
          const marker = L.marker([ocorrencia.latitude, ocorrencia.longitude])
            .addTo(this.mapaInstance)
            .bindPopup(textoPopup);

          marcadores.push(marker);
        });

        if (marcadores.length > 0) {
          const grupo = L.featureGroup(marcadores);
          this.mapaInstance.fitBounds(grupo.getBounds().pad(0.2));
        }
      } else {
        L.popup({ closeButton: false, autoClose: false, closeOnClick: false })
          .setLatLng(centro)
          .setContent('Nenhuma ocorrência georreferenciada disponível para esta espécie.')
          .openOn(this.mapaInstance);
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

<style scoped>
.animal-info-page {
  min-height: 100vh;
  background: linear-gradient(180deg, #f7fbf8 0%, #eef6ef 100%);
}

.state-panel,
.animal-shell {
  width: min(1180px, 100%);
  margin: 0 auto;
}

.animal-shell {
  display: grid;
  gap: 1.5rem;
}

.animal-hero,
.info-card,
.stats-card,
.map-card {
  background: #ffffff;
  border: 1px solid rgba(20, 83, 45, 0.08);
  border-radius: 28px;
  box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
}

.animal-hero {
  padding: 1.5rem;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 1rem;
}

.section-kicker {
  display: inline-flex;
  padding: 0.35rem 0.8rem;
  border-radius: 999px;
  background: rgba(132, 204, 22, 0.12);
  color: #14532d;
  font-size: 0.8rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  text-transform: uppercase;
}

.animal-title {
  font-size: clamp(2rem, 4vw, 3rem);
  color: #0f172a;
  line-height: 1.1;
}

.animal-scientific {
  color: #64748b;
  font-size: 1rem;
  font-style: italic;
}

.animal-grid {
  display: grid;
  grid-template-columns: minmax(0, 1.15fr) minmax(280px, 0.85fr);
  gap: 1.5rem;
}

.info-card,
.stats-card,
.map-card {
  padding: 1.5rem;
}

.card-heading {
  font-size: 1.25rem;
  font-weight: 800;
  color: #14532d;
}

.content-card {
  display: flex;
  flex-direction: column;
  justify-content: center;
  min-height: 100%;
}

.animal-image {
  width: 100%;
  height: 100%;
  min-height: 320px;
  object-fit: cover;
  border-radius: 20px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(3, minmax(0, 1fr));
  gap: 1rem;
}

.stat-item {
  padding: 1rem;
  border-radius: 20px;
  background: #f8fbf8;
  border: 1px solid rgba(20, 83, 45, 0.08);
}

.stat-item dt {
  color: #64748b;
  font-size: 0.85rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.04em;
}

.stat-item dd {
  margin: 0.35rem 0 0;
  color: #0f172a;
  font-size: 1.6rem;
  font-weight: 800;
}

.map-card {
  display: flex;
  flex-direction: column;
}

.mapa-especie {
  min-height: 400px;
  width: 100%;
  border-radius: 20px;
  overflow: hidden;
}

@media (max-width: 991.98px) {
  .animal-hero {
    flex-direction: column;
  }

  .animal-grid,
  .stats-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 575.98px) {
  .animal-hero,
  .info-card,
  .stats-card,
  .map-card {
    padding: 1.1rem;
    border-radius: 22px;
  }

  .mapa-especie,
  .animal-image {
    min-height: 280px;
  }
}
</style>