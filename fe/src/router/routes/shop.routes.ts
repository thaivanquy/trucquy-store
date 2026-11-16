import type { RouteRecordRaw } from 'vue-router'

export const shopRoutes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('@/layouts/ShopLayout.vue'),
    children: [
      {
        path: '',
        name: 'shop-home',
        component: () => import('@/features/shop/home/views/HomeView.vue'),
        meta: {
          title: 'Trang chủ - TrucQuy Store',
        },
      },
    ],
  },
]

