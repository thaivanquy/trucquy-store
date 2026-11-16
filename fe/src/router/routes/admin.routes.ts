import type { RouteRecordRaw } from 'vue-router'

export const adminRoutes: RouteRecordRaw[] = [
  {
    path: '/admin/login',
    name: 'admin-login',
    component: () => import('@/features/admin/auth/views/LoginView.vue'),
    meta: {
      title: 'Đăng nhập Quản trị',
    },
  },
  {
    path: '/admin',
    component: () => import('@/layouts/AdminLayout.vue'),
    children: [
      {
        path: '',
        redirect: { name: 'admin-dashboard' },
      },
      {
        path: 'dashboard',
        name: 'admin-dashboard',
        component: () => import('@/features/admin/dashboard/views/DashboardView.vue'),
        meta: {
          title: 'Tổng quan quản trị',
          requiresAuth: true,
        },
      },
    ],
  },
]

