<template>
  <footer class="bg-white border-t py-16">
    <div class="max-w-7xl mx-auto px-4">
      <div class="grid md:grid-cols-3 gap-12">
        
        <!-- Logo & Description -->
        <div>
          <span class="text-2xl font-bold">
            <span class="text-avenir-blue">Avenir</span>
            <span class="text-avenir-orange">Pro</span>
          </span>
          <p class="text-slate-500 mt-4 text-sm leading-relaxed">
            La plateforme d'orientation intelligente pour découvrir la carrière qui vous correspond vraiment, basée sur le modèle RIASEC.
          </p>
        </div>

        <!-- Liens Rapides -->
        <div>
          <h4 class="font-bold text-avenir-blue mb-4 uppercase tracking-wider text-sm">
            Découvrir
          </h4>
          <ul class="space-y-3 text-slate-500 text-sm">
            <li>
              <router-link to="/metiers" class="hover:text-avenir-orange transition-colors">Tous les métiers</router-link>
            </li>
            <li>
              <router-link to="/test" class="hover:text-avenir-orange transition-colors">Test d'orientation</router-link>
            </li>
          </ul>
        </div>

        <!-- Contact & Support -->
        <div>
          <h4 class="font-bold text-avenir-blue mb-4 uppercase tracking-wider text-sm">
            Contact & Support
          </h4>
          <ul class="space-y-3 text-slate-500 text-sm">
            <li>
              <a href="https://wa.me/22892380316" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 hover:text-green-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.898-4.45 9.896-9.898-.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.735-.968zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.347-.272.297-1.04 1.016-1.04 2.479 0 1.463 1.065 2.876 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/>
                </svg>
                WhatsApp : +228 92 38 03 16
              </a>
            </li>
            <li>
              <a href="mailto:contact@avenirpro.com?subject=Commentaire" class="hover:text-avenir-orange transition-colors">
                Laissez-nous un commentaire
              </a>
            </li>
            <li>
              <router-link to="/contact" class="hover:text-avenir-orange transition-colors">Support & FAQ</router-link>
            </li>
          </ul>
        </div>

      </div>
      
      <div class="mt-16 pt-8 border-t border-slate-100 text-center text-slate-400 text-sm">
        &copy; {{ new Date().getFullYear() }} AvenirPro. Tous droits réservés.
      </div>
    </div>
  </footer>
</template>

<script setup>
</script>