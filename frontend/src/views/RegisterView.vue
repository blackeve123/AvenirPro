<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const loading = ref(false)
const errorMessage = ref('')

const handleRegister = async () => {
  if (password.value !== passwordConfirmation.value) {
    errorMessage.value = "Les mots de passe ne correspondent pas."
    return
  }

  loading.value = true
  errorMessage.value = ''
  
  try {
    await authStore.registerUser({
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value
    })
    router.push('/test') // Suggesting test after registration
  } catch (error) {
    if (error.response && error.response.data && error.response.data.errors) {
      const errors = error.response.data.errors
      errorMessage.value = Object.values(errors).flat().join(' ')
    } else {
      errorMessage.value = "Erreur lors de l'inscription."
    }
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <div class="min-h-screen bg-slate-50 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full backdrop-blur-sm bg-white/80 p-8 rounded-2xl shadow-xl border border-white">
      <div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-avenir-blue">
          Créer un compte
        </h2>
        <p class="mt-2 text-center text-sm text-slate-600">
          Vous avez déjà un compte ?
          <router-link to="/login" class="font-medium text-avenir-orange hover:text-orange-500 transition-colors">
            Connectez-vous
          </router-link>
        </p>
      </div>
      
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div v-if="errorMessage" class="bg-red-50 text-red-500 p-3 rounded-lg text-sm text-center">
          {{ errorMessage }}
        </div>
        
        <div class="rounded-md shadow-sm space-y-4">
          <div>
            <label for="name" class="sr-only">Nom complet</label>
            <input id="name" name="name" type="text" required v-model="name"
              class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-avenir-orange focus:border-avenir-orange sm:text-sm"
              placeholder="Nom complet">
          </div>
          <div>
            <label for="email-address" class="sr-only">Email</label>
            <input id="email-address" name="email" type="email" autocomplete="email" required v-model="email"
              class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-avenir-orange focus:border-avenir-orange sm:text-sm"
              placeholder="Adresse email">
          </div>
          <div>
            <label for="password" class="sr-only">Mot de passe</label>
            <input id="password" name="password" type="password" required v-model="password"
              class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-avenir-orange focus:border-avenir-orange sm:text-sm"
              placeholder="Mot de passe">
          </div>
          <div>
            <label for="password-confirmation" class="sr-only">Confirmez le mot de passe</label>
            <input id="password-confirmation" name="password_confirmation" type="password" required v-model="passwordConfirmation"
              class="appearance-none rounded-lg relative block w-full px-4 py-3 border border-slate-300 placeholder-slate-400 text-slate-900 focus:outline-none focus:ring-avenir-orange focus:border-avenir-orange sm:text-sm"
              placeholder="Confirmez le mot de passe">
          </div>
        </div>

        <div>
          <button type="submit" :disabled="loading"
            class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-avenir-orange transition-all disabled:opacity-50 shadow-md hover:shadow-lg">
            <span v-if="loading" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Inscription...
            </span>
            <span v-else>S'inscrire</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
