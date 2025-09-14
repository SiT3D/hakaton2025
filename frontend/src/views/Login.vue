<template>
  <div class="login">
    <h1>Login</h1>
    <p>Sign in to continue or <a href="/register">Sign Up</a></p>

    <form @submit.prevent="submit">
      <label>Phone</label>
      <input
          v-model="idnp"
          type="tel"
          inputmode="tel"
          pattern="^\+[0-9]{10,15}$"
          placeholder="+0123456789"
          required
      />

      <label>Password</label>
      <input v-model="password" type="password" placeholder="******" required />

      <button type="submit">login</button>
    </form>

    <p v-if="error" style="color:red; margin-top:10px;">{{ error }}</p>
  </div>
</template>

<script setup>
import { ref } from "vue"
import axios from "axios"
import { useRouter } from "vue-router"   // 👈 добавить

const idnp = ref("")
const password = ref("")
const error = ref("")

const router = useRouter()   // 👈 инициализировать

async function submit() {
  error.value = ""
  try {
    const res = await axios.post("http://localhost:8085/login", {
      login: idnp.value,
      password: password.value,
    }, { headers: { "Content-Type": "application/json" }})
    localStorage.setItem("token", res.data.token)
    localStorage.setItem("user_id", res.data.user.id)
    router.push("/list")   // 👈 теперь работает корректно
  } catch (err) {
    error.value = err.response?.data?.message || "Ошибка входа"
  }
}
</script>


<style scoped>
.login {
  max-width: 400px;
  margin: 60px auto;
  text-align: center;
  font-family: sans-serif;
}
form {
  display: flex;
  flex-direction: column;
  gap: 15px;
}
input {
  padding: 10px;
  border: 1px solid #ccc;
  font-size: 16px;
}
button {
  background: black;
  color: white;
  padding: 12px;
  border: none;
  cursor: pointer;
  font-weight: bold;
}
</style>
