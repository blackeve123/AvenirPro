<script setup>
import { ref, onMounted } from 'vue'
import { getJobs, createJob, updateJob, deleteJob, addJobStep, deleteJobStep } from '../services/jobs'
import { getQuestions, createQuestion, updateQuestion, deleteQuestion } from '../services/test'
import Navbar from '../components/Navbar.vue'


const activeTab = ref('jobs')
const jobs = ref([])
const loading = ref(true)
const modalVisible = ref(false)
const isEditing = ref(false)
const error = ref('')
const successMessage = ref('')

// -- Questions State --
const questions = ref([])
const questionModalVisible = ref(false)
const isEditingQuestion = ref(false)
const questionForm = ref({ id: null, question_text: '', riasec_profile_id: 1, order: 0 })

// -- Steps State --
const selectedJobForSteps = ref(null)
const stepsModalVisible = ref(false)
const stepForm = ref({ id: null, title: '', description: '', duration: '', order: 1 })


const form = ref({
  id: null,
  title: '',
  description: '',
  category: '',
  sector: '',
  riasec_types: '',
  salary_range: '',
  image: null
})

const fileInput = ref(null)

const fetchJobs = async () => {
  loading.value = true
  try {
    const response = await getJobs()
    jobs.value = response.data.data
  } catch (err) {
    error.value = "Erreur lors de la récupération des métiers."
  } finally {
    loading.value = false
  }
}

const fetchQuestions = async () => {
  try {
    const response = await getQuestions()
    questions.value = response.data.data
  } catch (err) {
    error.value = "Erreur lors de la récupération des questions."
  }
}

onMounted(() => {
  fetchJobs()
  fetchQuestions()
})


const handleFileChange = (e) => {
  if (e.target.files.length > 0) {
    form.value.image = e.target.files[0]
  } else {
    form.value.image = null
  }
}

const openModal = (job = null) => {
  if (job) {
    isEditing.value = true
    form.value = { 
      id: job.id, 
      title: job.title, 
      description: job.description,
      category: job.category || '',
      sector: job.sector || '',
      riasec_types: job.riasec_types ? (Array.isArray(job.riasec_types) ? job.riasec_types.join(', ') : job.riasec_types) : '',
      salary_range: job.salary_range || '',
      image: null // dont bind existing image object
    }
  } else {
    isEditing.value = false
    form.value = {
      id: null, title: '', description: '', category: '', sector: '', riasec_types: '', salary_range: '', image: null
    }
  }
  if (fileInput.value) fileInput.value.value = ''
  error.value = ''
  modalVisible.value = true
}

const closeModal = () => {
  modalVisible.value = false
}

const saveJob = async () => {
  loading.value = true
  error.value = ''
  
  const formData = new FormData()
  formData.append('title', form.value.title)
  formData.append('description', form.value.description)
  formData.append('category', form.value.category)
  if (form.value.sector) formData.append('sector', form.value.sector)
  if (form.value.riasec_types) formData.append('riasec_types', form.value.riasec_types)
  if (form.value.salary_range) formData.append('salary_range', form.value.salary_range)
  if (form.value.image) {
    formData.append('image', form.value.image)
  }

  try {
    if (isEditing.value) {
      await updateJob(form.value.id, formData)
      successMessage.value = "Métier mis à jour avec succès."
    } else {
      await createJob(formData)
      successMessage.value = "Métier créé avec succès."
    }
    closeModal()
    fetchJobs()
    
    setTimeout(() => { successMessage.value = '' }, 3000)
  } catch (err) {
    if (err.response?.data?.errors) {
      error.value = Object.values(err.response.data.errors).flat().join(' ')
    } else {
      error.value = "Une erreur s'est produite lors de l'enregistrement."
    }
  } finally {
    loading.value = false
  }
}

const destroyJob = async (id) => {
  if (!confirm("Voulez-vous vraiment supprimer ce métier ?")) return
  
  try {
    await deleteJob(id)
    successMessage.value = "Métier supprimé avec succès."
    fetchJobs()
    setTimeout(() => { successMessage.value = '' }, 3000)
  } catch (err) {
    alert("Erreur lors de la suppression.")
  }
}

// -- Question Actions --
const openQuestionModal = (q = null) => {
  if (q) {
    isEditingQuestion.value = true
    questionForm.value = { ...q, riasec_profile_id: q.riasec_profile_id || 1 }
  } else {
    isEditingQuestion.value = false
    questionForm.value = { id: null, question_text: '', riasec_profile_id: 1, order: questions.value.length + 1 }
  }
  questionModalVisible.value = true
}

const saveQuestion = async () => {
  try {
    if (isEditingQuestion.value) {
      await updateQuestion(questionForm.value.id, questionForm.value)
    } else {
      await createQuestion(questionForm.value)
    }
    questionModalVisible.value = false
    fetchQuestions()
    successMessage.value = "Question enregistrée."
    setTimeout(() => { successMessage.value = '' }, 3000)
  } catch (err) {
    error.value = "Erreur lors de l'enregistrement de la question."
  }
}

const destroyQuestion = async (id) => {
  if (!confirm("Supprimer cette question ?")) return
  try {
    await deleteQuestion(id)
    fetchQuestions()
  } catch (err) { alert("Erreur suppression.") }
}

// -- Step Actions --
const openStepsManager = (job) => {
  selectedJobForSteps.value = job
  stepsModalVisible.value = true
}

const saveStep = async () => {
  try {
    await addJobStep(selectedJobForSteps.value.id, stepForm.value)
    // Refetch the job to get updated steps
    const updatedJob = (await getJobs()).data.data.find(j => j.id === selectedJobForSteps.value.id)
    selectedJobForSteps.value = updatedJob
    stepForm.value = { id: null, title: '', description: '', duration: '', order: (updatedJob.steps?.length || 0) + 1 }
    successMessage.value = "Étape ajoutée."
    setTimeout(() => { successMessage.value = '' }, 3000)
  } catch (err) {
    error.value = "Erreur lors de l'ajout de l'étape."
  }
}

const destroyStep = async (stepId) => {
  if (!confirm("Supprimer cette étape ?")) return
  try {
    await deleteJobStep(stepId)
    // Refetch
    fetchJobs()
    // Update local view
    selectedJobForSteps.value.steps = selectedJobForSteps.value.steps.filter(s => s.id !== stepId)
  } catch (err) { alert("Erreur suppression étape.") }
}

</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-100">
    <Navbar />
    
    <div class="flex-grow flex max-w-7xl mx-auto w-full pt-8 pb-12 px-4 sm:px-6 lg:px-8">
      
      <!-- Sidebar -->
      <div class="w-64 shrink-0 bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hidden md:block mr-8 self-start sticky top-28">
        <div class="p-6 border-b border-slate-100">
          <h2 class="text-xl font-bold text-avenir-blue">Administration</h2>
        </div>
        <nav class="p-4 space-y-2">
          <button @click="activeTab = 'jobs'" 
                  class="w-full text-left px-4 py-3 rounded-xl font-medium transition-colors flex items-center"
                  :class="activeTab === 'jobs' ? 'bg-orange-50 text-avenir-orange' : 'text-slate-600 hover:bg-slate-50'">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            Métiers
          </button>
          <button @click="activeTab = 'questions'" 
                  class="w-full text-left px-4 py-3 rounded-xl font-medium transition-colors flex items-center"
                  :class="activeTab === 'questions' ? 'bg-orange-50 text-avenir-orange' : 'text-slate-600 hover:bg-slate-50'">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Test (Questions)
          </button>
        </nav>
      </div>

      <!-- Main Content -->
      <div class="flex-grow">
        
        <!-- Alerts -->
        <div v-if="successMessage" class="mb-4 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm">
          <p class="text-green-700 font-medium">{{ successMessage }}</p>
        </div>

        <div v-if="activeTab === 'jobs'" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">Gestion des Métiers</h3>
            <button @click="openModal()" class="btn-orange-gradient text-white px-4 py-2 rounded-lg font-bold shadow-sm flex items-center text-sm disabled:opacity-50">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              Ajouter un métier
            </button>
          </div>

          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Image</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Titre</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Catégorie</th>
                  <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-slate-200">
                <tr v-if="loading"><td colspan="4" class="px-6 py-10 text-center text-slate-500">Chargement...</td></tr>
                <tr v-else-if="jobs.length === 0"><td colspan="4" class="px-6 py-10 text-center text-slate-500">Aucun métier trouvé.</td></tr>
                
                <tr v-for="job in jobs" :key="job.id" class="hover:bg-slate-50 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <img v-if="job.image_url" :src="job.image_url" class="h-10 w-14 object-cover rounded shadow-sm" />
                    <div v-else class="h-10 w-14 bg-slate-200 rounded flex items-center justify-center text-slate-400 text-xs">N/A</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap font-medium text-slate-800">{{ job.title }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                    <span class="bg-slate-100 text-slate-700 px-2.5 py-1 rounded text-xs font-medium">{{ job.category || 'Non spécifiée' }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button @click="openStepsManager(job)" class="text-orange-500 hover:text-orange-700 mr-4 transition-colors">Parcours</button>
                    <button @click="openModal(job)" class="text-avenir-blue hover:text-blue-800 mr-4 transition-colors">Éditer</button>
                    <button @click="destroyJob(job.id)" class="text-red-500 hover:text-red-700 transition-colors">Supprimer</button>
                  </td>
                </tr>

              </tbody>
            </table>
          </div>
        </div>
        
        <div v-if="activeTab === 'questions'" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
          <div class="px-6 py-5 border-b border-slate-200 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-lg font-bold text-slate-800">Gestion des Questions RIASEC</h3>
            <button @click="openQuestionModal()" class="bg-avenir-blue text-white px-4 py-2 rounded-lg font-bold shadow-sm flex items-center text-sm">
              Ajouter une question
            </button>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-200">
              <thead class="bg-slate-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Ordre</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Question</th>
                  <th class="px-6 py-3 text-left text-xs font-bold text-slate-500 uppercase tracking-wider">Profil</th>
                  <th class="px-6 py-3 text-right text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-slate-200">
                <tr v-for="q in questions" :key="q.id">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ q.order }}</td>
                  <td class="px-6 py-4 text-sm text-slate-800">{{ q.question_text }}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-bold">{{ q.riasec_profile?.code || '?' }}</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                    <button @click="openQuestionModal(q)" class="text-avenir-blue hover:text-blue-800 mr-4">Éditer</button>
                    <button @click="destroyQuestion(q.id)" class="text-red-500 hover:text-red-700">Supprimer</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>


      </div>
    </div>

    <!-- Job Form Modal -->
    <div v-if="modalVisible" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
      <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" @click="closeModal" aria-hidden="true"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full border border-slate-100">
          
          <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-xl leading-6 font-bold text-slate-900" id="modal-title">
              {{ isEditing ? 'Modifier le métier' : 'Ajouter un métier' }}
            </h3>
            <button @click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors focus:outline-none">
              <span class="sr-only">Fermer</span>
              <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
          
          <form @submit.prevent="saveJob" class="px-6 py-6" enctype="multipart/form-data">
            
            <div v-if="error" class="mb-4 bg-red-50 text-red-500 p-3 rounded-xl text-sm font-medium border border-red-100">
              {{ error }}
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Titre <span class="text-red-500">*</span></label>
                <input type="text" v-model="form.title" required 
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all" />
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Catégorie <span class="text-red-500">*</span></label>
                <input type="text" v-model="form.category" required 
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all" />
              </div>
            </div>

            <div class="mb-5">
              <label class="block text-sm font-bold text-slate-700 mb-1">Description <span class="text-red-500">*</span></label>
              <textarea v-model="form.description" rows="4" required
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all"></textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-5">
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Code RIASEC</label>
                <input type="text" v-model="form.riasec_types" placeholder="ex: R, I, C..."
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all" />
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Secteur</label>
                <input type="text" v-model="form.sector" 
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all" />
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Salaire</label>
                <input type="text" v-model="form.salary_range" placeholder="ex: 2000€ - 3000€"
                       class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all" />
              </div>
            </div>

            <div class="mb-8">
              <label class="block text-sm font-bold text-slate-700 mb-1">Image du métier</label>
              <input type="file" ref="fileInput" @change="handleFileChange" accept="image/jpeg,image/png,image/webp"
                     class="w-full text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-orange-50 file:text-avenir-orange hover:file:bg-orange-100 transition-colors" />
            </div>

            <div class="flex justify-end space-x-3 pt-5 border-t border-slate-100">
              <button type="button" @click="closeModal" 
                      class="px-5 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-700 font-medium hover:bg-slate-50 transition-colors focus:outline-none">
                Annuler
              </button>
              <button type="submit" :disabled="loading" 
                      class="px-5 py-2.5 btn-orange-gradient text-white rounded-xl font-bold shadow-md hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-avenir-orange disabled:opacity-50 flex items-center">
                <span v-if="loading" class="animate-spin mr-2 h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                {{ isEditing ? 'Mettre à jour' : 'Créer le métier' }}
              </button>
            </div>
            
          </form>
        </div>
      </div>
    </div>

    <!-- Question Modal -->
    <div v-if="questionModalVisible" class="fixed inset-0 z-[110] overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="questionModalVisible = false"></div>
        <div class="relative bg-white rounded-2xl shadow-2xl max-w-lg w-full p-8 border border-slate-100">
          <h3 class="text-xl font-bold text-avenir-blue mb-6">{{ isEditingQuestion ? 'Éditer la question' : 'Nouvelle question' }}</h3>
          <form @submit.prevent="saveQuestion" class="space-y-4">
            <div>
              <label class="block text-sm font-bold text-slate-700 mb-1 font-inter">Texte de la question</label>
              <textarea v-model="questionForm.question_text" required rows="3" class="w-full px-4 py-2 border rounded-xl outline-none focus:ring-2 focus:ring-avenir-blue"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Profil RIASEC</label>
                <select v-model="questionForm.riasec_profile_id" class="w-full px-4 py-2 border rounded-xl">
                  <option :value="1">Réaliste (R)</option>
                  <option :value="2">Investigateur (I)</option>
                  <option :value="3">Artistique (A)</option>
                  <option :value="4">Social (S)</option>
                  <option :value="5">Entreprenant (E)</option>
                  <option :value="6">Conventionnel (C)</option>
                </select>
              </div>
              <div>
                <label class="block text-sm font-bold text-slate-700 mb-1">Ordre</label>
                <input type="number" v-model="questionForm.order" class="w-full px-4 py-2 border rounded-xl" />
              </div>
            </div>
            <div class="flex justify-end space-x-3 pt-4">
              <button type="button" @click="questionModalVisible = false" class="px-6 py-2 rounded-xl border font-medium">Annuler</button>
              <button type="submit" class="px-6 py-2 rounded-xl bg-avenir-blue text-white font-bold shadow-md">Enregistrer</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Steps Manager Modal -->
    <div v-if="stepsModalVisible" class="fixed inset-0 z-[120] overflow-y-auto">
      <div class="flex items-center justify-center min-h-screen p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="stepsModalVisible = false"></div>
        <div class="relative bg-white rounded-3xl shadow-2xl max-w-4xl w-full flex flex-col md:flex-row h-[80vh] overflow-hidden border border-slate-100">
          
          <!-- Left: Add Step Form -->
          <div class="w-full md:w-1/3 p-8 border-r border-slate-100 bg-slate-50/50">
            <h3 class="text-xl font-bold text-avenir-blue mb-2">Ajouter une étape</h3>
            <p class="text-xs text-slate-500 mb-6">Pour le métier : {{ selectedJobForSteps?.title }}</p>
            
            <form @submit.prevent="saveStep" class="space-y-4">
              <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Titre de l'étape</label>
                <input type="text" v-model="stepForm.title" required class="w-full px-3 py-2 border rounded-lg" placeholder="ex: Bac S / STI2D" />
              </div>
              <div>
                <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Description</label>
                <textarea v-model="stepForm.description" rows="3" class="w-full px-3 py-2 border rounded-lg" placeholder="Détails sur la formation..."></textarea>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div>
                  <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Durée</label>
                  <input type="text" v-model="stepForm.duration" class="w-full px-3 py-2 border rounded-lg" placeholder="ex: 2 ans" />
                </div>
                <div>
                  <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Ordre</label>
                  <input type="number" v-model="stepForm.order" class="w-full px-3 py-2 border rounded-lg" />
                </div>
              </div>
              <button type="submit" class="w-full py-3 btn-orange-gradient text-white rounded-xl font-bold shadow-md mt-4 transition-all hover:scale-[1.02]">
                Ajouter au parcours
              </button>
            </form>
          </div>

          <!-- Right: Timeline Visualization -->
          <div class="flex-grow p-8 overflow-y-auto bg-white">
            <div class="flex justify-between items-center mb-8">
              <h3 class="text-xl font-bold text-slate-800">Timeline du Parcours</h3>
              <button @click="stepsModalVisible = false" class="text-slate-400 hover:text-slate-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
              </button>
            </div>

            <div v-if="selectedJobForSteps?.steps?.length === 0" class="flex flex-col items-center justify-center h-64 text-slate-400 border-2 border-dashed rounded-3xl">
              <p>Aucune étape définie pour ce métier.</p>
            </div>
            
            <div v-else class="space-y-6 relative before:absolute before:inset-0 before:ml-5 before:-translate-x-px before:h-full before:w-0.5 before:bg-gradient-to-b before:from-avenir-blue before:to-transparent">
              <div v-for="(step, idx) in selectedJobForSteps?.steps" :key="step.id" class="relative flex items-center justify-between group">
                <div class="flex items-center w-full">
                  <div class="flex items-center justify-center w-10 h-10 rounded-full bg-avenir-blue border-4 border-white shadow shrink-0 z-10 transition-transform group-hover:scale-110">
                    <span class="text-white text-xs font-bold">{{ idx + 1 }}</span>
                  </div>
                  <div class="flex-grow ml-6 bg-white p-4 rounded-2xl border border-slate-100 shadow-sm transition-all group-hover:shadow-md group-hover:border-avenir-blue/30">
                    <div class="flex justify-between items-start">
                      <div>
                        <h4 class="font-bold text-avenir-blue">{{ step.title }}</h4>
                        <p class="text-sm text-slate-500 line-clamp-2">{{ step.description }}</p>
                        <span class="inline-block mt-2 text-[10px] font-bold uppercase tracking-wider text-avenir-orange">{{ step.duration }}</span>
                      </div>
                      <button @click="destroyStep(step.id)" class="text-slate-300 hover:text-red-500 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


  </div>
</template>

<style scoped>
.btn-orange-gradient {
  background: linear-gradient(135deg, #FF7E5F, #FEB47B);
}
.btn-orange-gradient:hover {
  background: linear-gradient(135deg, #FF6A45, #FEA05C);
}
</style>
