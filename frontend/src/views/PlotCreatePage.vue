<script setup>
import { ref } from "vue"
import Map from "../components/Map.vue";

const name = ref("")
const area = ref("")
const crop = ref("")
const cadastral = ref("")
const sowingDate = ref("")
const thumbnails = ref([])

function handleFiles(e) {
  thumbnails.value = Array.from(e.target.files).map(f => URL.createObjectURL(f))
}

function submit() {
  console.log({
    name: name.value,
    area: area.value,
    crop: crop.value,
    cadastral: cadastral.value,
    sowingDate: sowingDate.value,
    thumbnails: thumbnails.value
  })
}
</script>


<template>
  <div class="create-plot">
    <h1>Create New Plot</h1>

    <form @submit.prevent="submit">

      <Map/>

      <label>Name</label>
      <input v-model="name" type="text" required />

      <label>Area (ha)</label>
      <input v-model="area" type="text" required />

      <label>Crop</label>
      <input v-model="crop" type="text" required />

      <label>Cadastral Number</label>
      <input v-model="cadastral" type="text" required />

      <label>Sowing Date</label>
      <input v-model="sowingDate" type="date" required />

      <label>Photos</label>
      <input type="file" accept="image/*" multiple @change="handleFiles" />

      <div class="thumbs">
        <img v-for="(img, i) in thumbnails" :key="i" :src="img" />
      </div>

      <button type="submit">Create</button>
    </form>
  </div>
</template>

<style scoped>
.create-plot {
  max-width: 500px;
  margin: 40px auto;
  font-family: sans-serif;
}
form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}
input, button {
  padding: 10px;
  font-size: 15px;
  border: 1px solid #ccc;
}
button {
  background: black;
  color: white;
  font-weight: bold;
  border: none;
  cursor: pointer;
}
.thumbs {
  display: flex;
  gap: 6px;
  flex-wrap: wrap;
}
.thumbs img {
  width: 80px;
  height: 60px;
  object-fit: cover;
  border: 1px solid #ccc;
  border-radius: 4px;
}
</style>
