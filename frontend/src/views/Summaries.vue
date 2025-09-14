<script setup>
import {ref, onMounted, nextTick} from "vue"
import axios from "axios"

const summaries = ref([])
const slices = ref(["procurement_dates","weather","pests","logistics"])
const slice = ref(slices.value[0])

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
const messages = ref([])
const input = ref("")
const chatWindow = ref(null)

async function sendMessage() {
  if (!input.value.trim()) return

  messages.value.push({ role: "user", content: input.value })
  const userMessage = input.value
  input.value = ""

  await nextTick()
  scrollToBottom()

  try {
    const res = await axios.post("http://localhost:8085/ai/slice-chat", {
      owner_id: localStorage.getItem("user_id"),
      messages: messages.value,
    })
    messages.value.push({ role: "assistant", content: res.data.reply })
  } catch (e) {
    console.error("Ошибка:", e)
    messages.value.push({ role: "assistant", content: "⚠ Ошибка сервера" })
  }

  await nextTick()
  scrollToBottom()
}

function scrollToBottom() {
  if (chatWindow.value) {
    chatWindow.value.scrollTop = chatWindow.value.scrollHeight
  }
}
</script>

<template>

  <a href="/list">back</a>
  
  <div class="chat">
    <h2>AI чат по саммари</h2>
    <div ref="chatWindow" class="chat-window">
      <div
          v-for="(msg, i) in messages"
          :key="i"
          :class="['message', msg.role]"
      >
        <b v-if="msg.role === 'user'">Вы:</b>
        <b v-else>AI:</b>
        <span>{{ msg.content }}</span>
      </div>
    </div>

    <div class="input-box">
      <input
          v-model="input"
          @keyup.enter="sendMessage"
          placeholder="Введите сообщение..."
      />
      <button @click="sendMessage">Отправить</button>
    </div>
  </div>

  <hr>


  <div class="summaries">
    <h1>Summaries</h1>
    <select v-model="slice" @change="loadSummaries">
      <option selected v-for="s in slices" :key="s" :value="s">{{ s }}</option>
    </select>

    <div v-for="s in summaries" :key="s.id" class="summary-card">
      <p><b>{{ s.slice_type }}</b> ({{ s.created_at }})</p>
      <pre>{{ s.summary }}</pre>
    </div>

  </div>
</template>

<style scoped>
.summary-card {
  border:1px solid #ccc;
  padding:12px;
  margin:10px 0;
  border-radius:6px;
  background:#fafafa;
}
pre { white-space: pre-wrap; }

.chat {
  margin-top:30px;
  display:flex;
  flex-direction:column;
  gap:20px;
}
.chat-window {
  border:1px solid #ddd;
  border-radius:8px;
  padding:15px;
  height:400px;
  overflow-y:auto;
  background:#fafafa;
}
.message {
  margin-bottom:12px;
  padding:8px 12px;
  border-radius:6px;
  max-width:80%;
  word-wrap:break-word;
}
.message.user {
  background:#e3f2fd;
  align-self:flex-end;
  text-align:right;
}
.message.assistant {
  background:#f1f8e9;
  align-self:flex-start;
  text-align:left;
}
.input-box {
  display:flex;
  gap:10px;
}
input {
  flex:1;
  padding:10px;
  border-radius:6px;
  border:1px solid #ccc;
}
button {
  padding:10px 20px;
  border:none;
  border-radius:6px;
  background:#4caf50;
  color:white;
  font-weight:bold;
  cursor:pointer;
}
</style>
