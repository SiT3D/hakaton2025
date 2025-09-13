<template>
  <div class="stats">
    <h1>Статистика</h1>
    <a href="/list">Назад</a>

    <div class="charts">
      <div class="chart-box">
        <h2>Занятая территория по типу</h2>
        <Bar v-if="barData" :data="barData" :options="chartOptions"/>
      </div>

      <div class="chart-box">
        <h2>Культуры и животные</h2>
        <Pie v-if="pieData" :data="pieData" :options="chartOptions"/>
      </div>
    </div>

    <div class="chart-box">
      <h2>Динамика по времени</h2>
      <Line v-if="lineData" :data="lineData" :options="chartOptions"/>
    </div>

    <!-- таблица анализа -->
    <h2>Детализация по культурам/животным</h2>
    <table class="stats-table">
      <thead>
      <tr>
        <th>Название</th>
        <th>Площадь</th>
        <th>Прогноз</th>
      </tr>
      </thead>
      <tbody>
      <tr v-for="(row, i) in tableData" :key="i">
        <td>{{ row.name }}</td>
        <td>{{ row.area.toFixed(2) }} га</td>
        <td :class="row.forecast > 0 ? 'up' : 'down'">
          {{ row.forecast > 0 ? 'Рост' : 'Падение' }} {{ Math.abs(row.forecast) }}%
        </td>
      </tr>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import {ref, onMounted} from "vue"
import axios from "axios"
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  ArcElement,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale
} from "chart.js"
import {Bar, Pie, Line} from "vue-chartjs"

ChartJS.register(
    Title, Tooltip, Legend,
    BarElement, ArcElement,
    LineElement, PointElement,
    CategoryScale, LinearScale
)

const barData = ref(null)
const pieData = ref(null)
const lineData = ref(null)
const chartOptions = {
  responsive: true,
  maintainAspectRatio: true
}

const tableData = ref([])

onMounted(async () => {
  try {
    const res = await axios.get("http://localhost:8085/plots-all", {
      params: {owner_id: localStorage.getItem("user_id")}
    })
    const plots = res.data

    // Бар-чарт: площади по типу землепользования
    const types = {}
    plots.forEach(p => {
      const key = p.land_use || "other"
      types[key] = (types[key] || 0) + parseFloat(p.area / 1000 || 0)
    })
    barData.value = {
      labels: Object.keys(types),
      datasets: [
        {
          label: "Площадь (га)",
          data: Object.values(types),
          backgroundColor: ["#4caf50", "#2196f3", "#ff9800", "#9c27b0"]
        }
      ]
    }

    // Пай-чарт: культуры и животные
    const categories = {}
    plots.forEach(p => {
      if (p.land_use === "crop") {
        const key = p.culture || "Другое"
        categories[key] = (categories[key] || 0) + parseFloat(p.area || 0)
      } else if (p.land_use === "livestock") {
        const key = p.livestock || "Скот"
        categories[key] = (categories[key] || 0) + (p.livestock_count || 0)
      }
    })
    pieData.value = {
      labels: Object.keys(categories),
      datasets: [
        {
          data: Object.values(categories),
          backgroundColor: ["#ff6384", "#36a2eb", "#ffce56", "#8bc34a", "#ff9800"]
        }
      ]
    }

    // Линейный график: динамика по дате посева
    const byDate = {}
    plots.forEach(p => {
      if (p.sowing_date) {
        const date = p.sowing_date.split("T")[0]
        byDate[date] = (byDate[date] || 0) + parseFloat(p.area || 0)
      }
    })
    const dates = Object.keys(byDate).sort()
    lineData.value = {
      labels: dates,
      datasets: [
        {
          label: "Площадь посева (га)",
          data: dates.map(d => byDate[d]),
          borderColor: "#42A5F5",
          backgroundColor: "rgba(66,165,245,0.3)",
          fill: true,
          tension: 0.3
        }
      ]
    }

    // Таблица: площади и прогноз
    tableData.value = Object.keys(categories).map(name => ({
      name,
      area: categories[name],
      forecast: Math.floor(Math.random() * 25) - 10 // -10..+15%
    }))
  } catch (err) {
    console.error("Ошибка загрузки статистики", err)
  }
})
</script>

<style scoped>
.stats {
  max-width: 900px;
  margin: 40px auto;
  font-family: sans-serif;
}
.charts {
  display: flex;
  gap: 30px;
  flex-wrap: wrap;
}
.chart-box {
  flex: 1 1 400px;
  min-height: 400px;
}
.stats-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 30px;
}
.stats-table th, .stats-table td {
  padding: 10px;
  border: 1px solid #ddd;
  text-align: center;
}
.up { color: green; font-weight: bold }
.down { color: red; font-weight: bold }
</style>
