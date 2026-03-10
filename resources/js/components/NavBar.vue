<template>
  <nav>
    <router-link class="nav-brand" to="/dashboard"><span>🎟️</span> TicketApp</router-link>
    <router-link to="/dashboard">Dashboard</router-link>
    <router-link to="/tickets">Tickets</router-link>
    <router-link v-if="canSee('usuarios')" to="/usuarios">Usuarios</router-link>
    <router-link v-if="canSee('reportes')" to="/reportes">Reportes</router-link>
    <div class="nav-right">
      <router-link v-if="role === 'cliente'" to="/tickets?new=1" class="new-ticket-btn">+ Nuevo Ticket</router-link>
      <div class="nav-user">
        <div class="nav-avatar">{{ initials }}</div>
        <span class="nav-name">{{ auth.user?.name?.split(' ')[0] }}</span>
        <span class="role-chip" :class="`role-${role}`">{{ roleLabel }}</span>
      </div>
      <button class="nav-logout" @click="handleLogout">Cerrar sesión</button>
    </div>
  </nav>
</template>

<script setup>
import { computed } from 'vue'
import { useRouter } from 'vue-router'
import { auth } from '../app.js'
import api from '../services/api.js'

const router = useRouter()
const role = computed(() => auth.user?.role ?? 'cliente')
const roleLabel = computed(() => ({
  admin: 'Administrador', agente: 'Agente',
  soporte: 'Soporte', cliente: 'Cliente'
}[role.value] ?? role.value))

const initials = computed(() => {
  const name = auth.user?.name ?? auth.user?.username ?? 'U'
  return name.split(' ').map(w => w[0]).join('').toUpperCase().slice(0,2)
})

function canSee(section) {
  if (section === 'usuarios') return ['admin','agente'].includes(role.value)
  if (section === 'reportes') return ['admin','agente'].includes(role.value)
  return true
}

async function handleLogout() {
  try { await api.logout() } catch(e) {}
  auth.logout()
  router.push('/login')
}
</script>

<style scoped>
nav {
  background: #1a73e8; height: 58px;
  display: flex; align-items: center; padding: 0 32px; gap: 28px;
  box-shadow: 0 2px 8px rgba(26,115,232,.3);
  position: sticky; top: 0; z-index: 100;
}
.nav-brand { display:flex; align-items:center; gap:8px; font-weight:800; font-size:1.15rem; color:#fff; text-decoration:none; margin-right:4px; }
.nav-brand span { font-size:1.3rem; }
nav a { color:rgba(255,255,255,.82); text-decoration:none; font-size:.92rem; font-weight:500; padding:6px 12px; border-radius:6px; transition:all .2s; }
nav a:hover, nav a.router-link-active { color:#fff; background:rgba(255,255,255,.18); }
.nav-right { margin-left:auto; display:flex; align-items:center; gap:12px; }
.nav-user { display:flex; align-items:center; gap:8px; }
.nav-avatar { width:32px; height:32px; border-radius:50%; background:rgba(255,255,255,.25); display:flex; align-items:center; justify-content:center; font-weight:700; font-size:.82rem; color:#fff; }
.nav-name { color:#fff; font-size:.88rem; font-weight:600; }
.role-chip { padding:2px 10px; border-radius:20px; font-size:.72rem; font-weight:700; }
.role-admin   { background:#ede9fe; color:#6d28d9; }
.role-agente  { background:#e8f0fe; color:#1a73e8; }
.role-soporte { background:#d1fae5; color:#065f46; }
.role-cliente { background:#fef3c7; color:#92400e; }
.nav-logout { background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3); color:#fff; padding:5px 14px; border-radius:20px; font-size:.82rem; font-weight:600; cursor:pointer; transition:all .2s; }
.nav-logout:hover { background:rgba(255,255,255,.28); }
.new-ticket-btn { background:rgba(255,255,255,.15); border:1px solid rgba(255,255,255,.3); color:#fff; padding:6px 16px; border-radius:20px; font-size:.85rem; font-weight:600; text-decoration:none; }
</style>
