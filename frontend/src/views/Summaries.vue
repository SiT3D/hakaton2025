<script setup>
import {ref, onMounted} from "vue"
import axios from "axios"

const summaries = ref([])
const slice = ref("")
const slices = ref(["procurement_dates","weather","pests","logistics"]) // список типов

async function loadSummaries() {
  const res = await axios.get("http://localhost:8085/summaries", {
    params: {
      owner_id: localStorage.getItem("user_id"),
      slice_type: slice.value || null
    }
  })
  summaries.value = res.data
}

onMounted(loadSummaries)
</script>

<template>


  <a href="/list">back</a>

  <div class="summaries">
    <h1>Summaries</h1>
    <select v-model="slice" @change="loadSummaries">
      <option value="">Все</option>
      <option v-for="s in slices" :key="s" :value="s">{{ s }}</option>
    </select>

    <div v-for="s in summaries" :key="s.id" class="summary-card">
      <p><b>{{ s.slice_type }}</b> ({{ s.created_at }})</p>
      <pre>{{ s.summary }}</pre>
    </div>
  </div>
</template>

<style scoped>
.summaries { max-width: 800px; margin: 20px auto; }
.summary-card { border:1px solid #ccc; padding:12px; margin:10px 0; border-radius:6px; background:#fafafa; }
pre { white-space: pre-wrap; }
</style>
