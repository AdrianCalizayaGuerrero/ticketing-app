import './bootstrap'
import { createApp, reactive } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import LoginView from './views/LoginView.vue'
import DashboardView from './views/DashboardView.vue'
import TicketsView from './views/TicketsView.vue'
import TicketDetail from './views/TicketDetail.vue'
import UsuariosView from './views/UsuariosView.vue'
import ReportesView from './views/ReportesView.vue'

// Estado global de autenticación
export const auth = reactive({
    user: JSON.parse(localStorage.getItem('user') || 'null'),
    token: localStorage.getItem('token') || null,
    isLoggedIn() { return !!this.token },
    setUser(user, token) {
        this.user = user
        this.token = token
        localStorage.setItem('user', JSON.stringify(user))
        localStorage.setItem('token', token)
    },
    logout() {
        this.user = null
        this.token = null
        localStorage.removeItem('user')
        localStorage.removeItem('token')
    }
})

const router = createRouter({
    history: createWebHistory(),
    routes: [
        { path: '/',          redirect: '/dashboard' },
        { path: '/login',     component: LoginView,     meta: { public: true } },
        { path: '/dashboard', component: DashboardView  },
        { path: '/tickets',   component: TicketsView    },
        { path: '/tickets/:id', component: TicketDetail },
        { path: '/usuarios',  component: UsuariosView,  meta: { roles: ['admin', 'agente'] } },
        { path: '/reportes',  component: ReportesView,  meta: { roles: ['admin', 'agente'] } },
    ]
})

router.beforeEach((to, from, next) => {
    if (!to.meta.public && !auth.isLoggedIn()) {
        return next('/login')
    }
    if (to.meta.roles && !to.meta.roles.includes(auth.user?.role)) {
        return next('/dashboard')
    }
    if (to.path === '/login' && auth.isLoggedIn()) {
        return next('/dashboard')
    }
    next()
})

const app = createApp(App)
app.provide('auth', auth)
app.use(router)
app.mount('#app')
