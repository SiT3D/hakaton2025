import { createRouter, createWebHistory } from 'vue-router'
import Register from '../views/Register.vue'
import Login from '../views/Login.vue'
import List from '../views/List.vue'

const routes = [
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/list', component: List },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

export default router
