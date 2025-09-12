<script setup>
import { ref } from "vue"
import { onMounted, onBeforeUnmount } from "vue"

import { useRouter } from "vue-router"

const router = useRouter()

function goToCreate() {
  router.push('/create')
}

function handleClickOutside(e) {
  const menu = document.querySelector(".menu")
  if (menu && !menu.contains(e.target) && !e.target.classList.contains("dots")) {
    openMenu.value = null
  }
}

onMounted(() => {
  document.addEventListener("click", handleClickOutside)
})
onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside)
})

const plots = ref([
  {
    id: 1,
    name: "North Field",
    area: "53 ha",
    crop: "Vegetables",
    cadastral: "MD-2025-001",
    sowingDate: "2025-03-15",
    thumbnails: [
      "https://placehold.co/80x60",
      "https://placehold.co/80x60",
      "https://placehold.co/80x60"
    ]
  },
  {
    id: 2,
    name: "South Plot",
    area: "23 ha",
    crop: "Pasture",
    cadastral: "MD-2025-002",
    sowingDate: "2025-04-02",
    thumbnails: [
      "https://placehold.co/80x60",
      "https://placehold.co/80x60"
    ]
  },
  {
    id: 3,
    name: "East Meadow",
    area: "12 ha",
    crop: "Corn",
    cadastral: "MD-2025-003",
    sowingDate: "2025-04-10",
    thumbnails: [
      "https://placehold.co/80x60",
      "https://placehold.co/80x60",
    ]
  }
])

const selected = ref([])
const openMenu = ref(null)

function toggleMenu(id) {
  openMenu.value = openMenu.value === id ? null : id
}

function change(plot) { console.log("Change", plot) }
function exportOne(plot) { console.log("Export", plot) }
function remove(plot) { console.log("Delete", plot) }
</script>

<template>
  <div class="farm">
    <div class="farm-header">
      <h1>Farm Plot</h1>
      <button class="add" @click="goToCreate">+</button>
    </div>

    <div v-for="plot in plots" :key="plot.id" class="plot-card">
      <input type="checkbox" :value="plot.id" v-model="selected" />

      <div class="content">
        <div class="card-header">
          <h3>{{ plot.name }}</h3>
          <button @click="toggleMenu(plot.id)" class="dots">...</button>
          <div v-if="openMenu === plot.id" class="menu">
            <button @click="change(plot)">Change</button>
            <button @click="exportOne(plot)">Export</button>
            <button @click="remove(plot)">Delete</button>
          </div>
        </div>

        <p><b>Area:</b> {{ plot.area }}</p>
        <p><b>Crop:</b> {{ plot.crop }}</p>
        <p><b>Cadastral:</b> {{ plot.cadastral }}</p>
        <p><b>Sowing date:</b> {{ plot.sowingDate }}</p>

        <div class="thumbs">
          <img v-for="(img, i) in plot.thumbnails" :key="i" :src="img" />
        </div>
      </div>
    </div>

    <button v-if="selected.length > 1" class="export-all">
      export selected ({{ selected.length }})
    </button>
  </div>
</template>



<style scoped>
.farm {
  max-width: 700px;
  margin: 40px auto;
  font-family: sans-serif;
}

/* верхний хедер */
.farm-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* кнопка добавить */
.add {
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: black;
  color: white;
  font-size: 20px;
  cursor: pointer;
}

.plot-card {
  display: grid;
  grid-template-columns: auto 1fr;
  gap: 12px;
  margin: 15px 0;
  padding: 12px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background: #fafafa;
  position: relative;
}


/* контент внутри */
.content {
  flex: 1;
}

/* заголовок карточки */
.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* кнопка троеточие */
.dots {
  border: none;
  background: none;
  font-size: 20px;
  cursor: pointer;
  color: black;
}

/* меню */
.menu {
  position: absolute;
  right: 10px;
  top: 40px;
  background: white;
  border: 1px solid #ccc;
  display: flex;
  flex-direction: column;
  z-index: 10;
  border-radius: 4px;
}
.menu button {
  border: none;
  background: none;
  padding: 8px 12px;
  cursor: pointer;
  text-align: left;
}
.menu button:nth-child(1) { color: green; }
.menu button:nth-child(2) { color: blue; }
.menu button:nth-child(3) { color: red; }

/* экспорт */
.export-all {
  display: block;
  position: fixed;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  padding: 12px;
  background: black;
  color: white;
  border: none;
  cursor: pointer;
  font-weight: bold;
}

/* чекбоксы */
input[type="checkbox"] {
  width: 20px;
  height: 20px;
  margin-top: 5px;
}

/* миниатюры */
.thumbs {
  display: flex;
  gap: 6px;
  margin-top: 8px;
  flex-wrap: wrap;
}
.thumbs img {
  width: 70px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
  border: 1px solid #ccc;
}
</style>
