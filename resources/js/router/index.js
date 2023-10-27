import { setupLayouts } from 'virtual:generated-layouts'
import { createRouter, createWebHistory } from 'vue-router'
import routes from '~pages'
import { isUserLoggedIn } from './utils'


/**
 * Get APP_ENV from .env file
 */
const path = import.meta.env.VITE_APP_ENV === 'production' ? '/' : import.meta.env.BASE_URL

const router = createRouter({
  history: createWebHistory(path),
  routes: [
    ...setupLayouts(routes),
  ],
})

router.beforeEach(async (to, from, next) => {
  const toPath = to.path
  const isLoggedIn = await isUserLoggedIn()

  if (!isLoggedIn && toPath !== '/login') {
    next({ path: '/login' })
  } else if (isLoggedIn && toPath === '/login') {
    next({ path: '/dashboard' })
  } else {
    next()
  }
})


// Docs: https://router.vuejs.org/guide/advanced/navigation-guards.html#global-before-guards
export default router
