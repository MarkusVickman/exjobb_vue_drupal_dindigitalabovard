import { createRouter, createWebHistory } from 'vue-router'
import StartView from '../views/StartView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'start',
      component: StartView,
      meta: {
        requiresAuth: false, // Indikerar att den inte behöver kontrolleras med requireAuth
      },
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../views/LoginView.vue'),
      meta: {
        requiresAuth: false, // Indikerar att den inte behöver kontrolleras med requireAuth
      },
    },
    {
      path: '/tenant',
      name: 'tenant',
      component: () => import('../views/TenantView.vue'),
      meta: {
        requiresAuth: false, // Indikerar att den inte behöver kontrolleras med requireAuth
      },
    },
    {
      path: '/realestate',
      name: 'home',
      component: () => import('../views/HomeView.vue'),
      meta: {
        requiresAuth: true, // Indikerar att den inte behöver kontrolleras med requireAuth
      },
    },
    {
      path: '/invoice',
      name: 'invoice',
      component: () => import('../views/InvoiceView.vue'),
      meta: {
        requiresAuth: true, // Indikerar att den inte behöver kontrolleras med requireAuth
      },
    },
    {
      path: '/reports',
      name: 'reports',
      component: () => import('../views/ErrorReportView.vue'),
      meta: {
        requiresAuth: true, // Indikerar att den inte behöver kontrolleras med requireAuth
      },
    },
    {
      path: '/information',
      name: 'information',
      component: () => import('../views/InformationView.vue'),
      meta: {
        requiresAuth: true, // Indikerar att den inte behöver kontrolleras med requireAuth
      },
    },
    {
      path: '/accessibility',
      name: 'accessibility',
      component: () => import('../views/AccessibilityStatmentView.vue'),
      meta: {
        requiresAuth: false, // Indikerar att den inte behöver kontrolleras med requireAuth
      },
    },
    {
      path: '/privacypolicy',
      name: 'privacypolicy',
      component: () => import('../views/PrivacyPolicy.vue'),
      meta: {
        requiresAuth: false, // Indikerar att den inte behöver kontrolleras med requireAuth
      },
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
      meta: {
        requiresAuth: false, // Indikerar att den inte behöver kontrolleras med requireAuth
      },
    },
  ],
})

//Innen dirigering testas om användaren har en token (vidare kontroll görs i headern)
router.beforeEach((to, from, next) => {
  if (to.meta.requiresAuth) {
    const token = localStorage.getItem('access_token')
    if (token) {
      next()
    } else {
      localStorage.removeItem('access_token')
      next('/login')
    }
  } else {
    // För icke skyddade routes ges access
    next()
  }
})

export default router
