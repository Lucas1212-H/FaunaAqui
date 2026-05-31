<template>
  <div class="min-vh-100 bg-light d-flex flex-column">
    <NavBarPublic />

    <div class="container-fluid flex-grow-1 d-flex align-items-start justify-content-center py-2 py-md-3">
      <div class="row justify-content-center align-items-start w-100 m-0">
        <div class="col-12 p-0 d-flex justify-content-center">

          <div class="card border-0 shadow rounded-4 overflow-hidden mix-container">
            <div class="row g-0 h-100 align-items-stretch painel-com-fina-divisao">

              <PainelStatusDenuncia v-if="!denunciaEnviada" :passo-atual="passoAtual" />

              <div class="col-12 col-md-8 bg-white p-3 p-sm-4 d-flex flex-column justify-content-start corpo-formulario">

                <!-- Alerta de Erro -->
                <UiMessage
                  v-if="mensagemErro && !denunciaEnviada"
                  type="error"
                  title="Não foi possível enviar"
                  :message="mensagemErro"
                  @dismiss="mensagemErro = ''"
                />

                <UiMessage
                  v-if="mensagemSucesso && !denunciaEnviada"
                  type="success"
                  title="Envio concluído"
                  :message="mensagemSucesso"
                  @dismiss="mensagemSucesso = ''"
                />

                <!-- Spinner de Carregamento -->
                <div v-if="enviando" class="text-center py-5">
                  <div class="spinner-border text-success" role="status">
                    <span class="visually-hidden">Enviando...</span>
                  </div>
                  <p class="mt-3 text-muted">Enviando denuncia...</p>
                </div>

                <!-- Tela de Conclusão -->
                <div v-else-if="denunciaEnviada" class="w-100 container-passos mx-auto">
                  <PassoConclusao 
                    @novaOcorrencia="iniciarNovaOcorrencia"
                    @voltar="voltarParaInicio"
                  />
                </div>

                <!-- Formulário -->
                <div v-else class="w-100 container-passos mx-auto">
                  <PassoContato v-if="passoAtual === 1" @proximo="avancarPasso1" />

                  <PassoInicial v-else-if="passoAtual === 2" @proximo="avancarPasso2" />

                  <PassoDetalhes 
                    v-else-if="passoAtual === 3" 
                    :categoria="formData.categoria"
                    @proximo="avancarPasso3" 
                  />

                  <PassoLocalizacao 
                    v-else-if="passoAtual === 4" 
                    @proximo="avancarPasso4" 
                  />

                  <PassoFoto 
                    v-else-if="passoAtual === 5" 
                    @proximo="finalizarFormulario" 
                  />
                </div>

              </div>

            </div>
          </div>

        </div>
      </div>
        </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import PassoInicial from '../pages/PassoInicial.vue'
import PassoContato from '../pages/PassoContato.vue'
import PassoDetalhes from '../pages/PassoDetalhes.vue'
import PassoLocalizacao from '../pages/PassoLocalização.vue'
import PassoFoto from '../pages/PassoFoto.vue'
import PassoConclusao from '../pages/PassoConclusao.vue'
import PainelStatusDenuncia from '../components/denuncia/PainelStatusDenuncia.vue'
import NavBarPublic from '../components/NavBarPublic.vue'
import UiMessage from '../components/UiMessage.vue'
import { ocorrenciaService } from '../services/ocorrenciaService'
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';

const passoAtual = ref(1)
const enviando = ref(false)
const mensagemErro = ref('')
const mensagemSucesso = ref('')
const denunciaEnviada = ref(false)

const formData = reactive({
  categoria: '',
  contatoNome: '',
  contato: { tipo: '', valor: '' },
  tipoAnimal: '',
  situacao: '',
  descricao: '',
  localizacao: null,
  referencia: '',
  foto: null
})

const avancarPasso1 = (cont) => {
  formData.contatoNome = cont.nome || ''
  formData.contato = cont.contato || { tipo: '', valor: '' }
  passoAtual.value = 2
}
const avancarPasso2 = (cat) => { formData.categoria = cat; passoAtual.value = 3; }
const avancarPasso3 = (det) => { 
  formData.tipoAnimal = det.tipoAnimal; 
  formData.situacao = det.situacao; 
  formData.descricao = det.descricao; 
  passoAtual.value = 4; 
}
const avancarPasso4 = (loc) => { 
  formData.localizacao = loc.localizacao; 
  formData.referencia = loc.referencia; 
  passoAtual.value = 5; 
}

const finalizarFormulario = async (dadosFoto) => {
  formData.foto = dadosFoto.foto
  
  // Validação
  if (!formData.foto) {
    mensagemErro.value = 'Foto é obrigatória'
    return
  }

  if (!formData.localizacao) {
    mensagemErro.value = 'Localização é obrigatória'
    return
  }

  try {
    enviando.value = true
    mensagemErro.value = ''

    // Prepara os dados conforme o backend espera
    const dadosParaEnviar = {
      denunciante_nome: formData.contatoNome,
      denunciante_contato_tipo: formData.contato.tipo,
      denunciante_contato_valor: formData.contato.valor,
      distincao_biologica: formData.categoria,
      tipo_animal: formData.tipoAnimal,
      situacao_animal: formData.situacao,
      descricao: formData.descricao,
      latitude: formData.localizacao.lat,
      longitude: formData.localizacao.lng,
      ponto_referencia: formData.referencia || 'Não informado',
      foto: formData.foto
    }

    // Envia para o backend
    const resposta = await ocorrenciaService.criarDenuncia(dadosParaEnviar)
    
    // Sucesso
    alert('Ocorrência enviada com sucesso!')
    // Sucesso - mostra tela de conclusão
    mensagemErro.value = ''
    denunciaEnviada.value = true
    
    // Reseta o formulário internal
    passoAtual.value = 1
    formData.categoria = ''
    formData.contatoNome = ''
    formData.contato = { tipo: '', valor: '' }
    formData.tipoAnimal = ''
    formData.situacao = ''
    formData.descricao = ''
    formData.localizacao = null
    formData.referencia = ''
    formData.foto = null

  } catch (error) {
    console.error('Erro ao enviar denuncia:', error)
    mensagemErro.value = error instanceof Error ? error.message : 'Erro ao enviar denuncia'
  } finally {
    enviando.value = false
  }
}

const iniciarNovaOcorrencia = () => {
  // Reinicia o fluxo
  denunciaEnviada.value = false
  passoAtual.value = 1
  mensagemSucesso.value = ''
  mensagemErro = ''
}

const voltarParaInicio = () => {
  // Já será tratado pelo PassoConclusao via router
}
</script>

<style scoped>
.rounded-4 { border-radius: 1.5rem !important; }

/* ==========================================================================
   ESTRUTURA COMPACTA E ENRIJECIDA (PC)
   ========================================================================== */
@media (min-width: 768px) {
  .mix-container {
    width: min(820px, calc(100vw - 36px)) !important;
    max-width: min(820px, calc(100vw - 36px)) !important;
    min-height: 500px !important;
  }

  .corpo-formulario {
    height: 100% !important;
    overflow: hidden !important;
    padding: 0.85rem 1rem !important;
  }

  .container-passos {
    max-width: 360px;
  }

  .painel-com-fina-divisao {
    border: 1px solid rgba(255, 255, 255, 0.08);
  }

  .corpo-formulario {
    border-left: 1px solid rgba(15, 23, 42, 0.05);
  }
}

/* ==========================================================================
   COMPORTAMENTO MOBILE (CELULAR)
   ========================================================================== */
@media (max-width: 767.98px) {
  .mix-container {
    max-width: calc(100vw - 24px);
    margin: 0 auto;
  }

  .container-passos {
    max-width: 100%;
  }
}

@media (min-width: 768px) {
  :deep(.painel-status-desktop) {
    padding: 1rem !important;
  }
}
</style>