<template>
  <div class="grid grid-cols-1 gap-6">
    <input 
      v-model="note.title" 
      :disabled="!isAuthenticated"
      placeholder="Title" 
      class="w-full text-4xl font-bold outline-none border-none bg-transparent text-slate-900 dark:text-slate-100 disabled:opacity-80" 
    />
    
    <CategorySelect 
      v-model="localNoteProxy.category" 
      :allCategories="categories" 
      :disabled="!isAuthenticated"
      :class="[!isAuthenticated ? 'pointer-events-none opacity-50' : '']"
    />

    <div v-if="isAuthenticated" class="sticky top-0 z-10 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md p-3 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm transition-all">
      <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-500 dark:text-slate-400">
        <div>
          <span class="font-bold text-indigo-600 dark:text-indigo-400">✨ Insertion Style:</span>
        </div>
        <label class="flex items-center gap-1.5 cursor-pointer">
          <input type="radio" v-model="insertStyle" value="newline" class="text-indigo-600 focus:ring-indigo-500" />
          <span>New line underneath</span>
        </label>
        <label class="flex items-center gap-1.5 cursor-pointer">
          <input type="radio" v-model="insertStyle" value="inline" class="text-indigo-600 focus:ring-indigo-500" />
          <span>Inline ( Parentheses )</span>
        </label>
      </div>
      
      <button 
        @click="translateSelection"
        type="button"
        :disabled="isSelectionTranslating"
        class="w-full sm:w-auto text-xs bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg transition shadow-sm disabled:opacity-50"
      >
        {{ isSelectionTranslating ? 'Translating...' : 'Translate Selection' }}
      </button>
    </div>

    <div>
      <span class="text-xl font-bold block mb-2 text-slate-900 dark:text-slate-100">English Section</span>
      <textarea 
        ref="englishTextarea"
        v-model="note.content" 
        :readonly="!isAuthenticated"
        :placeholder="isAuthenticated ? 'English content...' : 'Guest View Mode: Read-Only'" 
        :class="[!isAuthenticated ? 'bg-slate-50 dark:bg-slate-900/20 opacity-70 cursor-not-allowed' : 'bg-transparent']"
        class="w-full text-lg leading-relaxed border-2 border-slate-200 dark:border-slate-800 rounded p-4 outline-none focus:border-indigo-500 transition-colors text-slate-800 dark:text-slate-200 [field-sizing:content]"
      ></textarea>
    </div>

    <div>
      <h1 class="text-xl font-bold block mb-2 text-slate-900 dark:text-slate-100">Amharic Section</h1>
      <textarea 
        v-model="note.amharic_content" 
        :readonly="!isAuthenticated"
        :placeholder="isAuthenticated ? 'Amharic content...' : 'Guest View Mode: Read-Only'" 
        :class="[!isAuthenticated ? 'bg-slate-50 dark:bg-slate-900/20 opacity-70 cursor-not-allowed' : 'bg-transparent']"
        class="w-full text-lg leading-relaxed italic border-2 border-slate-200 dark:border-slate-800 rounded p-4 outline-none focus:border-indigo-500 transition-colors text-indigo-950 dark:text-indigo-200 [field-sizing:content]"
      ></textarea>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import axios from 'axios';

// TO THIS:
import CategorySelect from '@/Components/CategorySelect.vue';
const props = defineProps({
  modelValue: { type: Object, required: true },
  categories: { type: Array, required: true },
  isAuthenticated: { type: Boolean, default: false } // Catch validation status prop
});

const emit = defineEmits(['update:modelValue', 'trigger-alert']);

const englishTextarea = ref(null);
const isSelectionTranslating = ref(false);
const insertStyle = ref('newline');

// 1. Direct Computed Binding Proxy (Prevents rendering performance memory leak)
const note = computed({
  get: () => props.modelValue,
  set: (newValue) => emit('update:modelValue', newValue)
});

// 2. Intercept Object Mutation Events dynamically for child drop-down components
const localNoteProxy = computed(() => {
  return new Proxy(props.modelValue, {
    get(target, prop) {
      return Reflect.get(target, prop);
    },
    set(target, prop, value) {
      emit('update:modelValue', { ...target, [prop]: value });
      return true;
    }
  });
});

// 3. Thread-safe snippet extraction and inline injection engine
const translateSelection = async () => {
  if (!props.isAuthenticated) return; // Guard logic optimization block
  
  const textarea = englishTextarea.value;
  if (!textarea) return;

  const start = textarea.selectionStart;
  const end = textarea.selectionEnd;

  if (start === end) {
    emit('trigger-alert', {
      type: 'alert',
      title: 'No Text Highlighted',
      message: 'Please highlight a segment of text inside the English Section textarea box first!',
      confirmText: 'Got it',
      isDanger: false
    });
    return;
  }

  const selectedText = note.value.content.substring(start, end);
  isSelectionTranslating.value = true;
  
  const scrollContainer = document.documentElement || document.body;
  const savedWindowScrollTop = window.scrollY || scrollContainer.scrollTop;

  try {
    const res = await axios.post('/api/translate-snippet', { text: selectedText });
    const translatedChunk = res.data.translated_text;

    const originalText = note.value.content;
    let insertionString = insertStyle.value === 'newline'
      ? selectedText + "\n" + translatedChunk
      : selectedText + " (" + translatedChunk + ")";

    note.value.content = 
      originalText.substring(0, start) + insertionString + originalText.substring(end);

    setTimeout(() => {
      textarea.focus();
      const newCursorPos = start + insertionString.length;
      textarea.setSelectionRange(newCursorPos, newCursorPos);

      window.scrollTo(0, savedWindowScrollTop);
      scrollContainer.scrollTop = savedWindowScrollTop;
    }, 50);

} catch (error) {
    console.error("Selection translate module error:", error);
    emit('trigger-alert', {
      type: 'alert',
      title: 'API Request Error',
      message: 'Failed to complete snippet translation. Verify backend API endpoint status.',
      confirmText: 'Dismiss',
      isDanger: true
    });
  } finally { // <--- CHANGE THIS FROM 'declare' TO 'finally'
    isSelectionTranslating.value = false;
  }
};
</script>