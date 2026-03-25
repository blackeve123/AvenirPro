<template>
<section class="hero-gradient pt-16 pb-24 overflow-hidden">

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

<div class="flex flex-col lg:flex-row items-center gap-12">

<div class="flex-1 text-center lg:text-left space-y-6">

<h1 class="text-4xl md:text-6xl font-extrabold text-avenir-blue leading-tight">
  AvenirPro : Ton avenir,
  <br>
  <span class="text-transparent bg-clip-text bg-gradient-to-r from-avenir-orange to-orange-400">
    avec clarté
  </span>
</h1>

<p class="text-lg text-slate-600 max-w-2xl">
 Trouve ta voie, choisis ton métier, construis ton futur. 
</p>

<div class="flex gap-4">

<router-link to="/test" class="btn-orange-gradient text-white px-8 py-4 rounded-full font-bold inline-block text-center flex-1 sm:flex-none">
Lancer mon test
</router-link>

<router-link to="/metiers" class="bg-white text-slate-700 border px-8 py-4 rounded-full font-bold inline-block text-center flex-1 sm:flex-none hover:bg-slate-50 transition-colors">
En savoir plus
</router-link>

</div>

</div>

<div class="flex-1">
<img
class="rounded-3xl shadow-2xl"
src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f"
/>
</div>

</div>
</div>
</section>
</template>