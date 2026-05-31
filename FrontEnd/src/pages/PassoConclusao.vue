<template>
  <div class="conclusao-container text-center">
    
    <div class="conclusao-icon mb-4">
      <div class="icon-check">✓</div>
    </div>

    <h1 class="h3 fw-bold text-dark mb-3">Obrigado!</h1>
    
    <p class="text-muted mb-4 lead">
      Sua ocorrência foi registrada com sucesso e ajudará no cuidado da fauna local.
    </p>

    <div class="info-box bg-light border rounded-3 p-4 mb-4">
      <h2 class="h5 fw-semibold text-dark mb-3"> Acompanhe nas redes sociais</h2>
      <p class="small text-muted mb-3">
        Fique por dentro das ações do Programa ConViva! Siga nossas redes sociais para receber atualizações sobre o trabalho de resgate e conservação.
      </p>
      
      <div class="social-links d-flex justify-content-center gap-2 flex-wrap">
        <a href="https://www.instagram.com/conviva.labev/" target="_blank" rel="noopener noreferrer" class="btn btn-sm btn-outline-primary rounded-3 px-3">
          📷 Instagram
        </a>
      </div>
    </div>

    <div class="actions d-flex flex-column gap-2">
      <button 
        class="btn btn-success fw-bold py-3 rounded-3 w-100"
        @click="enviarNovaOcorrencia"
      >
        ➔ Enviar uma nova ocorrência
      </button>
      
      <button 
        class="btn btn-outline-secondary fw-bold py-3 rounded-3 w-100"
        @click="voltarParaInicio"
      >
        ← Voltar à tela inicial
      </button>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'

const router = useRouter()
const emit = defineEmits(['novaOcorrencia', 'voltar'])

const enviarNovaOcorrencia = () => {
  emit('novaOcorrencia')
}

const voltarParaInicio = () => {
  router.push('/')
}
</script>

<style scoped>
.conclusao-container {
  max-width: 380px;
  margin: 0 auto;
  animation: slideIn 0.4s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateY(8px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.icon-check {
  width: 80px;
  height: 80px;
  margin: 0 auto;
  background: linear-gradient(135deg, #2ecc71, #27ae60);
  border-radius: 999px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 2.5rem;
  color: white;
  font-weight: 700;
  box-shadow: 0 8px 20px rgba(46, 204, 113, 0.2);
}

.info-box {
  border-color: #e8f5e9 !important;
  background: #fafbfa !important;
}

.social-links a {
  transition: all 0.2s ease;
  font-size: 0.85rem;
}

.social-links a:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.actions {
  margin-top: 1.5rem;
}

.btn {
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
}
</style>
