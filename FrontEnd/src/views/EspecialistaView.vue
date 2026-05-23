<template>
  <div class="dashboard-page bg-light min-vh-100 d-flex flex-column">
    <NavBar :abaAtiva="abaAtiva" @mudarAba="mudarAba" />

    <div class="container-fluid px-4 flex-grow-1 py-4">
      
      <div v-if="carregando" class="text-center py-5 my-auto">
        <div class="spinner-border text-success" role="status"></div>
        <p class="text-muted mt-2">Buscando informações no phpMyAdmin...</p>
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
          v-else
          :arquivadas="denunciasArquivadas"
          @selecionarHistorico="abrirHistorico"
        />
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
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';
import NavBar from '@/components/NavBar.vue';
import ModalValidacao from '@/components/especialista/ModalValidação.vue';
import ModalHistoricoAnimal from '@/components/especialista/ModalHistoricoAnimal.vue';

// Importação das novas subpáginas estruturadas
import TriagemPainel from '@/pages/especialista/TriagemPainel.vue';
import HistoricoPainel from '@/pages/especialista/HistoricoPainel.vue';

const selectedDenuncia = ref(null);
const historicoSelecionado = ref(null);
const abaAtiva = ref('triagem');
const carregando = ref(true);

const denunciasTriagem = ref([]);
const denunciasArquivadas = ref([]);

const API_BASE = 'http://localhost:8000/api/ocorrencias';

const buscarDadosDoBanco = async () => {
  try {
    carregando.value = true;
    const [resPendentes, resArquivados] = await Promise.all([
      axios.get(`${API_BASE}/pendentes`),
      axios.get(`${API_BASE}/arquivadas`).catch(() => ({ data: [] }))
    ]);

    denunciasTriagem.value = resPendentes.data.map(item => ({
      id: item.id,
      animal: item.tipo_animal,
      titulo: `${item.categoria_ocorrencia} de ${item.tipo_animal}`,
      denunciante: item.denunciante_nome,
      tipo: item.categoria_ocorrencia,
      descricao: item.descricao,
      imagem: item.foto_path ? `http://localhost:8000/storage/${item.foto_path}` : 'https://picsum.photos/seed/fauna/640/360',
      local: item.ponto_referencia,
      data: new Date(item.created_at).toLocaleDateString('pt-BR'),
      status: item.situacao_animal,
      assigned: item.parecer_tecnico ? 'Especialista' : null,
      coordenadas: { lat: item.latitude, lng: item.longitude }
    }));

    denunciasArquivadas.value = resArquivados.data.map(item => ({
      id: item.id,
      animal: item.tipo_animal,
      denunciante: item.denunciante_nome,
      local: item.ponto_referencia,
      data: new Date(item.created_at).toLocaleDateString('pt-BR'),
      statusFinal: item.status,
      processoFinal: item.status === 'Resolvido' ? 'Resgate > Arquivamento' : 'Falso Alarme > Descarte',
      historico: [
        { titulo: 'Denúncia recebida', data: new Date(item.created_at).toLocaleDateString('pt-BR'), descricao: item.descricao },
        { titulo: 'Finalizado', data: new Date(item.updated_at).toLocaleDateString('pt-BR'), descricao: item.parecer_tecnico || 'Sem parecer.' }
      ]
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
};

const handleValidar = (d) => { selectedDenuncia.value = d; };
const abrirHistorico = (item) => { historicoSelecionado.value = item; };

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

const gerarLaudo = (d) => { alert(`Gerando Laudo Técnico em PDF para: ${d.animal}`); };
const handleGerenciarEspecies = () => console.log('Gerenciar Espécies');
</script>