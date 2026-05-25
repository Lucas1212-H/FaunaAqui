<template>
  <div class="home-page">
    <NavBarPublic />

    <header class="hero-section d-flex align-items-center">
      <div class="overlay"></div>
      <div class="container position-relative text-white">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="hero-card shadow-lg">
              <h1 class="display-4 fw-bold">Encontrou um animal silvestre na região?</h1>
              <p class="lead">Ajude a ciência e a preservação local com apenas alguns cliques.</p>
              <button @click="irParaDenuncia" class="btn btn-warning btn-lg fw-bold mt-3 px-5 py-3">Denuncie Aqui</button>
            </div>
          </div>
          <div class="col-lg-6 text-center">
            <button @click="visualizarMapa" class="btn btn-outline-light btn-lg px-5 py-3 blur-btn">Visualizar Mapa</button>
          </div>
        </div>
      </div>
    </header>

    <section class="py-5 bg-light">
      <div class="container">
        <h2 class="text-center fw-bold mb-5 section-title">Animais Catalogados</h2>
        <div class="row g-4">
          <AnimalCard 
            v-for="animal in animais" 
            :key="animal.id"
            v-bind="animal"
          />
        </div>
      </div>
    </section>

    <Footer />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import NavBarPublic from '@/components/NavBarPublic.vue';
import Footer from '@/components/Footer.vue';
import AnimalCard from '@/components/AnimalCard.vue';

const router = useRouter();

const irParaDenuncia = () => {
  router.push('/denuncia');
};

const visualizarMapa = () => {
  console.log('Abrindo visualizador de mapa');
  // TODO: Implementar visualização de mapa
};

// Dados que virão futuramente da sua API/Firebase
const animais = [
  { id: 1, nome: 'Lobo-guará', especie: 'Mamífero', local: 'Área rural', data: '12/05/2024', imagem: 'https://picsum.photos/500/300?1' },
  { id: 2, nome: 'Tucano', especie: 'Ave', local: 'Região central', data: '10/05/2024', imagem: 'https://picsum.photos/500/300?2' },
  { id: 3, nome: 'Jaguatirica', especie: 'Mamífero', local: 'Reserva ambiental', data: '09/05/2024', imagem: 'https://picsum.photos/500/300?3' }
];
</script>

<style scoped>
.hero-section {
  min-height: 80vh;
  background: url('@/assets/images/faunaaqui.png') center/cover no-repeat;
  position: relative;
}
.overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.4); }
.hero-card { background: rgba(0, 0, 0, 0.5); padding: 3rem; border-radius: 25px; backdrop-filter: blur(8px); }
.blur-btn { backdrop-filter: blur(5px); border-radius: 15px; }
.section-title { color: #14532d; font-size: 2.5rem; }
</style>