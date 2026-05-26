<template>
  <div class="row g-4">
    <div class="col-12">
      <CardsMetricas
        :total="totalRegistros"
        :pendentes="pendentes"
        :urgentes="urgentes"
        :especies="especies"
      />
    </div>

    <div class="col-lg-8">
      <div class="data-card p-4 h-100 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
          <h4 class="fw-bold m-0 text-dark">Fila de Triagem</h4>
          <div class="input-group w-auto">
            <span class="input-group-text bg-white border-0 shadow-sm">
              <i class="fas fa-search text-muted"></i>
            </span>
            <input 
              v-model="filtro" 
              type="text" 
              class="form-control border-0 shadow-sm" 
              placeholder="Buscar ocorrência..."
            >
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
              <tr v-for="d in denunciasFiltradas" :key="d.id">
                <td>
                  <span class="fw-bold">{{ d.animal }}</span>
                  <div v-if="d.assigned">
                    <small class="text-muted d-block">Alocado: {{ d.assigned }}</small>
                  </div>
                </td>
                <td><small class="text-muted">{{ d.local }}</small></td>
                <td><small>{{ d.data }}</small></td>
                <td>
                  <span :class="['badge rounded-pill', getStatusClass(d.status)]">
                    {{ d.status }}
                  </span>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-outline-secondary me-2 shadow-sm action-btn" @click="$emit('validar', d)">
                    Info
                  </button>
                  <button class="btn btn-sm btn-outline-success shadow-sm action-btn" @click="$emit('exportar', d)">
                    Exportar
                  </button>
                </td>
              </tr>
              <tr v-if="denunciasFiltradas.length === 0">
                <td colspan="5" class="text-center py-4 text-muted">Nenhuma ocorrência pendente encontrada.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <PainelAnalise @gerenciarEspecies="$emit('gerenciarEspecies')" />
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import CardsMetricas from '@/components/especialista/CardsMetricas.vue';
import PainelAnalise from '@/components/especialista/PainelAnalise.vue';

const props = defineProps({
  denuncias: { type: Array, required: true },
  totalRegistros: Number,
  pendentes: Number,
  urgentes: Number,
  especies: Number
});

defineEmits(['validar', 'exportar', 'gerenciarEspecies']);

const filtro = ref('');

const denunciasFiltradas = computed(() => {
  if (!props.denuncias) return [];
  if (!filtro.value.trim()) return props.denuncias;
  const termo = filtro.value.toLowerCase();
  return props.denuncias.filter(d => 
    d.animal?.toLowerCase().includes(termo) || 
    d.local?.toLowerCase().includes(termo) ||
    d.denunciante?.toLowerCase().includes(termo)
  );
});

const getStatusClass = (status) => {
  if (status === 'Morto') return 'bg-danger';
  if (status === 'Ferido') return 'bg-primary';
  return 'bg-warning text-dark';
};
</script>

<style scoped>
.data-card { 
  background: #dfe6df; 
  border-radius: 18px; 
  color: #2f3a33; 
  border: 1px solid #c8d0c9; 
}
.table { 
  --bs-table-bg: transparent; 
}
.table-responsive { 
  max-height: 400px; 
}
.rounded-4 { 
  border-radius: 0.9rem; 
}
.action-btn { 
  border-radius: 0.55rem; 
}
</style>