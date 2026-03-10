<template><span class="badge" :class="cls">{{ label }}</span></template>
<script setup>
import { computed } from 'vue'
const props = defineProps({ status: String })
const map = { OPEN:{cls:'badge-abierto',label:'Abierto'}, IN_PROGRESS:{cls:'badge-progreso',label:'En progreso'}, WAITING_FOR_USER:{cls:'badge-esperando',label:'Esperando'}, RESOLVED:{cls:'badge-resuelto',label:'Resuelto'}, CLOSED:{cls:'badge-cerrado',label:'Cerrado'} }
const cls   = computed(() => map[props.status]?.cls   ?? 'badge-cerrado')
const label = computed(() => map[props.status]?.label ?? props.status)
</script>
