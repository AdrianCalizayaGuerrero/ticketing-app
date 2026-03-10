<template>
  <div class="login-page">
    <div class="login-card">
      <div class="login-logo"><span>🎟️</span> TicketApp</div>
      <p class="login-sub">Inicia sesión para continuar</p>

      <div class="form-group">
        <label>Usuario</label>
        <input v-model="form.username" type="text" placeholder="tu.usuario" @keyup.enter="doLogin" />
      </div>
      <div class="form-group">
        <label>Contraseña</label>
        <input v-model="form.password" type="password" placeholder="••••••••" @keyup.enter="doLogin" />
      </div>

      <div v-if="error" class="login-error">{{ error }}</div>

      <button class="btn-login" :disabled="loading" @click="doLogin">
        {{ loading ? 'Entrando...' : 'Iniciar sesión' }}
      </button>

      <div class="login-hint">
        <strong>Credenciales de prueba:</strong><br>
        Admin: <code>admin</code> / <code>password</code><br>
        O usa cualquier usuario creado por el seeder con contraseña <code>password</code>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import api from '../services/api.js'
import { auth } from '../app.js'

const router = useRouter()
const form = reactive({ username: '', password: '' })
const error = ref('')
const loading = ref(false)

async function doLogin() {
  if (!form.username || !form.password) { error.value = 'Completa todos los campos'; return }
  loading.value = true
  error.value = ''
  try {
    const res = await api.login(form)
    auth.setUser(res.data.user, res.data.token)
    router.push('/dashboard')
  } catch(e) {
    error.value = e.response?.data?.message ?? 'Error al iniciar sesión'
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.login-page { min-height:100vh; background:linear-gradient(135deg,#1a73e8 0%,#0d47a1 100%); display:flex; justify-content:center; align-items:center; }
.login-card { background:#fff; border-radius:20px; padding:52px 48px; width:100%; max-width:420px; box-shadow:0 24px 60px rgba(0,0,0,.18); animation:fadeUp .5s ease; }
.login-logo { display:flex; align-items:center; gap:10px; font-size:1.4rem; font-weight:800; color:#1a73e8; margin-bottom:8px; }
.login-logo span { font-size:1.6rem; }
.login-sub { color:#6b7a8d; font-size:.9rem; margin-bottom:36px; }
.form-group { margin-bottom:20px; }
.form-group label { display:block; font-size:.82rem; font-weight:600; color:#1e2a3a; margin-bottom:7px; }
.form-group input { width:100%; padding:12px 16px; border:1.5px solid #dde3ee; border-radius:10px; font-size:.95rem; font-family:inherit; color:#1e2a3a; outline:none; transition:border .2s,box-shadow .2s; }
.form-group input:focus { border-color:#1a73e8; box-shadow:0 0 0 3px rgba(26,115,232,.12); }
.login-error { background:#fee2e2; color:#b91c1c; padding:10px 14px; border-radius:8px; font-size:.85rem; margin-bottom:16px; }
.btn-login { width:100%; padding:13px; background:#1a73e8; color:#fff; border:none; border-radius:10px; font-size:1rem; font-weight:700; cursor:pointer; font-family:inherit; transition:background .2s,transform .1s; margin-top:8px; }
.btn-login:hover:not(:disabled) { background:#1557b0; transform:translateY(-1px); }
.btn-login:disabled { opacity:.6; cursor:not-allowed; }
.login-hint { margin-top:20px; text-align:center; font-size:.8rem; color:#6b7a8d; background:#f4f6fb; border-radius:8px; padding:12px; line-height:1.6; }
.login-hint code { background:#e8f0fe; color:#1a73e8; padding:1px 5px; border-radius:4px; font-size:.82rem; }
@keyframes fadeUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }
</style>
