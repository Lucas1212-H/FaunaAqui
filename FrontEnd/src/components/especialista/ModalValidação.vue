<template>
  <div v-if="denuncia" class="custom-modal-overlay d-flex align-items-center justify-content-center">
    <div class="custom-modal bg-white rounded-4 shadow-lg">
      <div class="modal-header-custom">
        <h4 class="modal-title-custom">
          {{ denuncia.titulo || ('Validar Ocorrência: ' + (denuncia.animal || 'Sem título')) }}
        </h4>
        <button class="btn btn-sm btn-light border" @click="$emit('fechar')">Fechar</button>
      </div>

      <div class="modal-body-custom">
        <div class="row g-4">
          <div class="col-lg-6">
            <img
              :src="getImagemUrl(denuncia)"
              class="img-fluid rounded-3 border"
              alt="Foto da denúncia"
            >
          </div>

          <div class="col-lg-6">
            <div class="info-grid mb-3">
              <div class="info-item">
                <span class="label">Denunciante</span>
                <span class="value">{{ denuncia.denunciante || denuncia.reporter || 'Anônimo' }}</span>
              </div>
              <div class="info-item">
                <span class="label">Data</span>
                <span class="value">{{ denuncia.data || denuncia.date || 'Data não informada' }}</span>
              </div>
              <div class="info-item">
                <span class="label">Distinção Biológica</span>
                <span class="value">{{ denuncia.tipo || denuncia.type || 'Tipo não informado' }}</span>
              </div>
              <div class="info-item">
                <span class="label">Localização</span>
                <span class="value">{{ denuncia.local || 'Não informada' }}</span>
              </div>
              <div class="info-item info-item--full">
                <span class="label">Descrição</span>
                <span class="value muted">{{ denuncia.descricao || denuncia.description || 'Sem descrição' }}</span>
              </div>
              <div class="info-item">
                <span class="label">Status</span>
                <span class="status-badge" :class="getStatusClass(denuncia.status)">{{ denuncia.status }}</span>
              </div>
            </div>

            <div v-if="!alocadoPorMe" class="actions-stack">
              <button class="btn btn-outline-danger w-100" @click="arquivarLocal">Arquivar</button>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-success flex-grow-1" @click="alocar">Alocar-me à denúncia</button>
                <button class="btn btn-light border flex-grow-1" @click="$emit('fechar')">Fechar</button>
              </div>
            </div>

            <div v-else class="actions-stack">
              <label class="small fw-semibold text-muted mb-1">Parecer Técnico</label>
              <textarea
                v-model="parecer"
                class="form-control mb-2"
                rows="4"
                placeholder="Escreva o parecer técnico..."
              ></textarea>
              <div class="d-flex gap-2">
                <button class="btn btn-success flex-grow-1" @click="confirmar">Aprovar</button>
                <button class="btn btn-light border flex-grow-1" @click="$emit('fechar')">Fechar</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
  denuncia: Object
})
const emit = defineEmits(['fechar', 'aprovar', 'alocar', 'arquivar'])

const parecer = ref('')
const alocadoPorMe = ref(false)

// Obtém usuário atual do localStorage
const getCurrentUserName = () => {
  try {
    const stored = localStorage.getItem('usuarioLogado')
    if (!stored) return null
    const u = JSON.parse(stored)
    return u.nome || u.email || null
  } catch (e) {
    return null
  }
}

// Limpa estado e define se já está alocado por este usuário quando muda a denúncia
watch(() => props.denuncia, (novo) => {
  parecer.value = ''
  const nome = getCurrentUserName()
  alocadoPorMe.value = novo && novo.assigned && nome && novo.assigned === nome
})

const confirmar = () => {
  emit('aprovar', { denunciaId: props.denuncia.id, parecer: parecer.value })
}

const alocar = () => {
  const nome = getCurrentUserName() || 'Usuário'
  // sinaliza ao pai que este usuário quer alocar
  emit('alocar', { denunciaId: props.denuncia.id, usuario: nome })
  // marca localmente para que o modal mude para o estado de parecer
  alocadoPorMe.value = true
}

const arquivarLocal = () => {
  emit('arquivar', { denunciaId: props.denuncia.id })
  // opcional: fechar o modal após arquivar
  emit('fechar')
}

const getStatusClass = (status) => {
  if (status === 'Morto') return 'status-danger'
  if (status === 'Ferido') return 'status-warning'
  if (status === 'Preso') return 'status-info'
  return 'status-default'
}

const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';

const STORAGE_BASE = isLocal 
  ? 'http://localhost:8000/storage/' 
  : 'https://conviva-labev.onrender.com/storage/';

const getImagemUrl = (denuncia) => {
  if (!denuncia || !denuncia.imagem) return 'https://picsum.photos/640/420';
  const nomeArquivo = denuncia.foto || denuncia.imagem;
  if(!nomeArquivo) return 'https://picsum.photos/640/420';
  if (nomeArquivo.startWith('http')) return nomeArquivo; // já é uma URL completa
  return `${STORAGE_BASE}/${nomeArquivo}`;
</script>

<style scoped>
.custom-modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(12, 20, 33, 0.58);
  z-index: 1000;
  padding: 1rem;
}

.custom-modal {
  width: min(980px, 100%);
  max-height: calc(100vh - 2rem);
  overflow: auto;
}

.modal-header-custom {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 0.75rem;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #e8edf2;
}

.modal-title-custom {
  margin: 0;
  font-size: 1.2rem;
  font-weight: 700;
  color: #175d36;
}

.modal-body-custom {
  padding: 1.25rem;
}

.info-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.65rem;
}

.info-item {
  border: 1px solid #e8edf2;
  background: #f9fbfd;
  border-radius: 10px;
  padding: 0.6rem 0.7rem;
  display: flex;
  flex-direction: column;
  gap: 0.15rem;
}

.info-item--full {
  grid-column: 1 / -1;
}

.label {
  font-size: 0.73rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  color: #6e7b8d;
  font-weight: 700;
}

.value {
  font-size: 0.96rem;
  color: #253142;
  font-weight: 600;
}

.muted {
  color: #5d6a7a;
  font-weight: 500;
}

.status-badge {
  display: inline-flex;
  align-self: flex-start;
  padding: 0.2rem 0.6rem;
  border-radius: 999px;
  font-size: 0.8rem;
  font-weight: 700;
}

.status-danger {
  background: #fde8e8;
  color: #9f2c2c;
}

.status-warning {
  background: #fff4db;
  color: #946200;
}

.status-info {
  background: #e6f2ff;
  color: #1e5f9e;
}

.status-default {
  background: #e9eef4;
  color: #3f5065;
}

.actions-stack {
  display: flex;
  flex-direction: column;
  gap: 0.6rem;
}

@media (max-width: 768px) {
  .modal-body-custom {
    padding: 1rem;
  }

  .info-grid {
    grid-template-columns: 1fr;
  }
}
</style>