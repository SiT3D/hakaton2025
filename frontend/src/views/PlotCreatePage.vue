<template>
  <div class="create-plot">
    <h1>Создание нового участка</h1>

    <form @submit.prevent="submit" class="create-form">
      <label>Название</label>
      <input v-model="name" type="text" placeholder="Название участка" required />

      <label>Кадастровый номер</label>
      <input v-model="cadastral" type="text" placeholder="MD-2025-001" required />

      <label>Дата посева</label>
      <input v-model="sowingDate" type="date" />

      <label>Площадь (га)</label>
      <input v-model="area" type="number" step="0.01" placeholder="Например 23.5" />

      <!-- Ветвление по использованию -->
      <label>Использование земли</label>
      <select v-model="usageType" required>
        <option value="">-- выберите --</option>
        <option value="crop">Посев (культура)</option>
        <option value="livestock">Животноводство (скот)</option>
      </select>

      <div v-if="usageType === 'crop'">
        <label>Культура</label>
        <input list="cropList" v-model="crop" placeholder="Начните вводить..." />
        <datalist id="cropList">
          <option v-for="c in crops" :key="c" :value="c" />
        </datalist>

        <label>Описание культуры</label>
        <textarea v-model="cropDescription"></textarea>
      </div>

      <div v-if="usageType === 'livestock'">
        <label>Тип скота</label>
        <input list="animalList" v-model="livestock" placeholder="Начните вводить..." />
        <datalist id="animalList">
          <option v-for="a in animals" :key="a" :value="a" />
        </datalist>

        <label>Описание</label>
        <textarea v-model="livestockDescription"></textarea>
      </div>

      <label>Фото</label>
      <input type="file" multiple @change="handleFiles" />

      <div class="thumbs">
        <div v-for="(img, i) in previews" :key="i" class="thumb">
          <img :src="img" />
          <button type="button" @click="removeImage(i)">✖</button>
        </div>
      </div>

      <h3>Карта участка</h3>
      <div ref="map" class="map"></div>

      <div class="polygons">
        <h3>Выделенные полигоны</h3>
        <ul>
          <li v-for="(poly, index) in polygons" :key="index">
            Полигон {{ index + 1 }} —
            <span>{{ poly.length }} точек</span>
            <button type="button" @click="removePolygon(index)">✖</button>
          </li>
        </ul>
      </div>

      <button type="submit">Сохранить</button>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue"

const name = ref("")
const cadastral = ref("")
const sowingDate = ref("")
const area = ref("")

// выбор посев / скот
const usageType = ref("")
const crop = ref("")
const cropDescription = ref("")
const livestock = ref("")
const livestockDescription = ref("")

const crops = ["Пшеница", "Кукуруза", "Подсолнечник", "Картофель"]
const animals = ["Крупный рогатый скот", "Овцы", "Козы", "Свинина", "Птица"]

// фото
const previews = ref([])
function handleFiles(e) {
  const files = Array.from(e.target.files)
  previews.value = files.map((f) => URL.createObjectURL(f))
}
function removeImage(i) {
  previews.value.splice(i, 1)
}

// карта и полигоны
const map = ref(null)
const polygons = ref([])

onMounted(() => {
  const gmap = new google.maps.Map(map.value, {
    center: { lat: 47.0105, lng: 28.8638 },
    zoom: 10,
  })

  const drawingManager = new google.maps.drawing.DrawingManager({
    drawingMode: google.maps.drawing.OverlayType.POLYGON,
    drawingControl: true,
    drawingControlOptions: {
      position: google.maps.ControlPosition.TOP_CENTER,
      drawingModes: ["polygon"],
    },
  })
  drawingManager.setMap(gmap)

  google.maps.event.addListener(drawingManager, "overlaycomplete", (event) => {
    if (event.type === "polygon") {
      const path = event.overlay.getPath().getArray()
      const coords = path.map((p) => ({
        lat: p.lat(),
        lng: p.lng(),
      }))
      polygons.value.push(coords)
    }
  })
})

function removePolygon(index) {
  polygons.value.splice(index, 1)
}

function submit() {
  console.log("Submit:", {
    name: name.value,
    cadastral: cadastral.value,
    sowingDate: sowingDate.value,
    area: area.value,
    usageType: usageType.value,
    crop: crop.value,
    cropDescription: cropDescription.value,
    livestock: livestock.value,
    livestockDescription: livestockDescription.value,
    polygons: polygons.value,
    images: previews.value,
  })
}
</script>

<style>
.create-plot {
  max-width: 600px;
  margin: 40px auto;
  font-family: sans-serif;
}

.create-form {
  display: flex;
  flex-direction: column;
  gap: 12px;
  background: #fff;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.create-form label {
  font-weight: bold;
  font-size: 14px;
}

input,
select,
textarea {
  padding: 8px;
  font-size: 14px;
  border: 1px solid #ccc;
  border-radius: 4px;
  width: 100%;
  box-sizing: border-box;
}

textarea {
  min-height: 70px;
  resize: vertical;
}

button {
  background: black;
  color: white;
  padding: 10px;
  border: none;
  cursor: pointer;
  font-weight: bold;
  border-radius: 4px;
  align-self: flex-start;
}

.map {
  width: 100%;
  height: 400px;
  margin-top: 10px;
  border-radius: 6px;
  overflow: hidden;
  border: 1px solid #ccc;
}

.thumbs {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.thumb {
  position: relative;
}

.thumb img {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.thumb button {
  position: absolute;
  top: -6px;
  right: -6px;
  background: red;
  color: white;
  border: none;
  border-radius: 50%;
  width: 20px;
  height: 20px;
  cursor: pointer;
  font-size: 12px;
  line-height: 18px;
}

.polygons ul {
  list-style: none;
  padding: 0;
  margin: 0;
}

.polygons li {
  margin: 5px 0;
  background: #f9f9f9;
  padding: 6px 10px;
  border-radius: 4px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.polygons button {
  background: red;
  color: white;
  border: none;
  border-radius: 4px;
  padding: 2px 6px;
  cursor: pointer;
}

</style>
