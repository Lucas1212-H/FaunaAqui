<template>
  <div v-if="denuncia" class="custom-modal-overlay d-flex align-items-center justify-content-center">
    <div class="custom-modal bg-white p-4 rounded-4 shadow-lg w-50">
      <h4 class="fw-bold text-success mb-3">{{ denuncia.titulo || ('Validar Ocorrência: ' + (denuncia.animal || 'Sem título')) }}</h4>
      <div class="row">
        <div class="col-md-6">
          <img :src="denuncia.imagem || 'https://picsum.photos/400/300'" class="img-fluid rounded-3 mb-3" alt="Foto da denúncia">
        </div>
        <div class="col-md-6">
          <p><strong>Denunciante:</strong> {{ denuncia.denunciante || denuncia.reporter || 'Anônimo' }}</p>
          <div class="d-flex gap-2 mb-2">
            <small class="text-muted">{{ denuncia.data || denuncia.date || 'Data não informada' }}</small>
            <small class="text-muted ms-auto">{{ denuncia.tipo || denuncia.type || 'Tipo não informado' }}</small>
          </div>
          <p><strong>Localização:</strong> {{ denuncia.local || 'Não informada' }}</p>
          <p><strong>Descrição:</strong> <span class="text-muted">{{ denuncia.descricao || denuncia.description || 'Sem descrição' }}</span></p>
          <p><strong>Status:</strong> {{ denuncia.status }}</p>

          <!-- Se não foi alocado por você, mostrar apenas Alocar e Fechar -->
          <div v-if="!alocadoPorMe">
            <div class="d-flex gap-2 mb-2">
              <button class="btn btn-outline-danger flex-grow-1" @click="arquivarLocal">Arquivar</button>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-success flex-grow-1" @click="alocar">Alocar-me a denúncia</button>
              <button class="btn btn-light flex-grow-1" @click="$emit('fechar')">Fechar</button>
            </div>
          </div>

          <!-- Se já alocado por você, mostrar campo de parecer e aprovar -->
          <div v-else>
            <textarea 
              v-model="parecer" 
              class="form-control mb-3" 
              placeholder="Escreva o parecer técnico..."
            ></textarea>
            <div class="d-flex gap-2">
              <button class="btn btn-success flex-grow-1" @click="confirmar">Aprovar</button>
              <button class="btn btn-light flex-grow-1" @click="$emit('fechar')">Fechar</button>
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
  if (!alocadoPorMe.value) return
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
</script>

<style scoped>
.custom-modal-overlay {
  position: fixed; top: 0; left: 0; width: 100%; height: 100%;
  background: rgba(0,0,0,0.5); z-index: 1000;
}
.custom-modal { max-width: 90%; }
.rounded-4 { border-radius: 1.25rem; }

@media (max-width: 768px) {
  .custom-modal { width: 95%; }
}
</style>