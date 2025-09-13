<template>
  <div class="user">
    <h1>Прогресс</h1>
    <a href="/list">Home</a>
    <br><br>

    <div class="tabs">
      <button
          :class="{ active: activeTab === 'personal' }"
          @click="activeTab = 'personal'">
        Персональный
      </button>
      <button
          :class="{ active: activeTab === 'group' }"
          @click="activeTab = 'group'">
        Групповой
      </button>
    </div>

    <!-- персональный -->
    <div v-if="activeTab === 'personal'" class="progress-wrapper">
      <div class="bar">
        <div class="fill" :style="{ height: progress + '%' }"></div>
      </div>
      <div class="info">{{ progress }}%</div>
      <div class="cards">
        <div
            v-for="(step, i) in steps"
            :key="i"
            class="card"
            :class="{ active: progress >= step.threshold }"
            :style="{ bottom: step.threshold + '%', backgroundImage: `url(${step.image})` }">
          <div class="overlay"></div>
          <div class="content">
            <h3>{{ step.title }}</h3>
            <p>{{ step.descPersonal }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- групповой -->
    <div v-else class="progress-wrapper">
      <div class="bar">
        <div class="fill" :style="{ height: groupProgress + '%' }"></div>
      </div>
      <div class="info">{{ groupProgress }}%</div>
      <div class="cards">
        <div
            v-for="(step, i) in steps"
            :key="i"
            class="card"
            :class="{ active: groupProgress >= step.threshold }"
            :style="{ bottom: step.threshold + '%', backgroundImage: `url(${step.image})` }">
          <div class="overlay"></div>
          <div class="content">
            <h3>{{ step.title }}</h3>
            <p>{{ step.descGroup }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import {onMounted, ref} from "vue"
import axios from "axios"

const activeTab = ref("personal")
const progress = ref(0)
const groupProgress = ref(0)

onMounted(async () =>
{
  try {
    const userId = localStorage.getItem("user_id")

    // персональный прогресс
    const res = await axios.get("http://localhost:8085/plots", {
      params: {owner_id: userId}
    })
    const plots = res.data

    const personalScore = plots.reduce(
        (sum, p) => sum + parseFloat(p.area || 0) + (p.livestock_count || 0),
        0
    )
    progress.value = Math.min((personalScore / 100) * 100, 100)

    // групповой прогресс
    const resGroup = await axios.get(`http://localhost:8085/progress/group/${userId}`, {
      params: {radius: 1000}
    })
    groupProgress.value = resGroup.data.progress

  } catch (err) {
    console.error("Ошибка загрузки прогресса", err)
  }
})

const steps = [
  {
    title: "🎁 Скидка на семена",
    threshold: 15,
    image: "https://picsum.photos/id/1050/400/150",
    descPersonal: "Скидка на посевной материал (ваши земля и скот)",
    descGroup: "Суммарный вклад фермеров дает скидку на семена",
  },
  {
    title: "🚜 Льготная техника",
    threshold: 40,
    image: "https://picsum.photos/id/1060/400/150",
    descPersonal: "Доступ к технике на выгодных условиях",
    descGroup: "Совместный прогресс открывает доступ к технике",
  },
  {
    title: "💧 Субсидия на воду",
    threshold: 65,
    image: "https://picsum.photos/id/1070/400/150",
    descPersonal: "Поддержка для ирригации",
    descGroup: "Группа получает субсидию на воду",
  },
  {
    title: "💰 Гос. программа",
    threshold: 90,
    image: "https://picsum.photos/id/1080/400/150",
    descPersonal: "Финансирование фермеров",
    descGroup: "Доступ к гос. программе для группы",
  },
]
</script>

<style scoped>
.user {
  max-width: 900px;
  margin: 40px auto;
  font-family: sans-serif;
  text-align: center;
}

.tabs {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin-bottom: 20px;
}
.tabs button {
  padding: 10px 20px;
  border: none;
  background: #ddd;
  cursor: pointer;
  border-radius: 6px;
  font-weight: 600;
}
.tabs button.active {
  background: #4caf50;
  color: #fff;
}

.progress-wrapper {
  display: flex;
  justify-content: center;
  gap: 40px;
  margin-top: 20px;
  align-items: flex-start;
}

.bar {
  position: relative;
  width: 20px;
  height: 600px;
  border-radius: 10px;
  background: #e0e0e0;
}
.fill {
  position: absolute;
  left: 0;
  bottom: 0;
  width: 100%;
  background: linear-gradient(to top, #4caf50, #81c784);
  border-radius: 10px;
  transition: height 0.5s;
}

.info {
  font-weight: bold;
  font-size: 18px;
  margin-left: -65px;
  z-index: 19;
  align-self: center;
}

.cards {
  position: relative;
  width: 380px;
  height: 600px;
}
.card {
  position: absolute;
  left: 0;
  width: 100%;
  height: 120px;
  transform: translateY(50%);
  border-radius: 12px;
  overflow: hidden;
  background-size: cover;
  background-position: center;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  opacity: 0.5;
  transition: all 0.4s;
}
.card.active {
  opacity: 1;
  transform: translateY(50%) scale(1.05);
  box-shadow: 0 6px 16px rgba(0,0,0,.3);
}
.card .overlay {
  position: absolute;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
}
.card .content {
  position: relative;
  color: #fff;
  text-align: left;
  padding: 15px;
}
.card h3 {
  margin: 0 0 5px;
  font-size: 18px;
  font-weight: bold;
}
.card p {
  margin: 0;
  font-size: 14px;
}
</style>
