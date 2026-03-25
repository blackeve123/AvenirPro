<template>
  <section class="relative -mt-16 z-20 px-4">
    <div class="max-w-5xl mx-auto">
      <div class="glass-card rounded-3xl shadow-2xl p-6 md:p-10 border border-white/50">
        <div class="flex flex-col md:flex-row gap-4">
          <!-- Search Input Group -->
          <div class="relative flex-grow">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
              </svg>
            </div>
            <input
              v-model="search"
              @keyup.enter="searchJob"
              type="text"
              placeholder="Quel métier vous passionne ? (ex: Développeur, Médecin...)"
              class="w-full pl-12 pr-4 py-5 bg-white rounded-2xl border-none shadow-inner focus:ring-2 focus:ring-avenir-orange outline-none text-slate-700 text-lg transition-all"
            />
          </div>

          <!-- Search Button -->
          <button
            @click="searchJob"
            class="btn-orange-gradient text-white px-10 py-5 rounded-2xl font-bold text-lg shadow-lg hover:shadow-avenir-orange/30 flex items-center justify-center gap-2 whitespace-nowrap"
          >
            Trouver mon métier
          </button>
        </div>

        <!-- Popular Categories / Chips -->
        <div class="mt-8">
          <p class="text-sm font-semibold text-slate-500 mb-4 uppercase tracking-wider">Recherche populaire :</p>
          <div class="flex flex-wrap gap-3">
            <button 
              v-for="cat in popularCategories" 
              :key="cat"
              @click="searchByCategory(cat)"
              class="px-5 py-2.5 bg-white/60 hover:bg-avenir-blue hover:text-white text-slate-600 rounded-full text-sm font-medium border border-slate-200 transition-all duration-300 shadow-sm hover:shadow-md"
            >
              {{ cat }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const search = ref('')
const router = useRouter()

const popularCategories = ['Informatique', 'Santé', 'Arts et Création', 'Agriculture', 'Commerce']

const searchJob = () => {
  if (search.value.trim()) {
    router.push({
      path: '/metiers',
      query: { search: search.value.trim() }
    })
  } else {
    router.push('/metiers')
  }
}

const searchByCategory = (category) => {
  router.push({
    path: '/metiers',
    query: { category: category }
  })
}
</script>