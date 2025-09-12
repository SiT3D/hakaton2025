<template>
  <div class="register">
    <h1>Create new Account</h1>
    <p>Already Registered? <a href="#">Login</a></p>

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

      <button type="submit">sign up</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from "vue"

const idnp = ref("")
const password = ref("")

function submit() {
  console.log("Phone:", idnp.value, "Password:", password.value)
}
</script>

<style scoped>
.register {
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
