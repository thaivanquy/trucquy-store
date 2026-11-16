import { createRouter, createWebHistory } from 'vue-router'
import { adminRoutes } from './routes/admin.routes'
import { shopRoutes } from './routes/shop.routes'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    ...shopRoutes,
    ...adminRoutes,
    {
      path: '/login',
      redirect: '/admin/login',
    },
  ],
})

router.beforeEach((to, _from, next) => {
  if (to.meta?.title) {
    document.title = `${to.meta.title} - TrucQuy Store`
  }
  next()
})

export default router
