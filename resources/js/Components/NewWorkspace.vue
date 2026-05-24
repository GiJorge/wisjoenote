<template>
  <div class="w-full">
    <div v-if="!activeNoteSelected" class="flex flex-col items-center justify-center min-h-[500px] border-2 border-dashed border-stone-300 dark:border-slate-800 rounded-3xl p-8 text-center bg-[#e5decf]/20 dark:bg-slate-950/10 backdrop-blur-sm">
      <svg class="h-12 w-12 text-stone-400 dark:text-slate-600 mb-3 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
      </svg>
      <h3 class="text-sm font-bold text-stone-700 dark:text-slate-300 uppercase tracking-wider">No Active Note Selected</h3>
      <p class="text-xs text-stone-400 dark:text-slate-500 max-w-xs mt-1 leading-relaxed">Select an existing document record from the primary explorer repository stack or spin up a new node sequence workspace initialization layout.</p>
    </div>

    <div v-else class="w-full bg-[#e5decf] dark:bg-slate-900 rounded-3xl p-4 md:p-6 shadow-sm border border-stone-300/40 dark:border-slate-800/80">
      
      <div class="space-y-4 w-full flex flex-col">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-2 border-b border-stone-300/50 dark:border-slate-800/60 px-1">
          
          <div class="flex-1 min-w-0 space-y-1">
            <div v-if="!isEditing" class="flex items-center flex-wrap gap-2.5">
              <h1 class="text-xl md:text-2xl font-black tracking-tight text-stone-800 dark:text-slate-100 break-words max-w-full">
                {{ modelValue.title || 'Untitled Archive Node' }}
              </h1>
                 
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
                  </div>
              <span v-if="modelValue.category" class="text-[10px] font-bold px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20 uppercase tracking-wider">
                {{ modelValue.category }}
              </span>
               <span class="text-xs m-4 text-gray-500 "> <strong> created:</strong>  {{ formatTimeAgo(modelValue.created_at) }}</span>
       
            </div>

            <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-2.5 w-full">
              <input 
                v-model="modelValue.title" 
                type="text" 
                placeholder="Document Node Title..." 
                class="md:col-span-2 text-sm bg-white dark:bg-slate-950 border border-stone-300/60 dark:border-slate-800 rounded-xl px-3 py-2 text-stone-800 dark:text-slate-100 font-bold focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
              />
              <div class="relative">
                <input 
                  v-model="modelValue.category" 
                  type="text" 
                  placeholder="Category Tag..." 
                  class="w-full text-sm bg-white dark:bg-slate-950 border border-stone-300/60 dark:border-slate-800 rounded-xl px-3 py-2 text-stone-600 dark:text-slate-300 font-semibold focus:outline-none focus:ring-2 focus:ring-indigo-500/40"
                />
              </div>
            </div>

            <div v-if="isEditing && categorySuggestions.length > 0" class="flex flex-wrap items-center gap-1.5 pt-1">
              <span class="text-[9px] font-bold text-stone-400 dark:text-slate-500 uppercase tracking-wider select-none">Suggestions:</span>
              <button 
                v-for="suggestion in categorySuggestions" :key="suggestion"
                type="button"
                @click="modelValue.category = suggestion"
                class="text-[9px] font-bold px-2 py-0.5 rounded bg-stone-200/60 dark:bg-slate-950 text-stone-600 dark:text-slate-400 hover:bg-indigo-500 hover:text-white dark:hover:bg-indigo-600 transition-colors"
              >
                {{ suggestion }}
              </button>
            </div>
          </div>

          <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
            <div v-if="isAuthenticated" class="flex items-center gap-2">
              <button 
                v-if="!isEditing"
                @click="emit('update:isEditing', true)"
                class="flex items-center gap-1.5 text-xs font-black uppercase tracking-wider px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm rounded-xl transition-all"
              >
                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Document
              </button>
              

              <div v-else class="flex items-center gap-1.5">
                <button 
                  @click="emit('save-note')"
                  class="flex items-center gap-1.5 text-xs font-black uppercase tracking-wider px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm rounded-xl transition-all"
                >
                  Save Sync
                </button>
                <button 
                  @click="emit('update:isEditing', false)"
                  class="text-xs font-bold px-3 py-2 bg-stone-200 hover:bg-stone-300 dark:bg-slate-950 dark:hover:bg-slate-950/70 text-stone-600 dark:text-slate-400 rounded-xl transition-all"
                >
                  Cancel
                </button>


                 <button v-if="modelValue.id" @click="$emit('delete-note')" type="button" class="text-xs text-red-600 font-bold px-3 py-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-950/20 transition-colors">Delete Row</button>
       
              </div>
               
            </div>

            <span v-else class="text-[9px] font-mono font-bold tracking-widest uppercase text-stone-400 dark:text-slate-500 bg-stone-200/40 dark:bg-slate-950/40 px-2.5 py-1 rounded-lg border border-stone-300/30 dark:border-slate-800/50">
          
            </span>
          </div>

        </div>
        
        <div v-if="!isEditing" class="w-full bg-stone-50/70 dark:bg-slate-950/30 border border-stone-300/50 dark:border-slate-950 rounded-2xl shadow-inner relative">
          
          <div v-if="canvasData.headings.length > 0" class="sticky top-12 flex items-center justify-between px-4 py-3 bg-stone-100/95 dark:bg-slate-950/95 backdrop-blur-md border-b border-stone-300/40 dark:border-slate-900 z-40 rounded-t-2xl shadow-sm">
            
            <div class="flex items-center gap-2">
              <svg class="h-4 w-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
              </svg>
              <span class="text-xs font-black tracking-wide text-stone-700 dark:text-slate-300">
               <span class="font-mono text-[10px] text-stone-400">({{ canvasData.headings.length }} chapters)</span>
              </span>
            </div>

            <div class="relative">
              <button 
                @click="isIndexOpen = !isIndexOpen"
                class="flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-xl bg-white dark:bg-slate-900 text-stone-700 dark:text-slate-300 border border-stone-300/60 dark:border-slate-800 shadow-sm hover:border-indigo-500/50 transition-all focus:outline-none"
              >
                <span>📖 Quick Jump </span>
                <svg :class="{'rotate-180': isIndexOpen}" class="h-3.5 w-3.5 text-stone-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
              </button>

              <div v-if="isIndexOpen" @click="closeIndexMenu" class="fixed inset-0 z-30"></div>

              <transition
                enter-active-class="transition ease-out duration-150" enter-from-class="transform opacity-0 scale-95" enter-to-class="transform opacity-100 scale-100"
                leave-active-class="transition ease-in duration-100" leave-from-class="transform opacity-100 scale-100" leave-to-class="transform opacity-0 scale-95"
              >
                <div v-if="isIndexOpen" class="absolute right-0 mt-2 w-72 md:w-80 max-h-[400px] bg-white dark:bg-slate-950 border border-stone-300/70 dark:border-slate-800 rounded-2xl shadow-2xl flex flex-col overflow-hidden z-50">
                  <div class="p-2 border-b border-stone-100 dark:border-slate-900 bg-stone-50/50 dark:bg-slate-950">
                    <input 
                      v-model="indexSearchQuery"
                      type="text"
                      placeholder="Type chapter number or keyword..."
                      class="w-full text-xs bg-white dark:bg-slate-900 border border-stone-200 dark:border-slate-800 rounded-xl px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 text-stone-800 dark:text-slate-200"
                      @keydown.esc="closeIndexMenu"
                    />
                  </div>

                  <div class="flex-1 overflow-y-auto p-1.5 space-y-0.5 max-h-[320px]">
                    <button
                      v-for="nav in filteredHeadings" :key="nav.id"
                      @click="scrollToAnchor(nav.id)"
                      :class="nav.level === 3 ? 'pl-6 text-stone-500 dark:text-slate-400 text-[11px]' : 'pl-3 font-bold text-stone-800 dark:text-slate-200 text-xs'"
                      class="w-full text-left px-3 py-2 rounded-xl hover:bg-indigo-500 hover:text-white dark:hover:bg-indigo-600 transition-colors flex items-center justify-between group"
                    >
                      <span class="truncate pr-2 tracking-wide">{{ nav.text }}</span>
                      <span class="text-[9px] font-mono text-stone-400 group-hover:text-indigo-200 font-bold uppercase shrink-0">H0{{ nav.level }}</span>
                    </button>

                    <div v-if="filteredHeadings.length === 0" class="text-center py-6 text-xs text-stone-400 dark:text-slate-500 font-medium">
                      No matching chapters discovered.
                    </div>
                  </div>
                </div>
              </transition>
            </div>

          </div>

          <article 
            @mouseup="checkTextSelection"
            @keyup="checkTextSelection"
            @click="handleCanvasClick" 
            v-html="canvasData.html"
            class="p-6 md:p-10 font-sans prose dark:prose-invert text-stone-900 dark:text-slate-300 selection:bg-indigo-500/20 leading-relaxed text-sm md:text-base max-w-none w-full h-auto overflow-visible"
          ></article>
        </div>
        
        <textarea 
          v-else 
          v-model="modelValue.content" 
          class="w-full h-[calc(100vh-220px)] min-h-[500px] bg-white dark:bg-slate-950 border border-stone-300/60 dark:border-slate-800/80 rounded-2xl p-6 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 font-mono text-xs md:text-sm leading-relaxed text-stone-800 dark:text-slate-300 shadow-inner resize-none" 
          placeholder="Write bilingual markdown or notes layout systems content rows directly here..."
        ></textarea>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

// PROPERTIES AND COMPONENT CONFIGURATION CHANNELS
const props = defineProps({
  modelValue: { 
    type: Object, 
    default: () => ({ title: '', category: '', content: '' }) 
  },
  activeNoteSelected: { type: Boolean, default: false },
  isEditing: { type: Boolean, default: false },
  isAuthenticated: { type: Boolean, default: false },
  compiledMarkdown: { type: String, default: '' },
  categorySuggestions: { type: Array, default: () => [] }
});

const emit = defineEmits([
  'update:modelValue', 
  'update:isEditing', 
  'save-note', 
  'delete-note', 
  'translate-selection', 
  'open-glossary'
]);

// UX CONTROL PARAMETERS FOR SCALED 70+ LAYOUT STACKS
const isIndexOpen = ref(false);
const indexSearchQuery = ref('');

const showCopiedTooltip = ref(false);

const closeIndexMenu = () => {
  isIndexOpen.value = false;
  indexSearchQuery.value = '';
};


// LOCALIZED COMPOSITE GLOSSARY LEXICON TRANSCRIPTIONS
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

const copyShareLink = () => { navigator.clipboard.writeText(window.location.href); showCopiedTooltip.value = true; setTimeout(() => { showCopiedTooltip.value = false; }, 1500); };

// COMPILER COMPONENT BLOCK RUNNING AUTO INJECTIONS OVER OUTPUT DATA STRINGS
const canvasData = computed(() => {
  let html = props.compiledMarkdown || '';
  const headings = [];
  
  if (props.isEditing || !html) return { html, headings };

  // PART A: Injecting IDs and safe Tailwind scroll margins (scroll-mt-20) to clear sticky index bar offsets
  let headingCounter = 0;
  html = html.replace(/<h([23])([^>]*)>([\s\S]*?)<\/h\1>/gi, (match, level, attributes, text) => {
    const cleanText = text.replace(/<\/?[^>]+(>|$)/g, "").trim();
    
    let idMatch = attributes.match(/id="([^"]+)"/);
    let id = idMatch ? idMatch[1] : `nav-${headingCounter++}-${cleanText.replace(/\s+/g, '-').replace(/[.,\/#!$%\^&\*;:{}=\-_`~()?"']/g, "")}`;
    
    let newAttributes = attributes;
    if (!idMatch) {
      newAttributes += ` id="${id}"`;
    }
    
    // Increased offset slightly to scroll-mt-20 for an airy breathe gap right under the pinned dock
    if (!newAttributes.includes('class="')) {
      newAttributes += ' class="scroll-mt-20"';
    } else {
      newAttributes = newAttributes.replace('class="', 'class="scroll-mt-20 ');
    }
    
    headings.push({ id, text: cleanText, level: parseInt(level) });
    return `<h${level}${newAttributes}>${text}</h${level}>`;
  });

  // PART B: Parsing Dynamic Glossary Markers
  Object.keys(glossaryDictionary || {}).forEach(term => {
    const regex = new RegExp(`(?<!\\p{L})(${term})(?!\\p{L})`, 'gu');
    html = html.replace(regex, `<span class="glossary-term cursor-pointer underline decoration-dotted decoration-2 decoration-indigo-500/60 hover:decoration-indigo-600 font-bold text-indigo-600 dark:text-indigo-400 transition-colors" data-term="$1">$1</span>`);
  });

  return { html, headings };
});

// REACTIVE SEARCH STREAM SENSOR EXTRACTOR
const filteredHeadings = computed(() => {
  const headings = canvasData.value.headings || [];
  if (!indexSearchQuery.value.trim()) return headings;
  
  const query = indexSearchQuery.value.toLowerCase();
  return headings.filter(h => h.text.toLowerCase().includes(query));
});

// WINDOW VIEWPORT NATIVE SMOOTH SCROLLER JUMP ENGINE
const scrollToAnchor = (id) => {
  const targetElement = document.getElementById(id);
  if (targetElement) {
    targetElement.scrollIntoView({
      behavior: 'smooth',
      block: 'start'
    });
    closeIndexMenu();
  }
};

// DISPATCH INLINE SELECTION CLICKS ACCESSIBILITY INTERCEPTS
const handleCanvasClick = (event) => {
  const glossaryEl = event.target.closest('.glossary-term');
  if (glossaryEl) {
    event.preventDefault();
    const selectedTerm = glossaryEl.getAttribute('data-term');
    if (!selectedTerm) return;

    const matchedKey = Object.keys(glossaryDictionary || {}).find(
      key => key.toLowerCase() === selectedTerm.toLowerCase()
    );

    if (matchedKey) {
      emit('open-glossary', {
        term: matchedKey,
        definition: glossaryDictionary[matchedKey]
      });
    }
    return;
  }

  const anchorLink = event.target.closest('a[href^="#"]');
  if (anchorLink) {
    event.preventDefault();
    const targetId = anchorLink.getAttribute('href').slice(1);
    
    const targetElement = document.getElementById(targetId);
    if (targetElement) {
      targetElement.scrollIntoView({ 
        behavior: 'smooth', 
        block: 'start' 
      });
    }
  }
};

// TEXT GRAPH RANGE EXTRACTION EVENT SELECTION CAPTURING DETECTOR
const checkTextSelection = () => {
  if (props.isEditing) return;
  
  const selection = window.getSelection();
  const selectedText = selection.toString().trim();
  
  if (selectedText && selectedText.length > 1) {
    emit('translate-selection', selectedText);
  }
};


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