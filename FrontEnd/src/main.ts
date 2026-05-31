import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import './assets/main.css'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'leaflet/dist/leaflet.css';
import { showNotice } from './composables/useNotice'

const classifyAlert = (message: string) => {
	const texto = String(message).toLowerCase()

	if (texto.includes('erro') || texto.includes('falha') || texto.includes('inválid') || texto.includes('não foi possível')) {
		return { type: 'error' as const, title: 'Erro' }
	}

	if (texto.includes('sucesso') || texto.includes('conclu') || texto.includes('cadastrad') || texto.includes('enviada') || texto.includes('publicada')) {
		return { type: 'success' as const, title: 'Concluído' }
	}

	if (texto.includes('atenção') || texto.includes('obrigat') || texto.includes('selecion')) {
		return { type: 'warning' as const, title: 'Atenção' }
	}

	return { type: 'info' as const, title: 'Aviso' }
}

window.alert = ((message?: any) => {
	const texto = String(message ?? '')
	const notice = classifyAlert(texto)
	showNotice(texto, notice.type, notice.title)
}) as typeof window.alert

createApp(App).use(router).mount('#app')