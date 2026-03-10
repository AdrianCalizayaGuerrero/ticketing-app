<template>
  <main>
    <div class="page-header">
      <h1>Dashboard</h1>
      <p>Bienvenido, <strong>{{ auth.user?.name }}</strong> — {{ roleLabel }}</p>
    </div>

    <div v-if="loading" class="loading">Cargando...</div>
    <template v-else>
      <div class="stats-grid">
        <div class="stat-card"><div class="stat-num c-total">{{ stats.total }}</div><div class="stat-label">Total</div></div>
        <div class="stat-card"><div class="stat-num c-abierto">{{ stats.open }}</div><div class="stat-label">Abiertos</div></div>
        <div class="stat-card"><div class="stat-num c-progreso">{{ stats.in_progress }}</div><div class="stat-label">En progreso</div></div>
        <div class="stat-card"><div class="stat-num c-esperando">{{ stats.waiting }}</div><div class="stat-label">Esperando</div></div>
        <div class="stat-card"><div class="stat-num c-resuelto">{{ stats.resolved }}</div><div class="stat-label">Resueltos</div></div>
        <div class="stat-card"><div class="stat-num c-cerrado">{{ stats.closed }}</div><div class="stat-label">Cerrados</div></div>
      </div>

      <!-- Botón crear ticket para clientes -->
      <div v-if="role === 'cliente'" class="section-header">
        <span class="section-title">Mis tickets recientes</span>
        <button class="btn-primary" @click="showNewTicket = true">+ Nuevo Ticket</button>
      </div>
      <div v-else class="section-header">
        <span class="section-title">Tickets recientes</span>
        <router-link to="/tickets" class="btn-outline">Ver todos</router-link>
      </div>

      <div class="table-wrap">
        <table>
          <thead><tr><th>Referencia</th><th>Asunto</th><th>Estado</th><th>Prioridad</th><th>Agente</th></tr></thead>
          <tbody>
            <tr v-for="t in recent" :key="t.id">
              <td><router-link :to="`/tickets/${t.id}`" class="tk-link">{{ t.reference_code }}</router-link></td>
              <td>{{ t.subject }}</td>
              <td><StatusBadge :status="statusVal(t.status)" /></td>
              <td><PrioBadge :level="t.priority?.level" /></td>
              <td>{{ agentName(t.assigned_agent) }}</td>
            </tr>
            <tr v-if="recent.length === 0">
              <td colspan="5" style="text-align:center;color:#6b7a8d;padding:32px">No hay tickets aún</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>

    <!-- Modal nuevo ticket -->
    <TicketModal v-if="showNewTicket" @close="showNewTicket=false" @created="onCreated" />
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { auth } from '../app.js'
import api from '../services/api.js'
import StatusBadge from '../components/StatusBadge.vue'
import PrioBadge from '../components/PrioBadge.vue'
import TicketModal from '../components/TicketModal.vue'

const loading = ref(true)
const tickets = ref([])
const showNewTicket = ref(false)
const role = computed(() => auth.user?.role ?? 'cliente')
const roleLabel = computed(() => ({admin:'Administrador',agente:'Agente',soporte:'Soporte Técnico',cliente:'Cliente'}[role.value]))

const recent = computed(() => tickets.value.slice(0, 6))
const stats = computed(() => ({
  total: tickets.value.length,
  open: tickets.value.filter(t => statusVal(t.status) === 'OPEN').length,
  in_progress: tickets.value.filter(t => statusVal(t.status) === 'IN_PROGRESS').length,
  waiting: tickets.value.filter(t => statusVal(t.status) === 'WAITING_FOR_USER').length,
  resolved: tickets.value.filter(t => statusVal(t.status) === 'RESOLVED').length,
  closed: tickets.value.filter(t => statusVal(t.status) === 'CLOSED').length,
}))

function statusVal(s) { return typeof s === 'object' ? s?.value ?? s : s }
function agentName(agent) {
  if (!agent) return 'Sin asignar'
  const p = agent.person ?? agent.employee?.person
  return p ? `${p.first_name} ${p.last_name}` : 'Sin asignar'
}

async function load() {
  try {
    const res = await api.getTickets()
    tickets.value = res.data.data ?? res.data
  } catch(e) { console.error(e) }
  finally { loading.value = false }
}

function onCreated() { showNewTicket.value = false; load() }
onMounted(load)
</script>
