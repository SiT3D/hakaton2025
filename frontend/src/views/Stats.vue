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
  CategoryScale,
  LinearScale
} from "chart.js"
import {Bar, Pie} from "vue-chartjs"

ChartJS.register(Title, Tooltip, Legend, BarElement, ArcElement, CategoryScale, LinearScale)

const barData = ref(null)
const pieData = ref(null)
const chartOptions = {
  responsive: true,
  maintainAspectRatio: true
}

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
          label: "Площадь",
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
</style>
