<template>
  <div>
    <div class="d-flex gap-1 mb-3" style="height: 4px;">
      <div class="flex-grow-1 bg-success rounded"></div>
      <div class="flex-grow-1 bg-success rounded"></div>
      <div class="flex-grow-1 bg-success rounded"></div>
    </div>

    <h2 class="h4 fw-bold mb-1 text-dark">Evidência visual</h2>
    <p class="small text-muted mb-4">Passo 3 de 3 — adicione uma foto</p>

    <div 
      class="border border-2 border-dashed rounded-4 bg-light d-flex align-items-center justify-content-center mb-4 position-relative overflow-hidden"
      style="height: 220px; border-style: dashed !important;"
    >
      <div v-if="!imagemPreview" class="text-center text-secondary">
        <div class="display-6 mb-2">📸</div>
        <p class="small m-0">Nenhuma foto selecionada</p>
      </div>
      
      <div v-else class="w-100 h-100">
        <img :src="imagemPreview" class="w-100 h-100 object-fit-cover" />
        <button 
          class="btn btn-dark btn-sm position-absolute top-0 end-0 m-2 rounded-pill opacity-75"
          @click="removerFoto"
        >
          ✕ Remover
        </button>
      </div>
    </div>

    <input type="file" accept="image/*" capture="environment" ref="inputCamera" class="d-none" @change="manipularArquivo" />
    <input type="file" accept="image/*" ref="inputGaleria" class="d-none" @change="manipularArquivo" />

    <div class="d-grid gap-2 mb-4" v-if="!imagemPreview">
      <button class="btn btn-outline-success py-3 fw-bold d-flex align-items-center justify-content-center gap-2" @click="abrirCamera">
        <span>📷</span> Tirar foto agora
      </button>
      
      <button class="btn btn-outline-secondary py-3 fw-bold d-flex align-items-center justify-content-center gap-2" @click="abrirGaleria">
        <span>📁</span> Escolher da galeria
      </button>
    </div>

    <button 
      class="btn btn-success btn-lg w-100 py-3 rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2"
      :class="{ 'disabled': !fotoArquivo }"
      @click="enviar"
    >
      Enviar Ocorrência <span class="fs-5">➔</span>
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['proximo'])

const inputCamera = ref(null)
const inputGaleria = ref(null)
const fotoArquivo = ref(null)
const imagemPreview = ref(null)

const abrirCamera = () => inputCamera.value.click()
const abrirGaleria = () => inputGaleria.value.click()

const manipularArquivo = (event) => {
  const arquivo = event.target.files[0]
  if (arquivo) {
    fotoArquivo.value = arquivo
    imagemPreview.value = URL.createObjectURL(arquivo)
  }
}

const removerFoto = () => {
  fotoArquivo.value = null
  imagemPreview.value = null
}

const enviar = () => {
  if (fotoArquivo.value) {
    emit('proximo', { foto: fotoArquivo.value })
  }
}
</script>

<style scoped>
/* Classe específica para garantir o comportamento de "cobrir" a div com a imagem no Bootstrap */
.object-fit-cover {
  object-fit: cover;
}

/* Estilo para borda tracejada que não existe por padrão no Bootstrap */
.border-dashed {
  border-style: dashed !important;
}

/* Efeito de hover nos botões */
.btn {
  transition: transform 0.1s ease;
}
.btn:active {
  transform: scale(0.98);
}
</style>