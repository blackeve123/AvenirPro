import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue'
import router from './router'
import Toast from "vue-toastification"
import "vue-toastification/dist/index.css"

const pinia = createPinia()
const app = createApp(App)

app.use(pinia)
app.use(router)
app.use(Toast, {
  position: "top-right",
  timeout: 4000
})

app.mount('#app')
