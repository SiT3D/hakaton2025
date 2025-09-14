<template>
  <div class="chat">
    <h1>AI Ассистент</h1>
    <div class="chat-window">
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
</template>

<script setup>
import { ref } from "vue"
import axios from "axios"

const messages = ref([])
const input = ref("")

async function sendMessage() {
  if (!input.value.trim()) return

  // добавляем сообщение пользователя
  messages.value.push({ role: "user", content: input.value })

  const userMessage = input.value
  input.value = ""

  try {
    const res = await axios.post("http://localhost:8085/ai/chat", {
      message: userMessage,
    })

    messages.value.push({ role: "assistant", content: res.data.reply })
  } catch (e) {
    console.error("Ошибка:", e)
    messages.value.push({ role: "assistant", content: "⚠ Ошибка сервера" })
  }
}
</script>

<style scoped>
.chat {
  max-width: 700px;
  margin: 40px auto;
  font-family: sans-serif;
  display: flex;
  flex-direction: column;
  gap: 20px;
}
.chat-window {
  border: 1px solid #ddd;
  border-radius: 8px;
  padding: 15px;
  height: 400px;
  overflow-y: auto;
  background: #fafafa;
}
.message {
  margin-bottom: 12px;
  padding: 8px 12px;
  border-radius: 6px;
  max-width: 80%;
  word-wrap: break-word;
}
.message.user {
  background: #e3f2fd;
  align-self: flex-end;
  text-align: right;
}
.message.assistant {
  background: #f1f8e9;
  align-self: flex-start;
  text-align: left;
}
.input-box {
  display: flex;
  gap: 10px;
}
input {
  flex: 1;
  padding: 10px;
  border-radius: 6px;
  border: 1px solid #ccc;
}
button {
  padding: 10px 20px;
  border: none;
  border-radius: 6px;
  background: #4caf50;
  color: white;
  font-weight: bold;
  cursor: pointer;
}
</style>
