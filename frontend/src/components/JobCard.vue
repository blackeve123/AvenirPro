<script setup>

defineProps({
  job: Object
})

</script>


<template>

<article
class="group bg-avenir-gray rounded-2xl overflow-hidden border border-slate-100 hover:shadow-xl transition-all"
>

  <div class="h-48 overflow-hidden">

    <img
      :src="job.image_url"
      :alt="job.title"
      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
    />

  </div>

  <div class="p-6">

    <span class="text-xs font-bold text-avenir-orange uppercase tracking-widest">
      {{ job.category }}
    </span>

    <h3 class="text-xl font-bold text-avenir-blue mt-2">
      {{ job.title }}
    </h3>

    <p class="text-slate-600 mt-3 text-sm leading-relaxed">
      {{ job.description }}
    </p>

    <div
      class="mt-6 w-full text-center py-3 px-4 border-2 border-avenir-blue text-avenir-blue font-bold rounded-lg group-hover:bg-avenir-blue group-hover:text-white transition-all"
    >
      Voir parcours
    </div>

  </div>

</article>

</template>