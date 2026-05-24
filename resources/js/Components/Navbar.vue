<template>
  <header class="border-b border-slate-200 dark:border-slate-900 bg-white dark:bg-slate-900/50 backdrop-blur px-4 md:px-6 py-4 flex items-center justify-between shadow-sm sticky top-0 z-40">
    <div class="flex items-center gap-3">
      <button @click="$emit('toggle-mobile-menu')" class="p-1.5 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg lg:hidden" title="Toggle Sidebar">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <div class="h-8 w-8 rounded-lg flex items-center justify-center text-white font-black text-lg shadow shadow-indigo-500/20">

       <ApplicationLogo class="block  w-auto " />

      </div>
      <h1 class="text-lg md:text-xl font-black tracking-tight text-slate-900 dark:text-white">Wisdom Ethiopia </h1>
    </div>

    <div class="flex items-center gap-2 md:gap-4">
      <button
        @click="toggleDarkMode"
        class="p-2 rounded-xl bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors shadow-sm"
        title="Toggle Dark Mode Theme Layout"
      >
        <svg v-if="isDark" class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 17.657l.707.707M7.757 6.343l.707.707M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
        <svg v-else class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
      </button>

      <div :class="[isAuthenticated ? 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20' : 'bg-amber-500/10 text-amber-600 border-amber-500/20']" class="text-[11px] md:text-xs font-semibold px-2.5 py-1 md:py-1.5 rounded-full border flex items-center gap-1.5 shadow-sm">
        <span :class="[isAuthenticated ? 'bg-emerald-500' : 'bg-amber-500']" class="h-1.5 w-1.5 md:h-2 md:w-2 rounded-full"></span>
        <span class="hidden sm:inline">{{ isAuthenticated ? `Logged in as Editor` : 'Guest' }}</span>
        <!-- <span class="sm:hidden">{{ isAuthenticated ? `Editor` : 'Guest' }}</span> -->
      </div>
      <a v-if="!isAuthenticated" :href="route('login')" class="text-xs font-bold text-indigo-600 hover:underline">Log In</a>
    </div>
  </header>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import ApplicationLogo from './ApplicationLogo.vue';

defineProps({
  isAuthenticated: Boolean
});

defineEmits(['toggle-mobile-menu']);

const isDark = ref(false);

onMounted(() => {
  if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true;
    document.documentElement.classList.add('dark');
  } else {
    isDark.value = false;
    document.documentElement.classList.remove('dark');
  }
});

const toggleDarkMode = () => {
  isDark.value = !isDark.value;
  if (isDark.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  }
};
</script>