<template>
  <main>
    <div class="page-header">
      <h1>Reportes & Estadísticas</h1>
      <p>Análisis del sistema de tickets</p>
    </div>

    <div v-if="loading" class="loading">Cargando reportes...</div>
    <template v-else>
      <div class="kpi-row">
        <div class="kpi-card"><div class="kpi-num" style="color:#1a73e8">{{ stats.total }}</div><div class="kpi-label">Total de tickets</div></div>
        <div class="kpi-card"><div class="kpi-num" style="color:#10b981">{{ stats.resolved + stats.closed }}</div><div class="kpi-label">Resueltos / Cerrados</div></div>
        <div class="kpi-card"><div class="kpi-num" style="color:#f59e0b">{{ atencionPct }}%</div><div class="kpi-label">Tasa de atención</div></div>
      </div>

      <div class="reports-grid">
        <div class="report-card">
          <h3>Tickets por estado</h3>
          <div v-for="item in byStatus" :key="item.key" class="bar-row">
            <div class="bar-label">{{ item.label }}</div>
            <div class="bar-track"><div class="bar-fill" :style="{ width: pct(item.count, maxByStatus)+'%', background: item.color }"></div></div>
            <div class="bar-val" :style="{ color: item.color }">{{ item.count }}</div>
          </div>
        </div>

        <div class="report-card">
          <h3>Distribución por estado</h3>
          <div class="donut-wrap">
            <div class="donut" :style="{ background: donutGradient }">
              <div class="donut-hole">{{ stats.total }}<br><span style="font-size:.65rem">tickets</span></div>
            </div>
            <div class="legend">
              <div v-for="item in byStatus" :key="item.key" class="legend-item">
                <div class="legend-dot" :style="{ background: item.color }"></div>
                {{ item.label }} ({{ item.count }})
              </div>
            </div>
          </div>
        </div>

        <div class="report-card">
          <h3>Tickets por prioridad</h3>
          <div v-for="item in byPrio" :key="item.key" class="bar-row">
            <div class="bar-label">{{ item.label }}</div>
            <div class="bar-track"><div class="bar-fill" :style="{ width: pct(item.count, maxByPrio)+'%', background: item.color }"></div></div>
            <div class="bar-val" :style="{ color: item.color }">{{ item.count }}</div>
          </div>
        </div>

        <div class="report-card">
          <h3>Tickets por categoría</h3>
          <div v-for="item in byCategory" :key="item.name" class="bar-row">
            <div class="bar-label">{{ item.name }}</div>
            <div class="bar-track"><div class="bar-fill" :style="{ width: pct(item.count, maxByCategory)+'%', background: '#1a73e8' }"></div></div>
            <div class="bar-val" style="color:#1a73e8">{{ item.count }}</div>
          </div>
        </div>
      </div>
    </template>
  </main>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '../services/api.js'

const loading = ref(true)
const tickets = ref([])

function sv(s) { return typeof s === 'object' ? s?.value ?? s : s }

const stats = computed(() => ({
  total: tickets.value.length,
  open: tickets.value.filter(t => sv(t.status)==='OPEN').length,
  in_progress: tickets.value.filter(t => sv(t.status)==='IN_PROGRESS').length,
  waiting: tickets.value.filter(t => sv(t.status)==='WAITING_FOR_USER').length,
  resolved: tickets.value.filter(t => sv(t.status)==='RESOLVED').length,
  closed: tickets.value.filter(t => sv(t.status)==='CLOSED').length,
}))

const byStatus = computed(() => [
  { key:'OPEN',label:'Abiertos',color:'#e53935',count:stats.value.open },
  { key:'IN_PROGRESS',label:'En progreso',color:'#f59e0b',count:stats.value.in_progress },
  { key:'WAITING_FOR_USER',label:'Esperando',color:'#06b6d4',count:stats.value.waiting },
  { key:'RESOLVED',label:'Resueltos',color:'#10b981',count:stats.value.resolved },
  { key:'CLOSED',label:'Cerrados',color:'#6b7a8d',count:stats.value.closed },
])

const byPrio = computed(() => [
  { key:'CRITICAL',label:'Crítica',color:'#e53935',count:tickets.value.filter(t=>t.priority?.level?.toUpperCase()==='CRITICAL').length },
  { key:'HIGH',label:'Alta',color:'#f97316',count:tickets.value.filter(t=>t.priority?.level?.toUpperCase()==='HIGH').length },
  { key:'MEDIUM',label:'Media',color:'#06b6d4',count:tickets.value.filter(t=>t.priority?.level?.toUpperCase()==='MEDIUM').length },
  { key:'LOW',label:'Baja',color:'#10b981',count:tickets.value.filter(t=>t.priority?.level?.toUpperCase()==='LOW').length },
])

const byCategory = computed(() => {
  const map = {}
  tickets.value.forEach(t => { const n = t.category?.name ?? 'Sin categoría'; map[n] = (map[n]??0)+1 })
  return Object.entries(map).map(([name,count])=>({name,count})).sort((a,b)=>b.count-a.count)
})

const maxByStatus   = computed(() => Math.max(...byStatus.value.map(x=>x.count), 1))
const maxByPrio     = computed(() => Math.max(...byPrio.value.map(x=>x.count), 1))
const maxByCategory = computed(() => Math.max(...byCategory.value.map(x=>x.count), 1))
function pct(count, max) { return Math.round((count / max) * 100) }

const atencionPct = computed(() => {
  if (!stats.value.total) return 0
  return Math.round(((stats.value.in_progress + stats.value.waiting + stats.value.resolved + stats.value.closed) / stats.value.total) * 100)
})

const donutGradient = computed(() => {
  const total = stats.value.total || 1
  let acc = 0
  return `conic-gradient(${byStatus.value.map(item => {
    const p = (item.count/total)*100
    const s = `${item.color} ${acc}% ${acc+p}%`
    acc += p; return s
  }).join(', ')})`
})

onMounted(async () => {
  try {
    const res = await api.getTickets()
    tickets.value = res.data.data ?? res.data
  } catch(e) { console.error(e) }
  finally { loading.value = false }
})
</script>
