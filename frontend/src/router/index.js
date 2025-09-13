import { createRouter, createWebHistory } from 'vue-router'
import Register from '../views/Register.vue'
import Login from '../views/Login.vue'
import List from '../views/List.vue'
import PlotCreate from '../views/PlotCreatePage.vue'

const routes = [
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/list', component: List, meta: { requiresAuth: true } },
    { path: '/create', component: PlotCreate, meta: { requiresAuth: true } },
]

const router = createRouter({
    history: createWebHistory(),
    routes,
})

router.beforeEach((to, from, next) => {
    const token = localStorage.getItem('token')
    console.log('Guard:', { path: to.path, token })

    // если роут защищён и токена нет → редиректим на логин
    if (to.meta.requiresAuth && !token) {
        return next('/login')
    }

    // если уже авторизован и идёт на login/register → редиректим на list
    if (token && (to.path === '/login' || to.path === '/register')) {
        return next('/list')
    }

    return next()
})

export default router
