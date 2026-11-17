<script setup lang="ts">
import { ref, computed, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'
import {
  DashboardOutlined,
  ShoppingCartOutlined,
  ShoppingOutlined,
  AppstoreOutlined,
  TagsOutlined,
  InboxOutlined,
  TeamOutlined,
  ShopOutlined,
  BarChartOutlined,
  SettingOutlined,
  MenuFoldOutlined,
  MenuUnfoldOutlined,
  MenuOutlined,
  LogoutOutlined,
  UserOutlined,
  BellOutlined,
  ExportOutlined,
  DownOutlined,
  CheckCircleOutlined,
  WarningOutlined,
  CloseOutlined,
  DeploymentUnitOutlined,
} from '@ant-design/icons-vue'
import LanguageSwitcher from '@/components/common/LanguageSwitcher.vue'
import AppFooter from '@/components/common/AppFooter.vue'
import { useI18n } from '@/i18n'
import { useAuthStore } from '@/features/admin/auth/stores/authStore'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const route = useRoute()
const authStore = useAuthStore()
const toast = useToast()
const { t } = useI18n()

const breakpoints = useBreakpoints(breakpointsTailwind)
const isMobile = breakpoints.smaller('md')
const isTablet = breakpoints.between('md', 'lg')

const collapsed = ref<boolean>(breakpoints.smaller('lg').value)
const mobileDrawerOpen = ref<boolean>(false)

const selectedKeys = ref<string[]>([
  route.name === 'admin-products'
    ? 'products-list'
    : route.name === 'admin-categories'
    ? 'categories'
    : route.name === 'admin-units'
    ? 'units'
    : route.name === 'admin-suppliers'
    ? 'suppliers'
    : 'dashboard',
])
const openKeys = ref<string[]>(
  route.name === 'admin-products' || route.name === 'admin-categories' || route.name === 'admin-units'
    ? ['sub-inventory']
    : route.name === 'admin-suppliers'
    ? ['sub-customers']
    : [],
)

interface BreadcrumbItem {
  title: string
  to?: string
}

const breadcrumbs = computed<BreadcrumbItem[]>(() => {
  const metaCrumbs = (route.meta?.breadcrumbs || route.meta?.breadcrumb) as
    | Array<string | { title?: string; labelKey?: string; to?: string }>
    | undefined

  if (metaCrumbs && Array.isArray(metaCrumbs)) {
    return metaCrumbs.map((item) => {
      if (typeof item === 'string') {
        return {
          title: t(item),
        }
      }
      return {
        title: item.labelKey ? t(item.labelKey) : item.title || '',
        to: item.to,
      }
    })
  }

  if (route.name === 'admin-products') {
    return [
      { title: t('admin.products') },
      { title: t('admin.productsList') },
    ]
  }

  if (route.name === 'admin-categories') {
    return [
      { title: t('admin.products') },
      { title: t('admin.categories') },
    ]
  }

  if (route.name === 'admin-units') {
    return [
      { title: t('admin.products') },
      { title: t('admin.units') },
    ]
  }

  if (route.name === 'admin-suppliers') {
    return [
      { title: t('admin.customers') },
      { title: t('admin.suppliers') },
    ]
  }

  return [{ title: t('admin.dashboard') }]
})

watch(
  () => route.name,
  (name) => {
    if (name === 'admin-products') {
      selectedKeys.value = ['products-list']
      if (!collapsed.value) {
        openKeys.value = ['sub-inventory']
      }
    } else if (name === 'admin-categories') {
      selectedKeys.value = ['categories']
      if (!collapsed.value) {
        openKeys.value = ['sub-inventory']
      }
    } else if (name === 'admin-units') {
      selectedKeys.value = ['units']
      if (!collapsed.value) {
        openKeys.value = ['sub-inventory']
      }
    } else if (name === 'admin-suppliers') {
      selectedKeys.value = ['suppliers']
      if (!collapsed.value) {
        openKeys.value = ['sub-customers']
      }
    } else if (name === 'admin-dashboard') {
      selectedKeys.value = ['dashboard']
    }
  },
  { immediate: true }
)

const currentUserName = computed(() => authStore.user?.name || 'Admin TrucQuy')
const currentUserEmail = computed(() => authStore.user?.email || 'admin@trucquy.vn')

watch(isTablet, (val) => {
  if (val) {
    collapsed.value = true
  }
})

watch(isMobile, (mobile) => {
  if (!mobile) {
    mobileDrawerOpen.value = false
  }
})

const themeConfig = {
  token: {
    colorPrimary: '#0ea5e9',
    colorInfo: '#0ea5e9',
    borderRadius: 8,
    fontFamily: 'inherit',
  },
}

function handleMenuSelect({ key }: { key: string }) {
  if (isMobile.value) {
    mobileDrawerOpen.value = false
  }
  if (key === 'dashboard') {
    router.push('/admin/dashboard')
  } else if (key === 'products-list' || key === 'products') {
    router.push('/admin/products')
  } else if (key === 'categories') {
    router.push('/admin/categories')
  } else if (key === 'units') {
    router.push('/admin/units')
  } else if (key === 'suppliers') {
    router.push('/admin/suppliers')
  } else {
    toast.info(`${t('admin.overview')} - Tính năng đang phát triển`)
  }
}

function toggleSidebar() {
  if (isMobile.value) {
    mobileDrawerOpen.value = !mobileDrawerOpen.value
  } else {
    collapsed.value = !collapsed.value
  }
}

async function handleLogout() {
  await authStore.logout()
  toast.success(t('auth.logoutSuccess'))
  router.push('/admin/login')
}
</script>

<template>
  <a-config-provider :theme="themeConfig">
    <a-layout class="h-screen overflow-hidden bg-slate-100 flex flex-row">

      <a-layout-sider
        v-if="!isMobile"
        v-model:collapsed="collapsed"
        :trigger="null"
        collapsible
        :width="240"
        :collapsedWidth="72"
        class="admin-sider !bg-slate-900 shadow-xl shrink-0 z-30 h-screen flex flex-col"
      >

        <div class="h-16 shrink-0 flex items-center px-4 border-b border-slate-800 transition-all duration-300">
          <div class="flex items-center gap-3 overflow-hidden w-full" :class="{ 'justify-center': collapsed }">
            <div
              class="w-9 h-9 rounded-xl bg-gradient-to-tr from-sky-500 to-sky-400 flex items-center justify-center text-white font-black text-sm shrink-0 shadow-md shadow-sky-500/30"
            >
              TQ
            </div>
            <div v-if="!collapsed" class="flex flex-col min-w-0 transition-opacity duration-200">
              <span class="font-bold text-white tracking-tight truncate text-base leading-tight">
                {{ t('common.brandName') }}
              </span>
              <span class="text-[10px] text-sky-400 font-semibold tracking-wider uppercase mt-0.5">
                {{ t('common.adminPortal') }}
              </span>
            </div>
          </div>
        </div>

        <div class="flex-1 overflow-y-auto overflow-x-hidden py-3 admin-sider-scroll">
          <a-menu
            v-model:selectedKeys="selectedKeys"
            v-model:openKeys="openKeys"
            theme="dark"
            :mode="collapsed ? 'vertical' : 'inline'"
            class="!bg-transparent !border-r-0 admin-menu"
            @select="handleMenuSelect"
          >
            <a-menu-item key="dashboard">
              <template #icon><DashboardOutlined /></template>
              <span>{{ t('admin.dashboard') }}</span>
            </a-menu-item>

            <a-sub-menu key="sub-sales">
              <template #icon><ShoppingCartOutlined /></template>
              <template #title>{{ t('admin.orders') }}</template>
              <a-menu-item key="orders-list">
                <template #icon><ShoppingCartOutlined /></template>
                <span>{{ t('admin.orders') }}</span>
              </a-menu-item>
              <a-menu-item key="pos">
                <template #icon><ShoppingOutlined /></template>
                <span>{{ t('admin.pos') }}</span>
              </a-menu-item>
            </a-sub-menu>

            <a-sub-menu key="sub-inventory">
              <template #icon><AppstoreOutlined /></template>
              <template #title>{{ t('admin.products') }}</template>
              <a-menu-item key="products-list">
                <template #icon><AppstoreOutlined /></template>
                <span>{{ t('admin.products') }}</span>
              </a-menu-item>
              <a-menu-item key="categories">
                <template #icon><TagsOutlined /></template>
                <span>{{ t('admin.categories') }}</span>
              </a-menu-item>
              <a-menu-item key="units">
                <template #icon><DeploymentUnitOutlined /></template>
                <span>{{ t('admin.units') }}</span>
              </a-menu-item>
              <a-menu-item key="inventory">
                <template #icon><InboxOutlined /></template>
                <span>{{ t('admin.inventory') }}</span>
              </a-menu-item>
            </a-sub-menu>

            <a-sub-menu key="sub-customers">
              <template #icon><TeamOutlined /></template>
              <template #title>{{ t('admin.customers') }}</template>
              <a-menu-item key="customers-list">
                <template #icon><TeamOutlined /></template>
                <span>{{ t('admin.customers') }}</span>
              </a-menu-item>
              <a-menu-item key="suppliers">
                <template #icon><ShopOutlined /></template>
                <span>{{ t('admin.suppliers') }}</span>
              </a-menu-item>
            </a-sub-menu>

            <a-menu-item key="reports">
              <template #icon><BarChartOutlined /></template>
              <span>{{ t('admin.reports') }}</span>
            </a-menu-item>

            <a-menu-item key="settings">
              <template #icon><SettingOutlined /></template>
              <span>{{ t('admin.settings') }}</span>
            </a-menu-item>
          </a-menu>
        </div>

        <div class="p-3 border-t border-slate-800 bg-slate-900/60 shrink-0">
          <a
            href="/"
            target="_blank"
            class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-400 hover:text-sky-400 hover:bg-slate-800/80 rounded-lg transition-colors group"
            :class="{ 'justify-center': collapsed }"
            :title="t('common.viewStorefront')"
          >
            <ExportOutlined class="text-sm shrink-0 group-hover:scale-110 transition-transform" />
            <span v-if="!collapsed" class="truncate">{{ t('common.viewStorefront') }}</span>
          </a>
        </div>
      </a-layout-sider>

      <a-drawer
        v-if="isMobile"
        v-model:open="mobileDrawerOpen"
        placement="left"
        :closable="false"
        :width="260"
        :bodyStyle="{ padding: 0, backgroundColor: '#0f172a', display: 'flex', flexDirection: 'column', height: '100%' }"
      >

        <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800">
          <div class="flex items-center gap-3">
            <div
              class="w-8 h-8 rounded-xl bg-gradient-to-tr from-sky-500 to-sky-400 flex items-center justify-center text-white font-bold text-sm shadow-md shadow-sky-500/30"
            >
              TQ
            </div>
            <div>
              <span class="font-bold text-white tracking-tight text-sm block leading-tight">
                {{ t('common.brandName') }}
              </span>
              <span class="text-[10px] text-sky-400 font-semibold tracking-wider uppercase">
                {{ t('common.adminPortal') }}
              </span>
            </div>
          </div>
          <button
            type="button"
            @click="mobileDrawerOpen = false"
            class="w-8 h-8 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 flex items-center justify-center cursor-pointer"
          >
            <CloseOutlined class="text-sm" />
          </button>
        </div>

        <div class="flex-1 overflow-y-auto py-3">
          <a-menu
            v-model:selectedKeys="selectedKeys"
            v-model:openKeys="openKeys"
            theme="dark"
            mode="inline"
            class="!bg-transparent !border-r-0 admin-menu"
            @select="handleMenuSelect"
          >
            <a-menu-item key="dashboard">
              <template #icon><DashboardOutlined /></template>
              <span>{{ t('admin.dashboard') }}</span>
            </a-menu-item>

            <a-sub-menu key="sub-sales">
              <template #icon><ShoppingCartOutlined /></template>
              <template #title>{{ t('admin.orders') }}</template>
              <a-menu-item key="orders-list">
                <template #icon><ShoppingCartOutlined /></template>
                <span>{{ t('admin.orders') }}</span>
              </a-menu-item>
              <a-menu-item key="pos">
                <template #icon><ShoppingOutlined /></template>
                <span>{{ t('admin.pos') }}</span>
              </a-menu-item>
            </a-sub-menu>

            <a-sub-menu key="sub-inventory">
              <template #icon><AppstoreOutlined /></template>
              <template #title>{{ t('admin.products') }}</template>
              <a-menu-item key="products-list">
                <template #icon><AppstoreOutlined /></template>
                <span>{{ t('admin.products') }}</span>
              </a-menu-item>
              <a-menu-item key="categories">
                <template #icon><TagsOutlined /></template>
                <span>{{ t('admin.categories') }}</span>
              </a-menu-item>
              <a-menu-item key="units">
                <template #icon><DeploymentUnitOutlined /></template>
                <span>{{ t('admin.units') }}</span>
              </a-menu-item>
              <a-menu-item key="inventory">
                <template #icon><InboxOutlined /></template>
                <span>{{ t('admin.inventory') }}</span>
              </a-menu-item>
            </a-sub-menu>

            <a-sub-menu key="sub-customers">
              <template #icon><TeamOutlined /></template>
              <template #title>{{ t('admin.customers') }}</template>
              <a-menu-item key="customers-list">
                <template #icon><TeamOutlined /></template>
                <span>{{ t('admin.customers') }}</span>
              </a-menu-item>
              <a-menu-item key="suppliers">
                <template #icon><ShopOutlined /></template>
                <span>{{ t('admin.suppliers') }}</span>
              </a-menu-item>
            </a-sub-menu>

            <a-menu-item key="reports">
              <template #icon><BarChartOutlined /></template>
              <span>{{ t('admin.reports') }}</span>
            </a-menu-item>

            <a-menu-item key="settings">
              <template #icon><SettingOutlined /></template>
              <span>{{ t('admin.settings') }}</span>
            </a-menu-item>
          </a-menu>
        </div>

        <div class="p-4 border-t border-slate-800 bg-slate-900/80">
          <a
            href="/"
            target="_blank"
            class="flex items-center gap-2.5 px-3 py-2 text-xs text-slate-400 hover:text-sky-400 hover:bg-slate-800 rounded-lg transition-colors"
          >
            <ExportOutlined class="text-sm shrink-0" />
            <span>{{ t('common.viewStorefront') }}</span>
          </a>
        </div>
      </a-drawer>

      <a-layout class="bg-slate-100 flex flex-col flex-1 min-w-0 min-h-0 h-screen overflow-hidden">

        <a-layout-header
          class="!bg-white !px-3 sm:!px-6 !h-16 shrink-0 flex items-center justify-between border-b border-slate-200/80 z-20 shadow-xs admin-header leading-normal"
        >

          <div class="flex items-center gap-2.5 sm:gap-4">
            <button
              type="button"
              class="w-9 h-9 flex items-center justify-center rounded-lg text-slate-600 hover:text-sky-600 hover:bg-slate-100 transition-colors cursor-pointer shrink-0"
              @click="toggleSidebar"
              :aria-label="isMobile ? 'Open mobile menu' : collapsed ? 'Expand menu' : 'Collapse menu'"
            >
              <MenuOutlined v-if="isMobile" class="text-lg" />
              <MenuUnfoldOutlined v-else-if="collapsed" class="text-lg" />
              <MenuFoldOutlined v-else class="text-lg" />
            </button>

            <div v-if="isMobile" class="flex items-center gap-2">
              <div
                class="w-7 h-7 rounded-lg bg-gradient-to-tr from-sky-500 to-sky-400 flex items-center justify-center text-white font-bold text-xs shadow-xs"
              >
                TQ
              </div>
              <span class="font-bold text-slate-800 text-sm tracking-tight truncate">
                {{ t('common.brandName') }}
              </span>
            </div>

            <a-breadcrumb class="hidden md:inline-flex text-xs font-medium leading-normal">
              <a-breadcrumb-item>
                <router-link to="/admin/dashboard" class="text-slate-500 hover:text-sky-600">
                  {{ t('admin.systemManagement') }}
                </router-link>
              </a-breadcrumb-item>
              <a-breadcrumb-item
                v-for="(item, index) in breadcrumbs"
                :key="index"
              >
                <router-link
                  v-if="item.to && index < breadcrumbs.length - 1"
                  :to="item.to"
                  class="text-slate-500 hover:text-sky-600"
                >
                  {{ item.title }}
                </router-link>
                <span
                  v-else
                  :class="index === breadcrumbs.length - 1 ? 'text-slate-800 font-semibold' : 'text-slate-500'"
                >
                  {{ item.title }}
                </span>
              </a-breadcrumb-item>
            </a-breadcrumb>
          </div>

          <div class="flex items-center gap-2 sm:gap-3 leading-normal">

            <a-popover placement="bottomRight" trigger="click">
              <template #title>
                <div class="flex items-center justify-between py-1 leading-normal">
                  <span class="font-bold text-sm text-slate-800">Thông báo mới</span>
                  <a-tag color="processing" class="!mr-0 !bg-sky-50 !text-sky-600 !border-sky-200">3 mới</a-tag>
                </div>
              </template>
              <template #content>
                <div class="w-72 max-w-[85vw] divide-y divide-slate-100 max-h-80 overflow-y-auto leading-normal">
                  <div class="py-2.5 flex items-start gap-2.5 hover:bg-slate-50 p-2 rounded-lg cursor-pointer">
                    <CheckCircleOutlined class="text-sky-500 text-base mt-0.5 shrink-0" />
                    <div>
                      <p class="text-xs font-semibold text-slate-800 leading-snug">Đơn hàng mới #TQ-1082</p>
                      <p class="text-[11px] text-slate-400 mt-0.5">Vừa đặt thành công • 385.000 ₫</p>
                    </div>
                  </div>
                  <div class="py-2.5 flex items-start gap-2.5 hover:bg-slate-50 p-2 rounded-lg cursor-pointer">
                    <WarningOutlined class="text-amber-500 text-base mt-0.5 shrink-0" />
                    <div>
                      <p class="text-xs font-semibold text-slate-800 leading-snug">Cảnh báo tồn kho</p>
                      <p class="text-[11px] text-slate-400 mt-0.5">Sữa tươi Vinamilk 1L chỉ còn 3 hộp</p>
                    </div>
                  </div>
                  <div class="py-2.5 flex items-start gap-2.5 hover:bg-slate-50 p-2 rounded-lg cursor-pointer">
                    <CheckCircleOutlined class="text-emerald-500 text-base mt-0.5 shrink-0" />
                    <div>
                      <p class="text-xs font-semibold text-slate-800 leading-snug">Khách hàng mới</p>
                      <p class="text-[11px] text-slate-400 mt-0.5">Nguyễn Văn An vừa mở tài khoản</p>
                    </div>
                  </div>
                </div>
              </template>
              <button
                type="button"
                class="w-8 h-8 sm:w-9 sm:h-9 flex items-center justify-center rounded-lg text-slate-500 hover:text-sky-600 hover:bg-slate-100 transition-colors cursor-pointer relative"
                aria-label="Thông báo"
              >
                <a-badge :count="3" :offset="[2, -2]">
                  <BellOutlined class="text-base sm:text-lg text-slate-600" />
                </a-badge>
              </button>
            </a-popover>

            <LanguageSwitcher />

            <a-dropdown placement="bottomRight" :trigger="['click']">
              <div
                class="flex items-center gap-2 pl-1.5 py-1 pr-1.5 rounded-full hover:bg-slate-100 cursor-pointer transition-colors border border-transparent hover:border-slate-200 select-none"
              >
                <a-avatar
                  size="small"
                  class="!bg-gradient-to-tr !from-sky-500 !to-sky-400 !text-white font-semibold shadow-xs shrink-0"
                >
                  <template #icon><UserOutlined /></template>
                </a-avatar>
                <div class="hidden sm:flex flex-col text-left">
                  <span class="text-xs font-semibold text-slate-800 leading-tight max-w-[110px] md:max-w-[140px] truncate">
                    {{ currentUserName }}
                  </span>
                  <span class="text-[10px] text-sky-600 font-medium leading-none mt-0.5">
                    {{ t('admin.adminRole') }}
                  </span>
                </div>
                <DownOutlined class="text-[10px] text-slate-400 hidden sm:inline-block" />
              </div>

              <template #overlay>
                <a-menu class="w-56 !rounded-xl !p-2 !shadow-lg leading-normal">
                  <div class="px-3 py-2 border-b border-slate-100 mb-1">
                    <p class="text-xs font-bold text-slate-800 truncate">{{ currentUserName }}</p>
                    <p class="text-[11px] text-slate-400 truncate mt-0.5">{{ currentUserEmail }}</p>
                  </div>

                  <a-menu-item key="user-profile" @click="toast.info('Tính năng đang phát triển')">
                    <template #icon><UserOutlined /></template>
                    <span>{{ t('admin.profile') }}</span>
                  </a-menu-item>

                  <a-menu-item key="user-settings" @click="toast.info('Tính năng đang phát triển')">
                    <template #icon><SettingOutlined /></template>
                    <span>{{ t('admin.settings') }}</span>
                  </a-menu-item>

                  <a-menu-divider />

                  <a-menu-item key="user-logout" danger @click="handleLogout">
                    <template #icon><LogoutOutlined /></template>
                    <span>{{ t('common.logout') }}</span>
                  </a-menu-item>
                </a-menu>
              </template>
            </a-dropdown>
          </div>
        </a-layout-header>

        <a-layout-content class="flex-1 min-h-0 overflow-y-auto overflow-x-hidden min-w-0 flex flex-col admin-main-scroll">
          <div class="p-3 sm:p-5 md:p-6 pb-16 sm:pb-24 flex-1 min-h-[calc(100vh-64px-52px)]">
            <RouterView />
          </div>

        </a-layout-content>
      </a-layout>
    </a-layout>
  </a-config-provider>
</template>

<style scoped>
.admin-sider {
  display: flex;
  flex-direction: column;
}

.admin-header {
  line-height: normal !important;
}

:deep(.admin-menu.ant-menu-dark) {
  background: transparent !important;
}

:deep(.admin-menu.ant-menu-dark .ant-menu-item-selected) {
  background-color: #0ea5e9 !important;
  color: #ffffff !important;
  border-radius: 10px;
  margin-left: 8px;
  margin-right: 8px;
  width: calc(100% - 16px);
}

:deep(.admin-menu.ant-menu-dark .ant-menu-item),
:deep(.admin-menu.ant-menu-dark .ant-menu-submenu-title) {
  border-radius: 10px;
  margin-left: 8px;
  margin-right: 8px;
  width: calc(100% - 16px);
  margin-top: 4px;
  margin-bottom: 4px;
}
</style>
