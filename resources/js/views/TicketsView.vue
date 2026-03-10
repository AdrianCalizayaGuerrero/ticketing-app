<template>
  <main>
    <div class="page-header">
      <h1>Tickets</h1>
      <p>{{ role === 'cliente' ? 'Mis tickets' : 'Todos los tickets del sistema' }}</p>
    </div>

    <div v-if="loading" class="loading">Cargando tickets...</div>
    <template v-else>
      <div class="filters">
        <input v-model="search" placeholder="Buscar por asunto o referencia..." />
        <select v-model="filterStatus">
          <option value="">Todos los estados</option>
          <option value="OPEN">Abierto</option>
          <option value="IN_PROGRESS">En progreso</option>
          <option value="WAITING_FOR_USER">Esperando</option>
          <option value="RESOLVED">Resuelto</option>
          <option value="CLOSED">Cerrado</option>
        </select>
        <select v-model="filterPrio">
          <option value="">Todas las prioridades</option>
          <option value="CRITICAL">Crítica</option>
          <option value="HIGH">Alta</option>
          <option value="MEDIUM">Media</option>
          <option value="LOW">Baja</option>
        </select>
        <span class="filters-count">{{ filtered.length }} ticket(s)</span>
        <button v-if="role === 'cliente' || role === 'admin'" class="btn-primary" @click="showModal = true">+ Nuevo Ticket</button>
      </div>

      <div class="table-wrap">
        <table>
          <thead><tr>
            <th>Referencia</th><th>Asunto</th><th>Estado</th><th>Prioridad</th><th>Categoría</th><th>Agente asignado</th><th>Fecha</th>
            <th v-if="role === 'agente' || role === 'admin'">Acciones</th>
          </tr></thead>
          <tbody>
            <tr v-for="t in filtered" :key="t.id">
              <td><router-link :to="`/tickets/${t.id}`" class="tk-link">{{ t.reference_code }}</router-link></td>
              <td>{{ t.subject }}</td>
              <td><StatusBadge :status="statusVal(t.status)" /></td>
              <td><PrioBadge :level="t.priority?.level" /></td>
              <td>{{ t.category?.name ?? '—' }}</td>
              <td>{{ agentName(t.assigned_agent) }}</td>
              <td>{{ formatDate(t.created_at) }}</td>
              <td v-if="role === 'agente' || role === 'admin'">
                <button class="btn-sm" @click="openAssign(t)">Asignar</button>
              </td>
            </tr>
            <tr v-if="filtered.length === 0">
              <td :colspan="role === 'agente' || role === 'admin' ? 8 : 7" style="text-align:center;color:#6b7a8d;padding:32px">No se encontraron tickets</td>
            </tr>
          </tbody>
        </table>
      </div>
    </template>

    <TicketModal v-if="showModal" @close="showModal=false" @created="onCreated" />

    <!-- Modal asignar agente -->
    <div v-if="assignTicket" class="modal-overlay" @click.self="assignTicket=null">
      <div class="modal">
        <div class="modal-header">
          <h3>Asignar agente — {{ assignTicket.reference_code }}</h3>
          <button class="modal-close" @click="assignTicket=null">✕</button>
        </div>
        <div class="form-group">
          <label>Seleccionar agente</label>
          <select v-model="selectedAgent">
            <option value="">Sin asignar</option>
            <option v-for="a in agents" :key="a.id" :value="a.id">
              {{ a.person?.first_name }} {{ a.person?.last_name }} ({{ a.employee_code }})
            </option>
          </select>
        </div>
        <div class="modal-actions">
          <button class="btn-outline" @click="assignTicket=null">Cancelar</button>
          <button class="btn-primary" @click="doAssign">Guardar</button>
        </div>
      </div>
    </div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { auth } from '../app.js'
import api from '../services/api.js'
import StatusBadge from '../components/StatusBadge.vue'
import PrioBadge from '../components/PrioBadge.vue'
import TicketModal from '../components/TicketModal.vue'

const route = useRoute()
const loading = ref(true)
const tickets = ref([])
const agents = ref([])
const search = ref('')
const filterStatus = ref('')
const filterPrio = ref('')
const showModal = ref(false)
const assignTicket = ref(null)
const selectedAgent = ref('')
const role = computed(() => auth.user?.role ?? 'cliente')

function statusVal(s) { return typeof s === 'object' ? s?.value ?? s : s }
function agentName(agent) {
  if (!agent) return 'Sin asignar'
  const p = agent.person ?? agent.employee?.person
  return p ? `${p.first_name} ${p.last_name}` : 'Sin asignar'
}
function formatDate(d) { return d ? new Date(d).toLocaleDateString('es-BO') : '—' }

const filtered = computed(() => tickets.value.filter(t => {
  const q = search.value.toLowerCase()
  const sv = statusVal(t.status)
  return (!q || t.subject?.toLowerCase().includes(q) || t.reference_code?.toLowerCase().includes(q))
      && (!filterStatus.value || sv === filterStatus.value)
      && (!filterPrio.value   || t.priority?.level?.toUpperCase() === filterPrio.value)
}))

function openAssign(t) { assignTicket.value = t; selectedAgent.value = t.assigned_agent_id ?? '' }

async function doAssign() {
  try {
    await api.updateTicket(assignTicket.value.id, { assigned_agent_id: selectedAgent.value || null })
    await load()
    assignTicket.value = null
  } catch(e) { console.error(e) }
}

async function load() {
  try {
    const [res, agRes] = await Promise.all([api.getTickets(), api.getAgents()])
    tickets.value = res.data.data ?? res.data
    agents.value  = agRes.data.data ?? agRes.data
  } catch(e) { console.error(e) }
  finally { loading.value = false }
}

function onCreated() { showModal.value = false; load() }

onMounted(() => {
  if (route.query.new === '1') showModal.value = true
  load()
})
</script>

<style scoped>
.btn-sm { padding:4px 12px; background:#e8f0fe; color:#1a73e8; border:none; border-radius:6px; font-size:.78rem; font-weight:700; cursor:pointer; }
.btn-sm:hover { background:#d2e3fc; }
.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; z-index:200; }
.modal { background:#fff; border-radius:16px; padding:32px; width:100%; max-width:460px; box-shadow:0 24px 60px rgba(0,0,0,.2); }
.modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.modal-header h3 { font-size:1.1rem; font-weight:800; }
.modal-close { background:none; border:none; font-size:1.2rem; cursor:pointer; color:#6b7a8d; }
.form-group { margin-bottom:18px; }
.form-group label { display:block; font-size:.82rem; font-weight:600; margin-bottom:6px; }
.form-group select { width:100%; padding:10px 14px; border:1.5px solid #dde3ee; border-radius:9px; font-size:.9rem; font-family:inherit; outline:none; }
.modal-actions { display:flex; gap:12px; justify-content:flex-end; }
</style>
