<script setup>
import { onMounted } from 'vue'
import { RouterView } from 'vue-router'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()

onMounted(async () => {
  if (authStore.token) {
    try {
      await authStore.fetchUser()
    } catch (e) {
      console.error('Failed to restore session:', e)
    }
  }
})
</script>

<template>
  <router-view />
</template>

<style>
body{
  font-family: 'Inter', sans-serif;
}
</style>