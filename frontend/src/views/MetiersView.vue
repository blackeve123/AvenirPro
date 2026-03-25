<script setup>
import { ref, onMounted, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getJobs, getCategories } from '../services/jobs'
import JobCard from '../components/JobCard.vue'
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'

const route = useRoute()
const router = useRouter()
const jobs = ref([])
const categories = ref([])
const loading = ref(true)
const error = ref('')

const searchQuery = ref(route.query.search || '')
const selectedCategory = ref(route.query.category || '')

const currentPage = ref(Number(route.query.page) || 1)
const lastPage = ref(1)

const fetchCategories = async () => {
  try {
    const res = await getCategories()
    categories.value = res.data.data || []
  } catch (err) {
    console.warn("Could not fetch categories", err)
  }
}

const fetchJobs = async (page = 1) => {
  loading.value = true
  error.value = ''
  currentPage.value = page
  
  // Sync URL
  router.replace({ 
    query: { 
      ...route.query, 
      page: page, 
      search: searchQuery.value || undefined, 
      category: selectedCategory.value || undefined 
    } 
  })

  try {
    const response = await getJobs({ 
      page, 
      search: searchQuery.value, 
      category: selectedCategory.value 
    })
    
    // Using Resource::collection structure -> response.data.data
    jobs.value = response.data.data
    
    // Pagination Meta extraction
    if (response.data.meta) {
      lastPage.value = response.data.meta.last_page
    }
  } catch (err) {
    error.value = "Erreur lors du chargement des métiers."
  } finally {
    loading.value = false
  }
}

const resetFilters = () => {
  searchQuery.value = ''
  selectedCategory.value = ''
  fetchJobs(1)
}

onMounted(async () => {
  await fetchCategories()
  await fetchJobs(currentPage.value)
})

let searchTimeout 
watch([searchQuery, selectedCategory], () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    fetchJobs(1)
  }, 300)
})
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <Navbar />
    
    <main class="flex-grow container mx-auto px-4 py-12">
      <div class="text-center mb-12">
        <h1 class="text-4xl font-extrabold text-avenir-blue mb-4">Découvrez nos métiers</h1>
        <p class="text-lg text-slate-600 max-w-2xl mx-auto">Explorez une variété de carrières et trouvez celle qui correspond à votre profil RIASEC.</p>
      </div>

      <!-- Filters -->
      <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-100 mb-10">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Rechercher</label>
            <input v-model="searchQuery" type="text" placeholder="Nom du métier..."
              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all" />
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Catégorie</label>
            <select v-model="selectedCategory"
              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all bg-white">
              <option value="">Toutes les catégories</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.name">{{ cat.name }}</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Content -->
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-avenir-orange"></div>
      </div>

      <div v-else-if="error" class="bg-red-50 text-red-500 p-6 rounded-xl text-center shadow-sm">
        {{ error }}
      </div>

      <div v-else-if="jobs.length === 0" class="text-center py-20 bg-white rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-xl text-slate-500 mb-4">Aucun métier ne correspond à votre recherche.</p>
        <button @click="resetFilters" class="text-avenir-orange font-bold hover:bg-orange-50 px-6 py-2 rounded-lg transition-colors border border-avenir-orange">
          Réinitialiser les filtres
        </button>
      </div>

      <div v-else>
        <!-- Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
          <router-link v-for="job in jobs" :key="job.id" :to="`/metiers/${job.id}`" class="block h-full">
            <JobCard :job="job" class="h-full" />
          </router-link>
        </div>

        <!-- Pagination -->
        <div v-if="lastPage > 1" class="flex items-center justify-center space-x-4 mt-12">
          <button 
            @click="fetchJobs(currentPage - 1)" 
            :disabled="currentPage === 1"
            class="px-5 py-2 rounded-lg font-medium transition-colors"
            :class="currentPage === 1 ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-avenir-blue border border-slate-200 hover:bg-slate-50 hover:text-avenir-orange shadow-sm'"
          >
            Précédent
          </button>
          
          <span class="text-slate-600 font-medium">Page {{ currentPage }} sur {{ lastPage }}</span>
          
          <button 
            @click="fetchJobs(currentPage + 1)" 
            :disabled="currentPage === lastPage"
            class="px-5 py-2 rounded-lg font-medium transition-colors"
            :class="currentPage === lastPage ? 'bg-slate-100 text-slate-400 cursor-not-allowed' : 'bg-white text-avenir-blue border border-slate-200 hover:bg-slate-50 hover:text-avenir-orange shadow-sm'"
          >
            Suivant
          </button>
        </div>
      </div>
    </main>
    
    <Footer />
  </div>
</template>
