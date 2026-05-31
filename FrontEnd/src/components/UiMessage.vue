<template>
  <div :class="['ui-message', variantClass]" role="alert">
    <div class="ui-message__icon" aria-hidden="true">
      {{ icon }}
    </div>
    <div class="ui-message__content">
      <p v-if="title" class="ui-message__title">{{ title }}</p>
      <p class="ui-message__text mb-0">{{ message }}</p>
    </div>
    <button
      v-if="dismissible"
      type="button"
      class="ui-message__close"
      aria-label="Fechar"
      @click="$emit('dismiss')"
    >
      ×
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  type: {
    type: String,
    default: 'info'
  },
  title: {
    type: String,
    default: ''
  },
  message: {
    type: String,
    required: true
  },
  dismissible: {
    type: Boolean,
    default: true
  }
})

defineEmits(['dismiss'])

const variantClass = computed(() => `ui-message--${props.type}`)

const iconMap = {
  success: '✓',
  error: '!',
  warning: 'i',
  info: 'i'
}

const icon = computed(() => iconMap[props.type] || 'i')
</script>

<style scoped>
.ui-message {
  display: flex;
  align-items: flex-start;
  gap: 0.9rem;
  padding: 0.95rem 1rem;
  border-radius: 14px;
  border: 1px solid transparent;
  box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
  margin-bottom: 1rem;
}

.ui-message__icon {
  width: 28px;
  height: 28px;
  border-radius: 999px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  flex: 0 0 auto;
}

.ui-message__content {
  flex: 1;
  min-width: 0;
}

.ui-message__title {
  font-weight: 700;
  font-size: 0.95rem;
  margin: 0 0 0.15rem;
}

.ui-message__text {
  font-size: 0.92rem;
  line-height: 1.35;
}

.ui-message__close {
  background: transparent;
  border: 0;
  font-size: 1.2rem;
  line-height: 1;
  padding: 0;
  margin-left: 0.25rem;
  color: inherit;
  opacity: 0.7;
}

.ui-message--success {
  background: #f0f9f3;
  border-color: #b8e0c2;
  color: #1f5d36;
}

.ui-message--success .ui-message__icon {
  background: #d9f2df;
  color: #1f5d36;
}

.ui-message--error {
  background: #fff3f3;
  border-color: #f1c0c0;
  color: #8f2f2f;
}

.ui-message--error .ui-message__icon {
  background: #ffdede;
  color: #8f2f2f;
}

.ui-message--warning {
  background: #fff9ef;
  border-color: #efd6a1;
  color: #7a5a16;
}

.ui-message--warning .ui-message__icon,
.ui-message--info .ui-message__icon {
  background: #e8eef8;
  color: #395b8a;
}

.ui-message--info {
  background: #f4f7fb;
  border-color: #d7e0ee;
  color: #31445f;
}
</style>