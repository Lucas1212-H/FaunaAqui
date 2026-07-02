<template>
  <div class="dashboard-page bg-light min-vh-100 d-flex flex-column">
    <NavBar :abaAtiva="abaAtiva" @mudarAba="mudarAba" />

    <div class="container-fluid specialist-content px-3 px-md-4 flex-grow-1 py-3 py-md-4">
      
      <div v-if="carregando" class="text-center py-5 my-auto">
        <div class="spinner-border text-success" role="status"></div>
        <p class="text-muted mt-2">Carregando informações...</p>
      </div>

      <div v-else class="flex-grow-1">
        <TriagemPainel 
          v-if="abaAtiva === 'triagem'"
          :denuncias="denunciasTriagem"
          :totalRegistros="totalRegistros"
          :pendentes="pendentes"
          :urgentes="urgentes"
          :especies="especies"
          @validar="handleValidar"
          @exportar="gerarLaudo"
          @gerenciarEspecies="handleGerenciarEspecies"
        />

        <HistoricoPainel 
          v-else-if="abaAtiva === 'arquivadas'"
          :arquivadas="denunciasArquivadas"
          @selecionarHistorico="abrirHistorico"
          @alternarPublicacao="alternarPublicacaoArquivada"
        />

        <PublicadosPainel 
          v-else-if="abaAtiva === 'publicados'"
          :publicados="publicadosLista"
        />

        <AnalisesView
          v-else-if="abaAtiva === 'analises'"
        />
      </div>
    </div>

    <ModalValidacao 
      :denuncia="selectedDenuncia" 
      @fechar="selectedDenuncia = null" 
      @aprovar="processarAprovacao"
      @alocar="handleAlocar"
      @arquivar="handleArquivar"
      @publicar="handlePublicar"
    />

    <ModalHistoricoAnimal
      :item="historicoSelecionado"
      @fechar="historicoSelecionado = null"
      @publicar="handlePublicarHistorico"
    />
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import axios from 'axios';
import { useRoute, useRouter } from 'vue-router';
import NavBar from '@/components/NavBar.vue';
import ModalValidacao from '@/components/especialista/ModalValidação.vue';
import ModalHistoricoAnimal from '@/components/especialista/ModalHistoricoAnimal.vue';

// Importação das novas subpáginas estruturadas
import TriagemPainel from '@/pages/especialista/TriagemPainel.vue';
import HistoricoPainel from '@/pages/especialista/HistoricoPainel.vue';
import PublicadosPainel from '@/pages/especialista/PublicadosPainel.vue';
import AnalisesView from '@/pages/especialista/AnalisesView.vue';
import { exportarLaudoOcorrencia } from '@/utils/exportLaudo';
import { resolveStorageUrl } from '@/utils/mediaUrl';

const FALLBACK_OCORRENCIA_IMAGE = 'https://picsum.photos/seed/fauna/640/360';

const selectedDenuncia = ref(null);
const historicoSelecionado = ref(null);
const route = useRoute();
const router = useRouter();
const abasValidas = new Set(['triagem', 'arquivadas', 'publicados', 'analises']);
const normalizarAba = (valor) => (abasValidas.has(String(valor)) ? String(valor) : 'triagem');
const abaAtiva = ref(normalizarAba(route.query.aba));
const carregando = ref(true);

const denunciasTriagem = ref([]);
const denunciasArquivadas = ref([]);
const publicadosLista = ref([]);

const isLocal = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1'

const API_BASE = isLocal ? 'http://localhost:8000/api/ocorrencias' : 'https://conviva-labev.onrender.com/api/ocorrencias';

const buscarDadosDoBanco = async () => {
  try {
    carregando.value = true;
    const [resPendentes, resArquivados, resPublicados] = await Promise.all([
      axios.get(`${API_BASE}/pendentes`),
      axios.get(`${API_BASE}/arquivadas`).catch(() => ({ data: [] })),
      axios.get(`${API_BASE}/publicados`).catch(() => ({ data: [] }))
    ]);

    denunciasTriagem.value = resPendentes.data.map(item => ({
      id: item.id,
      animal: item.tipo_animal,
      titulo: `${item.distincao_biologica} de ${item.tipo_animal}`,
      denunciante: item.denunciante_nome,
      tipo: item.distincao_biologica,
      distincao_biologica: item.distincao_biologica,
      descricao: item.descricao,
      imagem: resolveStorageUrl(item.foto_url || item.foto_path, FALLBACK_OCORRENCIA_IMAGE),
      local: item.ponto_referencia,
      data: new Date(item.created_at).toLocaleDateString('pt-BR'),
      created_at: item.created_at,
      situacao_animal: item.situacao_animal,
      status: item.situacao_animal,
      statusWorkflow: item.status,
      parecer_tecnico: item.parecer_tecnico,
      assigned: item.parecer_tecnico ? 'Especialista' : null,
      coordenadas: { lat: item.latitude !== null ? parseFloat(item.latitude) : null, lng: item.longitude !== null ? parseFloat(item.longitude) : null }
    }));

    denunciasArquivadas.value = resArquivados.data.map(item => ({
      id: item.id,
      animal: item.tipo_animal,
      denunciante: item.denunciante_nome,
      distincaoBiologica: item.distincao_biologica,
      imagem: resolveStorageUrl(item.foto_url || item.foto_path, FALLBACK_OCORRENCIA_IMAGE),
      local: item.ponto_referencia,
      data: new Date(item.created_at).toLocaleDateString('pt-BR'),
      statusFinal: item.status,
      publicadoNoMapa: !!item.publicado_no_mapa,
      processoFinal: item.status === 'Publicado'
        ? (item.publicado_no_mapa ? 'Publicado no mapa' : 'Publicado fora do mapa')
        : (item.status === 'Resolvido' ? 'Resgate > Arquivamento' : 'Falso Alarme > Descarte'),
      historico: [
        { titulo: 'Denúncia recebida', data: new Date(item.created_at).toLocaleDateString('pt-BR'), descricao: item.descricao },
        { titulo: 'Finalizado', data: new Date(item.updated_at).toLocaleDateString('pt-BR'), descricao: item.parecer_tecnico || 'Sem parecer.' }
      ]
    }));

    publicadosLista.value = resPublicados.data.map(item => ({
      id: item.id,
      animal: item.tipo_animal,
      local: item.ponto_referencia,
      data: new Date(item.created_at).toLocaleDateString('pt-BR'),
      coordenadas: { lat: item.latitude !== null ? parseFloat(item.latitude) : null, lng: item.longitude !== null ? parseFloat(item.longitude) : null }
    }));
  } catch (error) {
    console.error("Erro na sincronização:", error);
  } finally {
    carregando.value = false;
  }
};

onMounted(() => { buscarDadosDoBanco(); });

// --- Métricas Globais ---
const totalRegistros = computed(() => denunciasTriagem.value.length + denunciasArquivadas.value.length);
const pendentes = computed(() => denunciasTriagem.value.length);
const urgentes = computed(() => denunciasTriagem.value.filter(item => item.status === 'Morto' || item.status === 'Ferido').length);
const especies = computed(() => new Set([...denunciasTriagem.value, ...denunciasArquivadas.value].map(item => item.animal)).size);

const mudarAba = (aba) => {
  abaAtiva.value = aba;
  selectedDenuncia.value = null;
  historicoSelecionado.value = null;
  
  router.replace({
    name: 'specialist-area',
    query: { ...route.query, aba },
  });
};
onMounted(() => { buscarDadosDoBanco(); });

watch(
  () => route.query.aba,
  (novaAba) => {
    abaAtiva.value = normalizarAba(novaAba);
  }
);

const handleValidar = (d) => {
  historicoSelecionado.value = null;
  selectedDenuncia.value = d;
};
const abrirHistorico = (item) => {
  selectedDenuncia.value = null;
  historicoSelecionado.value = item;
};

const processarAprovacao = async (dadosAprovacao) => {
  const denunciaAtual = selectedDenuncia.value;
  if (!denunciaAtual) return;
  try {
    await axios.put(`${API_BASE}/${denunciaAtual.id}/avaliar`, {
      status: 'Resolvido',
      parecer_tecnico: dadosAprovacao.parecer || 'Atendido pelo especialista.'
    });
    selectedDenuncia.value = null;
    alert('Ocorrência resolvida com sucesso!');
    buscarDadosDoBanco();
  } catch (error) {
    alert("Erro ao salvar parecer.");
  }
};

const handleAlocar = ({ denunciaId, usuario }) => {
  const idx = denunciasTriagem.value.findIndex(x => x.id === denunciaId);
  if (idx > -1) { denunciasTriagem.value[idx].assigned = usuario; }
  alert(`Alocado com sucesso!`);
};

const handleArquivar = async ({ denunciaId }) => {
  try {
    await axios.put(`${API_BASE}/${denunciaId}/avaliar`, {
      status: 'Falso Alarme',
      parecer_tecnico: 'Falso alarme / Trote.'
    });
    selectedDenuncia.value = null;
    alert('Caso arquivado.');
    buscarDadosDoBanco();
  } catch (error) {
    alert("Erro ao arquivar.");
  }
};

const handlePublicar = async ({ denunciaId }) => {
  try {
    await axios.put(`${API_BASE}/${denunciaId}/publicar`, {
      status: 'Publicado'
    });
    selectedDenuncia.value = null;
    alert('Ocorrência publicada no mapa!  ');
    buscarDadosDoBanco();
  } catch (error) {
    alert("Erro ao publicar.");
    console.error(error);
  }
};

const alternarPublicacaoArquivada = async (item) => {
  try {
    if (item.publicadoNoMapa) {
      await axios.put(`${API_BASE}/${item.id}/despublicar`)
      alert('Ocorrência removida do mapa!')
    } else {
      await axios.put(`${API_BASE}/${item.id}/publicar`, {
        status: 'Publicado'
      })
      alert('Ocorrência publicada no mapa!')
    }

    await buscarDadosDoBanco()
  } catch (error) {
    console.error(error)
    alert('Erro ao atualizar a publicação da ocorrência.')
  }
}

const handlePublicarHistorico = async (item) => {
  try {
    if (item.publicadoNoMapa) {
      await axios.put(`${API_BASE}/${item.id}/despublicar`)
      alert('Ocorrência removida do mapa!')
    } else {
      await axios.put(`${API_BASE}/${item.id}/publicar`, {
        status: 'Publicado'
      })
      alert('Ocorrência publicada no mapa!')
    }

    historicoSelecionado.value = null;
    await buscarDadosDoBanco();
  } catch (error) {
    alert('Erro ao publicar.');
    console.error(error);
  }
};

const gerarLaudo = (denuncia) => {
  const exportado = exportarLaudoOcorrencia(denuncia);
  if (!exportado) {
    alert('Não foi possível abrir a janela de exportação. Verifique se o bloqueador de pop-ups está desativado.');
  }
};
const handleGerenciarEspecies = () => console.log('Gerenciar Espécies');
</script>

<style scoped>
.specialist-content {
  width: 100%;
  max-width: 1440px;
  margin-inline: auto;
}
</style>