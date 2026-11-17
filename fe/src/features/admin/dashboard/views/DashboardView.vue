<script setup lang="ts">
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'
import {
  DollarCircleOutlined,
  ShoppingCartOutlined,
  AppstoreOutlined,
  TeamOutlined,
  ArrowUpOutlined,
  PlusOutlined,
  ReloadOutlined,
  DownloadOutlined,
  ClockCircleOutlined,
  WarningOutlined,
  EyeOutlined,
  ShopOutlined,
  BarcodeOutlined,
  CheckCircleOutlined,
  FileTextOutlined,
  TagsOutlined,
} from '@ant-design/icons-vue'
import { useI18n } from '@/i18n'
import { useAuthStore } from '@/features/admin/auth/stores/authStore'
import { useToast } from '@/composables/useToast'

const router = useRouter()
const { t } = useI18n()
const authStore = useAuthStore()
const toast = useToast()

const breakpoints = useBreakpoints(breakpointsTailwind)
const isMobile = breakpoints.smaller('md')

const selectedPeriod = ref<string>('today')
const isRefreshing = ref<boolean>(false)
const selectedOrder = ref<any>(null)
const isModalVisible = ref<boolean>(false)

const currentUserName = computed(() => authStore.user?.name || t('admin.adminRole'))

const periodOptions = computed(() => [
  { label: t('admin.today'), value: 'today' },
  { label: t('admin.sevenDays'), value: 'week' },
  { label: t('admin.month'), value: 'month' },
])

function refreshData() {
  isRefreshing.value = true
  setTimeout(() => {
    isRefreshing.value = false
    toast.success(t('admin.refreshSuccess'))
  }, 600)
}

function handleQuickAction(actionName: string) {
  toast.info(t('admin.comingSoon', { name: actionName }))
}

function viewOrderDetail(record: any) {
  selectedOrder.value = record
  isModalVisible.value = true
}

const recentOrders = ref([
  {
    key: '1',
    code: '#TQ-1082',
    customer: 'Trần Thị Mai',
    phone: '0987***321',
    items: 'Gạo ST25 (5kg), Dầu ăn Simply 1L, Nước mắm Nam Ngư',
    total: '385.000 ₫',
    paymentMethod: 'VietQR',
    status: 'completed',
    statusText: 'Hoàn thành',
    statusColor: 'success',
    time: '10 phút trước',
  },
  {
    key: '2',
    code: '#TQ-1081',
    customer: 'Nguyễn Văn Minh',
    phone: '0912***654',
    items: '2x Thùng bia Tiger Crystal, 1kg Lạc rang',
    total: '840.000 ₫',
    paymentMethod: 'Tiền mặt',
    status: 'shipping',
    statusText: 'Đang giao',
    statusColor: 'processing',
    time: '25 phút trước',
  },
  {
    key: '3',
    code: '#TQ-1080',
    customer: 'Lê Hoàng Yến',
    phone: '0978***112',
    items: 'Sữa tươi TH True Milk (Thùng 48 hộp), Bánh Chocopie',
    total: '495.000 ₫',
    paymentMethod: 'MoMo',
    status: 'pending',
    statusText: 'Chờ xử lý',
    statusColor: 'warning',
    time: '42 phút trước',
  },
  {
    key: '4',
    code: '#TQ-1079',
    customer: 'Phạm Thanh Tùng',
    phone: '0903***889',
    items: 'Nước rửa chén Sunlight 3.8kg, Bột giặt Omo 5kg',
    total: '310.000 ₫',
    paymentMethod: 'VietQR',
    status: 'completed',
    statusText: 'Hoàn thành',
    statusColor: 'success',
    time: '1 giờ trước',
  },
  {
    key: '5',
    code: '#TQ-1078',
    customer: 'Hoàng Kim Dung',
    phone: '0965***443',
    items: 'Mì ăn liền Hảo Hảo (1 thùng), Trứng gà Ba Huân (30 quả)',
    total: '215.000 ₫',
    paymentMethod: 'Tiền mặt',
    status: 'completed',
    statusText: 'Hoàn thành',
    statusColor: 'success',
    time: '2 giờ trước',
  },
])

const lowStockProducts = ref([
  {
    id: 1,
    name: 'Sữa tươi Vinamilk Không đường 1L',
    stock: 3,
    minStock: 20,
    percent: 15,
    unit: 'hộp',
  },
  {
    id: 2,
    name: 'Dầu đậu nành Simply 1L',
    stock: 5,
    minStock: 25,
    percent: 20,
    unit: 'chai',
  },
  {
    id: 3,
    name: 'Gạo ST25 Thơm Thượng Hạng 5kg',
    stock: 2,
    minStock: 15,
    percent: 13,
    unit: 'túi',
  },
  {
    id: 4,
    name: 'Nước tương Tam Thái Tử Nhất Ca 500ml',
    stock: 6,
    minStock: 30,
    percent: 20,
    unit: 'chai',
  },
])

const categorySales = [
  { name: 'Thực phẩm khô & Gia vị', percent: 45, color: '#0ea5e9', amount: '2.100.000 ₫' },
  { name: 'Bia, Nước ngọt & Đồ uống', percent: 28, color: '#38bdf8', amount: '1.310.000 ₫' },
  { name: 'Hóa mỹ phẩm & Tẩy rửa', percent: 17, color: '#8b5cf6', amount: '795.000 ₫' },
  { name: 'Bánh kẹo & Đồ ăn vặt', percent: 10, color: '#f59e0b', amount: '475.000 ₫' },
]

const orderColumns = [
  { title: 'Mã đơn', dataIndex: 'code', key: 'code', width: 110 },
  { title: 'Khách hàng', dataIndex: 'customer', key: 'customer', width: 140 },
  { title: 'Sản phẩm', dataIndex: 'items', key: 'items', ellipsis: true },
  { title: 'Tổng tiền', dataIndex: 'total', key: 'total', width: 110 },
  { title: 'Thanh toán', dataIndex: 'paymentMethod', key: 'paymentMethod', width: 105 },
  { title: 'Trạng thái', dataIndex: 'status', key: 'status', width: 110 },
  { title: 'Thao tác', key: 'action', width: 70, align: 'center' as const, fixed: 'right' as const },
]
</script>

<template>
  <div class="space-y-4 sm:space-y-6 max-w-full">

    <div
      class="bg-white rounded-2xl p-4 sm:p-5 md:p-6 border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4"
    >
      <div>
        <div class="flex items-center gap-2 flex-wrap">
          <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-slate-800 tracking-tight leading-tight">
            {{ t('admin.overview') }}
          </h1>
          <a-tag color="processing" class="!rounded-full !px-2.5 !py-0.5 !text-xs !border-0 !bg-sky-50 !text-sky-700 font-semibold">
            ● {{ t('admin.online') }}
          </a-tag>
        </div>
        <p class="text-xs sm:text-sm text-slate-500 mt-1">
          Xin chào, <span class="font-semibold text-slate-700">{{ currentUserName }}</span>! {{ t('admin.welcome') }}
        </p>
      </div>

      <div class="flex flex-wrap items-center gap-2 sm:gap-2.5">
        <a-segmented
          v-model:value="selectedPeriod"
          :options="periodOptions"
          class="shrink-0"
        />

        <a-button
          :loading="isRefreshing"
          @click="refreshData"
          class="!flex !items-center !gap-1.5 !rounded-lg text-xs sm:text-sm"
        >
          <template #icon><ReloadOutlined /></template>
          <span class="hidden sm:inline">{{ t('admin.refresh') }}</span>
        </a-button>

        <a-button
          type="primary"
          @click="handleQuickAction(t('admin.newOrderBtn'))"
          class="!bg-sky-400 hover:!bg-sky-500 active:!bg-sky-600 !flex !items-center !gap-1.5 !rounded-lg !border-none !shadow-md !shadow-sky-400/25 font-medium text-xs sm:text-sm"
        >
          <template #icon><PlusOutlined /></template>
          <span>{{ t('admin.newOrderBtn') }}</span>
        </a-button>
      </div>
    </div>

    <a-row :gutter="[12, 12]">

      <a-col :xs="12" :sm="12" :lg="6">
        <a-card :bordered="false" class="!rounded-2xl !shadow-xs hover:!shadow-md transition-shadow !h-full">
          <div class="flex items-start justify-between">
            <div class="min-w-0">
              <p class="text-[11px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 truncate">
                {{ t('admin.todayRevenue') }}
              </p>
              <div class="text-base sm:text-2xl font-black text-slate-800 leading-none truncate">
                4.680.000 <span class="text-[11px] sm:text-sm font-semibold text-slate-500">₫</span>
              </div>
            </div>
            <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-sky-50 flex items-center justify-center text-sky-500 text-sm sm:text-xl shadow-xs shrink-0">
              <DollarCircleOutlined />
            </div>
          </div>
          <div class="flex items-center gap-1.5 mt-2.5 sm:mt-3 pt-2.5 sm:pt-3 border-t border-slate-100 text-[11px] sm:text-xs">
            <span class="text-sky-600 font-semibold flex items-center gap-0.5">
              <ArrowUpOutlined /> +15.8%
            </span>
            <span class="text-slate-400 truncate hidden sm:inline">{{ t('admin.vsYesterday') }}</span>
          </div>
        </a-card>
      </a-col>

      <a-col :xs="12" :sm="12" :lg="6">
        <a-card :bordered="false" class="!rounded-2xl !shadow-xs hover:!shadow-md transition-shadow !h-full">
          <div class="flex items-start justify-between">
            <div class="min-w-0">
              <p class="text-[11px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 truncate">
                {{ t('admin.newOrders') }}
              </p>
              <div class="text-base sm:text-2xl font-black text-slate-800 leading-none truncate">
                28 <span class="text-[11px] sm:text-sm font-semibold text-slate-500">{{ t('admin.ordersUnit') }}</span>
              </div>
            </div>
            <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-cyan-50 flex items-center justify-center text-cyan-600 text-sm sm:text-xl shadow-xs shrink-0">
              <ShoppingCartOutlined />
            </div>
          </div>
          <div class="flex items-center justify-between mt-2.5 sm:mt-3 pt-2.5 sm:pt-3 border-t border-slate-100 text-[11px] sm:text-xs">
            <span class="text-amber-600 font-medium flex items-center gap-1 truncate">
              <ClockCircleOutlined /> {{ t('admin.pendingCount', { count: 4 }) }}
            </span>
            <span class="text-sky-600 font-semibold shrink-0 hidden sm:inline">+8%</span>
          </div>
        </a-card>
      </a-col>

      <a-col :xs="12" :sm="12" :lg="6">
        <a-card :bordered="false" class="!rounded-2xl !shadow-xs hover:!shadow-md transition-shadow !h-full">
          <div class="flex items-start justify-between">
            <div class="min-w-0">
              <p class="text-[11px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 truncate">
                {{ t('admin.products') }}
              </p>
              <div class="text-base sm:text-2xl font-black text-slate-800 leading-none truncate">
                542 <span class="text-[11px] sm:text-sm font-semibold text-slate-500">{{ t('admin.itemsUnit') }}</span>
              </div>
            </div>
            <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600 text-sm sm:text-xl shadow-xs shrink-0">
              <AppstoreOutlined />
            </div>
          </div>
          <div class="flex items-center gap-1.5 mt-2.5 sm:mt-3 pt-2.5 sm:pt-3 border-t border-slate-100 text-[11px] sm:text-xs">
            <span class="text-rose-500 font-semibold flex items-center gap-0.5">
              <WarningOutlined /> {{ t('admin.lowStockCount', { count: 4 }) }}
            </span>
            <span class="text-slate-400 truncate hidden sm:inline">{{ t('admin.aboutToRunOut') }}</span>
          </div>
        </a-card>
      </a-col>

      <a-col :xs="12" :sm="12" :lg="6">
        <a-card :bordered="false" class="!rounded-2xl !shadow-xs hover:!shadow-md transition-shadow !h-full">
          <div class="flex items-start justify-between">
            <div class="min-w-0">
              <p class="text-[11px] sm:text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1 truncate">
                {{ t('admin.customers') }}
              </p>
              <div class="text-base sm:text-2xl font-black text-slate-800 leading-none truncate">
                136 <span class="text-[11px] sm:text-sm font-semibold text-slate-500">{{ t('admin.membersUnit') }}</span>
              </div>
            </div>
            <div class="w-8 h-8 sm:w-11 sm:h-11 rounded-xl bg-amber-50 flex items-center justify-center text-amber-600 text-sm sm:text-xl shadow-xs shrink-0">
              <TeamOutlined />
            </div>
          </div>
          <div class="flex items-center gap-1.5 mt-2.5 sm:mt-3 pt-2.5 sm:pt-3 border-t border-slate-100 text-[11px] sm:text-xs">
            <span class="text-sky-600 font-semibold flex items-center gap-0.5">
              <ArrowUpOutlined /> +5
            </span>
            <span class="text-slate-400 truncate hidden sm:inline">{{ t('admin.newCustomersToday') }}</span>
          </div>
        </a-card>
      </a-col>
    </a-row>

    <a-row :gutter="[16, 16]">

      <a-col :xs="24" :lg="16" class="space-y-4 sm:space-y-6">

        <a-card :bordered="false" class="!rounded-2xl !shadow-xs overflow-hidden">
          <template #title>
            <div class="flex items-center gap-2">
              <span class="font-bold text-slate-800 text-sm sm:text-base">{{ t('admin.recentOrders') }}</span>
              <a-tag color="processing" class="!text-xs !bg-sky-50 !text-sky-600 !border-sky-200">{{ t('admin.newBadge') }}</a-tag>
            </div>
          </template>

          <template #extra>
            <a-button type="link" size="small" class="!text-sky-600 !p-0" @click="handleQuickAction('Xem tất cả đơn hàng')">
              {{ t('admin.viewAll') }} →
            </a-button>
          </template>

          <!-- Desktop Table (Tablet Landscape, Laptop & Desktop) -->
          <div class="hidden md:block">
            <a-table
              :columns="orderColumns"
              :dataSource="recentOrders"
              :pagination="false"
              size="middle"
              :scroll="{ x: 700 }"
            >
              <template #bodyCell="{ column, record }">
                <template v-if="column.key === 'code'">
                  <span class="font-mono font-bold text-slate-800 text-xs">{{ record.code }}</span>
                  <span class="block text-[10px] text-slate-400">{{ record.time }}</span>
                </template>

                <template v-else-if="column.key === 'customer'">
                  <div class="font-medium text-slate-800 text-xs leading-tight">{{ record.customer }}</div>
                  <div class="text-[11px] text-slate-400">{{ record.phone }}</div>
                </template>

                <template v-else-if="column.key === 'total'">
                  <span class="font-bold text-sky-600 text-xs">{{ record.total }}</span>
                </template>

                <template v-else-if="column.key === 'paymentMethod'">
                  <a-tag
                    :color="record.paymentMethod === 'VietQR' ? 'blue' : record.paymentMethod === 'MoMo' ? 'magenta' : 'default'"
                    class="!text-[11px]"
                  >
                    {{ record.paymentMethod }}
                  </a-tag>
                </template>

                <template v-else-if="column.key === 'status'">
                  <a-tag :color="record.statusColor" class="!text-[11px]">
                    {{ record.statusText }}
                  </a-tag>
                </template>

                <template v-else-if="column.key === 'action'">
                  <a-button
                    type="text"
                    size="small"
                    class="text-sky-600 hover:text-sky-700"
                    @click="viewOrderDetail(record)"
                  >
                    <EyeOutlined />
                  </a-button>
                </template>
              </template>
            </a-table>
          </div>

          <!-- Mobile Order Card List (< 768px: Phone & Tablet Portrait) -->
          <div class="md:hidden divide-y divide-slate-100 -mx-3 -my-2 sm:mx-0 sm:my-0">
            <div
              v-for="order in recentOrders"
              :key="order.key"
              class="p-3 sm:p-4 hover:bg-slate-50 transition-colors flex flex-col gap-2 cursor-pointer"
              @click="viewOrderDetail(order)"
            >
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <span class="font-mono font-bold text-slate-900 text-xs">{{ order.code }}</span>
                  <a-tag :color="order.statusColor" class="!text-[10px] !mr-0">{{ order.statusText }}</a-tag>
                </div>
                <span class="font-bold text-sky-600 text-sm">{{ order.total }}</span>
              </div>

              <div class="flex items-center justify-between text-xs text-slate-500">
                <span class="font-medium text-slate-700">{{ order.customer }} ({{ order.phone }})</span>
                <span class="text-[10px] text-slate-400">{{ order.time }}</span>
              </div>

              <p class="text-[11px] text-slate-600 line-clamp-1 bg-slate-50 p-1.5 rounded-lg border border-slate-100">
                {{ order.items }}
              </p>

              <div class="flex items-center justify-between pt-1">
                <a-tag
                  :color="order.paymentMethod === 'VietQR' ? 'blue' : order.paymentMethod === 'MoMo' ? 'magenta' : 'default'"
                  class="!text-[10px]"
                >
                  {{ order.paymentMethod }}
                </a-tag>

                <button
                  type="button"
                  class="text-xs font-semibold text-sky-600 hover:text-sky-700 flex items-center gap-1 cursor-pointer py-1 px-2.5 rounded-lg bg-sky-50 transition-colors"
                >
                  <EyeOutlined />
                  <span>Xem chi tiết</span>
                </button>
              </div>
            </div>
          </div>
        </a-card>

        <a-card :bordered="false" class="!rounded-2xl !shadow-xs">
          <template #title>
            <span class="font-bold text-slate-800 text-sm sm:text-base">Tỷ trọng doanh thu theo ngành hàng</span>
          </template>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div v-for="cat in categorySales" :key="cat.name" class="p-3.5 rounded-xl bg-slate-50 border border-slate-100">
              <div class="flex items-center justify-between text-xs mb-1.5">
                <span class="font-semibold text-slate-700">{{ cat.name }}</span>
                <span class="font-bold text-slate-900">{{ cat.amount }}</span>
              </div>
              <a-progress
                :percent="cat.percent"
                :stroke-color="cat.color"
                size="small"
                :format="(percent: number | undefined) => `${percent}%`"
              />
            </div>
          </div>
        </a-card>
      </a-col>

      <a-col :xs="24" :lg="8" class="space-y-4 sm:space-y-6">

        <a-card :bordered="false" class="!rounded-2xl !shadow-xs">
          <template #title>
            <span class="font-bold text-slate-800 text-sm sm:text-base">{{ t('admin.quickActions') }}</span>
          </template>

          <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-2 gap-2.5 sm:gap-3">
            <button
              type="button"
              @click="handleQuickAction('Bán hàng tại quầy POS')"
              class="flex flex-col items-center justify-center p-3 sm:p-3.5 rounded-xl bg-sky-50 hover:bg-sky-100/70 border border-sky-100 text-sky-800 transition-colors cursor-pointer group"
            >
              <ShopOutlined class="text-xl sm:text-2xl text-sky-500 group-hover:scale-110 transition-transform mb-1" />
              <span class="text-xs font-semibold">Bán hàng POS</span>
              <span class="text-[10px] text-sky-600">Tạo đơn nhanh</span>
            </button>

            <button
              type="button"
              @click="router.push('/admin/categories')"
              class="flex flex-col items-center justify-center p-3 sm:p-3.5 rounded-xl bg-emerald-50 hover:bg-emerald-100/70 border border-emerald-100 text-emerald-800 transition-colors cursor-pointer group"
            >
              <TagsOutlined class="text-xl sm:text-2xl text-emerald-600 group-hover:scale-110 transition-transform mb-1" />
              <span class="text-xs font-semibold">Quản lý Danh mục</span>
              <span class="text-[10px] text-emerald-600">Rau củ & Tạp hóa</span>
            </button>

            <button
              type="button"
              @click="handleQuickAction('Thêm sản phẩm mới')"
              class="flex flex-col items-center justify-center p-3 sm:p-3.5 rounded-xl bg-cyan-50 hover:bg-cyan-100/70 border border-cyan-100 text-cyan-800 transition-colors cursor-pointer group"
            >
              <PlusOutlined class="text-xl sm:text-2xl text-cyan-600 group-hover:scale-110 transition-transform mb-1" />
              <span class="text-xs font-semibold">Thêm sản phẩm</span>
              <span class="text-[10px] text-cyan-600">Mặt hàng mới</span>
            </button>

            <button
              type="button"
              @click="handleQuickAction('Quét mã vạch nhập kho')"
              class="flex flex-col items-center justify-center p-3 sm:p-3.5 rounded-xl bg-indigo-50 hover:bg-indigo-100/70 border border-indigo-100 text-indigo-800 transition-colors cursor-pointer group"
            >
              <BarcodeOutlined class="text-xl sm:text-2xl text-indigo-600 group-hover:scale-110 transition-transform mb-1" />
              <span class="text-xs font-semibold">Quét mã vạch</span>
              <span class="text-[10px] text-indigo-600">Nhập kho</span>
            </button>

            <button
              type="button"
              @click="handleQuickAction('Xuất báo cáo doanh thu')"
              class="flex flex-col items-center justify-center p-3 sm:p-3.5 rounded-xl bg-amber-50 hover:bg-amber-100/70 border border-amber-100 text-amber-800 transition-colors cursor-pointer group"
            >
              <DownloadOutlined class="text-xl sm:text-2xl text-amber-600 group-hover:scale-110 transition-transform mb-1" />
              <span class="text-xs font-semibold">Xuất báo cáo</span>
              <span class="text-[10px] text-amber-600">Excel / PDF</span>
            </button>
          </div>
        </a-card>

        <a-card :bordered="false" class="!rounded-2xl !shadow-xs">
          <template #title>
            <div class="flex items-center gap-2">
              <span class="font-bold text-slate-800 text-sm sm:text-base">{{ t('admin.lowStockAlert') }}</span>
              <a-badge :count="lowStockProducts.length" :overflow-count="99" />
            </div>
          </template>

          <div class="space-y-3">
            <div
              v-for="item in lowStockProducts"
              :key="item.id"
              class="p-2.5 sm:p-3 rounded-xl bg-rose-50/50 border border-rose-100"
            >
              <div class="flex items-start justify-between gap-2">
                <span class="font-semibold text-xs text-slate-800 leading-tight">
                  {{ item.name }}
                </span>
                <a-tag color="error" class="!text-[10px] !mr-0 shrink-0">
                  Còn {{ item.stock }} {{ item.unit }}
                </a-tag>
              </div>
              <div class="mt-2">
                <a-progress
                  :percent="item.percent"
                  status="exception"
                  size="small"
                  :showInfo="false"
                />
              </div>
            </div>

            <a-button
              block
              type="dashed"
              @click="handleQuickAction('Tạo đơn nhập hàng loạt')"
              class="!text-xs !rounded-lg hover:!text-sky-600 hover:!border-sky-500"
            >
              <PlusOutlined /> Nhập thêm hàng kho
            </a-button>
          </div>
        </a-card>

        <a-card :bordered="false" class="!rounded-2xl !shadow-xs">
          <template #title>
            <span class="font-bold text-slate-800 text-sm sm:text-base">{{ t('admin.recentActivity') }}</span>
          </template>

          <a-timeline class="!mt-2 !text-xs">
            <a-timeline-item color="blue">
              <p class="font-medium text-slate-800 leading-snug">Đơn #TQ-1082 thanh toán thành công</p>
              <span class="text-[11px] text-slate-400">10 phút trước • VietQR</span>
            </a-timeline-item>
            <a-timeline-item color="cyan">
              <p class="font-medium text-slate-800 leading-snug">Bắt đầu giao đơn hàng #TQ-1081</p>
              <span class="text-[11px] text-slate-400">25 phút trước • Shipper nhận hàng</span>
            </a-timeline-item>
            <a-timeline-item color="red">
              <p class="font-medium text-slate-800 leading-snug">Cảnh báo: Gạo ST25 chạm ngưỡng tồn tối thiểu</p>
              <span class="text-[11px] text-slate-400">45 phút trước • Hệ thống kho</span>
            </a-timeline-item>
            <a-timeline-item color="gray">
              <p class="font-medium text-slate-800 leading-snug">Đăng nhập thành công vào trang quản trị</p>
              <span class="text-[11px] text-slate-400">08:00 sáng • Quản trị viên</span>
            </a-timeline-item>
          </a-timeline>
        </a-card>
      </a-col>
    </a-row>

    <a-modal
      v-model:open="isModalVisible"
      :title="`Chi tiết đơn hàng ${selectedOrder?.code || ''}`"
      :footer="null"
      centered
      :width="isMobile ? '92%' : 520"
      class="!rounded-2xl"
    >
      <div v-if="selectedOrder" class="space-y-4 py-2">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
          <div>
            <span class="text-xs text-slate-400 block">Khách hàng</span>
            <span class="font-bold text-slate-800 text-sm">{{ selectedOrder.customer }}</span>
            <span class="text-xs text-slate-500 block">{{ selectedOrder.phone }}</span>
          </div>
          <div class="text-right">
            <span class="text-xs text-slate-400 block">Trạng thái</span>
            <a-tag :color="selectedOrder.statusColor">{{ selectedOrder.statusText }}</a-tag>
          </div>
        </div>

        <div>
          <span class="text-xs font-semibold text-slate-400 block mb-1">Mặt hàng đặt mua</span>
          <div class="p-3 rounded-xl bg-slate-50 border border-slate-100 text-xs text-slate-700 leading-relaxed">
            {{ selectedOrder.items }}
          </div>
        </div>

        <div class="flex items-center justify-between pt-2">
          <span class="text-xs text-slate-500">Hình thức thanh toán:</span>
          <span class="font-semibold text-xs text-slate-800">{{ selectedOrder.paymentMethod }}</span>
        </div>

        <div class="flex items-center justify-between pt-1 border-t border-slate-100">
          <span class="text-sm font-semibold text-slate-700">Tổng thanh toán:</span>
          <span class="text-lg font-bold text-sky-600">{{ selectedOrder.total }}</span>
        </div>

        <div class="pt-4 flex gap-2">
          <a-button block @click="isModalVisible = false">Đóng</a-button>
          <a-button
            block
            type="primary"
            class="!bg-sky-400 hover:!bg-sky-500"
            @click="toast.success('Đã in hóa đơn đơn hàng thành công!'); isModalVisible = false"
          >
            <template #icon><FileTextOutlined /></template>
            In hóa đơn
          </a-button>
        </div>
      </div>
    </a-modal>
  </div>
</template>

<style scoped>
:deep(.ant-card) {
  border-radius: 16px;
}
</style>
