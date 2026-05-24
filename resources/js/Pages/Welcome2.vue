<template>
 <div class="min-h-screen bg-[#fff6ec] dark:bg-slate-950 text-stone-900 dark:text-slate-100 font-sans antialiased transition-colors duration-200">
  
    <Navbar 
      :is-authenticated="isAuthenticated" 
      @toggle-mobile-menu="isMobileMenuOpen = !isMobileMenuOpen" 
    />

    <div class="max-w-[1600px] mx-auto flex min-h-[calc(100vh-69px)] relative">
      
      <Sidebar 
        v-model:searchQuery="searchQuery"
        v-model:selectedCategoryFilter="selectedCategoryFilter"
        :filtered-notes="filteredNotes"
        :category-suggestions="dynamicCategorySuggestions"
        :active-note-id="form.id"
        :is-authenticated="isAuthenticated"
        :is-mobile-open="isMobileMenuOpen"
        @select-note="selectNote"
        @create-note="startBlankNote"
        @close-mobile-menu="isMobileMenuOpen = false"
      />

      <NewWorkspace 
        v-model="form"
        v-model:isEditing="isEditing"
        :active-note-selected="activeNoteSelected"
        :is-authenticated="isAuthenticated"
        :compiled-markdown="compiledMarkdown"
        :category-suggestions="dynamicCategorySuggestions"
        :created-at="notes.find(n => n.id === form.id)?.created_at" 
         :updated-at="notes.find(n => n.id === form.id)?.updated_at" 
        @save-note="submitSaveNote"
        @delete-note="showDeleteModal = true" @translate-selection="translateSelectedText"
        @open-glossary="triggerGlossaryView"

      />

    </div>
<GlossaryPanel 
  :is-open="activeGlossary.isOpen"
  :term="activeGlossary.term"
  :definition="activeGlossary.definition" @close="activeGlossary.isOpen = false"
/>

    <ConfirmationModal 
      :show="showDeleteModal"
      @close="showDeleteModal = false"
      @confirm="deleteActiveNote"
    />
  </div>


</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { usePage, useForm, router } from '@inertiajs/vue3';
import { marked } from 'marked';

// Import our new atomic sub-components
import Navbar from '@/Components/Navbar.vue';
import Sidebar from '@/Components/Sidebar.vue';
import NewWorkspace from '@/Components/NewWorkspace.vue';
import ConfirmationModal from '@/Components/ConfirmationModal.vue'; 


const showDeleteModal = ref(false);

const props = defineProps({
  notes: Array,
  activeNoteId: Number
});



// Inside your setup function block, initialize reactive values for state tracking
const activeGlossary = ref({
  isOpen: false,
  term: '',
  definition: null // This will automatically adapt to hold a String or an Array
});

const triggerGlossaryView = (payload) => {
  activeGlossary.value.term = payload.term;
  activeGlossary.value.definition = payload.definition; // Catches string or array safely
  activeGlossary.value.isOpen = true;
};


const page = usePage();
const activeNoteSelected = ref(false);
const isEditing = ref(false);
const isMobileMenuOpen = ref(false);
import GlossaryPanel from '@/Components/GlossaryPanel.vue';
const searchQuery = ref('');
const selectedCategoryFilter = ref('ALL');

const isAuthenticated = computed(() => page.props.auth.user !== null);

const form = useForm({
  id: null,
  title: '',
  content: '',
  amharic_content: '',
  category: 'General',
  created_at: null, 
  updated_at: null
});

onMounted(() => {
  if (props.activeNoteId) {
    const sharedNote = props.notes.find(n => n.id === props.activeNoteId);
    if (sharedNote) {
      selectNote(sharedNote);
      return;
    }
  }
  if (props.notes && props.notes.length > 0) {
    selectNote(props.notes[0]);
  }
});

// CLIENT-SIDE ENGINE FILTERING MATRIX
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

const dynamicCategorySuggestions = computed(() => {
  const baseDefaults = ['Dhamma', 'Sutta', 'General'];
  if (!props.notes) return baseDefaults;
  const arrayExtracted = props.notes.map(n => n.category).filter(Boolean);
  return [...new Set([...baseDefaults, ...arrayExtracted])];
});

const compiledMarkdown = computed(() => {
  return form.content ? marked.parse(form.content) : '';
});

const selectNote = (note) => {
  if (!note) return;
  activeNoteSelected.value = true;
  isEditing.value = false;
  
  form.id = note.id;
  form.title = note.title;
  form.content = note.content;
  form.amharic_content = note.amharic_content;
  form.category = note.category || 'General';
  form.created_at = note.created_at; 
  form.updated_at = note.updated_at;

  window.history.pushState({}, '', `/notes/${note.id}`);
  forceScrollReset();
};

const startBlankNote = () => {
  activeNoteSelected.value = true;
  isEditing.value = true;
  
  form.id = null;
  form.title = 'Untitled Note';
  form.content = '';
  form.amharic_content = '';
  form.category = 'General';

  window.history.pushState({}, '', '/notes');
};

const forceScrollReset = () => {
  window.scrollTo({ top: 0, behavior: 'instant' });
  document.documentElement.scrollTop = 0;
  document.body.scrollTop = 0;
  document.querySelectorAll('main, .overflow-y-auto').forEach(el => {
    if (!el.closest('aside')) el.scrollTop = 0;
  });
};

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

const translateSelectedText = (textToTranslate) => {
  router.post(route('notes.translate'), {
    text: textToTranslate,
    note_id: form.id
  }, {
    preserveState: true,
    onSuccess: (pageResponse) => {
      if (pageResponse.props.notes) {
        const matchingNote = pageResponse.props.notes.find(n => n.id === form.id);
        if (matchingNote) form.amharic_content = matchingNote.amharic_content;
      }
      window.getSelection()?.removeAllRanges();
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
  // Close the open modal backdrop immediately
  showDeleteModal.value = false;
  
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