<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const isAuthenticated = computed(() => authStore.isAuthenticated)
const isAdmin = computed(() => authStore.isAdmin)

const handleLogout = async () => {
  await authStore.logoutUser()
  router.push('/login')
}
</script>

<template>
  <header class="sticky top-0 z-50 bg-white/90 backdrop-blur-md border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center h-20">
        
        <router-link to="/" class="flex items-center space-x-2">
          <span class="text-2xl font-bold tracking-tight">
            <span class="text-avenir-blue">Avenir</span>
            <span class="text-avenir-orange">Pro</span>
          </span>
        </router-link>

        <nav class="hidden md:flex space-x-8 items-center">
          <router-link to="/metiers" class="text-slate-600 hover:text-avenir-blue font-medium transition-colors">Métiers</router-link>
          <router-link to="/test" class="text-slate-600 hover:text-avenir-blue font-medium transition-colors">Test d'orientation</router-link>
          
          <router-link v-if="isAdmin" to="/admin" class="text-purple-600 hover:text-purple-800 font-medium transition-colors">Admin Dashboard</router-link>

          <template v-if="isAuthenticated">
            <router-link to="/profile" class="text-slate-500 text-sm font-medium hover:text-avenir-blue transition-colors underline-offset-4 hover:underline">
              Hello, {{ authStore.user?.name }}
            </router-link>
            <button @click="handleLogout" class="text-red-500 font-medium hover:text-red-700 transition-colors">
              Déconnexion
            </button>
          </template>

          <template v-else>
            <router-link to="/login" class="text-slate-600 hover:text-avenir-blue font-medium transition-colors">Se connecter</router-link>
            <router-link to="/register" class="bg-gradient-to-r from-orange-400 to-orange-500 hover:from-orange-500 hover:to-orange-600 text-white px-6 py-2.5 rounded-full font-semibold shadow-md transition-all hover:shadow-lg transform hover:-translate-y-0.5">
              S'inscrire
            </router-link>
          </template>
        </nav>

      </div>
    </div>
  </header>
</template>