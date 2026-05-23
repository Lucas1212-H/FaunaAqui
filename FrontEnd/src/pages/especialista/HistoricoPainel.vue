<template>
  <section class="row g-4">
    <article class="col-12">
      
      <div class="archived-card p-4 shadow-sm"> 
        
        <header class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
          <h4 class="fw-bold m-0 text-dark">Denuncias Arquivadas</h4>
          <small class="text-muted">Clique em qualquer linha para ver o histórico completo do animal</small>
        </header>

        <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th scope="col">Animal</th>
                <th scope="col">Denunciante</th>
                <th scope="col">Processo</th>
                <th scope="col">Data</th>
                <th scope="col">Status final</th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="item in arquivadas"
                :key="item.id"
                class="clickable-row"
                @click="$emit('selecionarHistorico', item)"
              >
                <td class="fw-bold">{{ item.animal }}</td>
                <td>{{ item.denunciante }}</td>
                <td>
                  <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis process-badge">
                    {{ item.processoFinal }}
                  </span>
                </td>
                <td>{{ item.data }}</td>
                <td>
                  <span class="badge rounded-pill bg-dark-subtle text-dark">{{ item.statusFinal }}</span>
                </td>
              </tr>
              
              <tr v-if="arquivadas.length === 0">
                <td colspan="5" class="text-center py-4 text-muted">Nenhum registro arquivado.</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </article>
  </section>
</template>

<script setup>
defineProps({
  arquivadas: { 
    type: Array, 
    required: true,
    default: () => []
  }
});

defineEmits(['selecionarHistorico']);
</script>

<style scoped>
.archived-card { 
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
.clickable-row { 
  cursor: pointer; 
}
.clickable-row:hover { 
  background: #f4f7f4; 
}
.process-badge { 
  border-radius: 999px; 
}
</style>