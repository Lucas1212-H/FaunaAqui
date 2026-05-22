<template>
  <div class="dashboard-page bg-light min-vh-100 d-flex flex-column">
    <NavBar />

    <div class="container-fluid px-4 flex-grow-1 py-4">
      
      <CardsMetricas :total="142" :pendentes="12" :urgentes="5" :especies="38" />

      <div class="row g-4">
        <div class="col-lg-8">
          <div class="data-card p-4 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
              <h4 class="fw-bold m-0 text-dark">Fila de Triagem</h4>
              <div class="input-group w-auto">
                <span class="input-group-text bg-white border-0 shadow-sm"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control border-0 shadow-sm" placeholder="Buscar ocorrência...">
              </div>
            </div>
            
            <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Animal</th>
                    <th>Localização</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="d in denuncias" :key="d.id">
                    <td><span class="fw-bold">{{ d.animal }}</span></td>
                    <td><small class="text-muted">{{ d.local }}</small></td>
                    <td><small>{{ d.data }}</small></td>
                    <td>
                      <span :class="['badge rounded-pill', getStatusClass(d.status)]">
                        {{ d.status }}
                      </span>
                    </td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-light text-success me-1 shadow-sm" @click="handleValidar(d)">
                        <i class="fas fa-eye"></i>
                      </button>
                      <button class="btn btn-sm btn-light text-primary shadow-sm" @click="gerarLaudo(d)">
                        <i class="fas fa-file-pdf"></i>
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <button class="btn btn-primary-light w-100 mt-4 py-3 fw-bold" @click="handleVerArquivados">
              Ver Histórico Completo
            </button>
          </div>
        </div>

        <div class="col-lg-4">
          <PainelAnalise @gerenciarEspecies="handleGerenciarEspecies" />
        </div>
      </div>
    </div>

    <ModalValidacao 
      :denuncia="selectedDenuncia" 
      @fechar="selectedDenuncia = null" 
      @aprovar="processarAprovacao"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue';
import NavBar from '@/components/NavBar.vue';
import CardsMetricas from '@/components/especialista/CardsMetricas.vue';
import PainelAnalise from '@/components/especialista/PainelAnalise.vue';
import ModalValidacao from '@/components/especialista/ModalValidacao.vue';

const selectedDenuncia = ref(null);

const denuncias = ref([
  { id: 1, animal: 'Serpente', local: 'Reserva Ambiental', data: '29/04/2025', status: 'Morto' },
  { id: 2, animal: 'Capivara', local: 'Parque do Lago', data: '27/04/2025', status: 'Ferido' },
  { id: 3, animal: 'Gavião', local: 'Área Urbana Central', data: '25/04/2025', status: 'Avistado' },
  { id: 4, animal: 'Lobo-Guará', local: 'Estrada da Ceasa', data: '24/04/2025', status: 'Morto' }
]);

const getStatusClass = (status) => {
  if (status === 'Morto') return 'bg-danger';
  if (status === 'Ferido') return 'bg-primary';
  return 'bg-warning text-dark';
};

const handleValidar = (d) => {
  selectedDenuncia.value = d;
};

const processarAprovacao = (dadosAprovacao) => {
  console.log('Aprovando chamado com parecer técnico:', dadosAprovacao);
  // Aqui você faria o seu commit de API futuramente
  selectedDenuncia.value = null;
  alert('Ocorrência validada e salva!');
};

const gerarLaudo = (d) => {
  alert(`Gerando Laudo Técnico em PDF para: ${d.animal}`);
};

const handleVerArquivados = () => console.log('Ver Arquivados');
const handleGerenciarEspecies = () => console.log('Gerenciar Espécies');
</script>

<style scoped>
.data-card {
  background: #58d68d;
  border-radius: 25px;
  color: #1e293b;
}
.table { --bs-table-bg: transparent; }
.table-responsive { max-height: 400px; }
.btn-primary-light { background-color: #27ae60; color: white; border: none; border-radius: 15px; }
.rounded-4 { border-radius: 1.25rem; }
</style>