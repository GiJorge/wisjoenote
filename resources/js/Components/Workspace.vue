<template>
<main class="flex-1 p-4 md:p-6 lg:p-8 bg-[#fff6ec] dark:bg-slate-900 overflow-x-hidden w-full">

    <div v-if="activeNoteSelected" class="space-y-6">

      <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 border-b border-slate-100 dark:border-slate-800 gap-4">
        <div class="flex-1 space-y-2 min-w-0">
          
          <div class="relative max-w-xs">
            <span v-if="!isEditing" class="inline-block text-[10px] bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-900 px-2.5 py-0.5 rounded font-bold uppercase tracking-wider mb-1">
              Category: {{ modelValue.category || 'General' }}
            </span>

            <div v-else class="flex flex-col gap-1">
              <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Category Classification</label>
              <div class="relative">
                <input
                  v-model="modelValue.category"
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
              {{ modelValue.title }}
            </h2>
            
            <div class="relative flex items-center justify-center">
              <transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0 translate-y-1 scale-95"
                enter-to-class="opacity-100 translate-y-0 scale-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100 translate-y-0 scale-100"
                leave-to-class="opacity-0 translate-y-1 scale-95"
              >
                <div v-if="showCopiedTooltip" class="absolute bottom-full mb-2 z-50 bg-slate-900 dark:bg-slate-800 text-white text-[10px] font-bold px-2 py-1 rounded-md shadow-md whitespace-nowrap pointer-events-none">
                  Copied!
                  <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-slate-900 dark:border-t-slate-800"></div>
                </div>
              </transition>

              <button
                @click="copyShareLink"
                type="button"
                class="p-1.5 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800/50 rounded-xl transition-colors shrink-0"
                title="Copy direct shareable URL link"
              >
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
              </button>
             <span class="text-xs m-4"> <strong> created:</strong>  {{ formatTimeAgo(modelValue.created_at) }}</span>
            </div>
          </div>
          
          <div v-else class="flex flex-col gap-1">
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Document Title</label>
            <input v-model="modelValue.title" type="text" class="text-base font-bold bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full max-w-md" placeholder="Enter note title..." />
          </div>
        </div>

        <div class="flex items-center gap-2 shrink-0">
          <button
            v-if="!isEditing && isAuthenticated && hasSelection"
            @click="$emit('translate-selection', selectedText)"
            class="text-xs font-bold bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-xl transition-all shadow flex items-center gap-1.5"
          >
            Translate Selection
          </button>
          <button
            v-if="isAuthenticated"
            @click="toggleEditMode"
            :class="[isEditing ? 'bg-amber-600 hover:bg-amber-700 text-white' : 'bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 text-slate-700 dark:text-slate-300']"
            class="text-xs font-bold px-3 py-2 rounded-xl transition-all shadow-sm flex items-center gap-1.5"
          >
            {{ isEditing ? 'View Mode' : 'Edit Note' }}
          </button>
        </div>
      </div>
  
      <div :key="modelValue.id" class="grid grid-cols-1 md:grid-cols-1 md:mx-8 gap-6">
        
        <div class="space-y-1 min-w-0">
          <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Notes</label>
          <!-- <div 
            v-if="!isEditing" 
            @mouseup="checkTextSelection"
            @keyup="checkTextSelection"
            v-html="compiledMarkdown"
            class="w-full min-h-[250px] md:min-h-[400px] bg-slate-50/50 dark:bg-slate-950/30 border border-slate-100 dark:border-slate-900 rounded-2xl p-4 font-sans prose dark:prose-invert text-slate-700 dark:text-slate-300 selection:bg-indigo-500/30 overflow-y-auto leading-relaxed text-sm md:text-base"
          ></div> -->
               <article  v-if="!isEditing"  class="prose dark:prose-invert max-w-none">
      <div class="text-lg" @click="handleCanvasClick" v-html="enhancedMarkdownContent" ></div>
    </article>

          <textarea v-else v-model="modelValue.content" rows="16" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono text-xs md:text-sm leading-relaxed" placeholder="Write markdown content layout rows..."></textarea>
        </div>
<!-- 
        <div class="space-y-1 min-w-0">
          <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Amharic Transliteration Panel</label>
          <div v-if="!isEditing" class="w-full min-h-[250px] md:min-h-[400px] bg-slate-50/50 dark:bg-slate-950/30 border border-slate-100 dark:border-slate-900 rounded-2xl p-4 text-slate-700 dark:text-slate-300 leading-relaxed font-medium overflow-y-auto text-sm md:text-base">
            <span v-if="modelValue.amharic_content" v-html="formattedAmharic"></span>
            <p v-else class="text-slate-400 italic text-xs">የአማርኛ ትርጉም ገና አልተመዘገበም።</p>
          </div>
          <textarea v-else v-model="modelValue.amharic_content" rows="16" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-2xl p-4 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-xs md:text-sm leading-relaxed" placeholder="የአማርኛ ፅሁፍ ይፃፉ..."></textarea>
        </div> -->

      </div>

      <div v-if="isAuthenticated && isEditing" class="pt-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-4">
        <button v-if="modelValue.id" @click="$emit('delete-note')" type="button" class="text-xs text-red-600 font-bold px-3 py-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">Delete Row</button>
        <div v-else></div>
        <button @click="$emit('save-note')" :disabled="modelValue.processing" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow">
          {{ modelValue.processing ? 'Saving...' : 'Commit Save' }}
        </button>
      </div>

    </div>

    <div v-else class="h-full min-h-[350px] flex flex-col items-center justify-center text-center p-6 text-slate-400">
      <h3 class="text-sm font-bold">No Note Canvas Active</h3>
      <p class="text-xs mt-1">Tap the top-left menu or pick a note index row item to load your content.</p>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'; // Added lifecycles

const props = defineProps({
  modelValue: Object,
  activeNoteSelected: Boolean,
  isEditing: Boolean,
  isAuthenticated: Boolean,
  compiledMarkdown: String,
  categorySuggestions: Array
});

// Add our explicit event emitter out to the parent engine
const emit = defineEmits(['update:modelValue', 'update:isEditing', 'save-note', 'delete-note', 'translate-selection', 'open-glossary']);

const showSuggestions = ref(false);
const selectedText = ref('');
const showCopiedTooltip = ref(false);

// 1. YOUR CENTRAL GLOSSARY DICTIONARY (Add your unique terms here)
const glossaryDictionary = {
   "አዕምሮአዊ አሰራሩን": "በበርካታ ሁኔታዎች የተሰራ ወይም የተዋሃደ አእምሮ። አእምሮአችንን የሚሠሩት የተለያዩ  ሁኔታዎች  ናቸው፣ ልክ የውሃ ጠብታዎች ወንዝ መስራት እንደሚችሉት ።",
  "ጃናስ": "   ጥልቅ ጸጥታ፣ ተመስጦ፡- ቃሉ “ልብ በማለት መመሰጥ” ማለት ነው። በጃና ውስጥ ሲሆኑ፣ ትኩረትዎ በጣም ጠንካራ ከመሆኑ የተነሳ  እስትንፋስ አንድ በመሆን፣ በዙሪያው ያለውን ዓለም እና ውጫዊ ድምፆች እንዲጠፉ ያደርጋል።   ",
  "ኒሚታ": "አእምሮአዊ ምልከታ፣ ሜደቴተሩ ብዙውን ጊዜ በድንገት ከተዘጉ ዓይኖቻቸው በስተጀርባ የሚያበራ ብርሃን ያያሉ። እንደ ደካማ ጭጋግ፣ ብልጭ ድርግም የሚል ኮከብ ወይም የሚያብለጨልጭ ኦርብ ሊጀምር ይችላል፣ እና ከዚያም ወደ ደማቅ፣ አንጸባራቂ ብርሃን ሊሰፋ ይችላል።",
  "ቺታ": "የጠራው አእምሮ፤ ምሳሌ ፦ የሚታዩት፣ የሚታሰቡት፣ የሚተነትኑት ሁሉ ተሰውረው  ዋናው እስካሁን ሲያሳየን የነበረው ያለምንም የሚታይ ነገር ማለት ነው",
 // "ሰባቱ የንቃት ምክንያቶች": "1.ማስተዋል  2. በጥልቀት ማየት 3. ቁርጠኝነት እና ትክክለኛ ጥረት 4. የደስታ መንፈስና እርካታ 5. እርጋታና ጸጥታ 6. የሰከነና አንድ ነገር ላይ የተመሰጠ አአምሮ 7.አስቸጋሪ ሁኔታ ላይ መረጋጋት የሚችል አእምሮ",
 // "": "1.አካልን  በጥልቀት መመልካት 2.ስሜቶቻችንን በጥልቀት መመልካት 3.የአእምሮን በጥልቀት መመልካት 4.የአዕምሮዊ ክስተቶችን በጥልቀት መመልካት",
"ሰባቱ የንቃት ምክንያቶች": [
    "ማስተዋል",
    "በጥልቀት ማየት",
    "ቁርጠኝነት እና ትክክለኛ ጥረት",
    "የደስታ መንፈስና እርካታ",
    "እርጋታና ጸጥታ",
    "የሰከነና አንድ ነገር ላይ የተመሰጠ አአምሮ",
    "አስቸጋሪ ሁኔታ ላይ መረጋጋት የሚችል አእምሮ"
  ],
  "አራቱ የማስተዋል": [
    "አካልን  በጥልቀት መመልካት",
    "ስሜቶቻችንን በጥልቀት መመልካት",
    "ቁርጠኝነት እና ትክክለኛ ጥረት",
    "የአዕምሮዊ ክስተቶችን በጥልቀት መመልከት",
 
  ]


};




// 2. PARSER INJECTOR: Automatically wraps glossary terms in text canvas with clickable classes
// 1. UPDATED ENGLISH CANVAS PARSER (Unicode Aware)
const enhancedMarkdownContent = computed(() => {
  let html = props.compiledMarkdown || '';
  if (props.isEditing) return html;

  Object.keys(glossaryDictionary).forEach(term => {
    // (?<!\p{L}) means: Not preceded by a letter
    // (?!\p{L})  means: Not followed by a letter
    // The 'gu' flags stand for Global and Unicode mapping mode
    const regex = new RegExp(`(?<!\\p{L})(${term})(?!\\p{L})`, 'gu');
    
    html = html.replace(regex, `<span class="glossary-term cursor-pointer underline decoration-dotted decoration-2 decoration-indigo-500/60 hover:decoration-indigo-600 font-bold text-indigo-600 dark:text-indigo-400 transition-colors" data-term="$1">$1</span>`);
  });
  
  return html;
});

// 2. NEW: AMHARIC PANEL PARSER (Allows users to tap terms directly inside the translation panel too!)
const enhancedAmharicContent = computed(() => {
  if (!props.modelValue.amharic_content) return '';
  
  // Apply standard line-break mapping first
  let html = props.modelValue.amharic_content.replace(/\n/g, '<br>');
  if (props.isEditing) return html;

  Object.keys(glossaryDictionary).forEach(term => {
    const regex = new RegExp(`(?<!\\p{L})(${term})(?!\\p{L})`, 'gu');
    html = html.replace(regex, `<span class="glossary-term cursor-pointer underline decoration-dotted decoration-2 decoration-indigo-500/60 hover:decoration-indigo-600 font-bold text-indigo-600 dark:text-indigo-400 transition-colors" data-term="$1">$1</span>`);
  });
  
  return html;
});

// 3. CAPTURE RENDERED DOM CLICKS: Handles events cleanly inside raw parsed v-html structures

// 2. Updated click handler to forward the exact definition payload (string or array)
const handleCanvasClick = (event) => {
  const glossaryEl = event.target.closest('.glossary-term');
  if (!glossaryEl) return;
  
  event.preventDefault();
  const selectedTerm = glossaryEl.getAttribute('data-term');
  if (!selectedTerm) return;

  const matchedKey = Object.keys(glossaryDictionary).find(
    key => key.toLowerCase() === selectedTerm.toLowerCase()
  );

  if (matchedKey) {
    // Emitting the term name and whatever its data type is directly
    emit('open-glossary', {
      term: matchedKey,
      definition: glossaryDictionary[matchedKey] 
    });
  }
};
// Rest of your existing functions unchanged...
const hasSelection = computed(() => selectedText.value.trim().length > 0);
const formattedAmharic = computed(() => {
  if (!props.modelValue.amharic_content) return '';
  return props.modelValue.amharic_content.replace(/\n/g, '<br>');
});
const filteredCategories = computed(() => {
  const currentInput = (props.modelValue.category || '').trim().toLowerCase();
  if (!currentInput) return props.categorySuggestions;
  return props.categorySuggestions.filter(cat => cat.toLowerCase().includes(currentInput));
});
const toggleEditMode = () => { emit('update:isEditing', !props.isEditing); };
const selectCategorySuggestion = (cat) => { props.modelValue.category = cat; showSuggestions.value = false; };
const hideSuggestionsWithDelay = () => { setTimeout(() => { showSuggestions.value = false; }, 200); };
const copyShareLink = () => { navigator.clipboard.writeText(window.location.href); showCopiedTooltip.value = true; setTimeout(() => { showCopiedTooltip.value = false; }, 1500); };
//const checkTextSelection = () => { const selection = window.getSelection(); if (selection && selection.toString().trim().length > 0) { selectedText.value = selection.toString(); } else { selectedText.value = ''; } };



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