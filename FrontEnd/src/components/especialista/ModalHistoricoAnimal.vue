<template>
  <div v-if="item" class="history-overlay d-flex align-items-center justify-content-center">
    <div class="history-modal bg-white shadow-lg p-4 w-100">
      <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
        <div>
          <h4 class="mb-1 title">Histórico do animal</h4>
          <p class="text-muted mb-0">{{ item.titulo || item.animal }}</p>
        </div>
        <button class="btn btn-sm btn-light close-btn" @click="$emit('fechar')">Fechar</button>
      </div>

      <div class="row g-3">
        <div class="col-md-5">
          <img :src="item.imagem || 'https://picsum.photos/500/320'" alt="Imagem do animal" class="img-fluid rounded-3 history-image">
          <div class="info-box mt-3">
            <div><strong>Denunciante:</strong> {{ item.denunciante || item.reporter || 'Anônimo' }}</div>
            <div><strong>Data:</strong> {{ item.data || item.date || '-' }}</div>
            <div><strong>Distinção Biológica:</strong> {{ item.distincaoBiologica || item.tipo || item.type || 'Tipo não informado' }}</div>
            <div><strong>Local:</strong> {{ item.local || '-' }}</div>
          </div>
        </div>

        <div class="col-md-7">
          <div class="timeline-box">
            <h6 class="section-title mb-3">Linha do processo</h6>
            <div v-for="(step, index) in item.historico || []" :key="index" class="timeline-step">
              <div class="dot"></div>
              <div class="step-content">
                <div class="d-flex justify-content-between gap-2 flex-wrap">
                  <strong>{{ step.titulo }}</strong>
                  <small class="text-muted">{{ step.data }}</small>
                </div>
                <p class="mb-0 text-muted">{{ step.descricao }}</p>
              </div>
            </div>
          </div>

          <div class="note-box mt-3">
            <strong>Resumo final:</strong>
            <p class="mb-0 text-muted">{{ item.resumoFinal || 'Processo concluído e enviado para arquivamento.' }}</p>
          </div>

          <div v-if="item.statusFinal === 'Resolvido' || item.status === 'Resolvido'" class="mt-3 d-grid">
            <button class="btn btn-outline-info fw-semibold" @click="$emit('publicar', item)">
               Publicar no Mapa
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  item: Object
})

defineEmits(['fechar', 'publicar'])
</script>

<style scoped>
.history-overlay {
  position: fixed;
  inset: 0;
  background: rgba(35, 44, 38, 0.45);
  z-index: 1100;
  padding: 1rem;
}

.history-modal {
  max-width: 920px;
  border-radius: 14px;
  border: 1px solid #d4dbd4;
}

.title {
  color: #2f4d3a;
}

.close-btn {
  border-radius: 0.55rem;
}

.history-image {
  border: 1px solid #d7ddd7;
}

.info-box,
.timeline-box,
.note-box {
  background: #f7f8f6;
  border: 1px solid #dfe5df;
  border-radius: 12px;
  padding: 1rem;
}

.section-title {
  color: #46604f;
}

.timeline-step {
  display: flex;
  gap: 0.75rem;
  padding-bottom: 0.9rem;
  margin-bottom: 0.9rem;
  border-bottom: 1px solid #e1e5e1;
}

.timeline-step:last-child {
  border-bottom: 0;
  margin-bottom: 0;
  padding-bottom: 0;
}

.dot {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  background: #4a6a57;
  margin-top: 0.35rem;
  flex: 0 0 auto;
}

.step-content {
  flex: 1;
}
</style>