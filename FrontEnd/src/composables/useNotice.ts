import { computed, readonly, ref } from 'vue'

export type NoticeType = 'success' | 'error' | 'warning' | 'info'

type Notice = {
  type: NoticeType
  title: string
  message: string
}

const currentNotice = ref<Notice | null>(null)
let dismissTimer: ReturnType<typeof setTimeout> | null = null

const clearTimer = () => {
  if (dismissTimer) {
    clearTimeout(dismissTimer)
    dismissTimer = null
  }
}

export const showNotice = (message: string, type: NoticeType = 'info', title = 'Aviso') => {
  clearTimer()
  currentNotice.value = { type, title, message }
  dismissTimer = setTimeout(() => {
    currentNotice.value = null
    dismissTimer = null
  }, 3500)
}

export const dismissNotice = () => {
  clearTimer()
  currentNotice.value = null
}

export const useNotice = () => ({
  notice: readonly(currentNotice),
  hasNotice: computed(() => currentNotice.value !== null),
  showNotice,
  dismissNotice,
})