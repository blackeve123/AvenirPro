import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const HomeView = () => import('../views/HomeView.vue')
const MetiersView = () => import('../views/MetiersView.vue')
const MetierDetail = () => import('../views/MetierDetail.vue')
const TestOrientation = () => import('../views/TestOrientation.vue')
const LoginView = () => import('../views/LoginView.vue')
const RegisterView = () => import('../views/RegisterView.vue')
const ResultView = () => import('../views/ResultView.vue')
const ProfileView = () => import('../views/ProfileView.vue')
const AdminDashboard = () => import('../views/AdminDashboard.vue')
const NotFoundView = () => import('../views/NotFoundView.vue')

const routes = [
  { path: '/', name: 'home', component: HomeView },
  { path: '/metiers', name: 'metiers', component: MetiersView },
  { path: '/metiers/:id', name: 'metier-detail', component: MetierDetail },
  { path: '/test', name: 'test', component: TestOrientation, meta: { requiresAuth: true } },
  { path: '/results', name: 'results', component: ResultView, meta: { requiresAuth: true } },
  { path: '/profile', name: 'profile', component: ProfileView, meta: { requiresAuth: true } },
  { path: '/login', name: 'login', component: LoginView, meta: { guest: true } },
  { path: '/register', name: 'register', component: RegisterView, meta: { guest: true } },
  { path: '/admin', name: 'admin', component: AdminDashboard, meta: { requiresAuth: true, requiresAdmin: true } },
  { path: '/:pathMatch(.*)*', name: 'not-found', component: NotFoundView }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

router.beforeEach(async (to, from) => {
  const authStore = useAuthStore()

  // Ensure user is fetched if we are marked as logged in but have no user data
  if (authStore.isLoggedIn && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (e) {
      // Session probably expired on server
      authStore.clearAuth()
      if (to.meta.requiresAuth) return { name: 'login', query: { redirect: to.fullPath } }
    }
  }


  const isAuthenticated = authStore.isAuthenticated
  const isAdmin = authStore.isAdmin

  if (to.meta.requiresAuth && !authStore.isAuthenticated) {
    return { name: 'login', query: { redirect: to.fullPath } }
  } 
  
  if (to.meta.guest && isAuthenticated) {
    return { name: 'home' }
  } 
  
  if (to.meta.requiresAdmin && !isAdmin) {
    return { name: 'home' } // or unauthorized page
  }

  return true // allow navigation
})

export default router