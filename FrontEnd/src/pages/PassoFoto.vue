<template>
  <div class="card border-0 shadow-sm p-4 mx-auto" style="max-width: 360px; border-radius: 24px;">
    
    <div class="d-flex gap-1 mb-3">
      <div class="flex-grow-1 bg-success" style="height: 4px; border-radius: 2px;"></div>
      <div class="flex-grow-1 bg-success" style="height: 4px; border-radius: 2px;"></div>
      <div class="flex-grow-1 bg-success" style="height: 4px; border-radius: 2px;"></div>
    </div>

    <h1 class="h4 fw-bold text-dark mb-1">Evidência visual</h1>
    <p class="small text-muted mb-4">Passo 4 de 4 — tire ou anexe uma foto</p>

    <div class="mb-4">
      <div 
        class="border border-dashed border-secondary border-opacity-50 rounded-4 d-flex flex-column align-items-center justify-content-center position-relative overflow-hidden bg-light"
        style="height: 200px; border-style: dashed !important; cursor: pointer;"
        @click="dispararUpload"
      >
        <div v-if="!imagemPreview" class="text-center p-3">
          <div class="display-5 text-muted mb-2">📷</div>
          <p class="fw-medium text-dark m-0 small">Tirar foto ou anexar</p>
          <p class="text-muted m-0 extra-small" style="font-size: 11px;">PNG, JPG de até 5MB</p>
        </div>

        <template v-else>
          <img 
            :src="imagemPreview" 
            alt="Preview do animal" 
            class="w-100 h-100 object-fit-cover"
          />
          <button 
            type="button" 
            class="btn btn-danger btn-sm position-absolute top-0 end-0 mt-2 me-2 rounded-circle d-flex align-items-center justify-content-center shadow"
            style="width: 28px; height: 28px;"
            @click.stop="removerFoto"
          >
            ✕
          </button>
        </template>

        <input 
          type="file" 
          ref="fileInput" 
          accept="image/*" 
          capture="environment"
          class="d-none" 
          @change="lidarComFoto"
        />
      </div>
    </div>

    <div class="alert alert-warning border-0 rounded-3 d-flex gap-2 p-2.5 mb-4" style="background-color: #fff9db;">
      <span class="small">⚠️</span>
      <p class="m-0 text-dark-emphasis fw-medium" style="font-size: 11px; line-height: 1.4;">
        <strong>Mantenha distância segura!</strong> Não se aproxime do animal apenas para registrar a foto.
      </p>
    </div>

    <button 
      class="btn btn-success w-100 py-3 fw-bold border-0 d-flex justify-content-center align-items-center gap-2 shadow-sm text-white"
      style="border-radius: 12px; font-size: 14px;"
      @click="enviarDados"
    >
      {{ imagemArquivo ? 'Enviar Ocorrência ✔' : 'Pular e Enviar ➔' }}
    </button>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const emit = defineEmits(['proximo'])

const fileInput = ref(null)
const imagemPreview = ref(null)
const imagemArquivo = ref(null) // Aqui fica o arquivo binário (Blob/File) pronto para o backend

// Abre o seletor do sistema operacional/câmera ao clicar na área tracejada
const dispararUpload = () => {
  fileInput.value.click()
}

// Processa a imagem escolhida ou tirada pela câmera
const lidarComFoto = (event) => {
  const arquivo = event.target.files[0]
  if (arquivo) {
    imagemArquivo.value = arquivo
    
    // Cria uma URL temporária para exibir a miniatura na tela
    imagemPreview.value = URL.createObjectURL(arquivo)
  }
}

// Limpa os estados caso o usuário desista da foto
const removerFoto = () => {
  imagemPreview.value = null
  imagemArquivo.value = null
  if (fileInput.value) {
    fileInput.value.value = '' // limpa o input de arquivo
  }
}

// Envia os dados para o formulário final
const enviarDados = () => {
  emit('proximo', {
    foto: imagemArquivo.value // Passa o arquivo bruto para o componente pai salvar
  })
}
</script>

<style scoped>
/* Garante que a imagem cortada preencha o espaço sem distorcer */
.object-fit-cover {
  object-fit: cover;
}

/* Estilização suave para o alerta */
.p-2\.5 {
  padding: 10px 12px;
}
</style>