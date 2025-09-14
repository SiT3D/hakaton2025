<script setup>
import {ref, onMounted} from "vue"
import axios from "axios"

const summaries = ref([])
const slice = ref("")
const slices = ref(["procurement_dates","weather","pests","logistics"])

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

// ---- чат ----
const chatInput = ref("")
const messages = ref([])

async function sendMessage() {
  if (!chatInput.value) return
  const msg = {role:"user", content: chatInput.value}
  messages.value.push(msg)

  try {
    const res = await axios.post("http://localhost:8085/ai/slice-chat", {
      owner_id: localStorage.getItem("user_id"),
      message: chatInput.value
    })
    messages.value.push({role:"assistant", content: res.data.reply})
  } catch (e) {
    console.error("chat error", e)
  }
  chatInput.value = ""
}
</script>

<template>
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

    <div class="chat">
      <h2>AI чат</h2>
      <div class="chat-window">
        <div v-for="(m,i) in messages" :key="i" :class="m.role">
          <b>{{ m.role }}:</b> {{ m.content }}
        </div>
      </div>
      <div class="chat-input">
        <input v-model="chatInput" @keyup.enter="sendMessage" placeholder="Спроси про данные..." />
        <button @click="sendMessage">Отправить</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.summary-card { border:1px solid #ccc; padding:12px; margin:10px 0; border-radius:6px; background:#fafafa; }
pre { white-space: pre-wrap; }

.chat { margin-top:30px; border-top:2px solid #ddd; padding-top:15px; }
.chat-window { max-height:300px; overflow-y:auto; margin-bottom:10px; }
.user { text-align:right; margin:5px; }
.assistant { text-align:left; margin:5px; color:#333; }
.chat-input { display:flex; gap:8px; }
.chat-input input { flex:1; padding:6px; }
.chat-input button { padding:6px 12px; }
</style>
