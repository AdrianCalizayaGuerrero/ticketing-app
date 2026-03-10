<template>
  <main>
    <div v-if="loading" class="loading">Cargando ticket...</div>
    <template v-else-if="ticket">
      <router-link to="/tickets" class="back-btn">← Volver a Tickets</router-link>

      <div class="detail-grid">
        <!-- Columna principal -->
        <div class="detail-main">
          <div class="detail-meta">
            <span style="font-weight:700;font-size:.9rem;color:#6b7a8d">{{ ticket.reference_code }}</span>
            <StatusBadge :status="statusVal(ticket.status)" />
            <PrioBadge :level="ticket.priority?.level" />
          </div>
          <div class="detail-title">{{ ticket.subject }}</div>
          <div style="font-size:.82rem;color:#6b7a8d;margin-bottom:20px">
            Creado: {{ formatDate(ticket.created_at) }} · Categoría: {{ ticket.category?.name ?? '—' }}
          </div>
          <div class="detail-desc">{{ ticket.description }}</div>

          <!-- Mensajes -->
          <div class="comments-section">
            <h3>Mensajes ({{ messages.length }})</h3>
            <div v-for="m in messages" :key="m.id" class="comment" :class="{ internal: m.is_internal }">
              <div class="comment-avatar">{{ initials(m.author) }}</div>
              <div class="comment-body">
                <div class="comment-author">
                  {{ authorName(m.author) }}
                  <span v-if="m.is_internal" class="internal-tag">Interno</span>
                </div>
                <div class="comment-time">{{ formatDate(m.created_at) }}</div>
                <div class="comment-text">{{ m.content }}</div>
              </div>
            </div>
            <div v-if="messages.length === 0" style="color:#6b7a8d;font-size:.88rem;margin-bottom:16px">No hay mensajes aún.</div>

            <!-- Enviar mensaje -->
            <div class="comment-input-wrap">
              <textarea v-model="newMsg" class="comment-input" rows="2" placeholder="Escribe un mensaje..."></textarea>
              <div class="msg-options">
                <label v-if="role !== 'cliente'" class="internal-check">
                  <input type="checkbox" v-model="isInternal" /> Mensaje interno (solo equipo)
                </label>
                <button class="btn-primary" :disabled="!newMsg.trim()" @click="sendMsg">Enviar</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Columna lateral -->
        <div class="detail-side">
          <div class="detail-side-card">
            <h4>Detalles</h4>
            <div class="detail-field"><label>Estado</label><StatusBadge :status="statusVal(ticket.status)" /></div>
            <div class="detail-field"><label>Prioridad</label><PrioBadge :level="ticket.priority?.level" /></div>
            <div class="detail-field"><label>Categoría</label><span style="font-size:.85rem;font-weight:600">{{ ticket.category?.name ?? '—' }}</span></div>
            <div class="detail-field"><label>Agente</label><span style="font-size:.85rem;color:#6b7a8d">{{ agentName(ticket.assigned_agent) }}</span></div>
            <div class="detail-field"><label>Reportado por</label><span style="font-size:.85rem;color:#6b7a8d">{{ reporterName }}</span></div>
          </div>

          <!-- Acciones para agente/admin/soporte -->
          <div v-if="role !== 'cliente'" class="detail-side-card">
            <h4>Cambiar estado</h4>
            <select v-model="newStatus" style="width:100%;padding:9px 12px;border:1.5px solid #dde3ee;border-radius:8px;font-size:.88rem;margin-bottom:10px;font-family:inherit">
              <option value="OPEN">Abierto</option>
              <option value="IN_PROGRESS">En progreso</option>
              <option value="WAITING_FOR_USER">Esperando usuario</option>
              <option value="RESOLVED">Resuelto</option>
              <option value="CLOSED">Cerrado</option>
            </select>
            <button class="btn-primary" style="width:100%;margin-bottom:8px" @click="changeStatus">Guardar estado</button>
            <div v-if="statusMsg" class="toast-inline">{{ statusMsg }}</div>
          </div>

          <!-- Asignar agente (solo agente/admin) -->
          <div v-if="role === 'agente' || role === 'admin'" class="detail-side-card">
            <h4>Asignar agente</h4>
            <select v-model="selectedAgent" style="width:100%;padding:9px 12px;border:1.5px solid #dde3ee;border-radius:8px;font-size:.88rem;margin-bottom:10px;font-family:inherit">
              <option value="">Sin asignar</option>
              <option v-for="a in agents" :key="a.id" :value="a.id">
                {{ a.person?.first_name }} {{ a.person?.last_name }}
              </option>
            </select>
            <button class="btn-primary" style="width:100%" @click="assignAgent">Asignar</button>
          </div>

          <!-- Historial de estados -->
          <div class="detail-side-card">
            <h4>Historial de cambios</h4>
            <div v-for="h in ticket.status_histories" :key="h.id" class="history-item">
              <div class="history-arrow">
                <StatusBadge :status="h.previous_status" />
                <span style="color:#6b7a8d;margin:0 4px">→</span>
                <StatusBadge :status="h.new_status" />
              </div>
              <div style="font-size:.75rem;color:#6b7a8d;margin-top:4px">{{ formatDate(h.changed_at) }}</div>
            </div>
            <div v-if="!ticket.status_histories?.length" style="font-size:.82rem;color:#6b7a8d">Sin historial</div>
          </div>
        </div>
      </div>
    </template>
    <div v-else class="loading">Ticket no encontrado.</div>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { auth } from '../app.js'
import api from '../services/api.js'
import StatusBadge from '../components/StatusBadge.vue'
import PrioBadge from '../components/PrioBadge.vue'

const route = useRoute()
const loading = ref(true)
const ticket = ref(null)
const messages = ref([])
const agents = ref([])
const newMsg = ref('')
const isInternal = ref(false)
const newStatus = ref('')
const statusMsg = ref('')
const selectedAgent = ref('')
const role = computed(() => auth.user?.role ?? 'cliente')

function statusVal(s) { return typeof s === 'object' ? s?.value ?? s : s }
function formatDate(d) { return d ? new Date(d).toLocaleDateString('es-BO') : '—' }
function agentName(agent) {
  if (!agent) return 'Sin asignar'
  const p = agent.person ?? agent.employee?.person
  return p ? `${p.first_name} ${p.last_name}` : 'Sin asignar'
}
function authorName(author) {
  if (!author) return 'Usuario'
  const p = author.person
  return p ? `${p.first_name} ${p.last_name}` : author.username ?? 'Usuario'
}
function initials(author) {
  return authorName(author).split(' ').map(w=>w[0]).join('').toUpperCase().slice(0,2)
}
const reporterName = computed(() => {
  const r = ticket.value?.reporter
  if (!r) return '—'
  const p = r.person ?? r.employee?.person
  return p ? `${p.first_name} ${p.last_name}` : r.username ?? '—'
})

async function sendMsg() {
  if (!newMsg.value.trim()) return
  try {
    await api.sendMessage({ content: newMsg.value, ticked_id: ticket.value.id, is_internal: isInternal.value })
    newMsg.value = ''
    await reload()
  } catch(e) { console.error(e) }
}

async function changeStatus() {
  try {
    await api.updateTicket(ticket.value.id, { status: newStatus.value })
    statusMsg.value = '✅ Estado actualizado'
    await reload()
    setTimeout(() => statusMsg.value = '', 3000)
  } catch(e) { statusMsg.value = '❌ Error al actualizar' }
}

async function assignAgent() {
  try {
    await api.updateTicket(ticket.value.id, { assigned_agent_id: selectedAgent.value || null })
    statusMsg.value = '✅ Agente asignado'
    await reload()
    setTimeout(() => statusMsg.value = '', 3000)
  } catch(e) { console.error(e) }
}

async function reload() {
  const res = await api.getTicket(route.params.id)
  ticket.value = res.data
  messages.value = res.data.messages ?? []
  newStatus.value = statusVal(res.data.status)
  selectedAgent.value = res.data.assigned_agent_id ?? ''
}

onMounted(async () => {
  try {
    const [res, agRes] = await Promise.all([api.getTicket(route.params.id), api.getAgents()])
    ticket.value = res.data
    messages.value = res.data.messages ?? []
    agents.value = agRes.data.data ?? agRes.data
    newStatus.value = statusVal(res.data.status)
    selectedAgent.value = res.data.assigned_agent_id ?? ''
  } catch(e) { console.error(e) }
  finally { loading.value = false }
})
</script>

<style scoped>
.comment.internal { opacity:.75; }
.comment.internal .comment-body { background:#fef3c7; }
.internal-tag { background:#fef3c7; color:#92400e; padding:2px 8px; border-radius:10px; font-size:.7rem; font-weight:700; margin-left:8px; }
.msg-options { display:flex; align-items:center; justify-content:space-between; margin-top:8px; }
.internal-check { display:flex; align-items:center; gap:6px; font-size:.82rem; color:#6b7a8d; cursor:pointer; }
.history-item { margin-bottom:12px; }
.history-arrow { display:flex; align-items:center; flex-wrap:wrap; gap:4px; }
.comment-input { width:100%; padding:10px 14px; border:1.5px solid #dde3ee; border-radius:9px; font-family:inherit; font-size:.88rem; outline:none; resize:vertical; }
.comment-input:focus { border-color:#1a73e8; }
</style>
