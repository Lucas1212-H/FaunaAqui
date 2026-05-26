<template>
    <div class="catalogo-page min-vh-100 bg-light">
        <NavBar />

        <header class="container py-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <p class="text-uppercase text-muted small fw-semibold mb-1">Catálogo de espécies</p>
                <h1 class="h3 fw-bold mb-0">
                    {{ tipoSelecionado ? `${tipoSelecionado}` : 'Todas as espécies publicadas' }}
                </h1>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-outline-secondary" type="button" @click="limparFiltro" :disabled="!tipoSelecionado">
                    Voltar
                </button>
                <button class="btn btn-primary" type="button">
                    Adicionar Espécie
                </button>
            </div>
        </header>

        <main class="container pb-5">
                <section v-if="!tipoSelecionado" class="row g-3">
                    <div v-for="tipo in tiposDisponiveis" :key="tipo.nome" class="col-12 col-sm-6 col-lg-4 col-xxl-3">
                        <article
                            class="card h-100 shadow-sm border-0 especie-card"
                            role="button"
                            tabindex="0"
                            @click="selecionarTipo(tipo.nome)"
                            @keydown.enter.prevent="selecionarTipo(tipo.nome)"
                            @keydown.space.prevent="selecionarTipo(tipo.nome)"
                        >
                            <div class="card-body p-4 text-center">
                                <div class="rounded-circle bg-success-subtle text-success d-inline-flex align-items-center justify-content-center especie-icon mb-3">
                                    <span class="fw-bold">{{ tipo.nome.charAt(0) }}</span>
                                </div>
                                <h2 class="h5 fw-bold mb-1">{{ tipo.nome }}</h2>
                                <p class="text-muted mb-0">{{ tipo.quantidade }} ocorrência(s) publicada(s)</p>
                            </div>
                        </article>
                    </div>

                    <div v-if="tiposDisponiveis.length === 0" class="col-12">
                        <div class="alert alert-info text-center mb-0">
                            Nenhuma ocorrência publicada disponível para catálogo.
                        </div>
                    </div>
                </section>

                <section v-else>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="text-muted mb-0">
                            Mostrando {{ animaisFiltrados.length }} ocorrência(s) para {{ tipoSelecionado }}
                        </p>
                    </div>

                    <div class="row g-3">
                        <div v-for="animal in animaisFiltrados" :key="animal.id" class="col-12 col-md-6 col-xl-4">
                            <article class="card h-100 shadow-sm border-0" role="button" tabindex="0"
                                @click="abrirDetalhe(animal.id)"
                                @keydown.enter.prevent="abrirDetalhe(animal.id)"
                            >
                                <img
                                    class="card-img-top catalogo-image"
                                    :src="animal.imagem"
                                    :alt="animal.tipoAnimal"
                                >
                                <div class="card-body">
                                    <div class="d-flex justify-content-between gap-2 mb-2">
                                        <span class="badge text-bg-success">{{ animal.tipoAnimal }}</span>
                                        <span class="badge text-bg-light text-dark">{{ animal.status }}</span>
                                    </div>
                                    <h2 class="h5 fw-bold mb-1">{{ animal.titulo }}</h2>
                                    <small class="text-muted d-block mb-2">{{ animal.nomeCientifico }}</small>
                                    <p class="mb-0 text-secondary">{{ animal.descricao }}</p>
                                </div>
                            </article>
                        </div>

                        <div v-if="animaisFiltrados.length === 0" class="col-12">
                            <div class="alert alert-info text-center mb-0">
                                Nenhuma ocorrência cadastrada para {{ tipoSelecionado }} ainda.
                            </div>
                        </div>
                    </div>
                </section>
        </main>
    </div>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import axios from 'axios'
import { useRouter } from 'vue-router'
import NavBar from '@/components/NavBar.vue'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.bundle.min.js'

const carregando = ref(true)
const tipoSelecionado = ref('')
const ocorrencias = ref([])

const API_BASE = 'http://localhost:8000/api/ocorrencias'

const normalizarTipo = (valor) => {
    return (valor ?? '').toString().trim()
}

const tipoParaTitulo = (valor) => {
    const tipo = normalizarTipo(valor)
    return tipo ? `${tipo.charAt(0).toUpperCase()}${tipo.slice(1)}` : 'Sem tipo'
}

const tiposDisponiveis = computed(() => {
    const acumulado = new Map()

    ocorrencias.value.forEach((item) => {
        const tipo = normalizarTipo(item.tipoAnimal)

        if (!tipo) {
            return
        }

        const atual = acumulado.get(tipo) ?? { nome: tipoParaTitulo(tipo), quantidade: 0 }
        acumulado.set(tipo, {
            nome: atual.nome,
            quantidade: atual.quantidade + 1,
        })
    })

    return Array.from(acumulado.values()).sort((a, b) => a.nome.localeCompare(b.nome, 'pt-BR'))
})

const animaisFiltrados = computed(() => {
    if (!tipoSelecionado.value) {
        return []
    }

    return ocorrencias.value.filter((animal) => animal.tipoAnimal === tipoSelecionado.value)
})

const carregarOcorrencias = async () => {
    try {
        carregando.value = true
        const response = await axios.get(`${API_BASE}/publicados`)

        ocorrencias.value = response.data.map((item) => ({
            id: item.id,
            tipoAnimal: normalizarTipo(item.tipo_animal),
            titulo: tipoParaTitulo(item.tipo_animal),
            nomeCientifico: item.categoria_ocorrencia || 'Categoria não informada',
            descricao: item.descricao_ocorrencia || item.descricao || 'Sem descrição',
            status: item.status || 'Publicado',
            imagem: item.foto_path
                ? `http://localhost:8000/storage/${item.foto_path}`
                : 'https://cdn-icons-png.flaticon.com/512/616/616408.png',
        }))
    } catch (error) {
        console.error('Erro ao carregar catálogo:', error)
        ocorrencias.value = []
    } finally {
        carregando.value = false
    }
}

const selecionarTipo = (tipo) => {
    tipoSelecionado.value = tipo
}

const limparFiltro = () => {
    tipoSelecionado.value = ''
}

const router = useRouter()

const abrirDetalhe = (id) => {
    if (!id) return
    router.push({ name: 'catalogo-detalhe', params: { id } })
}

onMounted(() => {
    carregarOcorrencias()
})
</script>

<style scoped>
.catalogo-page {
    background:
        radial-gradient(circle at top left, rgba(25, 135, 84, 0.12), transparent 34%),
        linear-gradient(180deg, #f8faf8 0%, #eef3ef 100%);
}

.especie-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.especie-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 1rem 2rem rgba(16, 24, 40, 0.08) !important;
}

.especie-icon {
    width: 64px;
    height: 64px;
    font-size: 1.2rem;
}

.catalogo-image {
    height: 220px;
    object-fit: cover;
}
</style>