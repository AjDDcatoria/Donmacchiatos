<script setup lang="ts">
import { setBarsOptions, setSalesOptions } from '@/helpers/charts'
import { useThemeStore } from '@/stores/themeStore'
import Button from 'primevue/button'
import Card from 'primevue/card'
import Chart from 'primevue/chart'
import IconField from 'primevue/iconfield'
import InputIcon from 'primevue/inputicon'
import InputText from 'primevue/inputtext'
import { ref, onMounted } from 'vue'
import Coffee from '@/assets/coffee.jpg'
import Temp from '@/assets/temp.jpg'
import Avatar from 'primevue/avatar'
import type { ChartDataTypes, ChartOptionsTypes } from '@/types/chart'

const salesChart = ref<ChartDataTypes>()
const salesOptions = ref<ChartOptionsTypes>()
const chartData = ref<ChartDataTypes>()
const chartOptions = ref<ChartOptionsTypes>()
const themeStore = useThemeStore()
const documentStyle = getComputedStyle(document.documentElement)

onMounted(() => {
  salesChart.value = setSalesChart(
    ['June', 'July', 'August', 'September', 'October'],
    [32, 16, 21, 38, 26],
  )
  salesOptions.value = setSalesOptions()
  chartData.value = setChartData()
  chartOptions.value = setBarsOptions()
})

const setSalesChart = (labels: string[], data: number[]): ChartDataTypes => {
  return {
    labels,
    datasets: [
      {
        label: 'Sales',
        data,
        fill: true,
        borderColor: documentStyle.getPropertyValue('--p-gray-500'),
        tension: 0.4,
        backgroundColor: 'rgba(107, 114, 128, 0.2)',
      },
    ],
  }
}

const setChartData = (): ChartDataTypes => {
  return {
    labels: ['Jun', 'Jul', 'Aug', 'Sep', 'Oct'],
    datasets: [
      {
        type: 'bar',
        label: 'Brown Spanish Latte',
        backgroundColor: documentStyle.getPropertyValue('--p-cyan-500'),
        data: [50, 25, 12, 48, 90],
      },
      {
        type: 'bar',
        label: 'Black Forest',
        backgroundColor: documentStyle.getPropertyValue('--p-gray-500'),
        data: [21, 84, 24, 75, 37],
      },
      {
        type: 'bar',
        label: 'Matcha berry',
        backgroundColor: documentStyle.getPropertyValue('--p-orange-500'),
        data: [41, 52, 24, 74],
      },
    ],
  }
}
</script>

<template>
  <div class="h-full flex justify-center pt-3 pb-5 pr-2">
    <div class="h-full w-full grid gap-3 max-w-[70rem]">
      <Card class="!rounded-md relative">
        <template #title> User </template>
        <template #content>
          <Button
            icon="pi pi-users !text-2xl"
            aria-label="Filter"
            class="!absolute !w-[50px] top-5 right-5"
            severity="info"
          />
          <div class="text-2xl font-bold">13,321</div>
          <div class="mt-3 font-semibold">
            <span class="text-green-600">325 new</span> this month
          </div>
        </template>
      </Card>
      <Card class="!rounded-md relative">
        <template #title> Orders </template>
        <template #content>
          <Button
            icon="pi pi-cart-arrow-down !text-2xl"
            aria-label="Filter"
            class="!absolute !w-[50px] top-5 right-5"
            severity="help"
          />
          <div class="text-2xl font-bold">17,321</div>
          <div class="mt-3 font-semibold">
            <span class="text-green-600">1,325 new</span> this month
          </div>
        </template>
      </Card>
      <Card class="!rounded-md relative">
        <template #title> Income </template>
        <template #content>
          <Button
            icon="pi pi-bitcoin !text-2xl"
            aria-label="Filter"
            class="!absolute !w-[50px] top-5 right-5"
            severity="success"
          />
          <div class="text-2xl font-bold">₱47,321</div>
          <div class="mt-3 font-semibold">
            <span class="text-green-600">₱8,325 income</span> this month
          </div>
        </template>
      </Card>
      <Card class="!rounded-md col-span-2 row-span-3">
        <template #title>Sales overview</template>
        <template #content>
          <Chart
            type="line"
            :data="salesChart"
            :options="salesOptions"
            class="h-[26rem]"
          />
        </template>
      </Card>
      <Card class="!rounded-md row-span-3 overflow-hidden">
        <template #title> Most Orders </template>
        <template #content>
          <div class="h-full w-full flex justify-center mt-3">
            <Chart
              type="bar"
              :data="chartData"
              :options="chartOptions"
              class="h-[25.3rem]"
            />
          </div>
        </template>
      </Card>
      <Card class="!rounded-md col-span-3 overflow-hidden row-span-3">
        <template #title>
          <div class="h-full py-2 flex justify-between">
            <div>Recent Sales</div>
            <IconField class="!h-8">
              <InputText placeholder="Search" class="!h-8" />
              <InputIcon class="pi pi-search" />
            </IconField>
          </div>
        </template>
        <template #content>
          <div class="overflow-y-scroll max-h-[25rem]">
            <table id="sales-table" class="w-full">
              <thead
                :class="[
                  'z-10 sticky top-0',
                  themeStore.getTheme() == 'dark' ? 'bg-[#18181b]' : 'bg-white',
                ]"
              >
                <tr>
                  <td class="w-14">No.</td>
                  <td>Name</td>
                  <td>Image</td>
                  <td class="text-center w-20">Quantity</td>
                  <td class="text-center">Total</td>
                  <td>User</td>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="item in [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17,
                    18, 19, 20,
                  ]"
                  :key="item"
                  class="h-20 border-b border-gray-300/50"
                >
                  <td>{{ item }}</td>
                  <td>
                    <h2 class="font-semibold">Black Forest</h2>
                  </td>
                  <td>
                    <div class="h-14 w-16 overflow-hidden rounded">
                      <img :src="Coffee" class="object-cover h-full w-full" />
                    </div>
                  </td>
                  <td class="text-center font-semibold">19</td>
                  <td class="text-center font-bold text-green-600">
                    ₱ 1,324.00
                  </td>
                  <td class="max-w-32">
                    <div class="h-full flex items-center gap-3">
                      <div class="h-14 w-16 overflow-hidden rounded">
                        <Avatar
                          :image="Temp"
                          shape="square"
                          class="!h-full !w-full object-fill"
                        />
                      </div>
                      <div>
                        <h2 class="font-semibold">AJ DDcatoria</h2>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </template>
      </Card>
    </div>
  </div>
</template>

<style scoped>
div.grid {
  grid-template-columns: repeat(3, minmax(21rem, 1fr));
  grid-template-rows: repeat(7, 10rem);
}

table#sales-table td {
  padding: 0 10px;
}
</style>
