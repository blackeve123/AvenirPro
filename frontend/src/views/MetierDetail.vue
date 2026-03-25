<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { getJobById } from '../services/jobs'
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'

const route = useRoute()
const router = useRouter()
const job = ref(null)
const loading = ref(true)
const error = ref('')

onMounted(async () => {
  try {
    const response = await getJobById(route.params.id)
    job.value = response.data.data || response.data
  } catch (err) {
    error.value = "Impossible de charger les détails de ce métier."
  } finally {
    loading.value = false
  }
})

const goBack = () => {
  router.push('/metiers')
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <Navbar />
    
    <main class="flex-grow container mx-auto px-4 py-12 max-w-5xl">
      <button @click="goBack" class="flex items-center text-slate-500 hover:text-avenir-orange mb-8 transition-colors">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Retour aux métiers
      </button>

      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-avenir-blue"></div>
      </div>

      <div v-else-if="error" class="bg-red-50 text-red-500 p-8 rounded-2xl text-center">
        <h3 class="text-xl font-bold mb-2">Erreur</h3>
        <p>{{ error }}</p>
        <button @click="goBack" class="mt-6 px-6 py-2 bg-white text-red-500 border border-red-200 rounded-lg hover:bg-red-50">
          Retour
        </button>
      </div>

      <div v-else-if="job" class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100">
        <!-- Hero Section of Job -->
        <div class="relative h-64 sm:h-80 md:h-96 w-full">
          <img v-if="job.image_url" :src="job.image_url" :alt="job.title" class="w-full h-full object-cover" />
          <div v-else class="w-full h-full bg-gradient-to-r from-avenir-blue to-teal-500"></div>
          
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
          
          <div class="absolute bottom-0 left-0 p-8 sm:p-12">
            <span class="inline-block px-4 py-1.5 bg-avenir-orange text-white text-xs font-bold uppercase tracking-wider rounded-full mb-4 shadow-sm">
              {{ job.category || 'Non catégorisé' }}
            </span>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white mb-2 shadow-sm drop-shadow-md">
              {{ job.title }}
            </h1>
          </div>
        </div>

        <!-- Content Section -->
        <div class="p-8 sm:p-12">
          
          <!-- Key Info / Required Profile -->
          <div class="flex flex-wrap items-center gap-4 mb-8">
            <div v-if="job.riasec_code" class="flex items-center bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-avenir-blue" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              Profil RIASEC: {{ job.riasec_code }}
            </div>
            
            <div v-if="job.salary_range" class="flex items-center bg-slate-100 px-4 py-2 rounded-lg text-sm font-medium text-slate-700">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              {{ job.salary_range }}
            </div>
          </div>

          <div class="prose prose-lg max-w-none text-slate-700 mb-12">
            <h2 class="text-2xl font-bold text-avenir-blue mb-4">Description</h2>
            <p class="leading-relaxed whitespace-pre-line">{{ job.description }}</p>
          </div>

          <!-- Steps Timeline -->
          <div v-if="job.steps && job.steps.length > 0" class="mt-16">
            <div class="flex items-center space-x-4 mb-10">
              <div class="h-10 w-2 bg-avenir-orange rounded-full"></div>
              <h2 class="text-3xl font-extrabold text-avenir-blue">Le Parcours Idéal</h2>
            </div>
            
            <div class="relative space-y-8 before:absolute before:inset-0 before:ml-5 before:-translate-x-px md:before:mx-auto md:before:translate-x-0 before:h-full before:w-0.5 before:bg-gradient-to-b before:from-avenir-blue before:via-avenir-blue/50 before:to-transparent">
              
              <div v-for="(step, index) in job.steps" :key="step.id" class="relative flex items-center justify-between md:justify-normal md:odd:flex-row-reverse group is-active">
                <!-- Icon -->
                <div class="flex items-center justify-center w-10 h-10 rounded-full border border-white bg-avenir-blue text-white shadow shrink-0 md:order-1 md:group-odd:-translate-x-1/2 md:group-even:translate-x-1/2 z-10 transition-all duration-300 group-hover:scale-125 group-hover:shadow-avenir-blue/20">
                  <svg v-if="index === job.steps.length - 1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812 3.066 3.066 0 00.723 1.745 3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                  <span v-else class="text-sm font-bold">{{ index + 1 }}</span>
                </div>
                <!-- Content -->
                <div class="w-[calc(100%-4rem)] md:w-[calc(50%-2.5rem)] p-6 rounded-2xl border border-slate-100 bg-white shadow-sm transition-all duration-300 group-hover:shadow-md group-hover:border-avenir-blue/20">
                  <div class="flex items-center justify-between space-x-2 mb-1">
                    <h3 class="font-bold text-slate-800 text-lg">{{ step.title }}</h3>
                    <time v-if="step.duration" class="font-bold text-avenir-orange text-xs whitespace-nowrap bg-orange-50 px-2 py-1 rounded-md">{{ step.duration }}</time>
                  </div>
                  <p class="text-slate-600 leading-relaxed">{{ step.description }}</p>
                </div>
              </div>

            </div>
          </div>


          <!-- Actions -->
          <div class="mt-16 text-center border-t border-slate-100 pt-10">
            <p class="text-slate-600 mb-6 text-lg">Ce métier vous intéresse ? Découvrez si votre profil correspond !</p>
            <router-link to="/test" class="inline-block px-8 py-4 bg-gradient-to-r from-avenir-blue to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-xl shadow-lg transition-all transform hover:-translate-y-1">
              Faire le test d'orientation
            </router-link>
          </div>

        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>

<style scoped>
.prose p {
  margin-bottom: 1.5em;
}
</style>
