<template>
  <teleport to="body">
    <transition
      enter-active-class="transition ease-out duration-200"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition ease-in duration-150"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div v-if="show" class="fixed inset-0 bg-slate-950/40 backdrop-blur-sm z-[100] flex items-center justify-center p-4">
        
        <transition
          enter-active-class="transition ease-out duration-200"
          enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          enter-to-class="opacity-100 translate-y-0 sm:scale-100"
          leave-active-class="transition ease-in duration-150"
          leave-from-class="opacity-100 translate-y-0 sm:scale-100"
          leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        >
          <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl max-w-md w-full p-6 shadow-xl relative overflow-hidden">
            
            <div class="flex items-start gap-4">
              <div class="h-10 w-10 rounded-xl bg-red-50 dark:bg-red-950/30 flex items-center justify-center text-red-600 shrink-0">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
              </div>
              
              <div class="space-y-1.5 flex-1">
                <h3 class="text-base font-black tracking-tight text-slate-900 dark:text-white">Delete Document Note</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  Are you entirely sure you want to permanently delete this document note record? This administrative data action cannot be undone.
                </p>
              </div>
            </div>

            <div class="mt-6 flex items-center justify-end gap-2">
              <button 
                @click="$emit('close')" 
                type="button" 
                class="px-3.5 py-2 text-xs font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60 rounded-xl transition-colors"
              >
                Cancel Keep
              </button>
              <button 
                @click="$emit('confirm')" 
                type="button" 
                class="px-4 py-2 text-xs font-bold bg-red-600 hover:bg-red-700 text-white rounded-xl shadow transition-colors"
              >
                Permanently Delete
              </button>
            </div>

          </div>
        </transition>

      </div>
    </transition>
  </teleport>
</template>

<script setup>
defineProps({
  show: Boolean
});

defineEmits(['close', 'confirm']);
</script>