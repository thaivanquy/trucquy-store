<script setup lang="ts">
import { ref } from 'vue'
import { RouterLink, RouterView, useRoute } from 'vue-router'
import {
  ShoppingCartOutlined,
  MenuOutlined,
  CloseOutlined,
  HomeOutlined,
  AppstoreOutlined,
  TagOutlined,
  CustomerServiceOutlined,
  PhoneOutlined,
  SafetyCertificateOutlined,
  ThunderboltOutlined,
  SearchOutlined,
  UserOutlined,
} from '@ant-design/icons-vue'
import LanguageSwitcher from '@/components/common/LanguageSwitcher.vue'
import { useI18n } from '@/i18n'
import { useToast } from '@/composables/useToast'

const route = useRoute()
const { t } = useI18n()
const toast = useToast()

const mobileMenuOpen = ref(false)
const cartCount = ref(2)
const searchQuery = ref('')

function handleSearch() {
  if (searchQuery.value.trim()) {
    toast.info(`Tìm kiếm: "${searchQuery.value.trim()}"`)
  }
}

function handleCartClick() {
  toast.info('Giỏ hàng có 2 sản phẩm (Tổng tạm tính: 155.000 ₫)')
}
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50/70 text-slate-800 antialiased overflow-x-hidden">
    <!-- Top Announcement Bar (Laptop & Large Desktop) -->
    <div class="hidden lg:block bg-emerald-950 text-emerald-100/90 text-xs py-2 px-4 sm:px-6 lg:px-8 border-b border-emerald-900/50">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-6">
          <span class="inline-flex items-center gap-1.5 font-medium">
            <ThunderboltOutlined class="text-amber-400" />
            {{ t('shop.fastDeliveryTitle') }} - {{ t('shop.fastDeliveryDesc') }}
          </span>
          <span class="text-emerald-700">|</span>
          <span class="inline-flex items-center gap-1.5">
            <PhoneOutlined class="text-emerald-400" />
            Hotline: <strong class="text-white font-semibold">0987.654.321</strong> (7:00 - 22:00)
          </span>
        </div>

        <div class="flex items-center gap-4 text-emerald-200">
          <LanguageSwitcher />
          <span class="text-emerald-700">|</span>
          <RouterLink
            to="/admin/login"
            class="hover:text-white transition-colors text-xs font-medium flex items-center gap-1"
          >
            <UserOutlined />
            {{ t('shop.adminChannel') }}
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Main Navigation Header -->
    <header class="bg-white/95 backdrop-blur-md border-b border-slate-200/80 sticky top-0 z-30 shadow-2xs">
      <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 h-15 sm:h-16 flex items-center justify-between gap-3 sm:gap-6">
        <!-- Logo -->
        <RouterLink to="/" class="flex items-center gap-2.5 shrink-0 group">
          <div
            class="w-8 h-8 sm:w-9 sm:h-9 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-white font-black text-sm shadow-md shadow-emerald-600/20 group-hover:scale-105 transition-transform"
          >
            TQ
          </div>
          <div class="flex flex-col">
            <span class="text-base sm:text-lg font-black text-slate-900 tracking-tight leading-none">
              {{ t('shop.storeName') }}
            </span>
            <span class="hidden sm:inline-block text-[10px] text-emerald-600 font-semibold tracking-wider uppercase mt-0.5">
              Siêu thị tạp hóa & tươi sống
            </span>
          </div>
        </RouterLink>

        <!-- Search Bar (Laptop & Large Desktop) -->
        <div class="hidden md:flex flex-1 max-w-lg mx-2 lg:mx-4">
          <div class="relative w-full">
            <input
              v-model="searchQuery"
              type="text"
              :placeholder="t('shop.searchPlaceholder')"
              @keyup.enter="handleSearch"
              class="w-full pl-9 pr-16 py-2 bg-slate-100 hover:bg-slate-100/80 focus:bg-white text-xs sm:text-sm text-slate-800 placeholder-slate-400 rounded-xl border border-transparent focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100 transition-all"
            />
            <SearchOutlined class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none" />
            <button
              type="button"
              @click="handleSearch"
              class="absolute right-1 top-1/2 -translate-y-1/2 px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition-colors cursor-pointer"
            >
              Tìm
            </button>
          </div>
        </div>

        <!-- Desktop Navigation Links -->
        <nav class="hidden lg:flex items-center gap-5 xl:gap-7 text-xs xl:text-sm font-semibold text-slate-600">
          <RouterLink
            to="/"
            class="hover:text-emerald-600 transition-colors py-1 border-b-2"
            :class="route.path === '/' ? 'text-emerald-600 border-emerald-600' : 'border-transparent'"
          >
            {{ t('shop.home') }}
          </RouterLink>
          <a href="#featured-products" class="hover:text-emerald-600 transition-colors py-1 border-b-2 border-transparent">
            {{ t('shop.products') }}
          </a>
          <a href="#flash-sale" class="hover:text-emerald-600 transition-colors py-1 border-b-2 border-transparent flex items-center gap-1">
            <ThunderboltOutlined class="text-amber-500" />
            {{ t('shop.promotions') }}
          </a>
          <a href="#footer-contact" class="hover:text-emerald-600 transition-colors py-1 border-b-2 border-transparent">
            {{ t('shop.contact') }}
          </a>
        </nav>

        <!-- Right Actions (Cart, Language, Mobile Toggle) -->
        <div class="flex items-center gap-2 sm:gap-3">
          <!-- Language Switcher (Tablet Landscape / Portrait) -->
          <div class="hidden sm:block lg:hidden">
            <LanguageSwitcher />
          </div>

          <!-- Cart Icon Button -->
          <button
            type="button"
            @click="handleCartClick"
            class="relative p-2 sm:px-3 sm:py-2 rounded-xl bg-emerald-50 hover:bg-emerald-100/80 text-emerald-700 font-semibold text-xs sm:text-sm flex items-center gap-2 transition-colors cursor-pointer touch-target-min"
            :title="t('shop.cart')"
          >
            <ShoppingCartOutlined class="text-base sm:text-lg" />
            <span class="hidden sm:inline">{{ t('shop.cart') }}</span>
            <span
              v-if="cartCount > 0"
              class="w-5 h-5 rounded-full bg-emerald-600 text-white text-[10px] font-bold flex items-center justify-center shrink-0 -ml-1 sm:ml-0 shadow-xs"
            >
              {{ cartCount }}
            </span>
          </button>

          <!-- Mobile Hamburger Toggle (<1024px) -->
          <button
            type="button"
            @click="mobileMenuOpen = !mobileMenuOpen"
            class="lg:hidden p-2 rounded-xl text-slate-600 hover:text-emerald-600 hover:bg-slate-100 transition-colors cursor-pointer touch-target-min flex items-center justify-center"
            aria-label="Menu"
          >
            <MenuOutlined class="text-lg" />
          </button>
        </div>
      </div>

      <!-- Mobile Search Sub-bar (Phone Viewport) -->
      <div class="md:hidden px-2 pb-2.5 pt-0.5">
        <div class="relative w-full">
          <input
            v-model="searchQuery"
            type="text"
            :placeholder="t('shop.searchPlaceholder')"
            @keyup.enter="handleSearch"
            class="w-full pl-8 pr-14 py-1.5 bg-slate-100 text-xs text-slate-800 placeholder-slate-400 rounded-lg border border-transparent focus:bg-white focus:border-emerald-500 focus:outline-none transition-all"
          />
          <SearchOutlined class="absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none" />
          <button
            type="button"
            @click="handleSearch"
            class="absolute right-1 top-1/2 -translate-y-1/2 px-2 py-0.5 bg-emerald-600 text-white rounded text-[11px] font-medium"
          >
            Tìm
          </button>
        </div>
      </div>
    </header>

    <!-- Mobile Slide-over Menu Drawer -->
    <a-drawer
      v-model:open="mobileMenuOpen"
      placement="left"
      :closable="false"
      :width="'85%'"
      :body-style="{ padding: 0, backgroundColor: '#ffffff', display: 'flex', flexDirection: 'column', height: '100%' }"
    >
      <div class="p-4 border-b border-slate-100 flex items-center justify-between bg-emerald-900 text-white">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center text-white font-bold text-sm">
            TQ
          </div>
          <div>
            <h4 class="font-bold text-white text-sm leading-tight">{{ t('shop.storeName') }}</h4>
            <p class="text-[10px] text-emerald-200">Tạp hóa & Nông sản tươi</p>
          </div>
        </div>
        <button
          type="button"
          @click="mobileMenuOpen = false"
          class="w-8 h-8 rounded-lg text-emerald-200 hover:text-white flex items-center justify-center cursor-pointer"
        >
          <CloseOutlined class="text-sm" />
        </button>
      </div>

      <div class="flex-1 overflow-y-auto p-4 space-y-4">
        <div>
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Menu chính</span>
          <div class="space-y-1">
            <RouterLink
              to="/"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors"
            >
              <HomeOutlined class="text-base text-emerald-600" />
              <span>{{ t('shop.home') }}</span>
            </RouterLink>
            <a
              href="#featured-products"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors"
            >
              <AppstoreOutlined class="text-base text-sky-600" />
              <span>{{ t('shop.products') }}</span>
            </a>
            <a
              href="#flash-sale"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors"
            >
              <TagOutlined class="text-base text-amber-500" />
              <span>{{ t('shop.promotions') }}</span>
            </a>
            <a
              href="#footer-contact"
              @click="mobileMenuOpen = false"
              class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors"
            >
              <CustomerServiceOutlined class="text-base text-purple-600" />
              <span>{{ t('shop.contact') }}</span>
            </a>
          </div>
        </div>

        <div class="pt-3 border-t border-slate-100">
          <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-2">Hỗ trợ & Quản trị</span>
          <div class="space-y-2">
            <div class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 text-xs">
              <span class="text-slate-600">Ngôn ngữ</span>
              <LanguageSwitcher />
            </div>

            <RouterLink
              to="/admin/login"
              @click="mobileMenuOpen = false"
              class="flex items-center justify-between p-2.5 rounded-xl bg-sky-50 text-sky-700 text-xs font-semibold hover:bg-sky-100 transition-colors"
            >
              <span class="flex items-center gap-2">
                <UserOutlined />
                {{ t('shop.adminChannel') }}
              </span>
              <span>→</span>
            </RouterLink>
          </div>
        </div>
      </div>

      <div class="p-4 border-t border-slate-100 bg-slate-50 text-xs text-slate-500 space-y-1">
        <p class="font-semibold text-slate-700">Hotline đặt hàng siêu tốc:</p>
        <p class="text-emerald-700 font-bold text-sm">0987.654.321</p>
        <p class="text-[11px] text-slate-400">7:00 – 22:00 tất cả các ngày trong tuần</p>
      </div>
    </a-drawer>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-2 sm:px-6 lg:px-8 py-5 sm:py-8 pb-20 md:pb-8">
      <RouterView />
    </main>

    <!-- Storefront Footer -->
    <footer id="footer-contact" class="bg-white border-t border-slate-200 mt-auto text-slate-600 pb-20 md:pb-0">
      <div class="max-w-7xl mx-auto px-2 sm:px-6 lg:px-8 py-10 sm:py-14">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- Col 1: Store Intro -->
          <div class="space-y-3">
            <div class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white font-bold flex items-center justify-center text-sm">
                TQ
              </div>
              <span class="font-black text-slate-900 text-lg tracking-tight">{{ t('shop.storeName') }}</span>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
              {{ t('shop.tagline') }} — Phục vụ thực phẩm tươi sạch, hàng bách hóa tiêu dùng chất lượng cao, giao nhanh tận cửa.
            </p>
            <div class="pt-2 text-xs text-slate-500 space-y-1.5">
              <p>📍 123 Đường Tân Trào, Phường Tân Phú, Quận 7, TP.HCM</p>
              <p>📞 Hotline: 0987.654.321</p>
              <p>✉️ Email: lienhe@trucquy.vn</p>
            </div>
          </div>

          <!-- Col 2: Customer Support -->
          <div class="space-y-3">
            <h4 class="font-bold text-slate-900 text-sm tracking-tight uppercase">Chăm sóc khách hàng</h4>
            <ul class="text-xs sm:text-sm space-y-2 text-slate-500">
              <li><a href="#" class="hover:text-emerald-600 transition-colors">Chính sách giao hàng siêu tốc</a></li>
              <li><a href="#" class="hover:text-emerald-600 transition-colors">Quy định đổi trả trong 24 giờ</a></li>
              <li><a href="#" class="hover:text-emerald-600 transition-colors">Hướng dẫn mua sắm & thanh toán</a></li>
              <li><a href="#" class="hover:text-emerald-600 transition-colors">Chính sách bảo mật thông tin</a></li>
              <li><a href="#" class="hover:text-emerald-600 transition-colors">Câu hỏi thường gặp (FAQ)</a></li>
            </ul>
          </div>

          <!-- Col 3: Categories -->
          <div class="space-y-3">
            <h4 class="font-bold text-slate-900 text-sm tracking-tight uppercase">Ngành hàng nổi bật</h4>
            <ul class="text-xs sm:text-sm space-y-2 text-slate-500">
              <li><a href="#featured-products" class="hover:text-emerald-600 transition-colors">Rau củ quả & Trái cây tươi</a></li>
              <li><a href="#featured-products" class="hover:text-emerald-600 transition-colors">Gạo, mì khô & Gia vị truyền thống</a></li>
              <li><a href="#featured-products" class="hover:text-emerald-600 transition-colors">Sữa, bơ & Thực phẩm dinh dưỡng</a></li>
              <li><a href="#featured-products" class="hover:text-emerald-600 transition-colors">Bia, nước giải khát & Nước khoáng</a></li>
              <li><a href="#featured-products" class="hover:text-emerald-600 transition-colors">Hóa mỹ phẩm & Tẩy rửa gia đình</a></li>
            </ul>
          </div>

          <!-- Col 4: Commitments -->
          <div class="space-y-3">
            <h4 class="font-bold text-slate-900 text-sm tracking-tight uppercase">Cam kết chất lượng</h4>
            <div class="space-y-2 text-xs text-slate-500">
              <div class="flex items-start gap-2">
                <SafetyCertificateOutlined class="text-emerald-600 text-base mt-0.5 shrink-0" />
                <span>Nguồn gốc xuất xứ rõ ràng, kiểm định an toàn vệ sinh thực phẩm.</span>
              </div>
              <div class="flex items-start gap-2">
                <ThunderboltOutlined class="text-amber-500 text-base mt-0.5 shrink-0" />
                <span>Đóng gói cẩn thận, bảo quản nhiệt độ tiêu chuẩn khi vận chuyển.</span>
              </div>
            </div>
            <div class="pt-3">
              <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1.5">Kênh đối tác:</span>
              <RouterLink
                to="/admin/login"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition-colors"
              >
                <UserOutlined />
                Đăng nhập Cổng Quản Trị
              </RouterLink>
            </div>
          </div>
        </div>

        <div class="mt-10 pt-6 border-t border-slate-200/80 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-400">
          <p>© {{ new Date().getFullYear() }} {{ t('shop.storeName') }}. {{ t('common.allRightsReserved') }}</p>
          <p class="text-center sm:text-right">Hệ thống phân phối bách hóa & tiêu dùng thông minh</p>
        </div>
      </div>
    </footer>

    <!-- Mobile Fixed Bottom Navigation Bar (Screens <768px) -->
    <nav
      class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/90 shadow-lg bottom-nav-safe flex items-center justify-around h-14"
      aria-label="Mobile Bottom Navigation"
    >
      <RouterLink
        to="/"
        class="flex flex-col items-center justify-center flex-1 h-full text-slate-500 hover:text-emerald-600 transition-colors select-none"
        :class="route.path === '/' ? '!text-emerald-600 font-bold' : ''"
      >
        <HomeOutlined class="text-lg leading-none mb-0.5" />
        <span class="text-[10px] leading-tight">{{ t('shop.home') }}</span>
      </RouterLink>

      <a
        href="#featured-products"
        class="flex flex-col items-center justify-center flex-1 h-full text-slate-500 hover:text-emerald-600 transition-colors select-none"
      >
        <AppstoreOutlined class="text-lg leading-none mb-0.5" />
        <span class="text-[10px] leading-tight">{{ t('shop.products') }}</span>
      </a>

      <button
        type="button"
        @click="handleCartClick"
        class="flex flex-col items-center justify-center flex-1 h-full text-slate-500 hover:text-emerald-600 transition-colors select-none relative cursor-pointer"
      >
        <div class="relative">
          <ShoppingCartOutlined class="text-lg leading-none mb-0.5" />
          <span
            v-if="cartCount > 0"
            class="absolute -top-1 -right-2.5 w-4 h-4 rounded-full bg-emerald-600 text-white text-[9px] font-bold flex items-center justify-center"
          >
            {{ cartCount }}
          </span>
        </div>
        <span class="text-[10px] leading-tight">{{ t('shop.cart') }}</span>
      </button>

      <RouterLink
        to="/admin/login"
        class="flex flex-col items-center justify-center flex-1 h-full text-slate-500 hover:text-emerald-600 transition-colors select-none"
      >
        <UserOutlined class="text-lg leading-none mb-0.5" />
        <span class="text-[10px] leading-tight">Admin</span>
      </RouterLink>
    </nav>
  </div>
</template>
