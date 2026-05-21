<template>
  <div class="flex items-center gap-2">
    <label 
      for="category-select" 
      class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500"
    >
      Folder / Category:
    </label>

    <div class="relative inline-block text-left">
      <select
        id="category-select"
        v-model="selectedCategory"
        :disabled="disabled"
        class="appearance-none bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700/80 text-slate-800 dark:text-slate-200 text-xs font-medium px-3 py-1.5 pr-8 rounded-lg outline-none cursor-pointer border border-transparent focus:border-indigo-500/50 transition-all shadow-sm disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <option value="">Uncategorized</option>
        
        <option 
          v-for="cat in allCategories" 
          :key="cat" 
          :value="cat"
        >
          {{ cat }}
        </option>
      </select>

      <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-slate-400 dark:text-slate-500">
        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
          <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
        </svg>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

// 1. Declare properties arriving from the parent NoteEditor layout hierarchy
const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  allCategories: {
    type: Array,
    required: true,
    default: () => []
  },
  disabled: {
    type: Boolean,
    default: false
  }
});

// 2. Map standard Vue emit channels
const emit = defineEmits(['update:modelValue']);

// 3. Clean local proxy to handle bindings safely without direct prop mutations
const selectedCategory = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value)
});
</script>