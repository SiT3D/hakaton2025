<script setup>
import {onBeforeUnmount, onMounted, ref} from "vue"
import axios from "axios"
import {useRouter} from "vue-router"

const router = useRouter()

function goToCreate() {
  router.push("/create")
}

function handleClickOutside(e) {
  const menu = document.querySelector(".menu")
  if (menu && !menu.contains(e.target) && !e.target.classList.contains("dots")) {
    openMenu.value = null
  }
}

onMounted(async () => {
  document.addEventListener("click", handleClickOutside)

  try {
    const res = await axios.get("http://localhost:8085/plots", {
      params: {owner_id: localStorage.getItem("user_id")}
    })

    plots.value = res.data.map(p => ({
      id: p.id,
      owner_id: p.owner_id,
      name: p.name,
      cadastral: p.cadastral_number,
      sowingDate: p.sowing_date,
      area: p.area,
      land_use: p.land_use,
      culture: p.culture,
      culture_description: p.culture_description,
      livestock: p.livestock,
      livestock_description: p.livestock_description,
      livestock_count: p.livestock_count,
      geometry: p.geometry,
      created_at: p.created_at,
      updated_at: p.updated_at,
      thumbnails: p.photos && p.photos.length ? p.photos : []
    }))

  } catch (err) {
    console.error("Ошибка загрузки плотов", err)
  }
})

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside)
})

const plots = ref([])
const selected = ref([])
const openMenu = ref(null)

function toggleMenu(id) {
  openMenu.value = openMenu.value === id ? null : id
}

function exportOne(plot) {
  exportCsv([plot])
}

function exportCsv(data) {
  if (!data.length) return
  const headers = Object.keys(data[0]).join(",")
  const rows = data.map(obj =>
      Object.values(obj)
          .map(v => `"${String(v).replace(/"/g, '""')}"`)
          .join(",")
  )
  const csv = [headers, ...rows].join("\n")

  const blob = new Blob([csv], {type: "text/csv;charset=utf-8;"})
  const url = URL.createObjectURL(blob)
  const link = document.createElement("a")
  link.setAttribute("href", url)
  link.setAttribute("download", "plots-report.csv")
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

async function remove(plot) {
  try {
    await axios.delete(`http://localhost:8085/plots/${plot.id}`)
    plots.value = plots.value.filter(p => p.id !== plot.id)
  } catch (e) {
    console.error("Ошибка удаления", e)
  }
}

function exportSelected() {
  const rows = plots.value.filter(p => selected.value.includes(p.id))
  exportCsv(rows)
}
</script>

<template>
  надо добавить
  <br>
  <br>
  - надо еще поля данных добавить (тип почвы и тд для анализа ИИ!)
  <br>
  - хотят не раскрывать реальные цифры, спастись от налогов, но иметь анализ цифр!
  <br>
  - нужен ии ассистент, который посмотрит данные POLS и ответит на вопросы.
  - и не только по данным полс, нужна обработка погоды вредителей и др. чего я еще не знаю
  - так же должен быть мониторинг разных ситуаций, вредители, болезни, общие движухи типа закупки продажи опт выгода и логистика.
  <br>
  - НОВЫЕ ВЫЗОВЫ
  <br>


  <a href="/user">Progress page</a>
  <br>
  <a href="/stats">Stats</a>
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
            <button @click="exportOne(plot)">Export</button>
            <button @click="remove(plot)">Delete</button>
          </div>
        </div>

        <p><b>Area:</b> {{ plot.area }}</p>
        <p v-if="plot.land_use === 'crop'"><b>Culture:</b> {{ plot.culture }}</p>
        <p v-else-if="plot.land_use === 'livestock'"><b>Livestock:</b> {{ plot.livestock }}</p>
        <p><b>Cadastral:</b> {{ plot.cadastral }}</p>
        <p><b>Sowing date:</b> {{ plot.sowingDate }}</p>

        <div class="thumbs">
          <img v-for="(img, i) in plot.thumbnails" :key="i" :src="img" />
        </div>
      </div>
    </div>

    <button v-if="selected.length > 0" class="export-all" @click="exportSelected">
      export selected ({{ selected.length }})
    </button>
  </div>
</template>

<style scoped>
.farm
{
  max-width: 700px;
  margin: 40px auto;
  font-family: sans-serif;
}

/* верхний хедер */
.farm-header
{
  display: flex;
  justify-content: space-between;
  align-items: center;
}

/* кнопка добавить */
.add
{
  width: 40px;
  height: 40px;
  border: none;
  border-radius: 50%;
  background: black;
  color: white;
  font-size: 20px;
  cursor: pointer;
}

.plot-card
{
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
.content
{
  flex: 1;
}

/* заголовок карточки */
.card-header
{
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.card-header h3
{
  margin: 6px 0;
}

/* кнопка троеточие */
.dots
{
  border: none;
  background: none;
  font-size: 20px;
  cursor: pointer;
  color: black;
}

/* меню */
.menu
{
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

.menu button
{
  border: none;
  background: none;
  padding: 8px 12px;
  cursor: pointer;
  text-align: left;
}

.menu button:nth-child(1)
{
  color: green;
}

.menu button:nth-child(2)
{
  color: blue;
}

.menu button:nth-child(3)
{
  color: red;
}

/* экспорт */
.export-all
{
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
input[type="checkbox"]
{
  width: 20px;
  height: 20px;
  margin-top: 5px;
}

/* миниатюры */
.thumbs
{
  display: flex;
  gap: 6px;
  margin-top: 8px;
  flex-wrap: wrap;
}

.thumbs img
{
  width: 70px;
  height: 50px;
  object-fit: cover;
  border-radius: 4px;
  border: 1px solid #ccc;
}
</style>
