<template>
  <div class="col-md-5 col-lg-4 bg-success text-white p-4 d-none d-md-flex flex-column justify-content-between painel-lateral">
    <div>
      <div class="d-flex align-items-center gap-1 mb-3">
        <img src="/home/lucas/Documentos/MeusProjetos/Outros Proj./FaunaAqui/FaunaAqui/FrontEnd/src/assets/images/conviva.png" alt="ConViva" height="40px" width="auto">
        <h2 class="h2 m-0 fw-bold tracking-wider ">ConViva</h2>
      </div>
      <p class="small text-white-50 lh-sm">Sistema de Monitoramento e Registro de Ocorrências Animais no Campus.</p>
    </div>

    <div class="my-auto py-3">
      <div :class="['d-flex align-items-center gap-3 mb-3 transition-all', passoAtual === 1 ? 'opacity-100 fw-bold' : 'opacity-50']">
        <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold step-number" style="width: 32px; height: 32px;">1</div>
        <div class="small lh-1">Contato <small class="d-block text-white-50 fw-normal mt-1">Nome e telefone</small></div>
      </div>

      <div :class="['d-flex align-items-center gap-3 mb-3 transition-all', passoAtual === 2 ? 'opacity-100 fw-bold' : 'opacity-50']">
        <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold step-number" style="width: 32px; height: 32px;">2</div>
        <div class="small lh-1">Identificação <small class="d-block text-white-50 fw-normal mt-1">O que você observou?</small></div>
      </div>

      <div :class="['d-flex align-items-center gap-3 mb-3 transition-all', passoAtual === 3 ? 'opacity-100 fw-bold' : 'opacity-50']">
        <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold step-number" style="width: 32px; height: 32px;">3</div>
        <div class="small lh-1">Detalhes <small class="d-block text-white-50 fw-normal mt-1">Tipo e situação</small></div>
      </div>

      <div :class="['d-flex align-items-center gap-3 mb-3 transition-all', passoAtual === 4 ? 'opacity-100 fw-bold' : 'opacity-50']">
        <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold step-number" style="width: 32px; height: 32px;">4</div>
        <div class="small lh-1">Localização <small class="d-block text-white-50 fw-normal mt-1">Onde aconteceu?</small></div>
      </div>

      <div :class="['d-flex align-items-center gap-3 transition-all', passoAtual === 5 ? 'opacity-100 fw-bold' : 'opacity-50']">
        <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center fw-bold step-number" style="width: 32px; height: 32px;">5</div>
        <div class="small lh-1">Foto <small class="d-block text-white-50 fw-normal mt-1">Evidência visual</small></div>
      </div>
    </div>

    <div class="small text-white-50 border-top border-white-10 pt-3">
      © 2026 UFPA — Segurança Ambiental
    </div>
  </div>
</template>

<script setup>
defineProps({
  passoAtual: {
    type: Number,
    required: true
  }
})
</script>
