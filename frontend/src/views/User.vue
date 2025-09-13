<template>
  <div class="user">
    <h1>Прогресс</h1>

    <div class="progress-wrapper">
      <!-- шкала -->
      <div class="bar">
        <div class="fill" :style="{ height: progress + '%' }"></div>

        <!-- карточки прямо на шкале -->
        <div
            v-for="(step, i) in steps"
            :key="i"
            class="marker"
            :style="{ bottom: step.threshold + '%' }"
        >
          <div class="card" :class="{ active: progress >= step.threshold }">
            {{ step.title }}
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue"

const progress = ref(45) // %
const steps = [
  { title: "🎁 Скидка на семена", threshold: 10 },
  { title: "🚜 Льготная техника", threshold: 30 },
  { title: "💧 Субсидия на воду", threshold: 60 },
  { title: "💰 Гос. программа", threshold: 90 },
]
</script>

<style scoped>
.user {
  max-width: 600px;
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
  height: 600px; /* фикс высота шкалы */
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
  left: 50px; /* карточка справа от шкалы */
  transform: translateY(50%); /* выравнивание */
}
.card {
  width: 180px;
  padding: 10px;
  border-radius: 10px;
  border: 2px solid #aaa;
  background: #eee;
  color: #777;
  transition: all 0.3s;
}
.card.active {
  background: #4caf50;
  color: #fff;
  border-color: #2e7d32;
}
</style>
