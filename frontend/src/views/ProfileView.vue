<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'
import { updateProfile as apiUpdateProfile } from '../services/auth'
import { useToast } from 'vue-toastification'
import Navbar from '../components/Navbar.vue'
import Footer from '../components/Footer.vue'

const authStore = useAuthStore()
const router = useRouter()
const toast = useToast()

const name = ref(authStore.user?.name || '')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)

const handleUpdate = async () => {
  if (password.value && password.value !== passwordConfirmation.value) {
    toast.error("Les mots de passe ne correspondent pas.")
    return
  }

  loading.value = true
  try {
    const payload = { name: name.value }
    if (password.value) {
      payload.password = password.value
      payload.password_confirmation = passwordConfirmation.value
    }
    
    const res = await apiUpdateProfile(payload)
    // Update local store user
    authStore.user = res.data.data
    toast.success("Profil mis à jour avec succès !")
    
    // Clear passwords
    password.value = ''
    passwordConfirmation.value = ''
  } catch (error) {
    toast.error(error.response?.data?.message || "Erreur lors de la mise à jour.")
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50">
    <Navbar />
    
    <main class="flex-grow container mx-auto px-4 py-12 flex justify-center items-center">
      <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 w-full max-w-md">
        <div class="text-center mb-8">
          <div class="w-20 h-20 bg-avenir-blue/10 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-avenir-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <h1 class="text-2xl font-bold text-slate-800">Mon Compte</h1>
          <p class="text-slate-500">Mettez à jour vos informations personnelles</p>
        </div>

        <form @submit.prevent="handleUpdate" class="space-y-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Nom complet</label>
            <input v-model="name" type="text" required
              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all" />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">
              Nouveau mot de passe <span class="text-slate-400 font-normal">(Optionnel)</span>
            </label>
            <input v-model="password" type="password" placeholder="Laisser vide pour ne pas changer"
              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all" />
          </div>

          <div v-if="password">
            <label class="block text-sm font-medium text-slate-700 mb-2">Confirmer le mot de passe</label>
            <input v-model="passwordConfirmation" type="password" required
              class="w-full px-4 py-3 rounded-lg border border-slate-300 focus:ring-2 focus:ring-avenir-orange focus:border-avenir-orange outline-none transition-all" />
          </div>

          <button type="submit" :disabled="loading"
            class="w-full py-3 px-4 bg-gradient-to-r from-avenir-blue to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-bold shadow-md shadow-blue-500/25 transition-all outline-none flex justify-center items-center">
            <svg v-if="loading" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span v-else>Mettre à jour le profil</span>
          </button>
        </form>
      </div>
    </main>
    
    <Footer />
  </div>
</template>
