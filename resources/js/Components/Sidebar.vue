<template>
  <aside class="hidden lg:flex w-80 shrink-0 border-r border-slate-200 dark:border-slate-900 p-4 bg-white/50 dark:bg-slate-900/20 flex-col sticky top-[69px] h-[calc(100vh-69px)]">
    <div class="flex items-center justify-between mb-3">
      <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Notes Collection ({{ filteredNotes.length }})</h2>
      <button v-if="isAuthenticated" @click="$emit('create-note')" class="p-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all shadow-md shadow-indigo-500/10">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
      </button>
    </div>

    <div class="space-y-2 mb-4">
      <input
        :value="searchQuery"
        @input="$emit('update:searchQuery', $event.target.value)"
        type="text"
        placeholder="Search titles & contents..."
        class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
      />
      <select
        :value="selectedCategoryFilter"
        @change="$emit('update:selectedCategoryFilter', $event.target.value)"
        class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium"
      >
        <option value="ALL">All Categories</option>
        <option v-for="cat in categorySuggestions" :key="cat" :value="cat">{{ cat }}</option>
      </select>
    </div>

    <div class="space-y-1 flex-1 overflow-y-auto pr-1">
      <button
        v-for="note in filteredNotes"
        :key="note.id"
        @click="$emit('select-note', note)"
        :class="[activeNoteId === note.id ? 'bg-indigo-50 border-indigo-200 dark:bg-indigo-950/40 dark:border-indigo-900 text-indigo-900 dark:text-indigo-200' : 'border-transparent hover:bg-slate-100 dark:hover:bg-slate-900 text-slate-600 dark:text-slate-400']"
        class="w-full text-left p-3 rounded-xl border flex flex-col gap-1.5 transition-all duration-150"
      >
        <div class="flex items-start justify-between gap-2">
          <span class="font-bold text-sm truncate text-slate-900 dark:text-slate-100 flex-1">{{ note.title || 'Untitled Document' }}</span>
          <span class="text-[9px] bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-1.5 py-0.5 rounded uppercase font-bold shrink-0 tracking-wider">{{ note.category || 'General' }}</span>
        </div>
        <span class="text-xs opacity-75 truncate line-clamp-1 block">Updated: {{ formatTimeAgo(note.updated_at) }}</span>
      </button>
      <div v-if="filteredNotes.length === 0" class="text-center py-8 text-xs text-slate-400 italic">No notes match query filters.</div>
    </div>
  </aside>

  <div v-if="isMobileOpen" class="lg:hidden fixed inset-0 bg-slate-950/60 z-50 backdrop-blur-sm" @click="$emit('close-mobile-menu')"></div>
  <aside :class="[isMobileOpen ? 'translate-x-0' : '-translate-x-full']" class="lg:hidden fixed inset-y-0 left-0 w-72 bg-white dark:bg-slate-950 z-50 p-4 transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">
    <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-900">
      <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Notes ({{ filteredNotes.length }})</h2>
      <div class="flex items-center gap-2">
        <button v-if="isAuthenticated" @click="() => { $emit('create-note'); $emit('close-mobile-menu'); }" class="p-1.5 bg-indigo-600 text-white rounded-lg"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg></button>
        <button @click="$emit('close-mobile-menu')" class="p-1.5 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
      </div>
    </div>

    <div class="space-y-2 mb-4 shrink-0">
      <input :value="searchQuery" @input="$emit('update:searchQuery', $event.target.value)" type="text" placeholder="Search..." class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 text-xs focus:outline-none" />
      <select :value="selectedCategoryFilter" @change="$emit('update:selectedCategoryFilter', $event.target.value)" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 text-xs font-medium">
        <option value="ALL">All Categories</option>
        <option v-for="cat in categorySuggestions" :key="cat" :value="cat">{{ cat }}</option>
      </select>
    </div>

    <div class="space-y-1 flex-1 overflow-y-auto pr-1">
      <button
        v-for="note in filteredNotes"
        :key="note.id"
        @click="() => { $emit('select-note', note); $emit('close-mobile-menu'); }"
        :class="[activeNoteId === note.id ? 'bg-indigo-50 border-indigo-200 dark:bg-indigo-950/40 dark:border-indigo-900 text-indigo-900 dark:text-indigo-200' : 'border-transparent hover:bg-slate-100 dark:hover:bg-slate-900 text-slate-600 dark:text-slate-400']"
        class="w-full text-left p-3 rounded-xl border flex flex-col gap-1 transition-all"
      >
        <div class="flex items-center justify-between gap-2">
          <span class="font-bold text-sm truncate text-slate-900 dark:text-slate-100">{{ note.title || 'Untitled' }}</span>
          <span class="text-[9px] bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-1.5 py-0.5 rounded font-bold uppercase shrink-0">{{ note.category || 'General' }}</span>
        </div>
      </button>
    </div>
  </aside>
</template>

<script setup>
defineProps({
  filteredNotes: Array,
  categorySuggestions: Array,
  searchQuery: String,
  selectedCategoryFilter: String,
  activeNoteId: Number,
  isAuthenticated: Boolean,
  isMobileOpen: Boolean
});

defineEmits(['update:searchQuery', 'update:selectedCategoryFilter', 'select-note', 'create-note', 'close-mobile-menu']);

const formatTimeAgo = (dateString) => {
  if (!dateString) return 'Unknown date';
  const date = new Date(dateString);
  const now = new Date();
  const secondsDiff = Math.floor((now - date) / 1000);
  if (secondsDiff < 1) return 'Just now';
  const intervals = { year: 31536000, month: 2592000, week: 604800, day: 86400, hour: 3600, minute: 60, second: 1 };
  for (const [unit, value] of Object.entries(intervals)) {
    const count = Math.floor(secondsDiff / value);
    if (count >= 1) return `${count} ${unit}${count > 1 ? 's' : ''} ago`;
  }
  return 'Just now';
};
</script>