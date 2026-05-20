<template>
  <div class="card border-0 shadow-sm p-4 mx-auto" style="max-width: 360px; border-radius: 24px;">
    
    <div class="d-flex gap-1 mb-3">
      <div class="flex-grow-1 bg-success" style="height: 4px; border-radius: 2px;"></div>
      <div class="flex-grow-1 bg-secondary bg-opacity-25" style="height: 4px; border-radius: 2px;"></div>
      <div class="flex-grow-1 bg-secondary bg-opacity-25" style="height: 4px; border-radius: 2px;"></div>
    </div>

    <h1 class="h4 fw-bold text-dark mb-1">Tipo e situação</h1>
    <p class="small text-muted mb-4">Passo 1 de 4</p>

    <div class="mb-4">
      <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.8px;">
        Tipo de animal
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button 
          v-for="tipo in tipos" 
          :key="tipo"
          @click="tipoAnimal = tipo"
          :class="[
            'btn btn-sm rounded-pill px-3 py-1 border transition-all fs-7',
            tipoAnimal === tipo 
              ? 'btn-success fw-medium' 
              : 'btn-outline-secondary border-light-subtle text-secondary bg-white'
          ]"
        >
          <span v-if="tipoAnimal === tipo" class="me-1 small">✓</span>
          {{ tipo }}
        </button>
      </div>
    </div>

    <div class="mb-4">
      <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.8px;">
        Situação
      </div>
      <div class="d-flex flex-wrap gap-2">
        <button 
          v-for="sit in situacoes" 
          :key="sit"
          @click="situacao = sit"
          :class="[
            'btn btn-sm rounded-pill px-3 py-1 border transition-all fs-7',
            situacao === sit 
              ? 'btn-success fw-medium' 
              : 'btn-outline-secondary border-light-subtle text-secondary bg-white'
          ]"
        >
          {{ sit }}
        </button>
      </div>
    </div>

    <div class="mb-4">
      <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.8px;">
        Descrição (Opcional)
      </div>
      <textarea 
        v-model="descricao" 
        placeholder="Ex: animal preso na grade, aparenta estar desorientado..."
        rows="3"
        class="form-control shadow-none border-light-subtle rounded-3 small"
        style="font-size: 13px; resize: none;"
      ></textarea>
    </div>

    <button 
      class="btn btn-light w-100 py-3 fw-bold border d-flex justify-content-center align-items-center gap-2 mt-2"
      style="border-radius: 12px; font-size: 14px;"
      @click="enviarDados" 
      :disabled="!tipoAnimal || !situacao"
    >
      ➔ Próximo — localização
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  categoria: String
})
const emit = defineEmits(['proximo'])

const tipoAnimal = ref('')
const situacao = ref('')
const descricao = ref('')

const tipos = ['Ave', 'Réptil', 'Mamífero', 'Anfíbio', 'Artrópode', 'Outro']
const situacoes = ['Ferido', 'Morto', 'Área de risco', 'Peçonhento', 'Ninho', 'Outro']

const enviarDados = () => {
  if (tipoAnimal.value && situacao.value) {
    emit('proximo', {
      tipoAnimal: tipoAnimal.value,
      situacao: situacao.value,
      descricao: descricao.value
    })
  }
}
</script>

<style scoped>
/* Pequeno ajuste para garantir que as fontes pequenas fiquem idênticas ao seu design */
.fs-7 {
  font-size: 13px;
}
/* Transição suave ao alternar as cores dos botões */
.transition-all {
  transition: all 0.2s ease-in-out;
}
/* Cor do foco verde no textarea para combinar com a identidade do app */
.form-control:focus {
  border-color: #198754;
  box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}
</style>