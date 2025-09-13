<template>
  <div class="user">
    <h1>Прогресс</h1>

    <!-- табы -->
    <div class="tabs">
      <button
          :class="{ active: activeTab === 'personal' }"
          @click="activeTab = 'personal'"
      >
        Персональный
      </button>
      <button
          :class="{ active: activeTab === 'group' }"
          @click="activeTab = 'group'"
      >
        Групповой
      </button>
    </div>

    <!-- персональный -->
    <div class="principle">
      В персональном прогрессе учитываются только ваши ресурсы: земля и скот.
      Чем больше хозяйство и вклад, тем выше ваш личный прогресс.
    </div>
    <div class="principle">
      В групповом прогрессе считается вклад фермеров в вашем радиусе.
      Все участники делятся результатами, и суммарный прогресс открывает бонусы для всей группы.
    </div>
    <div v-if="activeTab === 'personal'" class="progress-wrapper">
      <div class="bar">
        <div class="fill" :style="{ height: progress + '%' }"></div>
      </div>
      <div class="cards">
        <!-- описание принципа -->

        <!-- карточки -->
        <div
            v-for="(step, i) in steps"
            :key="i"
            class="card"
            :class="{ active: progress >= step.threshold }"
            :style="{ bottom: step.threshold + '%', backgroundImage: `url(${step.image})` }"
        >
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
      <div class="cards">
        <!-- описание принципа -->

        <!-- карточки -->
        <div
            v-for="(step, i) in steps"
            :key="i"
            class="card"
            :class="{ active: groupProgress >= step.threshold }"
            :style="{ bottom: step.threshold + '%', backgroundImage: `url(${step.image})` }"
        >
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
import { ref } from "vue"

const activeTab = ref("personal")

// прогресс
const progress = ref(45)
const groupProgress = ref(70)

// бонусы одинаковые
const steps = [
  {
    title: "🎁 Скидка на семена",
    threshold: 15,
    image: "https://picsum.photos/id/1050/400/150",
    descPersonal: "Получите скидку на посевной материал, исходя из вашей земли и скота",
    descGroup: "Суммарный вклад фермеров рядом с вами дает скидку на семена",
  },
  {
    title: "🚜 Льготная техника",
    threshold: 40,
    image: "https://picsum.photos/id/1060/400/150",
    descPersonal: "Доступ к технике на выгодных условиях, исходя из вашего хозяйства",
    descGroup: "Совместный прогресс фермеров открывает доступ к льготной технике",
  },
  {
    title: "💧 Субсидия на воду",
    threshold: 65,
    image: "https://picsum.photos/id/1070/400/150",
    descPersonal: "Господдержка для ирригации на вашей земле",
    descGroup: "Когда фермеры в вашем радиусе развиваются, группа получает субсидию",
  },
  {
    title: "💰 Гос. программа",
    threshold: 90,
    image: "https://picsum.photos/id/1080/400/150",
    descPersonal: "Финансирование фермеров с учетом ваших ресурсов",
    descGroup: "Групповой прогресс обеспечивает доступ к гос. программе",
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

/* шкала */
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

/* карточки */
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

/* описание принципа */
.principle {
  position: static;
  width: 100%;
  text-align: left;
  font-size: 14px;
  color: #444;
  margin-bottom: 20px;
}
</style>
