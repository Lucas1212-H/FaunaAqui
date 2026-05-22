# Integração Front-end com Back-end - Denúncia de Ocorrências

## 📋 Alterações Realizadas

### Frontend (Vue 3 + TypeScript)

1. **Criado serviço Axios** (`src/services/ocorrenciaService.ts`)
   - Interface TypeScript para tipagem
   - Método `criarDenuncia()` que envia os dados para o backend
   - Método `obterDenunciaPorCodigo()` para buscar denúncias
   - Tratamento de erros com mensagens amigáveis
   - Suporte a upload de arquivos com FormData

2. **Atualizado `DenunciaView.vue`**
   - Integração com o serviço de Axios
   - Estados para `enviando` (loading) e `mensagemErro`
   - Validação antes de enviar
   - Spinner de carregamento durante submissão
   - Alerta com código de acesso após sucesso
   - Reset do formulário após envio bem-sucedido

3. **Instalado Axios**
   - Adicionado `axios` como dependência do projeto

### Backend (Laravel)

1. **Criado arquivo de configuração CORS** (`config/cors.php`)
   - Configurado para aceitar requisições do Vite dev server (localhost:5173)
   - Suporte a múltiplas portas e origens
   - Regex patterns para desenvolvimento local

2. **Habilitado middleware statefulApi** (`bootstrap/app.php`)
   - Middleware CORS automático

## 🚀 Como Testar

### 1. Iniciar o Backend
```bash
cd Backend
php artisan serve
# Será acessível em http://localhost:8000
```

### 2. Iniciar o Frontend
```bash
cd FrontEnd
npm run dev
# Será acessível em http://localhost:5173
```

### 3. Testar o Formulário
1. Acesse `http://localhost:5173/denuncia`
2. Preencha todos os passos do formulário:
   - **Passo 1**: Nome e contato
   - **Passo 2**: Categoria (silvestre, peçonhento, ninho)
   - **Passo 3**: Tipo animal e situação
   - **Passo 4**: Localização (clique no mapa)
   - **Passo 5**: Foto (câmera ou galeria)
3. Clique em "Enviar Ocorrência"
4. Receberá um código de acesso em caso de sucesso

## 📦 Estrutura do Envio

O frontend envia os dados no seguinte formato:

```json
{
  "denunciante_nome": "João Silva",
  "denunciante_contato_tipo": "telefone",
  "denunciante_contato_valor": "(11) 99999-9999",
  "categoria_ocorrencia": "silvestre",
  "tipo_animal": "Ave",
  "situacao_animal": "Ferido",
  "descricao": "Descrição do animal",
  "latitude": -1.4748,
  "longitude": -48.4452,
  "ponto_referencia": "Reitoria",
  "foto": <File>
}
```

## ✅ Resposta do Backend

Em caso de sucesso (201):
```json
{
  "message": "Ocorrência registrada com sucesso!",
  "codigo": "ABC123",
  "data": {
    "id": 1,
    "denunciante_nome": "João Silva",
    "codigo_acesso": "ABC123",
    "status": "Pendente",
    ...
  }
}
```

## 🐛 Troubleshooting

### Erro CORS
Se receber erro de CORS:
- Verifique se o backend está rodando em `http://localhost:8000`
- Verifique a porta do frontend (deve ser 5173)
- Limpe o cache do navegador

### Erro de Validação
- Todas as fotos devem ser em formato jpeg, png ou jpg (máx 2MB)
- Coordenadas devem ser números válidos

### Foto não salva
- Verifique permissões de escrita em `storage/app/public/ocorrencias`
- Execute `php artisan storage:link` se necessário

## 📝 Notas

- A integração usa `FormData` para envio de arquivos
- Todos os erros do backend são capturados e exibidos no frontend
- O código de acesso é gerado automaticamente (6 caracteres aleatórios)
- Status inicial de todas as denúncias é "Pendente"
