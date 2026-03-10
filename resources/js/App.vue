<template>
  <div>
    <NavBar v-if="auth.isLoggedIn()" />
    <router-view />
    <Toast :msg="toastMsg" />
  </div>
</template>

<script setup>
import { inject, ref, provide } from 'vue'
import NavBar from './components/NavBar.vue'
import Toast from './components/Toast.vue'
import { auth } from './app.js'

const toastMsg = ref('')
function showToast(msg) {
  toastMsg.value = msg
  setTimeout(() => toastMsg.value = '', 3000)
}
provide('toast', showToast)
</script>
