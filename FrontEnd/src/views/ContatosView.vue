<template>
  <div class="contatos-page bg-light text-dark text-sans-serif">    
    <NavBarPublic />
      
    <header class="hero-banner position-relative d-flex align-items-center justify-content-start text-start">
      <div class="hero-overlay position-absolute top-0 start-0 w-100 h-100"></div>
      
      <div class="container position-relative z-index-2 text-white px-4 px-md-5">
        <nav aria-label="breadcrumb" class="mb-3">
          <ol class="breadcrumb tiny-text tracking-widest text-uppercase m-0">
            <li class="breadcrumb-item">
              <RouterLink class="text-white-50 text-decoration-none" to="/">Início</RouterLink>
            </li>
            <li class="breadcrumb-item active text-white fw-bold" aria-current="page">Contatos</li>
          </ol>
        </nav>
        
        <span class="text-uppercase tracking-widest fw-bold text-success-light mb-1 d-inline-block small">Atendimento e Suporte</span>
        <h1 class="display-3 fw-extrabold m-0 text-uppercase tracking-tight hero-title">Fale Conosco</h1>
      </div>
    </header>

    <main class="container my-5 py-4">
      <div class="row g-5 text-start">
        
        <div class="col-lg-7">
          <article class="p-4 p-md-5 bg-white border rounded-0 shadow-sm">
            <h2 class="h3 fw-extrabold text-uppercase tracking-tight text-dark mb-2">Envie uma Mensagem</h2>
            <p class="text-secondary small mb-4">Tem alguma dúvida acadêmica, sugestão ou feedback sobre o projeto? Preencha os campos abaixo.</p>
            
            <form @submit.prevent="handleSubmit">
              <div class="mb-3">
                <label for="nome" class="form-label text-uppercase small fw-bold tracking-wider text-secondary">Nome Completo</label>
                <input v-model="form.nome" id="nome" type="text" class="form-control rounded-0 p-3" placeholder="Insira seu nome" required />
              </div>
              
              <div class="mb-3">
                <label for="email" class="form-label text-uppercase small fw-bold tracking-wider text-secondary">Endereço de E-mail</label>
                <input v-model="form.email" id="email" type="email" class="form-control rounded-0 p-3" placeholder="seu@email.com" required />
              </div>
              
              <div class="mb-4">
                <label for="mensagem" class="form-label text-uppercase small fw-bold tracking-wider text-secondary">Sua Mensagem</label>
                <textarea v-model="form.mensagem" id="mensagem" rows="5" class="form-control rounded-0 p-3" placeholder="Escreva detalhadamente o motivo do seu contato..." required></textarea>
              </div>
              
              <button type="submit" class="btn btn-dark rounded-0 fw-bold text-uppercase tracking-wider px-5 py-3 w-100">
                Enviar Mensagem
              </button>
            </form>
          </article>
        </div>

        <div class="col-lg-5">
          <div class="d-flex flex-column gap-4">
            <h2 class="h3 fw-extrabold text-uppercase tracking-tight text-dark m-0 border-bottom pb-2">Coordenação do Projeto</h2>
            
            <section class="card border rounded-0 shadow-sm bg-white overflow-hidden">
              <div class="row g-0 align-items-center">
                <div class="col-sm-4 bg-light border-end d-flex align-items-center justify-content-center overflow-hidden h-100" style="min-height: 140px;">
                  <img src="../assets/images/perfil2.jpg" alt="Foto de Perfil — Profa. Dra. Kita" class="w-100 h-100" style="object-fit: cover;" />
                </div>
                <div class="col-sm-8">
                  <div class="card-body p-3">
                    <span class="badge rounded-0 bg-success text-uppercase mb-1 small">Fundadora</span>
                    <h3 class="h6 fw-bold text-dark m-0 text-uppercase">Profa. Dra. Kita</h3>
                    <p class="text-muted small m-0 mt-2 lh-sm">
                      Contato institucional, parcerias científicas, e-mail acadêmico e redes de monitoramento biológico do campus.
                    </p>
                  </div>
                </div>
              </div>
            </section>

            <section class="card border rounded-0 shadow-sm bg-white overflow-hidden">
              <div class="row g-0 align-items-center">
                <div class="col-sm-4 bg-light border-end d-flex align-items-center justify-content-center overflow-hidden h-100" style="min-height: 140px;">
                  <img src="../assets/images/perfil2.jpg" alt="Foto de Perfil — Prof. Dr. André Nunes" class="w-100 h-100" style="object-fit: cover;" />
                </div>
                <div class="col-sm-8">
                  <div class="card-body p-3">
                    <span class="badge rounded-0 bg-dark text-white text-uppercase mb-1 small">Coordenador</span>
                    <h3 class="h6 fw-bold text-dark m-0 text-uppercase">Prof. Dr. André Nunes</h3>
                    <p class="text-muted small m-0 mt-2 lh-sm">
                      Manejo técnico de ecologia, supervisão do LABEV, dados estatísticos de fauna silvestre e mapeamento espacial.
                    </p>
                  </div>
                </div>
              </div>
            </section>

          </div>
        </div>

      </div>
    </main>

    <Footer />
  </div>
</template>

<script setup>
import { reactive } from 'vue'
import { RouterLink } from 'vue-router'
import NavBarPublic from '@/components/NavBarPublic.vue'
import Footer from '@/components/Footer.vue'

const form = reactive({
  nome: '',
  email: '',
  mensagem: ''
})

const handleSubmit = () => {
  alert(`Mensagem enviada com sucesso!\n\nNome: ${form.nome}\nEmail: ${form.email}`)
  form.nome = ''
  form.email = ''
  form.mensagem = ''
}
</script>

<style scoped>
.rounded-0 { border-radius: 0px !important; }
.fw-extrabold { font-weight: 800; }
.tracking-widest { letter-spacing: 0.15em; }
.tracking-wider { letter-spacing: 0.08em; }
.tracking-tight { letter-spacing: -0.02em; }
.text-success-light { color: #58d68d; }

.text-sans-serif {
  font-family: 'Inter', system-ui, -apple-system, sans-serif;
  -webkit-font-smoothing: antialiased;
}

.hero-banner {
  min-height: 400px;
  background: url('@/assets/images/banner_macaco.jpg') center/cover no-repeat;
}

.hero-overlay {
  background: linear-gradient(90deg, rgba(15, 23, 42, 0.95) 0%, rgba(15, 23, 42, 0.5) 100%);
  z-index: 1;
}

.z-index-2 {
  z-index: 2;
}

.form-control {
  border: 1px solid #d1d5db;
  font-size: 0.95rem;
  transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.form-control:focus {
  border-color: #111827;
  box-shadow: none;
}

.breadcrumb-item + .breadcrumb-item::before {
  content: "→" !important;
  color: rgba(255, 255, 255, 0.4) !important;
  font-size: 0.85rem;
  padding: 0 8px;
}

.tiny-text {
  font-size: 0.75rem;
}

@media (max-width: 767.98px) {
  .hero-banner { min-height: 320px; }
  .display-3 { font-size: 2.25rem; }
}
</style>