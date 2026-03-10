<template>
  <div class="toast" :class="{ show: msg }">{{ msg }}</div>
</template>
<script setup>
defineProps({ msg: String })
</script>
<style scoped>
.toast { position:fixed; bottom:28px; right:28px; background:#1e2a3a; color:#fff; padding:14px 22px; border-radius:12px; font-size:.9rem; font-weight:600; box-shadow:0 8px 24px rgba(0,0,0,.2); opacity:0; transform:translateY(12px); transition:all .3s; pointer-events:none; z-index:999; }
.toast.show { opacity:1; transform:translateY(0); }
</style>
