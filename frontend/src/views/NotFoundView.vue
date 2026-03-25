<template>
  <div class="min-h-screen bg-[#0A0F24] flex items-center justify-center relative overflow-hidden px-4">
    <!-- Background Glows -->
    <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-avenir-orange/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-1/4 right-1/4 w-96 h-96 bg-avenir-blue/30 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="text-center relative z-10 max-w-lg">
      <h1 class="text-9xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-avenir-orange to-orange-400 mb-4">404</h1>
      <h2 class="text-3xl font-bold text-white mb-6">Page introuvable</h2>
      <p class="text-slate-400 text-lg mb-10">
        Il semblerait que vous vous soyez égaré de votre trajectoire. Ne vous inquiétez pas, l'orientation, c'est fait pour ça.
      </p>

      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <router-link
          to="/"
          class="px-8 py-4 bg-gradient-to-r from-avenir-orange to-orange-500 rounded-xl font-bold text-white shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:-translate-y-1 transition-all duration-300"
        >
          Retour à l'accueil
        </router-link>
        <router-link
          to="/metiers"
          class="px-8 py-4 bg-white/5 backdrop-blur-sm border border-white/10 rounded-xl font-bold text-white hover:bg-white/10 transition-colors"
        >
          Découvrir les métiers
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
</script>
