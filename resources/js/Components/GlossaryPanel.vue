<template>
  <teleport to="body">
    <transition enter-active-class="transition ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
      <div v-if="isOpen" @click="$emit('close')" class="fixed inset-0 bg-slate-950/20 dark:bg-slate-950/50 backdrop-blur-sm z-50"></div>
    </transition>

    <transition enter-active-class="transform transition ease-out duration-300" enter-from-class="translate-x-full" enter-to-class="translate-x-0" leave-active-class="transform transition ease-in duration-200" leave-from-class="translate-x-0" leave-to-class="translate-x-full">
      <aside v-if="isOpen" class="fixed inset-y-0 right-0 w-full sm:w-96 bg-[#e5decf] dark:bg-slate-900 border-l border-stone-300/60 dark:border-slate-800 z-50 shadow-2xl flex flex-col font-sans">
        
        <div class="p-4 border-b border-stone-300/60 dark:border-slate-800 flex items-center justify-between bg-stone-900/[0.02] dark:bg-slate-950/20">
          <div class="flex items-center gap-2 text-indigo-600 dark:text-indigo-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
            <h3 class="text-xs font-black uppercase tracking-wider text-stone-500 dark:text-slate-400">Glossary Definition</h3>
          </div>
          <button @click="$emit('close')" class="p-1.5 text-stone-400 hover:text-stone-700 dark:hover:text-slate-200 hover:bg-stone-900/[0.04] dark:hover:bg-slate-800 rounded-xl transition-colors">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
          </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-4">
          <div class="space-y-1">
            <span class="text-[9px] uppercase tracking-widest font-black text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-950 px-2 py-0.5 rounded font-mono">
              {{ Array.isArray(definition) ? 'Structured Breakdown' : 'Term Context' }}
            </span>
            <h2 class="text-2xl font-black tracking-tight text-stone-900 dark:text-white pt-1">
              {{ term }}
            </h2>
            <div class="h-0.5 w-10 bg-indigo-500 rounded-full"></div>
          </div>

          <div class="bg-stone-50/90 dark:bg-slate-950/50 border border-stone-300/70 dark:border-slate-800 rounded-2xl p-5 shadow-sm">
            
            <ol v-if="Array.isArray(definition)" class="space-y-4">
              <li v-for="(item, index) in definition" :key="index" class="text-sm md:text-base text-stone-800 dark:text-slate-300 leading-relaxed font-medium flex items-start gap-3 group">
                <span class="flex-shrink-0 flex items-center justify-center h-6 w-6 rounded-xl bg-indigo-500 text-white dark:bg-indigo-950/60 dark:text-indigo-400 text-xs font-black font-mono shadow-sm border border-indigo-600/10 dark:border-indigo-900/50">
                  {{ index + 1 }}
                </span>
                <span class="pt-0.5 tracking-wide">{{ item }}</span>
              </li>
            </ol>
            
            <p v-else class="text-sm md:text-base text-stone-800 dark:text-slate-300 leading-relaxed font-medium whitespace-pre-line">
              {{ definition || 'No definition text configured.' }}
            </p>

          </div>
        </div>

        <div class="p-4 border-t border-stone-300/40 dark:border-slate-800 text-center text-[10px] text-stone-400 font-medium bg-stone-900/[0.01] dark:bg-slate-950/10">
          Tap outside or press close button to return to translation work.
        </div>

      </aside>
    </transition>
  </teleport>
</template>

<script setup>
defineProps({
  isOpen: Boolean,
  term: String,
  definition: [String, Array] // <-- Tells Vue to expect either data model type
});

defineEmits(['close']);
</script>