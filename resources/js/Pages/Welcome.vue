<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-950 text-slate-800 dark:text-slate-100 font-sans antialiased transition-colors duration-200">
    
    <header class="border-b border-slate-200 dark:border-slate-900 bg-white dark:bg-slate-900/50 backdrop-blur px-4 md:px-6 py-4 flex items-center justify-between shadow-sm sticky top-0 z-40">
      <div class="flex items-center gap-3">
        <button @click="isMobileMenuOpen = !isMobileMenuOpen" class="p-1.5 text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg lg:hidden" title="Toggle Sidebar">
          <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="h-8 w-8 bg-indigo-600 rounded-lg flex items-center justify-center text-white font-black text-lg shadow shadow-indigo-500/20">G</div>
        <h1 class="text-lg md:text-xl font-black tracking-tight text-slate-900 dark:text-white">GiNote Studio</h1>
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
          <span class="hidden sm:inline">{{ isAuthenticated ? `Logged in as Editor` : 'Guest Mode (Read-Only)' }}</span>
          <span class="sm:hidden">{{ isAuthenticated ? `Editor` : 'Guest' }}</span>
        </div>
        <a v-if="!isAuthenticated" :href="route('login')" class="text-xs font-bold text-indigo-600 hover:underline">Log In</a>
      </div>
    </header>

    <div class="max-w-[1600px] mx-auto flex min-h-[calc(100vh-69px)] relative">
      
      <aside class="hidden lg:flex w-80 shrink-0 border-r border-slate-200 dark:border-slate-900 p-4 bg-white/50 dark:bg-slate-900/20 flex-col sticky top-[69px] h-[calc(100vh-69px)]">
        <div class="flex items-center justify-between mb-3">
          <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">Notes Collection ({{ filteredNotes.length }})</h2>
          <button v-if="isAuthenticated" @click="startBlankNote" class="p-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-all shadow-md shadow-indigo-500/10">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
          </button>
        </div>

        <div class="space-y-2 mb-4">
          <input 
            v-model="searchQuery" 
            type="text" 
            placeholder="Search titles & contents..." 
            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500"
          />
          <select 
            v-model="selectedCategoryFilter" 
            class="w-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium"
          >
            <option value="ALL">All Categories</option>
            <option v-for="cat in dynamicCategorySuggestions" :key="cat" :value="cat">{{ cat }}</option>
          </select>
          
        </div>

        <div class="space-y-1 flex-1 overflow-y-auto pr-1">
          <button
            v-for="note in filteredNotes"
            :key="note.id"
            @click="selectNote(note)"
            :class="[form.id === note.id ? 'bg-indigo-50 border-indigo-200 dark:bg-indigo-950/40 dark:border-indigo-900 text-indigo-900 dark:text-indigo-200' : 'border-transparent hover:bg-slate-100 dark:hover:bg-slate-900 text-slate-600 dark:text-slate-400']"
            class="w-full text-left p-3 rounded-xl border flex flex-col gap-1.5 transition-all duration-150"
          >
            <div class="flex items-start justify-between gap-2">
              <span class="font-bold text-sm truncate text-slate-900 dark:text-slate-100 flex-1">{{ note.title || 'Untitled Document' }}</span>
              <span class="text-[9px] bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-1.5 py-0.5 rounded uppercase font-bold shrink-0 tracking-wider">{{ note.category || 'General' }}</span>
            </div>
            <span class="text-xs opacity-75 truncate line-clamp-1 block">{{ note.content || 'Empty block contents...' }}</span>
          </button>
          <div v-if="filteredNotes.length === 0" class="text-center py-8 text-xs text-slate-400 italic">No notes match query filters.</div>
        </div>
      </aside>

      <div v-if="isMobileMenuOpen" class="lg:hidden fixed inset-0 bg-slate-950/60 z-50 backdrop-blur-sm" @click="isMobileMenuOpen = false"></div>
      <aside :class="[isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full']" class="lg:hidden fixed inset-y-0 left-0 w-72 bg-white dark:bg-slate-950 z-50 p-4 transition-transform duration-300 ease-in-out shadow-2xl flex flex-col">
        <div class="flex items-center justify-between mb-4 pb-2 border-b border-slate-100 dark:border-slate-900">
          <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400">Notes ({{ filteredNotes.length }})</h2>
          <div class="flex items-center gap-2">
            <button v-if="isAuthenticated" @click="() => { startBlankNote(); isMobileMenuOpen = false; }" class="p-1.5 bg-indigo-600 text-white rounded-lg"><svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg></button>
            <button @click="isMobileMenuOpen = false" class="p-1.5 text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900 rounded-lg"><svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
          </div>
        </div>

        <div class="space-y-2 mb-4 shrink-0">
          <input v-model="searchQuery" type="text" placeholder="Search..." class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 text-xs focus:outline-none" />
          <select v-model="selectedCategoryFilter" class="w-full bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 text-xs font-medium">
            <option value="ALL">All Categories</option>
            <option v-for="cat in dynamicCategorySuggestions" :key="cat" :value="cat">{{ cat }}</option>
          </select>
        </div>

        <div class="space-y-1 flex-1 overflow-y-auto pr-1">
          <button
            v-for="note in filteredNotes"
            :key="note.id"
            @click="() => { selectNote(note); isMobileMenuOpen = false; }"
            :class="[form.id === note.id ? 'bg-indigo-50 border-indigo-200 dark:bg-indigo-950/40 dark:border-indigo-900 text-indigo-900 dark:text-indigo-200' : 'border-transparent hover:bg-slate-100 dark:hover:bg-slate-900 text-slate-600 dark:text-slate-400']"
            class="w-full text-left p-3 rounded-xl border flex flex-col gap-1 transition-all"
          >
            <div class="flex items-center justify-between gap-2">
              <span class="font-bold text-sm truncate text-slate-900 dark:text-slate-100">{{ note.title || 'Untitled' }}</span>
              <span class="text-[9px] bg-slate-200 dark:bg-slate-800 text-slate-600 dark:text-slate-400 px-1.5 py-0.5 rounded font-bold uppercase shrink-0">{{ note.category || 'General' }}</span>
            </div>
          </button>
        </div>
      </aside>

      <main class="flex-1 p-4 md:p-6 lg:p-8 bg-white dark:bg-slate-900 overflow-x-hidden w-full">
        <div v-if="activeNoteSelected" class="space-y-6">
          
          <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-4">
            <div class="flex-1 space-y-2 min-w-0">
              
              <div class="relative max-w-xs">
                <span v-if="!isEditing" class="inline-block text-[10px] bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 px-2.5 py-0.5 rounded font-bold uppercase tracking-wider mb-1">
                  Category: {{ form.category || 'General' }}
                </span>
                
                <div v-else class="flex flex-col gap-1">
                  <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Category Classification</label>
                  <div class="relative">
                    <input 
                      v-model="form.category" 
                      @focus="showSuggestions = true"
                      @blur="hideSuggestionsWithDelay"
                      type="text" 
                      class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-medium text-sm" 
                      placeholder="Type or select category..." 
                    />
                    <ul v-if="showSuggestions && filteredCategories.length > 0" class="absolute left-0 right-0 mt-1 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-xl z-50 max-h-40 overflow-y-auto py-1">
                      <li 
                        v-for="cat in filteredCategories" 
                        :key="cat"
                        @mousedown="selectCategorySuggestion(cat)"
                        class="px-3 py-2 text-xs font-medium cursor-pointer hover:bg-indigo-600 hover:text-white dark:hover:bg-indigo-900 transition-colors"
                      >
                        {{ cat }}
                      </li>
                    </ul>
                  </div>
                </div>
              </div>

              <div v-if="!isEditing" class="flex items-center gap-2">
    <h2 class="text-xl md:text-2xl font-black tracking-tight text-slate-900 dark:text-white truncate">
      {{ form.title }}
    </h2>
    
    <button 
      @click="copyShareLink" 
      type="button"
      class="p-1.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/50 rounded-xl transition-colors shrink-0"
      title="Copy direct shareable URL link"
    >
      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
      </svg>
    </button>
  </div>
              <div v-else class="flex flex-col gap-1">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Document Title</label>
                <input v-model="form.title" type="text" class="text-base font-bold bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full max-w-md" placeholder="Enter note title..." />
             
             
          <div v-if="isAuthenticated && isEditing" class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
            <button v-if="form.id" @click="deleteActiveNote" type="button" class="text-xs text-red-600 font-bold px-3 py-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">Delete Row</button>
            <div v-else></div>

            <button @click="submitSaveNote" :disabled="form.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow">
              {{ form.processing ? 'Saving...' : 'Commit Save' }}
            </button>
          </div>
              </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
              <button 
                v-if="!isEditing && isAuthenticated && hasSelection"
                @click="translateSelectedText"
                class="text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-xl transition-all shadow flex items-center gap-1.5"
              >
                Translate Selection
              </button>

              <button 
                v-if="isAuthenticated"
                @click="isEditing = !isEditing" 
                :class="[isEditing ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300']"
                class="text-xs font-bold px-3 py-2 rounded-xl transition-all shadow-sm flex items-center gap-1.5"
              >
                {{ isEditing ? 'View Mode' : 'Edit Note' }}
              </button>
            </div>
          </div>

          <div class="grid grid-cols gap-1">
            <div class="space-y-1 min-w-0">
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">English Canvas</label>
              <!-- <div 
                v-if="!isEditing" 
                @mouseup="checkTextSelection"
                @keyup="checkTextSelection"
                v-html="compiledMarkdown"
                class="w-full min-h-[250px] md:min-h-[400px] bg-slate-50/50 dark:bg-slate-950/30 border border-slate-100 dark:border-slate-900 rounded-2xl p-4 font-sans prose dark:prose-invert text-slate-700 dark:text-slate-300 selection:bg-indigo-500/30 overflow-y-auto leading-relaxed text-sm md:text-base"
              >
              </div> -->
               <article  v-if="!isEditing" class="prose dark:prose-invert max-w-none">
      <div class="text-md" v-html="compiledMarkdown"></div>
    </article>

  
 
    <textarea v-if="isEditing" v-model="form.content" rows="66" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono text-xs md:text-sm leading-relaxed" placeholder="Write markdown content layout rows..."></textarea>
      

<!-- 
              <textarea v-if="isEditing" v-model="form.content" rows="16" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono text-xs md:text-sm leading-relaxed" placeholder="Write markdown content layout rows..."></textarea>
            -->
            
            </div>

            <!-- <div class="space-y-1 min-w-0">
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Amharic Transliteration Panel</label>
              <div v-if="!isEditing" class="w-full min-h-[250px] md:min-h-[400px] bg-slate-50/50 dark:bg-slate-950/30 border border-slate-100 dark:border-slate-900 rounded-2xl p-4 text-slate-700 dark:text-slate-300 leading-relaxed font-medium overflow-y-auto text-sm md:text-base">
                <span v-if="form.amharic_content" v-html="formattedAmharic"></span>
                <p v-else class="text-slate-400 italic text-xs">የአማርኛ ትርጉም ገና አልተመዘገበም።</p>
              </div>
              <textarea v-else v-model="form.amharic_content" rows="16" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs md:text-sm leading-relaxed" placeholder="የአማርኛ ፅሁፍ ይፃፉ..."></textarea>
            </div> -->
            
          </div>

        </div>

        <div v-else class="h-full min-h-[350px] flex flex-col items-center justify-center text-center p-6 text-slate-400">
          <h3 class="text-sm font-bold">No Note Canvas Active</h3>
          <p class="text-xs mt-1">Tap the top-left menu or pick a note index row item to load your content.</p>
        </div>
      </main>

    </div>
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import { marked } from 'marked';

const props = defineProps({
  notes: Array,
  activeNoteId: Number // <-- Add this incoming prop
});

const page = usePage();
const activeNoteSelected = ref(false);
const isEditing = ref(false);

// SEARCH, FILTERS, AND THEME CONTROL PROPERTIES
const searchQuery = ref('');
const selectedCategoryFilter = ref('ALL');
const isDark = ref(false);

const isMobileMenuOpen = ref(false);
const showSuggestions = ref(false);
const selectedText = ref('');

const isAuthenticated = computed(() => page.props.auth.user !== null);
const hasSelection = computed(() => selectedText.value.trim().length > 0);

const form = useForm({
  id: null,
  title: '',
  content: '',
  amharic_content: '',
  category: 'General'
});

// INITIALIZE THEME AND DEFAULT ASSIGNMENT
onMounted(() => {
  // Check for saved dark mode preference or fallback to system settings
  if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    isDark.value = true;
    document.documentElement.classList.add('dark');
  } else {
    isDark.value = false;
    document.documentElement.classList.remove('dark');
  }

  if (props.notes && props.notes.length > 0) {
    selectNote(props.notes[0]);
  }
});

// SYSTEM THEME MANAGEMENT ENGINE
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

// LIVE CLIENT-SIDE SEARCH AND CATEGORY FILTERING MATRIX
const filteredNotes = computed(() => {
  if (!props.notes) return [];
  return props.notes.filter(note => {
    const matchesCategory = selectedCategoryFilter.value === 'ALL' || (note.category || 'General') === selectedCategoryFilter.value;
    
    const query = searchQuery.value.toLowerCase().trim();
    const matchesSearch = !query || 
                          (note.title || '').toLowerCase().includes(query) || 
                          (note.content || '').toLowerCase().includes(query);
                          
    return matchesCategory && matchesSearch;
  });
});

// Extract dynamic categories across arrays to keep filtering tags active
const dynamicCategorySuggestions = computed(() => {
  const baseDefaults = ['Development', 'Homelab', 'Database', 'General'];
  if (!props.notes) return baseDefaults;
  const arrayExtracted = props.notes.map(n => n.category).filter(Boolean);
  return [...new Set([...baseDefaults, ...arrayExtracted])];
});

// Suggestive input options when active note mode is running
const filteredCategories = computed(() => {
  const currentInput = (form.category || '').trim().toLowerCase();
  if (!currentInput) return dynamicCategorySuggestions.value;
  return dynamicCategorySuggestions.value.filter(cat => cat.toLowerCase().includes(currentInput));
});

const compiledMarkdown = computed(() => {
  return form.content ? marked.parse(form.content) : '';
});

const formattedAmharic = computed(() => {
  return form.amharic_content ? form.amharic_content.replace(/\n/g, '<br>') : '';
});

// const selectNote = (note) => {
//   if (!note) return;
//   activeNoteSelected.value = true;
//   isEditing.value = false;
//   selectedText.value = '';
  
//   form.id = note.id;
//   form.title = note.title;
//   form.content = note.content;
//   form.amharic_content = note.amharic_content;
//   form.category = note.category || 'General';
// };


const selectNote = (note) => {
  if (!note) return;
  activeNoteSelected.value = true;
  isEditing.value = false;
  selectedText.value = '';
  
  form.id = note.id;
  form.title = note.title;
  form.content = note.content;
  form.amharic_content = note.amharic_content;
  form.category = note.category || 'General';

  // FIX: Push the note's ID to the browser URL bar cleanly without refreshing
  const newUrl = `/notes/${note.id}`;
  window.history.pushState({}, '', newUrl);
};

onMounted(() => {
  // Check if the user landed on a direct shared link
  if (props.activeNoteId) {
    const sharedNote = props.notes.find(n => n.id === props.activeNoteId);
    if (sharedNote) {
      selectNote(sharedNote);
      return;
    }
  }

  // Fallback to selecting the first note if no share link was used
  if (props.notes && props.notes.length > 0) {
    selectNote(props.notes[0]);
  }
});

// const startBlankNote = () => {
//   activeNoteSelected.value = true;
//   isEditing.value = true;
  
//   form.id = null;
//   form.title = 'Untitled Note';
//   form.content = '';
//   form.amharic_content = '';
//   form.category = 'General';
// };

const copyShareLink = () => {
  navigator.clipboard.writeText(window.location.href);
  alert("Shareable URL copied to clipboard!");
};

const startBlankNote = () => {
  activeNoteSelected.value = true;
  isEditing.value = true;
  
  form.id = null;
  form.title = 'Untitled Note';
  form.content = '';
  form.amharic_content = '';
  form.category = 'General';

  // Reset URL to base dashboard path
  window.history.pushState({}, '', '/notes');
};

const selectCategorySuggestion = (categoryName) => {
  form.category = categoryName;
  showSuggestions.value = false;
};

const hideSuggestionsWithDelay = () => {
  setTimeout(() => { showSuggestions.value = false; }, 200);
};

// Watch for modifications to refresh layouts cleanly
watch(() => props.notes, (newNotes) => {
  if (newNotes && newNotes.length > 0 && form.id) {
    const currentActive = newNotes.find(n => n.id === form.id);
    if (currentActive) {
      form.title = currentActive.title;
      form.content = currentActive.content;
      form.amharic_content = currentActive.amharic_content;
      form.category = currentActive.category || 'General';
    }
  }
}, { deep: true });

const checkTextSelection = () => {
  const selection = window.getSelection();
  if (selection && selection.toString().trim().length > 0) {
    selectedText.value = selection.toString();
  } else {
    selectedText.value = '';
  }
};

const translateSelectedText = () => {
  if (!hasSelection.value) return;

  router.post(route('notes.translate'), {
    text: selectedText.value,
    note_id: form.id
  }, {
    preserveState: true,
    onSuccess: (pageResponse) => {
      if (pageResponse.props.notes) {
        const matchingNote = pageResponse.props.notes.find(n => n.id === form.id);
        if (matchingNote) {
          form.amharic_content = matchingNote.amharic_content;
        }
      }
      window.getSelection()?.removeAllRanges();
      selectedText.value = '';
    }
  });
};

const submitSaveNote = () => {
  form.post(route('notes.store'), {
    onSuccess: (pageResponse) => {
      isEditing.value = false;
      if (!form.id && pageResponse.props.notes.length > 0) {
        selectNote(pageResponse.props.notes[0]);
      }
    }
  });
};

const deleteActiveNote = () => {
  if (!confirm("Are you entirely sure you want to permanently delete this document note record?")) return;
  
  const targetId = form.id;
  activeNoteSelected.value = false;
  isEditing.value = false;

  router.delete(route('notes.destroy', targetId), {
    preserveState: false,
    onSuccess: (pageResponse) => {
      form.id = null;
      form.title = '';
      form.content = '';
      form.amharic_content = '';
      
      if (pageResponse.props.notes && pageResponse.props.notes.length > 0) {
        selectNote(pageResponse.props.notes[0]);
      }
    }
  });
};
</script>