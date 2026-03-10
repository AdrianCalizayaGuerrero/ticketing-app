<template><span class="badge" :class="cls">{{ label }}</span></template>
<script setup>
import { computed } from 'vue'
const props = defineProps({ level: String })
const map = { CRITICAL:{cls:'badge-critica',label:'Crítica'}, HIGH:{cls:'badge-alta',label:'Alta'}, MEDIUM:{cls:'badge-media',label:'Media'}, LOW:{cls:'badge-baja',label:'Baja'} }
const cls   = computed(() => map[props.level?.toUpperCase()]?.cls   ?? 'badge-media')
const label = computed(() => map[props.level?.toUpperCase()]?.label ?? props.level)
</script>
