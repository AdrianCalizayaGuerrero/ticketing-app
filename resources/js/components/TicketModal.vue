<template>
  <div class="modal-overlay" @click.self="$emit('close')">
    <div class="modal">
      <div class="modal-header">
        <h3>{{ ticket ? 'Editar Ticket' : 'Nuevo Ticket' }}</h3>
        <button class="modal-close" @click="$emit('close')">✕</button>
      </div>

      <div class="form-group">
        <label>Asunto *</label>
        <input v-model="form.subject" type="text" placeholder="Describe brevemente el problema" />
      </div>
      <div class="form-group">
        <label>Descripción *</label>
        <textarea v-model="form.description" rows="4" placeholder="Explica el problema con detalle..."></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Categoría *</label>
          <select v-model="form.category_id">
            <option value="">Seleccionar...</option>
            <option v-for="c in categories" :key="c.id" :value="c.id">{{ c.name }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>Prioridad *</label>
          <select v-model="form.priority_id">
            <option value="">Seleccionar...</option>
            <option v-for="p in priorities" :key="p.id" :value="p.id">{{ p.level }}</option>
          </select>
        </div>
      </div>

      <div v-if="error" class="form-error">{{ error }}</div>

      <div class="modal-actions">
        <button class="btn-outline" @click="$emit('close')">Cancelar</button>
        <button class="btn-primary" :disabled="saving" @click="save">
          {{ saving ? 'Guardando...' : (ticket ? 'Guardar cambios' : 'Crear ticket') }}
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import api from '../services/api.js'

const props = defineProps({ ticket: Object })
const emit = defineEmits(['close', 'created'])

const categories = ref([])
const priorities = ref([])
const saving = ref(false)
const error = ref('')

const form = reactive({
  subject: props.ticket?.subject ?? '',
  description: props.ticket?.description ?? '',
  category_id: props.ticket?.category_id ?? '',
  priority_id: props.ticket?.priority_id ?? '',
})

async function save() {
  if (!form.subject || !form.description || !form.category_id || !form.priority_id) {
    error.value = 'Todos los campos son obligatorios'; return
  }
  saving.value = true; error.value = ''
  try {
    if (props.ticket) {
      await api.updateTicket(props.ticket.id, form)
    } else {
      await api.createTicket(form)
    }
    emit('created')
  } catch(e) {
    error.value = e.response?.data?.message ?? 'Error al guardar'
  } finally { saving.value = false }
}

onMounted(async () => {
  const [cats, prios] = await Promise.all([api.getCategories(), api.getPriorities()])
  categories.value = cats.data.data ?? cats.data
  priorities.value = prios.data.data ?? prios.data
})
</script>

<style scoped>
.modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); display:flex; align-items:center; justify-content:center; z-index:200; }
.modal { background:#fff; border-radius:16px; padding:32px; width:100%; max-width:540px; box-shadow:0 24px 60px rgba(0,0,0,.2); animation:fadeUp .3s ease; }
.modal-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:24px; }
.modal-header h3 { font-size:1.15rem; font-weight:800; }
.modal-close { background:none; border:none; font-size:1.2rem; cursor:pointer; color:#6b7a8d; }
.form-group { margin-bottom:18px; }
.form-group label { display:block; font-size:.82rem; font-weight:600; color:#1e2a3a; margin-bottom:6px; }
.form-group input, .form-group textarea, .form-group select { width:100%; padding:10px 14px; border:1.5px solid #dde3ee; border-radius:9px; font-size:.9rem; font-family:inherit; color:#1e2a3a; outline:none; transition:border .2s; }
.form-group input:focus, .form-group textarea:focus, .form-group select:focus { border-color:#1a73e8; }
.form-row { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
.form-error { background:#fee2e2; color:#b91c1c; padding:10px 14px; border-radius:8px; font-size:.85rem; margin-bottom:16px; }
.modal-actions { display:flex; gap:12px; justify-content:flex-end; margin-top:8px; }
@keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
</style>
