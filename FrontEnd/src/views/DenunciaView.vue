<template>
  <div class="min-vh-100 bg-light d-flex align-items-stretch align-items-md-center justify-content-center p-0 p-md-4">
    
    <div class="container-fluid bg-white p-0 shadow-lg" style="max-width: 960px; border-radius: 0;">
      <div class="row g-0 min-vh-100 min-vh-md-0" style="min-height: 550px;">
        
        <div class="col-md-5 bg-success text-white p-4 d-none d-md-flex flex-column justify-content-between p-lg-5" style="border-top-left-radius: 16px; border-bottom-left-radius: 16px;">
          <div>
            <div class="d-flex align-items-center gap-2 mb-4">
              <span class="fs-3">🌱</span>
              <h2 class="h5 m-0 fw-bold tracking-wide text-uppercase">Fauna Aqui</h2>
            </div>
            <p class="small text-white-50">Sistema de Monitoramento e Registro de Ocorrências Animais no Campus.</p>
          </div>

          <div class="my-4">
            <div :class="['d-flex align-items-center gap-3 mb-4 transition-all', passoAtual === 1 ? 'opacity-100 fw-bold' : 'opacity-50']">
              <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">1</div>
              <div>Identificação <small class="d-block text-white-50 fw-normal">O que você observou?</small></div>
            </div>
            
            <div :class="['d-flex align-items-center gap-3 mb-4 transition-all', passoAtual === 2 ? 'opacity-100 fw-bold' : 'opacity-50']">
              <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">2</div>
              <div>Detalhes <small class="d-block text-white-50 fw-normal">Tipo e situação</small></div>
            </div>
            
            <div :class="['d-flex align-items-center gap-3 mb-4 transition-all', passoAtual === 3 ? 'opacity-100 fw-bold' : 'opacity-50']">
              <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">3</div>
              <div>Localização <small class="d-block text-white-50 fw-normal">Onde aconteceu?</small></div>
            </div>

            <div :class="['d-flex align-items-center gap-3 transition-all', passoAtual === 4 ? 'opacity-100 fw-bold' : 'opacity-50']">
              <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">4</div>
              <div>Foto <small class="d-block text-white-50 fw-normal">Evidência visual</small></div>
            </div>
          </div>

          <div class="small text-white-50">
            © 2026 UFPA — Segurança e Meio Ambiente
          </div>
        </div>

        <div class="col-12 col-md-7 d-flex align-items-center justify-content-center p-3 p-sm-4 p-lg-5 bg-white" style="border-top-right-radius: 16px; border-bottom-right-radius: 16px;">
          <div class="w-100 wrapper-conteudo-filho">
            <PassoInicial 
              v-if="passoAtual === 1" 
              @proximo="avancarPasso1" 
            />
            
            <PassoDetalhes 
              v-else-if="passoAtual === 2" 
              :categoria="formData.categoria"
              @proximo="avancarPasso2" 
            />
            
            <PassoLocalizacao 
              v-else-if="passoAtual === 3" 
              @proximo="avancarPasso3" 
            />

            <PassoFoto 
              v-else-if="passoAtual === 4" 
              @proximo="finalizarFormulario" 
            />

          </div>
        </div>

      </div>
    </div>

    <div v-if="avisoEnvio" class="success-overlay d-flex align-items-center justify-content-center px-3">
      <div class="success-card text-center p-4 p-md-5 shadow-lg bg-white">
        <div class="success-badge mx-auto mb-3 d-flex align-items-center justify-content-center">
          ✓
        </div>
        <h3 class="h4 fw-bold text-success mb-2">Ocorrência enviada com sucesso</h3>
        <p class="text-muted mb-0">Seu registro foi concluído. Você será redirecionado para a guia inicial em instantes.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import PassoInicial from '../pages/PassoInicial.vue'
import PassoDetalhes from '../pages/PassoDetalhes.vue'
import PassoLocalizacao from '../pages/PassoLocalização.vue'
import PassoFoto from '../pages/PassoFoto.vue'

const router = useRouter()

// Estado do fluxo
const passoAtual = ref(1)
const avisoEnvio = ref(false)
let timeoutRedirecionamento = null

// Estado centralizado dos dados
const formData = reactive({
  categoria: '',      // Silvestre, Peçonhento, Ninho
  tipoAnimal: '',     // Ave, Réptil, etc.
  situacao: '',       // Ferido, Morto, etc.
  descricao: '',
  localizacao: null,  // Coordenadas { lat, lng }
  referencia: '',
  foto: null
})

// Funções de navegação e salvamento
const avancarPasso1 = (categoriaSelecionada) => {
  formData.categoria = categoriaSelecionada
  passoAtual.value = 2
}

const avancarPasso2 = (dadosDetalhes) => {
  formData.tipoAnimal = dadosDetalhes.tipoAnimal
  formData.situacao = dadosDetalhes.situacao
  formData.descricao = dadosDetalhes.descricao
  passoAtual.value = 3
}

const avancarPasso3 = (dadosLocalizacao) => {
  formData.localizacao = dadosLocalizacao.localizacao
  formData.referencia = dadosLocalizacao.referencia
  passoAtual.value = 4 // Avança para o passo da foto
}

const finalizarFormulario = (dadosLocalizacao) => {
  formData.localizacao = dadosLocalizacao.localizacao
  formData.referencia = dadosLocalizacao.referencia
  formData.foto = dadosLocalizacao.foto
  
  console.log('Formulário Pronto para Envio ao Backend:', formData)
  avisoEnvio.value = true

  timeoutRedirecionamento = window.setTimeout(() => {
    router.push('/')
  }, 2200)
}

onBeforeUnmount(() => {
  if (timeoutRedirecionamento) {
    window.clearTimeout(timeoutRedirecionamento)
  }
})
</script>

<style scoped>
/* Transição suave para opacidade dos passos laterais */
.transition-all {
  transition: all 0.3s ease;
}

.success-overlay {
  position: fixed;
  inset: 0;
  z-index: 2000;
  background: rgba(10, 28, 18, 0.45);
  backdrop-filter: blur(8px);
}

.success-card {
  width: min(92vw, 460px);
  border-radius: 24px;
  border: 1px solid rgba(25, 135, 84, 0.12);
}

.success-badge {
  width: 72px;
  height: 72px;
  border-radius: 999px;
  background: linear-gradient(135deg, #198754, #34c759);
  color: #fff;
  font-size: 2rem;
  box-shadow: 0 18px 40px rgba(25, 135, 84, 0.25);
}

/* Regras específicas para Notebooks (telas a partir de 768px) */
@media (min-width: 768px) {
  .container-fluid {
    border-radius: 16px !important; /* Aplica borda arredondada no container inteiro */
  }
}

/* Ajuste crucial: Como os filhos tinham "max-width: 360px" fixo na tag style deles, 
  forçamos para que dentro do painel de notebook eles usem 100% do espaço disponível 
  (até um limite elegante de 450px) para não parecer um app de celular minúsculo na tela do PC.
*/
:deep(.card) {
  max-width: 450px !important;
  box-shadow: none !important; /* Remove a sombra do filho já que o pai já tem sombra */
  padding: 0 !important; /* Deixa o padding por conta do grid pai */
}
</style>