<template>
  <div class="notice-host" aria-live="polite" aria-atomic="true">
    <Transition name="notice-fade">
      <UiMessage
        v-if="notice"
        :type="notice.type"
        :title="notice.title"
        :message="notice.message"
        @dismiss="dismissNotice"
      />
    </Transition>
  </div>
</template>

<script setup>
import UiMessage from '@/components/UiMessage.vue'
import { useNotice } from '@/composables/useNotice'

const { notice, dismissNotice } = useNotice()
</script>

<style scoped>
.notice-host {
  position: fixed;
  top: 1rem;
  right: 1rem;
  z-index: 1080;
  width: min(92vw, 420px);
  pointer-events: none;
}

.notice-host :deep(.ui-message) {
  pointer-events: auto;
  margin-bottom: 0;
}

.notice-fade-enter-active,
.notice-fade-leave-active {
  transition: opacity 0.18s ease, transform 0.18s ease;
}

.notice-fade-enter-from,
.notice-fade-leave-to {
  opacity: 0;
  transform: translateY(-6px);
}
</style>