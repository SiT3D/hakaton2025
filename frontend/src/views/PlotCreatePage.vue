<template>
  <div class="create-plot">
    <h1>Создание нового участка</h1>

    <form @submit.prevent="submit" class="create-form">
      <label>Название</label>
      <input v-model="name" type="text" placeholder="Название участка" required />

      <label>Кадастровый номер</label>
      <input v-model="cadastral" type="text" placeholder="MD-2025-001" required />

      <label>Дата посева</label>
      <input v-model="sowingDate" type="date" required />

      <label>Площадь (га)</label>
      <input v-model="area" type="number" step="0.01" placeholder="Например 23.5" required />

      <label>Использование земли</label>
      <select v-model="usageType" required>
        <option value="">-- выберите --</option>
        <option value="crop">Посев (культура)</option>
        <option value="livestock">Животноводство (скот)</option>
      </select>

      <div v-if="usageType === 'crop'">
        <label>Культура</label>
        <input list="cropList" v-model="crop" placeholder="Начните вводить..." required />
        <datalist id="cropList">
          <option v-for="c in crops" :key="c" :value="c" />
        </datalist>

        <label>Описание культуры</label>
        <textarea v-model="cropDescription"></textarea>
      </div>

      <div v-if="usageType === 'livestock'">
        <label>Тип скота</label>
        <input list="animalList" v-model="livestock" placeholder="Начните вводить..." required />
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
      <!-- пробрасываем координаты из карты -->
      <Map v-model:coords="coords" />

      <button type="submit">Сохранить</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue"
import axios from "axios"
import Map from "@/components/Map.vue"
import { useRouter } from "vue-router"

const router = useRouter()

const name = ref("")
const cadastral = ref("")
const sowingDate = ref("")
const area = ref("")
const usageType = ref("")
const crop = ref("")
const cropDescription = ref("")
const livestock = ref("")
const livestockDescription = ref("")
const coords = ref([]) // сюда прилетят точки из карты

const crops = ["Пшеница", "Кукуруза", "Подсолнечник", "Картофель"]
const animals = ["Крупный рогатый скот", "Овцы", "Козы", "Свинина", "Птица"]

const files = ref([])
const previews = ref([])

function handleFiles(e) {
  files.value = Array.from(e.target.files)
  previews.value = files.value.map((f) => URL.createObjectURL(f))
}
function removeImage(i) {
  files.value.splice(i, 1)
  previews.value.splice(i, 1)
}

async function submit() {
  if (!coords.value.length) {
    alert("Нужно нарисовать хотя бы один участок на карте")
    return
  }
  try {
    const form = new FormData()
    form.append("name", name.value)
    form.append("owner_id", localStorage.getItem("user_id"))
    form.append("cadastral_number", cadastral.value)
    form.append("sowing_date", sowingDate.value)
    form.append("area", area.value)
    form.append("land_use", usageType.value)
    form.append("culture", crop.value)
    form.append("culture_description", cropDescription.value)
    form.append("livestock", livestock.value)
    form.append("livestock_description", livestockDescription.value)
    form.append("coordinates", JSON.stringify(coords.value))

    files.value.forEach((f) => form.append("photos[]", f))

    await axios.post("http://localhost:8085/create-plot", form, {
      headers: { "Content-Type": "multipart/form-data" },
    })

    alert("Участок сохранён ✅")
    router.push("/list");
  } catch (err) {
    console.error(err)
    alert("Ошибка сохранения")
  }
}
</script>

<style scoped>
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
</style>
