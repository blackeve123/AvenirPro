<script setup>
import { ref, onMounted } from 'vue'
import { getJobs } from '../services/jobs'
import JobCard from './JobCard.vue'

const jobs = ref([])
const loading = ref(true)

onMounted(async () => {
  try {
    const response = await getJobs()
    const body = response.data
    const allJobs = body.data?.data ? body.data.data : (Array.isArray(body.data) ? body.data : (Array.isArray(body) ? body : []))
    jobs.value = allJobs.slice(0, 3) // Show top 3 jobs
  } catch (err) {
    console.error("Failed to fetch jobs", err)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      
      <div class="flex justify-between items-end mb-12">
        <div>
          <h2 class="text-3xl font-bold text-avenir-blue">
            Métiers qui recrutent
          </h2>
          <p class="text-slate-500 mt-2">
            Découvre les parcours pour accéder aux métiers les plus demandés.
          </p>
        </div>
        <router-link to="/metiers" class="text-avenir-orange font-bold hover:underline hidden sm:block">
          Tout voir →
        </router-link>
      </div>

      <!-- GRID JOBS -->
      <div v-if="loading" class="flex justify-center py-10">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-avenir-blue"></div>
      </div>
      
      <div v-else class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <router-link v-for="job in jobs" :key="job.id" :to="`/metiers/${job.id}`" class="block h-full">
          <JobCard :job="job" class="h-full" />
        </router-link>
      </div>

      <div class="mt-10 text-center sm:hidden">
        <router-link to="/metiers" class="inline-block px-6 py-3 bg-slate-100 text-avenir-blue font-bold rounded-xl border border-slate-200">
          Voir tous les métiers
        </router-link>
      </div>

    </div>
  </section>
</template>