<template>
  <div class="user">
    <h1>Прогресс</h1>

    <div class="progress-wrapper">
      <div class="bar">
        <div class="fill" :style="{ height: progress + '%' }"></div>

        <div
            v-for="(step, i) in steps"
            :key="i"
            class="marker"
            :style="{ bottom: step.threshold + '%' }"
        >
          <div
              class="card"
              :class="{ active: progress >= step.threshold }"
              :style="{ backgroundImage: `url(${step.image})` }"
          >
            <div class="overlay"></div>
            <div class="content">
              <h3>{{ step.title }}</h3>
              <p>{{ step.desc }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"

const progress = ref(45)

const steps = [
  {
    title: "🎁 Скидка на семена",
    desc: "Получите скидку на посевной материал",
    threshold: 10,
    image: "https://picsum.photos/id/1050/400/150", // тут своя картинка
  },
  {
    title: "🚜 Льготная техника",
    desc: "Доступ к технике на выгодных условиях",
    threshold: 30,
    image: "https://picsum.photos/id/1060/400/150",
  },
  {
    title: "💧 Субсидия на воду",
    desc: "Господдержка для ирригации",
    threshold: 60,
    image: "https://picsum.photos/id/1070/400/150",
  },
  {
    title: "💰 Гос. программа",
    desc: "Финансирование фермеров",
    threshold: 90,
    image: "https://picsum.photos/id/1080/400/150",
  },
]
</script>

<style scoped>
.user {
  max-width: 800px;
  margin: 40px auto;
  font-family: sans-serif;
  text-align: center;
}

.progress-wrapper {
  display: flex;
  justify-content: center;
  margin-top: 40px;
}

.bar {
  position: relative;
  width: 40px;
  height: 600px;
  border: 2px solid #333;
  background: #f5f5f5;
}

.fill {
  position: absolute;
  bottom: 0;
  width: 100%;
  background: linear-gradient(to top, #4caf50, #81c784);
  transition: height 0.5s;
}

.marker {
  position: absolute;
  left: 60px;
  transform: translateY(50%);
}

.card {
  position: relative;
  width: 350px;
  height: 120px;
  border-radius: 12px;
  overflow: hidden;
  background-size: cover;
  background-position: center;
  margin-bottom: 15px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
  opacity: 0.5;
  transition: all 0.4s;
}

.card.active {
  opacity: 1;
  transform: scale(1.05);
  box-shadow: 0 6px 16px rgba(0, 0, 0, 0.3);
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
