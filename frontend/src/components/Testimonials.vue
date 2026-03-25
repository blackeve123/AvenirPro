<template>
  <section class="py-24 relative overflow-hidden bg-[#0A0F24]">
    <!-- Background glowing accents -->
    <div class="absolute top-0 left-1/4 w-96 h-96 bg-avenir-orange/20 rounded-full blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-avenir-blue/30 rounded-full blur-[120px] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
      
      <!-- Emotional Header -->
      <div class="text-center max-w-3xl mx-auto mb-20 space-y-6">
        <h2 class="text-4xl md:text-6xl font-extrabold text-white tracking-tight leading-tight">
          De l'incertitude à <span class="text-transparent bg-clip-text bg-gradient-to-r from-avenir-orange to-orange-400">l'évidence</span>
        </h2>
        <p class="text-lg md:text-xl text-slate-400 font-light">
          Ils étaient perdus, angoissés par l'avenir, paralysés par les attentes des autres.<br class="hidden md:block"/>
          Découvrez comment un simple test a transformé leur vie.
        </p>
      </div>

      <!-- Testimonials Grid -->
      <div class="grid md:grid-cols-3 gap-8 lg:gap-12">
        <div
          v-for="(item, index) in testimonials"
          :key="index"
          class="relative bg-white/5 backdrop-blur-xl border border-white/10 p-8 rounded-3xl hover:-translate-y-2 transition-transform duration-500 flex flex-col h-full"
        >
          <!-- Quote Icon -->
          <svg class="absolute top-6 right-6 w-12 h-12 text-white/10" fill="currentColor" viewBox="0 0 24 24">
            <path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z" />
          </svg>

          <!-- The "Before" state - The Pain -->
          <div class="mb-6">
            <p class="text-slate-400 italic text-sm mb-2 font-medium">Avant AvenirPro :</p>
            <p class="text-slate-300 leading-relaxed">"{{ item.before }}"</p>
          </div>

          <!-- Divider -->
          <div class="w-full h-px bg-gradient-to-r from-transparent via-white/20 to-transparent my-6"></div>

          <!-- The "After" state - The Relief -->
          <div class="flex-grow mb-8">
            <p class="text-avenir-orange font-medium text-sm mb-2">Aujourd'hui :</p>
            <p class="text-white text-lg font-medium leading-relaxed">"{{ item.after }}"</p>
          </div>

          <!-- Profile -->
          <div class="flex items-center gap-4 mt-auto">
            <div class="relative">
              <img :src="item.image" :alt="item.name" class="w-14 h-14 rounded-full object-cover border-2 border-avenir-orange/50" />
              <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-500 rounded-full border-2 border-[#0A0F24]"></div>
            </div>
            <div>
              <h4 class="text-white font-bold">{{ item.name }}, {{ item.age }} ans</h4>
              <p class="text-slate-400 text-sm">{{ item.vocation }}</p>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>
</template>

<script setup>
const testimonials = [
  {
    name: "Lucas",
    age: 18,
    vocation: "Étudiant en Architecture (Profil Artistique/Réaliste)",
    before: "Je pleurais presque devant Parcoursup. Mes parents voulaient que je fasse médecine, mais je détestais l'idée. J'avais l'impression de gâcher ma vie avant même de l'avoir commencée.",
    after: "Le test a révélé un profil créatif et manuel que je refoulais. Quand j'ai vu « Architecte » avec 92% de compatibilité, un poids immense a disparu de mes épaules. C'était une évidence.",
    image: "https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=400&h=400&fit=crop"
  },
  {
    name: "Sarah",
    age: 21,
    vocation: "Data Analyst (Profil Investigateur/Conventionnel)",
    before: "J'ai abandonné deux licences en 3 ans. Je me sentais nulle, bonne à rien. Je pensais que j'étais juste incapable de trouver ma place dans la société.",
    after: "AvenirPro ne m'a pas seulement donné un métier, il m'a redonné confiance. L'IA m'a expliqué POURQUOI j'étais faite pour l'analyse de données. Aujourd'hui, je suis enfin à ma place.",
    image: "https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=400&h=400&fit=crop"
  },
  {
    name: "Maxime",
    age: 17,
    vocation: "Éducateur Spécialisé (Profil Social)",
    before: "Au lycée, on ne valorise que les maths. Moi, j'aimais juste écouter les autres. On me disait que « gentil » n'était pas un métier. J'étais totalement perdu pour mon orientation.",
    after: "La révélation. Le rapport RIASEC m'a montré que mon empathie était ma plus grande force professionnelle, pas une faiblesse. J'ai enfin arrêté d'avoir peur de l'avenir.",
    image: "https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=400&h=400&fit=crop"
  }
]
</script>