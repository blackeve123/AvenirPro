<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { getQuestions, submitAnswer, calculateTestResults, getTestStatus, resetTest } from '../services/test'
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'

const router = useRouter()

const questions = ref([])
const currentQuestionIndex = ref(0)
const currentPage = ref(1)
const lastPage = ref(1)
const loading = ref(true)
const submitting = ref(false)
const isCalculating = ref(false)
const calculateStep = ref(0)
const error = ref('')
const showIntro = ref(true)
const isResetting = ref(false)

// Track progress
const totalQuestionsAnswered = ref(0)
const totalQuestionsInTest = ref(0) 
const answeredIds = ref([])

const loadQuestions = async (page) => {
  loading.value = true
  try {
    const response = await getQuestions(page)
    const body = response.data
    const allOnPage = body.data

    currentPage.value = body.meta?.current_page || body.current_page || 1
    lastPage.value = body.meta?.last_page || body.last_page || 1
    totalQuestionsInTest.value = body.meta?.total || body.total || allOnPage.length || 0

    // Filter out already answered questions on this page
    const unanswered = allOnPage.filter(q => !answeredIds.value.includes(q.id))
    
    if (unanswered.length === 0 && currentPage.value < lastPage.value) {
      // Entire page already answered, move to next
      await loadQuestions(currentPage.value + 1)
      return
    }

    questions.value = unanswered
    currentQuestionIndex.value = 0
  } catch (err) {
    error.value = "Erreur lors du chargement des questions."
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  try {
    const statusRes = await getTestStatus()
    answeredIds.value = statusRes.data?.data?.answered_ids || []
    totalQuestionsAnswered.value = answeredIds.value.length
    await loadQuestions(1)
  } catch (err) {
    // If status fails, just load normally (maybe public test?)
    await loadQuestions(1)
  }
})


const currentQuestion = computed(() => questions.value[currentQuestionIndex.value] || null)

const progressPercentage = computed(() => {
  if (totalQuestionsInTest.value === 0) return 0
  return Math.round((totalQuestionsAnswered.value / totalQuestionsInTest.value) * 100)
})

const handleAnswer = async (answerId) => {
  if (!currentQuestion.value || submitting.value) return
  
  submitting.value = true
  try {
    await submitAnswer({
      question_id: currentQuestion.value.id,
      answer_id: answerId
    })
    
    totalQuestionsAnswered.value++

    if (currentQuestionIndex.value < questions.value.length - 1) {
      // Next question on same page
      currentQuestionIndex.value++
    } else if (currentPage.value < lastPage.value) {
      // Fetch next page
      await loadQuestions(currentPage.value + 1)
    } else {
      // Test finished
      finishTest()
    }
  } catch (err) {
    error.value = "Erreur lors de l'enregistrement de la réponse."
  } finally {
    submitting.value = false
  }
}

const finishTest = async () => {
  isCalculating.value = true
  
  // Sequence of loading texts
  const intervals = [
    setTimeout(() => calculateStep.value = 1, 3000), // "Croisement avec notre base de métiers..."
    setTimeout(() => calculateStep.value = 2, 7000), // "Génération des recommandations IA..."
    setTimeout(() => calculateStep.value = 3, 15000) // "Finalisation de votre profil exclusif..."
  ]

  try {
    // Actually call the calculate endpoint here
    await calculateTestResults()
    // Go to results but since we already calculated, we just fetch them normally
    router.push('/results')
  } catch (err) {
    error.value = "Erreur silencieuse lors de l'analyse, redirection vers vos résultats par défaut."
    setTimeout(() => router.push('/results'), 2000)
  } finally {
    intervals.forEach(clearTimeout)
  }
}

const handleReset = async () => {
  if (isResetting.value) return
  isResetting.value = true
  try {
    await resetTest()
    totalQuestionsAnswered.value = 0
    answeredIds.value = []
    showIntro.value = true
    await loadQuestions(1)
  } catch (err) {
    error.value = "Impossible de réinitialiser le test."
  } finally {
    isResetting.value = false
  }
}

</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <Navbar />
    
    <main v-if="isCalculating" class="flex-grow flex flex-col items-center justify-center p-4 relative overflow-hidden h-screen bg-[#0A0F24]">
      <!-- Immersive AI background glows -->
      <div class="absolute w-[500px] h-[500px] bg-avenir-blue/20 rounded-full blur-[100px] animate-pulse"></div>
      <div class="absolute w-[300px] h-[300px] bg-avenir-orange/20 rounded-full blur-[80px] animate-pulse animation-delay-400" style="bottom: 10%; right: 10%;"></div>
      
      <!-- Core Element -->
      <div class="relative z-10 flex flex-col items-center">
        <div class="relative w-32 h-32 mb-10">
          <div class="absolute inset-0 border-4 border-slate-700 rounded-full"></div>
          <div class="absolute inset-0 border-4 border-avenir-orange rounded-full border-t-transparent animate-spin"></div>
          <div class="absolute inset-2 border-4 border-avenir-blue rounded-full border-b-transparent animate-spin animation-reverse"></div>
          <div class="absolute inset-0 flex items-center justify-center">
            <span class="text-3xl">🧠</span>
          </div>
        </div>
        
        <h2 class="text-2xl md:text-3xl font-bold text-white mb-4 text-center">
          <span v-if="calculateStep === 0">Analyse de votre profil RIASEC...</span>
          <span v-else-if="calculateStep === 1">Recherche des meilleurs métiers...</span>
          <span v-else-if="calculateStep === 2" class="text-transparent bg-clip-text bg-gradient-to-r from-avenir-orange to-yellow-400">L'IA génère vos motivations uniques...</span>
          <span v-else>Finalisation du rapport d'orientation...</span>
        </h2>
        
        <p class="text-slate-400 text-lg max-w-lg text-center animate-pulse">
          <span v-if="calculateStep < 2">Nous comparons vos intérêts aux bases de données professionnelles.</span>
          <span v-else>Cette étape nécessite un peu de réflexion artificielle. Merci de patienter quelques secondes.</span>
        </p>
      </div>
    </main>

    <main v-else class="flex-grow container mx-auto px-4 py-12 flex flex-col items-center justify-center">
      
      <!-- Welcome Screen -->
      <div v-if="showIntro" class="w-full max-w-2xl bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100 p-10 text-center animate-fade-in">
        <div class="h-20 w-20 bg-orange-100 text-avenir-orange rounded-3xl flex items-center justify-center mx-auto mb-6">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
          </svg>
        </div>
        <h1 class="text-3xl font-extrabold text-avenir-blue mb-4">Test d'Orientation RIASEC</h1>
        <p class="text-slate-600 text-lg mb-8 leading-relaxed">
          Découvre les domaines et métiers qui te correspondent réellement. Ce test analyse tes intérêts à travers 6 dimensions de personnalité. 
          <br><br>
          <span class="font-bold text-avenir-orange">Durée estimée : 5 minutes</span>
        </p>
        <button @click="showIntro = false" class="btn-orange-gradient text-white px-10 py-4 rounded-2xl font-bold text-xl shadow-lg hover:shadow-orange-200/50 transition-all hover:scale-[1.02]">
          Commencer le test
        </button>
      </div>

      <div v-else-if="loading && !questions.length" class="flex flex-col items-center justify-center py-20">
        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-avenir-blue mb-4"></div>
        <p class="text-slate-500 font-medium">Préparation du test...</p>
      </div>

      <div v-else-if="error" class="bg-red-50 w-full max-w-2xl text-red-500 p-6 rounded-2xl text-center shadow-sm">
        <p class="font-bold text-lg mb-2">Une erreur est survenue</p>
        <p>{{ error }}</p>
        <button @click="loadQuestions(currentPage)" class="mt-4 bg-red-100 text-red-700 px-6 py-2 rounded-lg font-medium hover:bg-red-200">
          Réessayer
        </button>
      </div>

      <div v-else-if="currentQuestion" class="w-full max-w-3xl">
        
        <!-- Progress Bar -->
        <div class="mb-10 text-center">
          <div class="flex justify-between items-end mb-2 px-2">
            <span class="text-sm font-bold text-slate-500 uppercase tracking-wider">Progression</span>
            <span class="text-sm font-bold text-avenir-orange">{{ progressPercentage }}%</span>
          </div>
          <div class="w-full bg-slate-200 rounded-full h-2.5 overflow-hidden">
            <div class="bg-gradient-to-r from-avenir-orange to-avenir-blue h-2.5 rounded-full transition-all duration-500 ease-out" 
                 :style="{ width: `${progressPercentage}%` }">
            </div>
          </div>
          <p class="text-xs text-slate-400 mt-2">Question {{ totalQuestionsAnswered + 1 }} sur {{ totalQuestionsInTest }}</p>
        </div>

        <!-- Question Card -->
        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-slate-100 relative">
          <!-- Loading overlay during submit -->
          <div v-if="submitting" class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center z-10 transition-opacity">
            <div class="animate-pulse flex space-x-2">
              <div class="h-3 w-3 bg-avenir-orange rounded-full"></div>
              <div class="h-3 w-3 bg-avenir-blue rounded-full animation-delay-200"></div>
              <div class="h-3 w-3 bg-avenir-orange rounded-full animation-delay-400"></div>
            </div>
          </div>

          <div class="p-8 sm:p-12 text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-avenir-blue mb-10 leading-tight">
              {{ currentQuestion.question_text }}
            </h2>

            <div class="flex flex-col sm:flex-row justify-center items-center gap-4 sm:gap-6 mt-8">
              <button 
                v-for="answer in currentQuestion.answers" 
                :key="answer.id"
                @click="handleAnswer(answer.id)"
                :disabled="submitting"
                class="w-full sm:w-auto px-8 py-4 rounded-xl font-bold text-lg transition-all border-2
                       focus:outline-none focus:ring-4 focus:ring-opacity-50
                       hover:-translate-y-1 shadow-sm hover:shadow-md
                       border-slate-200 text-slate-700 hover:border-avenir-orange hover:text-avenir-orange"
                :class="{
                  'bg-green-50 hover:bg-green-100 border-green-200 text-green-700 hover:border-green-400': answer.score > 3,
                  'bg-slate-50 hover:bg-slate-100': answer.score === 3,
                  'bg-red-50 hover:bg-red-100 border-red-200 text-red-700 hover:border-red-400': answer.score < 3
                }"
              >
                {{ answer.answer_text }}
              </button>
            </div>
          </div>
        </div>

      </div>

      <div v-else class="text-center py-20 max-w-xl mx-auto">
        <div v-if="totalQuestionsAnswered >= totalQuestionsInTest && totalQuestionsInTest > 0" class="bg-white p-10 rounded-3xl shadow-xl border border-slate-100">
          <div class="h-20 w-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <h2 class="text-2xl font-extrabold text-avenir-blue mb-4">Félicitations !</h2>
          <p class="text-slate-600 mb-8">Tu as déjà répondu à toutes les questions du test d'orientation.</p>
          <div class="flex flex-col sm:flex-row gap-3 justify-center flex-wrap">
            <button @click="finishTest" class="btn-orange-gradient text-white px-6 py-3 rounded-xl font-bold shadow-md shadow-orange-500/30 hover:-translate-y-1 transition-all">
              Générer mes résultats
            </button>
            <button @click="handleReset" :disabled="isResetting" class="bg-indigo-50 text-indigo-700 px-6 py-3 rounded-xl font-bold hover:bg-indigo-100 transition-colors flex items-center justify-center gap-2">
              <span v-if="isResetting" class="animate-spin h-4 w-4 border-2 border-indigo-700 border-t-transparent rounded-full"></span>
              Refaire le test
            </button>
            <router-link to="/metiers" class="bg-slate-100 text-slate-700 px-6 py-3 rounded-xl font-bold hover:bg-slate-200 transition-colors">
              Explorer les métiers
            </router-link>
          </div>
        </div>
        <div v-else>
          <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-xl font-medium text-slate-500 mb-2">Aucune question disponible</p>
          <p class="text-slate-400">Le test d'orientation n'a pas encore été configuré par l'administrateur.</p>
          <router-link to="/metiers" class="mt-6 inline-block btn-orange-gradient text-white px-6 py-2 rounded-lg font-bold">
            Découvrir les métiers
          </router-link>
        </div>
      </div>

    </main>

    <Footer />
  </div>
</template>

<style scoped>
.animation-delay-200 { animation-delay: 200ms; }
.animation-delay-400 { animation-delay: 400ms; }
</style>
