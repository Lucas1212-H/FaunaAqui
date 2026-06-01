<template>
  <div class="card border-0 shadow-sm p-3 p-sm-4 mx-auto passo-contato-card">

    <h1 class="h5 fw-bold text-dark mb-1">Seus dados</h1>
    <p class="small text-muted mb-3">Precisamos de um contato para retorno</p>

    <div class="mb-2">
      <label class="form-label small text-uppercase text-muted fw-bold">Nome</label>
      <input
        v-model.trim="nome"
        class="form-control shadow-none rounded-3"
        placeholder="Nome completo"
        autocomplete="name"
      />
    </div>

    <div class="mb-2">
      <div class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.7rem; letter-spacing: 0.8px;">Contato</div>
      <div class="d-flex gap-2 mb-2">
        <button type="button" @click="contatoTipo = 'telefone'" :class="contatoTipo === 'telefone' ? 'btn btn-success' : 'btn btn-outline-secondary'" class="btn btn-sm">Telefone</button>
        <button type="button" @click="contatoTipo = 'email'" :class="contatoTipo === 'email' ? 'btn btn-success' : 'btn btn-outline-secondary'" class="btn btn-sm">Email</button>
      </div>
      <input
        :value="contatoValor"
        @input="onContatoInput"
        :type="contatoTipo === 'email' ? 'email' : 'tel'"
        :inputmode="contatoTipo === 'email' ? 'email' : 'numeric'"
        :maxlength="contatoTipo === 'email' ? 120 : 11"
        :placeholder="contatoTipo === 'email' ? 'seu@email.com' : '99 99999-9999'"
        :autocomplete="contatoTipo === 'email' ? 'email' : 'tel'"
        class="form-control shadow-none rounded-3"
      />
      <div class="form-text small text-muted mt-1">
        {{ contatoTipo === 'email' ? 'Use um e-mail válido.' : 'Digite somente números, sem espaços, parênteses ou traços.' }}
      </div>
    </div>

    <button 
      class="btn btn-light w-100 py-3 fw-bold border d-flex justify-content-center align-items-center gap-2 mt-2" 
      style="border-radius: 12px; font-size: 14px;"
      @click="enviar" 
      :disabled="!podeAvancar"
    >
      ➔ Próximo — detalhes do animal
    </button>

  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
const emit = defineEmits(['proximo'])

const nome = ref('')
const contatoTipo = ref('telefone')
const contatoValor = ref('')

const contatoSanitizado = computed(() => {
  if (contatoTipo.value === 'telefone') {
    return contatoValor.value.replace(/\D/g, '').slice(0, 11)
  }

  return contatoValor.value.trim().toLowerCase()
})

const nomeValido = computed(() => nome.value.trim().length > 0)

const contatoValido = computed(() => {
  const valor = contatoSanitizado.value

  if (contatoTipo.value === 'telefone') {
    return /^\d{10,11}$/.test(valor)
  }

  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(valor)
})

const podeAvancar = computed(() => nomeValido.value && contatoValido.value)

const onContatoInput = (event) => {
  const valorDigitado = event.target.value

  if (contatoTipo.value === 'telefone') {
    contatoValor.value = valorDigitado.replace(/\D/g, '').slice(0, 11)
    return
  }

  contatoValor.value = valorDigitado.slice(0, 120)
}

const enviar = () => {
  if (!podeAvancar.value) return

  emit('proximo', {
    nome: nome.value.trim(),
    contato: { tipo: contatoTipo.value, valor: contatoSanitizado.value }
  })
}
</script>

<style scoped>
.btn-sm { padding: 0.35rem 0.6rem; }
.form-control:focus { border-color: #198754; box-shadow: 0 0 0 0.15rem rgba(25,135,84,0.12); }

.passo-contato-card {
  max-width: 360px;
  border-radius: 20px;
}

@media (max-width: 767.98px) {
  .passo-contato-card {
    max-width: 100%;
    padding: 1rem !important;
  }
}
</style>