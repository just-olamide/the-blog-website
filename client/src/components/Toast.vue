<script>
export default {
  name: 'Toast',
  props: {
    show: {
      type: Boolean,
      default: false,
    },
    type: {
      type: String,
      default: 'success', // success, error, warning, info
      validator: (value) => ['success', 'error', 'warning', 'info'].includes(value),
    },
    title: {
      type: String,
      default: '',
    },
    message: {
      type: String,
      required: true,
    },
    duration: {
      type: Number,
      default: 5000,
    },
  },
  data() {
    return {
      visible: false,
      timer: null,
    }
  },
  watch: {
    show(newVal) {
      if (newVal) {
        this.showToast()
      } else {
        this.hideToast()
      }
    },
  },
  methods: {
    showToast() {
      this.visible = true
      if (this.duration > 0) {
        this.timer = setTimeout(() => {
          this.hideToast()
        }, this.duration)
      }
    },
    hideToast() {
      this.visible = false
      if (this.timer) {
        clearTimeout(this.timer)
        this.timer = null
      }
      this.$emit('close')
    },
  },
  beforeUnmount() {
    if (this.timer) {
      clearTimeout(this.timer)
    }
  },
}
</script>

<template>
  <Transition
    name="toast"
    enter-active-class="toast-enter-active"
    leave-active-class="toast-leave-active"
    enter-from-class="toast-enter-from"
    leave-to-class="toast-leave-to"
  >
    <div v-if="visible" class="toast-container">
      <div :class="['toast-card', `toast-${type}`]">
        <div class="toast-icon">
          <!-- Success Icon -->
          <svg
            v-if="type === 'success'"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M20 6L9 17L4 12"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
          </svg>
          <!-- Error Icon -->
          <svg
            v-else-if="type === 'error'"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
            <line x1="15" y1="9" x2="9" y2="15" stroke="currentColor" stroke-width="2" />
            <line x1="9" y1="9" x2="15" y2="15" stroke="currentColor" stroke-width="2" />
          </svg>
          <!-- Warning Icon -->
          <svg
            v-else-if="type === 'warning'"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <path
              d="M10.29 3.86L1.82 18A2 2 0 003.68 21H20.32A2 2 0 0022.18 18L13.71 3.86A2 2 0 0010.29 3.86Z"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
            />
            <line x1="12" y1="9" x2="12" y2="13" stroke="currentColor" stroke-width="2" />
            <circle cx="12" cy="17" r="1" fill="currentColor" />
          </svg>
          <!-- Info Icon -->
          <svg
            v-else-if="type === 'info'"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2" />
            <path d="M12 16V12" stroke="currentColor" stroke-width="2" />
            <circle cx="12" cy="8" r="1" fill="currentColor" />
          </svg>
        </div>
        <div class="toast-content">
          <h4 v-if="title" class="toast-title">{{ title }}</h4>
          <p class="toast-message">{{ message }}</p>
        </div>
        <button @click="hideToast" class="toast-close">
          <svg
            width="20"
            height="20"
            viewBox="0 0 24 24"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
          >
            <line x1="18" y1="6" x2="6" y2="18" stroke="currentColor" stroke-width="2" />
            <line x1="6" y1="6" x2="18" y2="18" stroke="currentColor" stroke-width="2" />
          </svg>
        </button>
      </div>
    </div>
  </Transition>
</template>

<style scoped>
.toast-container {
  position: fixed;
  top: 20px;
  right: 20px;
  z-index: 9999;
  max-width: 400px;
}

.toast-card {
  display: flex;
  align-items: flex-start;
  gap: 12px;
  background: white;
  border-radius: 12px;
  padding: 16px;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  border-left: 4px solid;
  min-width: 320px;
}

.toast-success {
  border-left-color: #10b981;
}

.toast-success .toast-icon {
  color: #10b981;
}

.toast-error {
  border-left-color: #ef4444;
}

.toast-error .toast-icon {
  color: #ef4444;
}

.toast-warning {
  border-left-color: #f59e0b;
}

.toast-warning .toast-icon {
  color: #f59e0b;
}

.toast-info {
  border-left-color: #3b82f6;
}

.toast-info .toast-icon {
  color: #3b82f6;
}

.toast-icon {
  flex-shrink: 0;
  margin-top: 2px;
}

.toast-content {
  flex: 1;
}

.toast-title {
  font-size: 16px;
  font-weight: 600;
  color: #1f2937;
  margin: 0 0 4px 0;
}

.toast-message {
  font-size: 14px;
  color: #6b7280;
  margin: 0;
  line-height: 1.4;
}

.toast-close {
  background: none;
  border: none;
  color: #9ca3af;
  cursor: pointer;
  padding: 4px;
  border-radius: 6px;
  flex-shrink: 0;
  transition: all 0.2s ease;
}

.toast-close:hover {
  color: #6b7280;
  background-color: #f3f4f6;
}

/* Animations */
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%) scale(0.95);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%) scale(0.95);
}

@media (max-width: 640px) {
  .toast-container {
    top: 10px;
    right: 10px;
    left: 10px;
    max-width: none;
  }

  .toast-card {
    min-width: unset;
  }
}
</style>
