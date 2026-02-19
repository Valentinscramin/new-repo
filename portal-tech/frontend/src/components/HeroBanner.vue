<template>
  <div 
    class="relative h-[70vh] md:h-[80vh] flex items-center justify-center overflow-hidden"
    :style="backgroundStyle"
  >
    <!-- Background overlay gradient -->
    <div class="absolute inset-0 netflix-gradient"></div>
    
    <!-- Content -->
    <div class="relative z-10 max-w-4xl mx-auto px-4 text-center md:text-left">
      <h1 
        class="text-4xl md:text-6xl lg:text-7xl font-bold text-white mb-4 md:mb-6 animate-slide-up"
        v-html="title"
      ></h1>
      
      <p 
        class="text-lg md:text-xl text-gray-300 mb-6 md:mb-8 max-w-2xl animate-slide-up"
        style="animation-delay: 0.1s"
      >
        {{ description }}
      </p>
      
      <div class="flex flex-col sm:flex-row gap-3 justify-center md:justify-start animate-slide-up" style="animation-delay: 0.2s">
        <button 
          v-for="(button, index) in buttons" 
          :key="index"
          @click="handleButtonClick(button)"
          :class="[
            'px-6 md:px-8 py-3 md:py-4 rounded font-semibold text-base md:text-lg transition-all duration-200 flex items-center justify-center gap-2',
            button.variant === 'primary' 
              ? 'netflix-button-primary hover:scale-105' 
              : 'netflix-button-secondary hover:scale-105'
          ]"
        >
          <component v-if="button.icon" :is="button.icon" class="w-5 h-5 md:w-6 md:h-6" />
          {{ button.text }}
        </button>
      </div>
    </div>

    <!-- Fade effect at bottom -->
    <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-netflix-black to-transparent"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  description: {
    type: String,
    required: true
  },
  backgroundImage: {
    type: String,
    default: ''
  },
  buttons: {
    type: Array,
    default: () => [
      { text: 'Explorar', variant: 'primary', action: 'explore' },
      { text: 'Saiba Mais', variant: 'secondary', action: 'info' }
    ]
  }
})

const emit = defineEmits(['button-click'])

const backgroundStyle = computed(() => {
  if (props.backgroundImage) {
    return {
      backgroundImage: `url(${props.backgroundImage})`,
      backgroundSize: 'cover',
      backgroundPosition: 'center',
    }
  }
  return {
    background: 'linear-gradient(135deg, #141414 0%, #1a1a1a 50%, #0a0a0a 100%)'
  }
})

const handleButtonClick = (button) => {
  emit('button-click', button.action)
}
</script>

<style scoped>
/* Animations defined in tailwind.config.cjs */
</style>
