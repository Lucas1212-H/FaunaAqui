<template>
  <div class="dashboard-page bg-light min-vh-100 d-flex flex-column">
    <NavBar :abaAtiva="abaAtiva" @mudarAba="mudarAba" />

    <div class="container-fluid px-4 flex-grow-1 py-4">
      
      <CardsMetricas
        :total="totalRegistros"
        :pendentes="pendentes"
        :urgentes="urgentes"
        :especies="especies"
      />

      <div v-if="abaAtiva === 'triagem'" class="row g-4">
        <div class="col-lg-8">
          <div class="data-card p-4 h-100 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
              <h4 class="fw-bold m-0 text-dark">Fila de Triagem</h4>
              <div class="input-group w-auto">
                <span class="input-group-text bg-white border-0 shadow-sm"><i class="fas fa-search text-muted"></i></span>
                <input type="text" class="form-control border-0 shadow-sm" placeholder="Buscar ocorrência...">
              </div>
            </div>
            
            <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Animal</th>
                    <th>Localização</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="d in denunciasTriagem" :key="d.id">
                    <td>
                      <span class="fw-bold">{{ d.animal }}</span>
                      <div v-if="d.assigned"><small class="text-muted d-block">Alocado: {{ d.assigned }}</small></div>
                    </td>
                    <td><small class="text-muted">{{ d.local }}</small></td>
                    <td><small>{{ d.data }}</small></td>
                    <td>
                      <span :class="['badge rounded-pill', getStatusClass(d.status)]">
                        {{ d.status }}
                      </span>
                    </td>
                    <td class="text-center">
                      <button class="btn btn-sm btn-outline-secondary me-2 shadow-sm action-btn" @click="handleValidar(d)">
                        Info
                      </button>
                      <button class="btn btn-sm btn-outline-success shadow-sm action-btn" @click="gerarLaudo(d)">
                        Exportar
                      </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <PainelAnalise @gerenciarEspecies="handleGerenciarEspecies" />
        </div>
      </div>

      <div v-else class="row g-4">
        <div class="col-12">
          <div class="archived-card p-4 shadow-sm">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
              <div>
                <h4 class="fw-bold m-0 text-dark">Denuncias Arquivadas</h4>
                <small class="text-muted">Clique em qualquer linha para ver o histórico completo do animal</small>
              </div>
            </div>

            <div class="table-responsive bg-white rounded-4 shadow-sm p-2">
              <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Animal</th>
                    <th>Denunciante</th>
                    <th>Processo</th>
                    <th>Data</th>
                    <th>Status final</th>
                  </tr>
                </thead>
                <tbody>
                  <tr
                    v-for="item in denunciasArquivadas"
                    :key="item.id"
                    class="clickable-row"
                    @click="abrirHistorico(item)"
                  >
                    <td class="fw-bold">{{ item.animal }}</td>
                    <td>{{ item.denunciante }}</td>
                    <td>
                      <span class="badge rounded-pill bg-secondary-subtle text-secondary-emphasis process-badge">
                        {{ item.processoFinal }}
                      </span>
                    </td>
                    <td>{{ item.data }}</td>
                    <td>
                      <span class="badge rounded-pill bg-dark-subtle text-dark">{{ item.statusFinal }}</span>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <ModalValidacao 
      :denuncia="selectedDenuncia" 
      @fechar="selectedDenuncia = null" 
      @aprovar="processarAprovacao"
      @alocar="handleAlocar"
      @arquivar="handleArquivar"
    />

    <ModalHistoricoAnimal
      :item="historicoSelecionado"
      @fechar="historicoSelecionado = null"
    />
  </div>
</template>

<script setup>
import { computed, ref } from 'vue';
import NavBar from '@/components/NavBar.vue';
import CardsMetricas from '@/components/especialista/CardsMetricas.vue';
import PainelAnalise from '@/components/especialista/PainelAnalise.vue';
import ModalValidacao from '@/components/especialista/ModalValidação.vue';
import ModalHistoricoAnimal from '@/components/especialista/ModalHistoricoAnimal.vue';


const selectedDenuncia = ref(null);
const historicoSelecionado = ref(null);
const abaAtiva = ref('triagem');

const denunciasTriagem = ref([
  {
    id: 1,
    animal: 'Serpente',
    titulo: 'Ocorrência de serpente em área residencial',
    denunciante: 'Maria Souza',
    tipo: 'Réptil',
    descricao: 'Animal avistado próximo ao muro lateral.',
    imagem: 'https://picsum.photos/seed/serpente/640/360',
    local: 'Reserva Ambiental',
    data: '29/04/2025',
    status: 'Morto'
  },
  {
    id: 2,
    animal: 'Capivara',
    titulo: 'Capivara ferida próxima ao lago',
    denunciante: 'João Pedro',
    tipo: 'Mamífero',
    descricao: 'Aparenta lesão na pata traseira.',
    imagem: 'https://picsum.photos/seed/capivara/640/360',
    local: 'Parque do Lago',
    data: '27/04/2025',
    status: 'Ferido'
  },
  {
    id: 3,
    animal: 'Gavião',
    titulo: 'Avistamento de gavião no campus',
    denunciante: 'Ana Lima',
    tipo: 'Ave',
    descricao: 'Apenas avistamento, sem ferimentos aparentes.',
    imagem: 'https://picsum.photos/seed/gaviao/640/360',
    local: 'Área Urbana Central',
    data: '25/04/2025',
    status: 'Avistado'
  },
  {
    id: 4,
    animal: 'Lobo-Guará',
    titulo: 'Lobo-guará encontrado sem vida',
    denunciante: 'Carlos Mendes',
    tipo: 'Mamífero',
    descricao: 'Necessário resgate e análise posterior.',
    imagem: 'https://picsum.photos/seed/lobo/640/360',
    local: 'Estrada da Ceasa',
    data: '24/04/2025',
    status: 'Morto'
  }
]);

const denunciasArquivadas = ref([
  {
    id: 101,
    animal: 'Tamanduá-mirim',
    titulo: 'Tamanduá resgatado e em análise',
    denunciante: 'Beatriz Alves',
    tipo: 'Mamífero',
    descricao: 'Animal encaminhado para avaliação técnica após resgate.',
    imagem: 'https://picsum.photos/seed/tamandua/640/360',
    local: 'Trilha do Bosque',
    data: '20/04/2025',
    statusFinal: 'Arquivado',
    processoFinal: 'Resgate > Cuidado > Arquivamento',
    resumoFinal: 'Animal recuperado e encaminhado para arquivamento após acompanhamento.',
    historico: [
      { titulo: 'Denúncia recebida', data: '20/04/2025 08:12', descricao: 'Ocorrência registrada na central.' },
      { titulo: 'Resgate realizado', data: '20/04/2025 09:05', descricao: 'Equipe acionada e animal removido com segurança.' },
      { titulo: 'Análise concluída', data: '21/04/2025 11:40', descricao: 'Animal avaliado e liberado para arquivamento.' },
      { titulo: 'Arquivado', data: '21/04/2025 12:00', descricao: 'Processo finalizado e enviado ao histórico.' }
    ]
  },
  {
    id: 102,
    animal: 'Sabiá',
    titulo: 'Avistamento direto para arquivamento',
    denunciante: 'Ricardo Nunes',
    tipo: 'Ave',
    descricao: 'Somente registro visual; sem necessidade de intervenção.',
    imagem: 'https://picsum.photos/seed/sabia/640/360',
    local: 'Jardim Central',
    data: '18/04/2025',
    statusFinal: 'Arquivado',
    processoFinal: 'Avistamento > Arquivamento',
    resumoFinal: 'Caso encerrado e enviado diretamente ao arquivo histórico.',
    historico: [
      { titulo: 'Denúncia recebida', data: '18/04/2025 16:20', descricao: 'Avistamento registrado pelo cidadão.' },
      { titulo: 'Validado', data: '18/04/2025 16:45', descricao: 'Classificado como avistamento sem resgate.' },
      { titulo: 'Arquivado', data: '18/04/2025 17:00', descricao: 'Processo finalizado e enviado ao histórico.' }
    ]
  }
]);

const totalRegistros = computed(() => denunciasTriagem.value.length + denunciasArquivadas.value.length)
const pendentes = computed(() => denunciasTriagem.value.length)
const urgentes = computed(() => denunciasTriagem.value.filter(item => item.status === 'Morto' || item.status === 'Ferido').length)
const especies = computed(() => new Set([...denunciasTriagem.value, ...denunciasArquivadas.value].map(item => item.animal)).size)

const mudarAba = (aba) => {
  abaAtiva.value = aba
  selectedDenuncia.value = null
  historicoSelecionado.value = null
}

const getStatusClass = (status) => {
  if (status === 'Morto') return 'bg-danger';
  if (status === 'Ferido') return 'bg-primary';
  return 'bg-warning text-dark';
};

const handleValidar = (d) => {
  selectedDenuncia.value = d;
};

const processarAprovacao = (dadosAprovacao) => {
  console.log('Aprovando chamado com parecer técnico:', dadosAprovacao);
  const denunciaAtual = selectedDenuncia.value
  if (!denunciaAtual) return

  moverParaArquivadas(denunciaAtual, {
    processoFinal: denunciaAtual.status === 'Avistado'
      ? 'Avistamento > Arquivamento'
      : 'Resgate > Cuidado > Arquivamento',
    resumoFinal: denunciaAtual.status === 'Avistado'
      ? 'Caso encerrado após validação do avistamento.'
      : 'Animal estabilizado e enviado para arquivamento após acompanhamento.',
    historicoExtra: [
      { titulo: 'Parecer técnico', data: new Date().toLocaleString('pt-BR'), descricao: dadosAprovacao.parecer || 'Parecer registrado pelo especialista.' }
    ]
  })
  const triagemIndex = denunciasTriagem.value.findIndex(item => item.id === denunciaAtual.id)
  if (triagemIndex > -1) {
    denunciasTriagem.value.splice(triagemIndex, 1)
  }
  selectedDenuncia.value = null;
  alert('Ocorrência validada e salva!');
};

const handleAlocar = ({ denunciaId, usuario }) => {
  const idx = denunciasTriagem.value.findIndex(x => x.id === denunciaId)
  if (idx > -1) {
    denunciasTriagem.value[idx].assigned = usuario
  }
  if (selectedDenuncia.value && selectedDenuncia.value.id === denunciaId) {
    selectedDenuncia.value = { ...selectedDenuncia.value, assigned: usuario }
  }
  alert(`Você foi alocado à denúncia: ${usuario}`)
}

const handleArquivar = ({ denunciaId }) => {
  const idx = denunciasTriagem.value.findIndex(x => x.id === denunciaId)
  if (idx > -1) {
    const denuncia = denunciasTriagem.value[idx]
    moverParaArquivadas(denuncia, {
      processoFinal: 'Arquivamento direto',
      resumoFinal: 'Caso enviado diretamente ao arquivo após análise inicial.'
    })
    denunciasTriagem.value.splice(idx, 1)
  }
  if (selectedDenuncia.value && selectedDenuncia.value.id === denunciaId) {
    selectedDenuncia.value = null
  }
  alert('Denúncia arquivada.')
}

const moverParaArquivadas = (denuncia, extras = {}) => {
  const historicoBase = [
    { titulo: 'Denúncia recebida', data: denuncia.data, descricao: `Registro inicial de ${denuncia.animal}.` }
  ]

  const historicoProcesso = denuncia.status === 'Avistado'
    ? [
        { titulo: 'Validada', data: denuncia.data, descricao: 'Avistamento validado para arquivamento.' },
        { titulo: 'Arquivada', data: new Date().toLocaleDateString('pt-BR'), descricao: 'Caso encerrado e enviado ao arquivo.' }
      ]
    : [
        { titulo: 'Resgate', data: new Date().toLocaleDateString('pt-BR'), descricao: 'Equipe acionada para intervenção no animal.' },
        { titulo: 'Análise / cuidado', data: new Date().toLocaleDateString('pt-BR'), descricao: 'Animal acompanhado até definição do destino final.' },
        { titulo: 'Arquivada', data: new Date().toLocaleDateString('pt-BR'), descricao: 'Caso finalizado e migrado para denúncias arquivadas.' }
      ]

  const record = {
    ...denuncia,
    statusFinal: 'Arquivado',
    processoFinal: extras.processoFinal || (denuncia.status === 'Avistado' ? 'Avistamento > Arquivamento' : 'Resgate > Cuidado > Arquivamento'),
    resumoFinal: extras.resumoFinal || 'Caso concluído e arquivado.',
    historico: [...historicoBase, ...historicoProcesso, ...(extras.historicoExtra || [])]
  }

  const existingIndex = denunciasArquivadas.value.findIndex(item => item.id === record.id)
  if (existingIndex > -1) {
    denunciasArquivadas.value[existingIndex] = record
  } else {
    denunciasArquivadas.value.unshift(record)
  }
}

const abrirHistorico = (item) => {
  historicoSelecionado.value = item
}

const gerarLaudo = (d) => {
  alert(`Gerando Laudo Técnico em PDF para: ${d.animal}`);
};

const handleVerArquivados = () => console.log('Ver Arquivados');
const handleGerenciarEspecies = () => console.log('Gerenciar Espécies');
</script>

<style scoped>
.data-card {
  background: #dfe6df;
  border-radius: 18px;
  color: #2f3a33;
  border: 1px solid #c8d0c9;
}
.archived-card {
  background: #dfe6df;
  border-radius: 18px;
  color: #2f3a33;
  border: 1px solid #c8d0c9;
}
.table { --bs-table-bg: transparent; }
.table-responsive { max-height: 400px; }
.btn-primary-light { background-color: #4a6a57; color: white; border: none; border-radius: 12px; }
.rounded-4 { border-radius: 0.9rem; }
.action-btn { border-radius: 0.55rem; }
.clickable-row { cursor: pointer; }
.clickable-row:hover { background: #f4f7f4; }
.process-badge { border-radius: 999px; }
</style>