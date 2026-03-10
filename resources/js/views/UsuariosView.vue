<template>
  <main>
    <div class="page-header">
      <h1>Usuarios & Agentes</h1>
      <p>Gestión del equipo de soporte</p>
    </div>

    <div v-if="loading" class="loading">Cargando usuarios...</div>
    <template v-else>

      <!-- Tabs + botón nuevo usuario -->
      <div class="toolbar">
        <div class="tabs">
          <button :class="['tab', {active: tab==='agentes'}]"  @click="tab='agentes'">Agentes ({{ agents.length }})</button>
          <button :class="['tab', {active: tab==='usuarios'}]" @click="tab='usuarios'">Todos los usuarios ({{ users.length }})</button>
        </div>
        <button v-if="role==='admin'" class="btn-primary" @click="showModal=true">+ Nuevo Usuario</button>
      </div>

      <!-- AGENTES -->
      <div v-if="tab==='agentes'" class="users-grid">
        <div v-for="a in agents" :key="a.id" class="user-card">
          <div class="user-avatar-big" :style="{ background: avatarColor(a.id) }">{{ initials(a.person) }}</div>
          <div class="user-name">{{ fullName(a.person) }}</div>
          <div class="user-email">{{ a.person?.email ?? '—' }}</div>
          <span class="user-role role-agente">Agente</span>
          <div class="user-stats">
            <div class="user-stat">
              <div class="user-stat-num">{{ a.employee_code }}</div>
              <div class="user-stat-label">Código</div>
            </div>
            <div class="user-stat">
              <div class="user-stat-num" :style="{ color: a.is_available ? '#10b981' : '#e53935' }">
                {{ a.is_available ? 'Sí' : 'No' }}
              </div>
              <div class="user-stat-label">Disponible</div>
            </div>
          </div>
        </div>
        <div v-if="agents.length === 0" class="empty-state">No hay agentes registrados</div>
      </div>

      <!-- TODOS LOS USUARIOS -->
      <div v-if="tab==='usuarios'">
        <div class="search-bar">
          <input v-model="search" placeholder="Buscar por nombre o usuario..." />
          <select v-model="filterRole">
            <option value="">Todos los roles</option>
            <option value="admin">Administrador</option>
            <option value="agente">Agente</option>
            <option value="soporte">Soporte</option>
            <option value="cliente">Cliente</option>
          </select>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Email</th>
                <th>Rol</th>
                <th v-if="role==='admin'">Acciones</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for="u in filteredUsers" :key="u.id">
                <td>
                  <div class="user-inline">
                    <div class="mini-avatar" :style="{ background: avatarColor(u.id) }">
                      {{ initialsFromUser(u) }}
                    </div>
                    {{ u.person ? `${u.person.first_name} ${u.person.last_name}` : '—' }}
                  </div>
                </td>
                <td style="color:#6b7a8d;font-size:.85rem">{{ u.username }}</td>
                <td style="color:#6b7a8d;font-size:.85rem">{{ u.person?.email ?? '—' }}</td>
                <td><span class="user-role" :class="`role-${u.role}`">{{ roleLabel(u.role) }}</span></td>
                <td v-if="role==='admin'">
                  <button class="btn-danger-sm" @click="confirmDelete(u)">Eliminar</button>
                </td>
              </tr>
              <tr v-if="filteredUsers.length === 0">
                <td :colspan="role==='admin' ? 5 : 4" style="text-align:center;color:#6b7a8d;padding:32px">
                  No se encontraron usuarios
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </template>

    <!-- ===== MODAL NUEVO USUARIO ===== -->
    <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
      <div class="modal">
        <div class="modal-header">
          <h3>Crear nuevo usuario</h3>
          <button class="modal-close" @click="closeModal">✕</button>
        </div>

        <div class="modal-section-title">Datos personales</div>
        <div class="form-row">
          <div class="form-group">
            <label>Nombre *</label>
            <input v-model="form.first_name" type="text" placeholder="Ej: María" />
          </div>
          <div class="form-group">
            <label>Apellido *</label>
            <input v-model="form.last_name" type="text" placeholder="Ej: López" />
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Email *</label>
            <input v-model="form.email" type="email" placeholder="correo@ejemplo.com" />
          </div>
          <div class="form-group">
            <label>Teléfono</label>
            <input v-model="form.phone" type="text" placeholder="Ej: 70012345" />
          </div>
        </div>

        <div class="modal-section-title" style="margin-top:8px">Credenciales de acceso</div>
        <div class="form-row">
          <div class="form-group">
            <label>Usuario *</label>
            <input v-model="form.username" type="text" placeholder="nombre.apellido" />
          </div>
          <div class="form-group">
            <label>Contraseña *</label>
            <input v-model="form.password" type="password" placeholder="Mínimo 6 caracteres" />
          </div>
        </div>
        <div class="form-group">
          <label>Rol *</label>
          <div class="role-selector">
            <div
              v-for="r in roles"
              :key="r.value"
              :class="['role-option', { selected: form.role === r.value }]"
              @click="form.role = r.value"
            >
              <span class="role-icon">{{ r.icon }}</span>
              <span class="role-name">{{ r.label }}</span>
              <span class="role-desc">{{ r.desc }}</span>
            </div>
          </div>
        </div>

        <div v-if="formError" class="form-error">{{ formError }}</div>
        <div v-if="formSuccess" class="form-success">{{ formSuccess }}</div>

        <div class="modal-actions">
          <button class="btn-outline" @click="closeModal">Cancelar</button>
          <button class="btn-primary" :disabled="saving" @click="createUser">
            {{ saving ? 'Creando...' : 'Crear usuario' }}
          </button>
        </div>
      </div>
    </div>

    <!-- ===== MODAL CONFIRMAR ELIMINAR ===== -->
    <div v-if="deleteTarget" class="modal-overlay" @click.self="deleteTarget=null">
      <div class="modal modal-sm">
        <div class="modal-header">
          <h3>Eliminar usuario</h3>
          <button class="modal-close" @click="deleteTarget=null">✕</button>
        </div>
        <p style="font-size:.9rem;color:#374151;margin-bottom:24px">
          ¿Estás segura de eliminar a
          <strong>{{ deleteTarget.person?.first_name }} {{ deleteTarget.person?.last_name }}</strong>?
          Esta acción no se puede deshacer.
        </p>
        <div class="modal-actions">
          <button class="btn-outline" @click="deleteTarget=null">Cancelar</button>
          <button class="btn-danger" :disabled="deleting" @click="doDelete">
            {{ deleting ? 'Eliminando...' : 'Sí, eliminar' }}
          </button>
        </div>
      </div>
    </div>

  </main>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import { auth } from '../app.js'
import api from '../services/api.js'

const loading    = ref(true)
const agents     = ref([])
const users      = ref([])
const tab        = ref('agentes')
const search     = ref('')
const filterRole = ref('')
const showModal  = ref(false)
const saving     = ref(false)
const formError  = ref('')
const formSuccess = ref('')
const deleteTarget = ref(null)
const deleting   = ref(false)

const role = computed(() => auth.user?.role ?? 'cliente')

const roles = [
  { value: 'cliente',  icon: '👤', label: 'Cliente',       desc: 'Puede crear y ver sus tickets' },
  { value: 'soporte',  icon: '🛠️', label: 'Soporte',       desc: 'Atiende tickets asignados' },
  { value: 'agente',   icon: '🎯', label: 'Agente',        desc: 'Asigna y gestiona tickets' },
  { value: 'admin',    icon: '⚙️', label: 'Administrador', desc: 'Acceso completo al sistema' },
]

const form = reactive({
  first_name: '', last_name: '', email: '', phone: '',
  username: '', password: '', role: 'cliente'
})

const filteredUsers = computed(() => users.value.filter(u => {
  const q = search.value.toLowerCase()
  const nombre = u.person ? `${u.person.first_name} ${u.person.last_name}`.toLowerCase() : ''
  return (!q || nombre.includes(q) || u.username?.toLowerCase().includes(q))
      && (!filterRole.value || u.role === filterRole.value)
}))

const COLORS = ['#1a73e8','#10b981','#f59e0b','#e53935','#6d28d9','#06b6d4','#f97316']
function avatarColor(id) { const n = id ? id.charCodeAt(0) + id.charCodeAt(id.length-1) : 0; return COLORS[n % COLORS.length] }
function initials(p)     { return p ? `${p.first_name?.[0]??''}${p.last_name?.[0]??''}`.toUpperCase() : '?' }
function initialsFromUser(u) { return u.person ? initials(u.person) : u.username?.[0]?.toUpperCase() ?? '?' }
function fullName(p)     { return p ? `${p.first_name} ${p.last_name}` : '—' }
function roleLabel(r)    { return {admin:'Administrador',agente:'Agente',soporte:'Soporte',cliente:'Cliente'}[r] ?? r }

function closeModal() {
  showModal.value = false
  formError.value = ''
  formSuccess.value = ''
  Object.assign(form, { first_name:'', last_name:'', email:'', phone:'', username:'', password:'', role:'cliente' })
}

async function createUser() {
  formError.value = ''
  if (!form.first_name || !form.last_name || !form.email || !form.username || !form.password) {
    formError.value = 'Completa todos los campos obligatorios (*)'; return
  }
  saving.value = true
  try {
    await api.createUser({ ...form })
    formSuccess.value = '✅ Usuario creado correctamente'
    await load()
    setTimeout(() => { closeModal(); tab.value = 'usuarios' }, 1200)
  } catch(e) {
    const errors = e.response?.data?.errors
    if (errors) {
      formError.value = Object.values(errors).flat().join(' · ')
    } else {
      formError.value = e.response?.data?.message ?? 'Error al crear el usuario'
    }
  } finally { saving.value = false }
}

function confirmDelete(u) { deleteTarget.value = u }

async function doDelete() {
  deleting.value = true
  try {
    await api.deleteUser(deleteTarget.value.id)
    await load()
    deleteTarget.value = null
  } catch(e) { console.error(e) }
  finally { deleting.value = false }
}

async function load() {
  try {
    const [agRes, usRes] = await Promise.all([api.getAgents(), api.getUsers()])
    agents.value = agRes.data.data ?? agRes.data
    users.value  = usRes.data.data ?? usRes.data
  } catch(e) { console.error(e) }
  finally { loading.value = false }
}

onMounted(load)
</script>

<style scoped>
.toolbar { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.tabs { display:flex; gap:8px; }
.tab { padding:8px 20px; border:1.5px solid #dde3ee; border-radius:8px; background:#fff; font-size:.88rem; font-weight:600; cursor:pointer; transition:all .2s; color:#6b7a8d; }
.tab.active { background:#1a73e8; color:#fff; border-color:#1a73e8; }
.search-bar { display:flex; gap:12px; margin-bottom:16px; }
.search-bar input { flex:1; padding:9px 14px; border:1.5px solid #dde3ee; border-radius:9px; font-size:.88rem; font-family:inherit; outline:none; }
.search-bar input:focus { border-color:#1a73e8; }
.search-bar select { padding:9px 14px; border:1.5px solid #dde3ee; border-radius:9px; font-size:.88rem; font-family:inherit; outline:none; background:#fff; }
.user-inline { display:flex; align-items:center; gap:10px; }
.mini-avatar { width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.72rem; font-weight:700; color:#fff; flex-shrink:0; }
.empty-state { grid-column:1/-1; text-align:center; padding:48px; color:#6b7a8d; font-size:.9rem; }
.btn-danger-sm { padding:4px 12px; background:#fee2e2; color:#b91c1c; border:none; border-radius:6px; font-size:.78rem; font-weight:700; cursor:pointer; }
.btn-danger-sm:hover { background:#fecaca; }
.btn-danger { padding:10px 20px; background:#e53935; color:#fff; border:none; border-radius:9px; font-size:.88rem; font-weight:700; cursor:pointer; font-family:inherit; }
.btn-danger:hover:not(:disabled) { background:#c62828; }
.btn-danger:disabled { opacity:.6; cursor:not-allowed; }

/* Modal */
.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; z-index:200; padding:20px; }
.modal { background:#fff; border-radius:16px; padding:32px; width:100%; max-width:580px; box-shadow:0 24px 60px rgba(0,0,0,.2); animation:fadeUp .3s ease; max-height:90vh; overflow-y:auto; }
.modal-sm { max-width:420px; }
.modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; }
.modal-header h3 { font-size:1.15rem; font-weight:800; }
.modal-close { background:none; border:none; font-size:1.2rem; cursor:pointer; color:#6b7a8d; }
.modal-section-title { font-size:.75rem; font-weight:700; color:#6b7a8d; text-transform:uppercase; letter-spacing:.5px; margin-bottom:14px; padding-bottom:6px; border-bottom:1.5px solid #f0f2f8; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.form-group { margin-bottom:16px; }
.form-group label { display:block; font-size:.82rem; font-weight:600; color:#1e2a3a; margin-bottom:6px; }
.form-group input { width:100%; padding:10px 14px; border:1.5px solid #dde3ee; border-radius:9px; font-size:.9rem; font-family:inherit; color:#1e2a3a; outline:none; transition:border .2s; }
.form-group input:focus { border-color:#1a73e8; box-shadow:0 0 0 3px rgba(26,115,232,.1); }

/* Role selector */
.role-selector { display:grid; grid-template-columns:1fr 1fr; gap:10px; }
.role-option { border:1.5px solid #dde3ee; border-radius:10px; padding:12px 14px; cursor:pointer; transition:all .2s; display:flex; flex-direction:column; gap:3px; }
.role-option:hover { border-color:#1a73e8; background:#f8faff; }
.role-option.selected { border-color:#1a73e8; background:#e8f0fe; }
.role-icon { font-size:1.2rem; }
.role-name { font-size:.85rem; font-weight:700; color:#1e2a3a; }
.role-desc { font-size:.75rem; color:#6b7a8d; }

.form-error   { background:#fee2e2; color:#b91c1c; padding:10px 14px; border-radius:8px; font-size:.85rem; margin-bottom:14px; }
.form-success { background:#d1fae5; color:#065f46; padding:10px 14px; border-radius:8px; font-size:.85rem; margin-bottom:14px; font-weight:600; }
.modal-actions { display:flex; gap:12px; justify-content:flex-end; margin-top:8px; }
@keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
</style>
