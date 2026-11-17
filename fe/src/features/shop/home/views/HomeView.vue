<script setup lang="ts">
import { ref } from 'vue'
import {
  ThunderboltOutlined,
  CheckCircleOutlined,
  ClockCircleOutlined,
  PlusOutlined,
  FireOutlined,
  ShoppingOutlined,
  SafetyCertificateOutlined,
  CustomerServiceOutlined,
  RightOutlined,
} from '@ant-design/icons-vue'
import { useI18n } from '@/i18n'
import { useToast } from '@/composables/useToast'
import { formatPrice } from '@/utils/format'

const { t } = useI18n()
const toast = useToast()

const activeCategory = ref('all')

const categories = [
  { id: 'all', name: 'Tất cả', icon: '🧺', count: 120 },
  { id: 'vegetables', name: 'Rau củ quả', icon: '🥦', count: 35 },
  { id: 'fruits', name: 'Trái cây tươi', icon: '🍎', count: 24 },
  { id: 'meat', name: 'Thịt & Thủy sản', icon: '🥩', count: 18 },
  { id: 'drinks', name: 'Bia & Nước ngọt', icon: '🥤', count: 42 },
  { id: 'dry-food', name: 'Gạo & Gia vị', icon: '🌾', count: 28 },
  { id: 'milk', name: 'Sữa & Bơ trứng', icon: '🥛', count: 19 },
  { id: 'household', name: 'Hóa mỹ phẩm', icon: '🧼', count: 31 },
]

const flashSaleProducts = [
  {
    id: 101,
    name: 'Gạo ST25 Thơm Thượng Hạng Túi 5kg',
    category: 'Gạo & Gia vị',
    price: 185000,
    originalPrice: 220000,
    unit: 'túi',
    discount: 16,
    soldPercent: 82,
    soldCount: 41,
    image: 'https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=400&q=80',
    badge: 'FLASH SALE',
  },
  {
    id: 102,
    name: 'Dầu ăn Simply Đậu Nành Chai 1L',
    category: 'Gạo & Gia vị',
    price: 58000,
    originalPrice: 68000,
    unit: 'chai',
    discount: 15,
    soldPercent: 65,
    soldCount: 32,
    image: 'https://images.unsplash.com/photo-1474979266404-7eaacbcd87c5?auto=format&fit=crop&w=400&q=80',
    badge: 'HOT',
  },
  {
    id: 103,
    name: 'Thùng 24 Lon Bia Tiger Crystal 330ml',
    category: 'Bia & Nước ngọt',
    price: 385000,
    originalPrice: 425000,
    unit: 'thùng',
    discount: 9,
    soldPercent: 90,
    soldCount: 54,
    image: 'https://images.unsplash.com/photo-1608270111559-00f7b534b5c7?auto=format&fit=crop&w=400&q=80',
    badge: 'GIẢM SỐC',
  },
  {
    id: 104,
    name: 'Sữa tươi tiệt trùng TH True Milk 1L',
    category: 'Sữa & Bơ trứng',
    price: 36000,
    originalPrice: 42000,
    unit: 'hộp',
    discount: 14,
    soldPercent: 48,
    soldCount: 22,
    image: 'https://images.unsplash.com/photo-1550583724-b2692b85b150?auto=format&fit=crop&w=400&q=80',
    badge: 'TIẾT KIỆM',
  },
]

const featuredProducts = [
  {
    id: 1,
    code: 'SP-1001',
    name: 'Bắp Cải Trắng Đà Lạt Tươi Sạch',
    category: 'Rau củ quả',
    price: 18000,
    originalPrice: 22000,
    unit: 'kg',
    stock: 25,
    image: 'https://images.unsplash.com/photo-1594282486552-05b4d80fbb9f?auto=format&fit=crop&w=400&q=80',
    isHot: true,
  },
  {
    id: 2,
    code: 'SP-1002',
    name: 'Cà Chua Beef Xuất Khẩu Đà Lạt',
    category: 'Rau củ quả',
    price: 26000,
    originalPrice: 30000,
    unit: 'kg',
    stock: 40,
    image: 'https://images.unsplash.com/photo-1592924357228-91a4daadcfea?auto=format&fit=crop&w=400&q=80',
    isHot: false,
  },
  {
    id: 3,
    code: 'SP-1003',
    name: 'Táo Envy New Zealand Nhập Khẩu Size 70',
    category: 'Trái cây tươi',
    price: 135000,
    originalPrice: 155000,
    unit: 'kg',
    stock: 18,
    image: 'https://images.unsplash.com/photo-1560806887-1e4cd0b6cbd6?auto=format&fit=crop&w=400&q=80',
    isHot: true,
  },
  {
    id: 4,
    code: 'SP-1004',
    name: 'Thịt Ba Rọi Heo VietGAP Tươi Sạch',
    category: 'Thịt & Thủy sản',
    price: 145000,
    originalPrice: 165000,
    unit: 'kg',
    stock: 12,
    image: 'https://images.unsplash.com/photo-1607623814075-e51df1bdc82f?auto=format&fit=crop&w=400&q=80',
    isHot: true,
  },
  {
    id: 5,
    code: 'SP-1005',
    name: 'Trứng Gà Ta Ba Huân Hộp 10 Quả',
    category: 'Sữa & Bơ trứng',
    price: 32000,
    originalPrice: 36000,
    unit: 'hộp',
    stock: 50,
    image: 'https://images.unsplash.com/photo-1582722872445-44dc5f7e3c8f?auto=format&fit=crop&w=400&q=80',
    isHot: false,
  },
  {
    id: 6,
    code: 'SP-1006',
    name: 'Nước mắm Nam Ngư Đệ Nhị Chai 900ml',
    category: 'Gạo & Gia vị',
    price: 24000,
    originalPrice: 28000,
    unit: 'chai',
    stock: 35,
    image: 'https://images.unsplash.com/photo-1589135233689-d56d70df8865?auto=format&fit=crop&w=400&q=80',
    isHot: false,
  },
  {
    id: 7,
    code: 'SP-1007',
    name: 'Lốc 6 Chai Nước Khoáng Lavie 500ml',
    category: 'Bia & Nước ngọt',
    price: 28000,
    originalPrice: 32000,
    unit: 'lốc',
    stock: 45,
    image: 'https://images.unsplash.com/photo-1523362628745-0c100150b504?auto=format&fit=crop&w=400&q=80',
    isHot: false,
  },
  {
    id: 8,
    code: 'SP-1008',
    name: 'Nước Rửa Chén Sunlight Chanh Chai 750g',
    category: 'Hóa mỹ phẩm',
    price: 31000,
    originalPrice: 35000,
    unit: 'chai',
    stock: 22,
    image: 'https://images.unsplash.com/photo-1585421514738-01798e348b17?auto=format&fit=crop&w=400&q=80',
    isHot: false,
  },
  {
    id: 9,
    code: 'SP-1009',
    name: 'Bánh Chocopie Orion Hộp 12 Cái',
    category: 'Bánh kẹo',
    price: 52000,
    originalPrice: 58000,
    unit: 'hộp',
    stock: 28,
    image: 'https://images.unsplash.com/photo-1558961363-fa8fdf82db35?auto=format&fit=crop&w=400&q=80',
    isHot: false,
  },
  {
    id: 10,
    code: 'SP-1010',
    name: 'Mì Ăn Liền Hảo Hảo Tôm Chua Cay Thùng 30 Gói',
    category: 'Gạo & Gia vị',
    price: 118000,
    originalPrice: 125000,
    unit: 'thùng',
    stock: 33,
    image: 'https://images.unsplash.com/photo-1612927601601-6638404737ce?auto=format&fit=crop&w=400&q=80',
    isHot: true,
  },
]

function handleAddToCart(productName: string) {
  toast.success(t('shop.addedToCart', { name: productName }))
}
</script>

<template>
  <div class="space-y-6 sm:space-y-10">
    <!-- 1. HERO SECTION -->
    <section class="relative overflow-hidden rounded-2xl sm:rounded-3xl bg-gradient-to-br from-emerald-700 via-teal-700 to-emerald-900 text-white shadow-lg">
      <!-- Background subtle decorative shapes -->
      <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-white/10 blur-3xl pointer-events-none"></div>
      <div class="absolute right-1/4 -bottom-20 w-72 h-72 rounded-full bg-teal-400/15 blur-2xl pointer-events-none"></div>

      <div class="relative z-10 px-4 py-8 sm:p-10 md:p-12 lg:p-14 flex flex-col lg:flex-row items-center justify-between gap-6 sm:gap-8">
        <div class="max-w-xl text-center lg:text-left space-y-3 sm:space-y-4">
          <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/30 border border-emerald-300/40 text-emerald-100 text-xs sm:text-sm font-semibold backdrop-blur-md">
            <ThunderboltOutlined class="text-amber-400" />
            <span>{{ t('shop.fastDeliveryTitle') }} — Tươi Ngon Tận Cửa</span>
          </div>

          <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black tracking-tight leading-[1.15]">
            {{ t('shop.welcomeBanner') }}
          </h1>

          <p class="text-emerald-100/90 text-xs sm:text-base lg:text-lg leading-relaxed">
            {{ t('shop.welcomeDesc') }}
          </p>

          <div class="pt-2 flex flex-wrap items-center justify-center lg:justify-start gap-3">
            <a
              href="#featured-products"
              class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold text-xs sm:text-sm shadow-md transition-all active:scale-95 touch-target-min flex items-center justify-center gap-2"
            >
              <ShoppingOutlined />
              {{ t('shop.exploreNow') }}
            </a>
            <a
              href="#flash-sale"
              class="px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-white/15 hover:bg-white/25 text-white font-semibold text-xs sm:text-sm border border-white/30 backdrop-blur-md transition-all active:scale-95 touch-target-min flex items-center justify-center gap-1.5"
            >
              <FireOutlined class="text-amber-300" />
              {{ t('shop.viewDeals') }}
            </a>
          </div>
        </div>

        <!-- Hero Feature Card (Laptop & Large Desktop) -->
        <div class="w-full lg:w-80 shrink-0 bg-white/10 backdrop-blur-md rounded-2xl p-4 sm:p-5 border border-white/20 shadow-xl space-y-3">
          <div class="flex items-center justify-between pb-2 border-b border-white/15 text-xs">
            <span class="font-bold uppercase tracking-wider text-amber-300 flex items-center gap-1">
              <FireOutlined /> Ưu đãi nổi bật
            </span>
            <span class="text-emerald-200">Hôm nay</span>
          </div>

          <div class="flex items-center gap-3">
            <img
              src="https://images.unsplash.com/photo-1586201375761-83865001e31c?auto=format&fit=crop&w=200&q=80"
              alt="Gạo ST25"
              class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl object-cover shrink-0 shadow-xs"
            />
            <div class="min-w-0">
              <h4 class="font-bold text-white text-xs sm:text-sm line-clamp-2 leading-tight">
                Gạo ST25 Thơm Thượng Hạng 5kg
              </h4>
              <div class="mt-1 flex items-baseline gap-1.5">
                <span class="text-amber-300 font-black text-sm sm:text-base">185.000 ₫</span>
                <span class="text-emerald-200 text-xs line-through">220.000 ₫</span>
              </div>
              <span class="inline-block mt-0.5 text-[10px] font-bold text-rose-300 bg-rose-950/60 px-1.5 py-0.5 rounded">
                -16% GIẢM SÂU
              </span>
            </div>
          </div>

          <button
            type="button"
            @click="handleAddToCart('Gạo ST25 Thơm Thượng Hạng 5kg')"
            class="w-full py-2 bg-white text-emerald-800 hover:bg-emerald-50 rounded-xl text-xs font-bold transition-colors cursor-pointer shadow-xs"
          >
            + {{ t('shop.addToCart') }}
          </button>
        </div>
      </div>
    </section>

    <!-- 2. VALUE PROPOSITION STRIP (Responsive 4-col -> 2-col -> swipe on mobile) -->
    <section class="grid grid-cols-2 md:grid-cols-4 gap-2.5 sm:gap-4">
      <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200/80 shadow-2xs flex items-start gap-2.5 sm:gap-3.5">
        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-lg sm:text-xl shrink-0">
          <ThunderboltOutlined />
        </div>
        <div>
          <h4 class="font-bold text-slate-800 text-xs sm:text-sm leading-tight">{{ t('shop.fastDeliveryTitle') }}</h4>
          <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">{{ t('shop.fastDeliveryDesc') }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200/80 shadow-2xs flex items-start gap-2.5 sm:gap-3.5">
        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-teal-50 flex items-center justify-center text-teal-600 text-lg sm:text-xl shrink-0">
          <SafetyCertificateOutlined />
        </div>
        <div>
          <h4 class="font-bold text-slate-800 text-xs sm:text-sm leading-tight">{{ t('shop.freshGuaranteeTitle') }}</h4>
          <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">{{ t('shop.freshGuaranteeDesc') }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200/80 shadow-2xs flex items-start gap-2.5 sm:gap-3.5">
        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-lg sm:text-xl shrink-0">
          <CheckCircleOutlined />
        </div>
        <div>
          <h4 class="font-bold text-slate-800 text-xs sm:text-sm leading-tight">{{ t('shop.bestPriceTitle') }}</h4>
          <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">{{ t('shop.bestPriceDesc') }}</p>
        </div>
      </div>

      <div class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 border border-slate-200/80 shadow-2xs flex items-start gap-2.5 sm:gap-3.5">
        <div class="w-9 h-9 sm:w-11 sm:h-11 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-lg sm:text-xl shrink-0">
          <CustomerServiceOutlined />
        </div>
        <div>
          <h4 class="font-bold text-slate-800 text-xs sm:text-sm leading-tight">{{ t('shop.supportTitle') }}</h4>
          <p class="text-[11px] sm:text-xs text-slate-400 mt-0.5">{{ t('shop.supportDesc') }}</p>
        </div>
      </div>
    </section>

    <!-- 3. QUICK CATEGORIES BROWSER (Horizontal swipeable on Mobile/Tablet) -->
    <section class="space-y-3">
      <div class="flex items-center justify-between">
        <h2 class="text-base sm:text-xl font-bold text-slate-900 tracking-tight flex items-center gap-2">
          <span>{{ t('shop.topCategories') }}</span>
        </h2>
        <span class="text-xs text-slate-400">8 ngành hàng chính</span>
      </div>

      <div class="flex items-center gap-2.5 overflow-x-auto no-scrollbar pb-1 -mx-2 px-2 sm:mx-0 sm:px-0">
        <button
          v-for="cat in categories"
          :key="cat.id"
          type="button"
          @click="activeCategory = cat.id"
          class="shrink-0 flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs sm:text-sm font-semibold transition-all border cursor-pointer select-none"
          :class="
            activeCategory === cat.id
              ? 'bg-emerald-600 text-white border-emerald-600 shadow-xs'
              : 'bg-white text-slate-700 border-slate-200/90 hover:border-emerald-300 hover:bg-emerald-50/50'
          "
        >
          <span class="text-base">{{ cat.icon }}</span>
          <span>{{ cat.name }}</span>
          <span
            class="text-[10px] px-1.5 py-0.2 rounded-full"
            :class="activeCategory === cat.id ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-500'"
          >
            {{ cat.count }}
          </span>
        </button>
      </div>
    </section>

    <!-- 4. FLASH SALE / HOT DEALS SECTION -->
    <section id="flash-sale" class="bg-gradient-to-r from-amber-500 via-orange-500 to-rose-500 rounded-2xl sm:rounded-3xl p-4 sm:p-6 text-white shadow-md space-y-4">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
        <div class="flex items-center gap-2.5">
          <div class="w-8 h-8 rounded-lg bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-lg">
            <ThunderboltOutlined />
          </div>
          <h2 class="text-lg sm:text-2xl font-black tracking-tight text-white m-0">
            {{ t('shop.flashSale') }}
          </h2>
        </div>

        <div class="flex items-center gap-2 text-xs sm:text-sm font-semibold">
          <ClockCircleOutlined />
          <span>{{ t('shop.flashSaleEnds') }}</span>
          <div class="flex items-center gap-1 font-mono font-bold">
            <span class="bg-slate-900/80 px-2 py-0.5 rounded text-white">03</span>:
            <span class="bg-slate-900/80 px-2 py-0.5 rounded text-white">45</span>:
            <span class="bg-slate-900/80 px-2 py-0.5 rounded text-white">18</span>
          </div>
        </div>
      </div>

      <!-- Flash sale cards grid: 2 col on mobile, 4 col on desktop -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4">
        <div
          v-for="item in flashSaleProducts"
          :key="item.id"
          class="bg-white rounded-xl sm:rounded-2xl p-3 sm:p-4 text-slate-800 shadow-sm flex flex-col justify-between group"
        >
          <div>
            <div class="relative aspect-square rounded-lg sm:rounded-xl overflow-hidden bg-slate-100 mb-2.5">
              <img
                :src="item.image"
                :alt="item.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
              />
              <span class="absolute top-2 left-2 px-1.5 py-0.5 rounded-md bg-rose-600 text-white font-black text-[10px] sm:text-xs">
                -{{ item.discount }}%
              </span>
            </div>

            <span class="text-[10px] sm:text-[11px] font-medium text-emerald-600 block mb-0.5 truncate">
              {{ item.category }}
            </span>
            <h4 class="font-bold text-xs sm:text-sm text-slate-800 line-clamp-2 leading-snug group-hover:text-emerald-700 transition-colors">
              {{ item.name }}
            </h4>
          </div>

          <div class="mt-2.5 pt-2 border-t border-slate-100 space-y-2">
            <div class="flex items-baseline justify-between gap-1">
              <div>
                <span class="text-xs sm:text-base font-black text-rose-600">
                  {{ formatPrice(item.price) }}
                </span>
                <span class="text-[10px] text-slate-400 line-through block">
                  {{ formatPrice(item.originalPrice) }}
                </span>
              </div>
              <span class="text-[10px] sm:text-xs text-slate-500 font-medium">/ {{ item.unit }}</span>
            </div>

            <!-- Sold progress bar -->
            <div>
              <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                <span>Đã bán {{ item.soldCount }}</span>
                <span>{{ item.soldPercent }}%</span>
              </div>
              <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-rose-500 rounded-full" :style="{ width: `${item.soldPercent}%` }"></div>
              </div>
            </div>

            <button
              type="button"
              @click="handleAddToCart(item.name)"
              class="w-full py-1.5 sm:py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg sm:rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1 cursor-pointer touch-target-min"
            >
              <PlusOutlined class="text-xs" />
              <span>{{ t('shop.addToCart') }}</span>
            </button>
          </div>
        </div>
      </div>
    </section>

    <!-- 5. FEATURED PRODUCTS SECTION -->
    <section id="featured-products" class="space-y-4">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-lg sm:text-2xl font-black text-slate-900 tracking-tight">
            {{ t('shop.featuredProducts') }}
          </h2>
          <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Mặt hàng bách hóa & tiêu dùng gia đình bán chạy nhất</p>
        </div>

        <a href="#featured-products" class="text-xs sm:text-sm font-semibold text-emerald-600 hover:text-emerald-700 flex items-center gap-1">
          <span>Xem tất cả</span>
          <RightOutlined class="text-[10px]" />
        </a>
      </div>

      <!-- Adaptive Responsive Grid:
           - Large Desktop: 5 cols
           - Laptop: 4 cols
           - Tablet Landscape: 3 cols
           - Tablet Portrait: 2-3 cols
           - Mobile & Small Mobile: 2 cols
      -->
      <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-4">
        <div
          v-for="product in featuredProducts"
          :key="product.id"
          class="bg-white rounded-xl sm:rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-md hover:border-emerald-300 transition-all flex flex-col justify-between overflow-hidden group"
        >
          <div>
            <div class="relative aspect-[4/3] bg-slate-100 overflow-hidden">
              <img
                :src="product.image"
                :alt="product.name"
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                loading="lazy"
              />
              <span
                v-if="product.isHot"
                class="absolute top-2 left-2 px-1.5 py-0.5 rounded bg-emerald-600 text-white font-bold text-[10px]"
              >
                Bán chạy
              </span>
            </div>

            <div class="p-3 sm:p-3.5 space-y-1">
              <span class="text-[10px] sm:text-[11px] font-semibold text-emerald-600 block">
                {{ product.category }}
              </span>
              <h3 class="font-bold text-xs sm:text-sm text-slate-800 line-clamp-2 leading-snug group-hover:text-emerald-600 transition-colors min-h-[32px] sm:min-h-[36px]">
                {{ product.name }}
              </h3>
            </div>
          </div>

          <div class="p-3 sm:p-3.5 pt-0 space-y-2.5">
            <div class="flex items-baseline justify-between gap-1 pt-1 border-t border-slate-100">
              <div>
                <span class="text-xs sm:text-base font-black text-slate-900 tracking-tight">
                  {{ formatPrice(product.price) }}
                </span>
                <span class="text-[10px] text-slate-400 line-through block">
                  {{ formatPrice(product.originalPrice) }}
                </span>
              </div>
              <span class="text-[10px] sm:text-xs text-slate-500 font-medium">/ {{ product.unit }}</span>
            </div>

            <button
              type="button"
              @click="handleAddToCart(product.name)"
              class="w-full py-2 bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white rounded-xl text-xs font-bold transition-all flex items-center justify-center gap-1.5 cursor-pointer touch-target-min"
            >
              <PlusOutlined class="text-xs" />
              <span>{{ t('shop.addToCart') }}</span>
            </button>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>
