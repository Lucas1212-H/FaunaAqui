<template>
  <div class="data-card p-4 h-100 text-center d-flex flex-column shadow-sm">
    <h4 class="mb-4 fw-bold">Análise de Campo</h4>

    <div v-if="carregando" class="py-5">
      <div class="spinner-border text-white" role="status"></div>
      <p class="small text-uppercase mt-3 mb-0">Carregando gráfico...</p>
    </div>

    <div v-else class="d-flex flex-column align-items-center">
      <div class="pie-chart-sim mx-auto mb-3" :style="donutStyle"></div>
      <h3 class="fw-bold mb-1">{{ totalRegistros }}</h3>
      <p class="small text-uppercase mb-4 text-muted">Registros analisados</p>

      <div class="status-legend d-flex justify-content-center text-start small mb-4 flex-wrap gap-3">
        <span v-for="item in categorias" :key="item.key">
          <i class="fas fa-circle me-1" :style="{ color: item.color }"></i>
          {{ item.label }}: {{ contagem[item.key] }}
        </span>
      </div>
    </div>
    
    <RouterLink class="btn btn-purple w-100 py-3 fw-bold text-white mt-auto" to="/catalogo">
      Base de Dados Taxonômica
    </RouterLink>
  </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'

defineEmits(['gerenciarEspecies'])

const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'
const API_BASE_URL = isLocal ? 'http://localhost:8000' : 'https://conviva-labev.onrender.com'

const carregando = ref(true)
const dadosAnalise = ref([])

const categorias = [
  { key: 'Morto', label: 'Mortos', color: '#e74c3c' },
  { key: 'Ferido', label: 'Feridos', color: '#3498db' },
  { key: 'Avistado', label: 'Avistados', color: '#f1c40f' },
]

const contagem = computed(() => {
  return categorias.reduce((acc, categoria) => {
    acc[categoria.key] = dadosAnalise.value.filter((item) => item.situacao === categoria.key).length
    return acc
  }, {})
})

const totalRegistros = computed(() => dadosAnalise.value.length)

const donutStyle = computed(() => {
  const total = totalRegistros.value
  if (!total) {
    return { background: 'conic-gradient(#dfe6e9 0% 100%)' }
  }

  let acumulado = 0
  const partes = categorias.map((categoria) => {
    const valor = dadosAnalise.value.filter((item) => item.situacao === categoria.key).length
    const inicio = acumulado
    acumulado += (valor / total) * 100
    return `${categoria.color} ${inicio}% ${acumulado}%`
  })

  return {
    background: `conic-gradient(${partes.join(', ')})`
  }
})

const carregarDados = async () => {
  try {
    carregando.value = true
    const response = await axios.get(`${API_BASE_URL}/api/analise/ocorrencias`)
    dadosAnalise.value = Array.isArray(response.data) ? response.data : []
  } catch (error) {
    console.error('Erro ao carregar análise do painel:', error)
    dadosAnalise.value = []
  } finally {
    carregando.value = false
  }
}

onMounted(() => {
  carregarDados()
})

</script>

<style scoped>
.data-card {
  background: #58d68d;
  border-radius: 25px;
  color: #1e293b;
}
.pie-chart-sim {
  width: 180px;
  height: 180px;
  border-radius: 50%;
  border: 10px solid white;
}
.btn-purple { background-color: #a569bd; border: none; border-radius: 15px; }
</style>