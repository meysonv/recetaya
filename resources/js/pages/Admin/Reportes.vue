<template>
  <div class="p-8">
    <h1 class="text-3xl font-bold mb-6 text-orange-600">📊 Reportes del sistema</h1>

    <!-- Filtros -->
    <div class="flex flex-wrap gap-4 mb-6 text-black items-center w-full">
      <input
        type="text"
        v-model="filtroNombre"
        placeholder="Filtrar por nombre de usuario"
        class="px-4 py-2 rounded border w-64"
      />
    
      <input
        type="text"
        v-model="filtroAccion"
        placeholder="Filtrar por acción"
        class="px-4 py-2 rounded border w-64"
      />
    
      <input
        type="date"
        v-model="filtroFecha"
        class="px-4 py-2 rounded border"
      />
    
      <button
        @click="limpiarFiltros"
        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600"
      >
        Limpiar filtros
      </button>
    
      <a
        :href="rutaExportarPDF"
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 ml-auto"
        target="_blank"
      >
        Guardar registros
      </a>
    </div>
    
    <!-- Tabla -->
    <div class="overflow-x-auto bg-white shadow rounded-xl text-black">
      <table class="min-w-full table-auto border-collapse">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">ID</th>
            <th class="px-4 py-2 text-left">Usuario</th>
            <th class="px-4 py-2 text-left">Acción</th>
            <th class="px-4 py-2 text-left">Descripción</th>
            <th class="px-4 py-2 text-left">Fecha</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="reporte in reportesFiltrados" :key="reporte.id" class="border-t">
            <td class="px-4 py-2">{{ reporte.id }}</td>
            <td class="px-4 py-2">{{ reporte.user?.name || 'Desconocido' }}</td>
            <td class="px-4 py-2">{{ reporte.tipo }}</td>
            <td class="px-4 py-2 whitespace-pre-wrap">
              <template v-if="typeof reporte.datos === 'object'">
                <div v-for="(valor, clave) in reporte.datos" :key="clave">
                  <strong>{{ clave }}:</strong> {{ valor }}
                </div>
              </template>
              <template v-else>
                <div v-html="formatearDescripcion(reporte.datos)"></div>
              </template>
            </td>
            <td class="px-4 py-2">{{ formatoFecha(reporte.created_at) }}</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div v-if="reportesFiltrados.length === 0" class="text-center text-gray-500 mt-6">
      No hay reportes que coincidan con los filtros.
    </div>
  </div>

  <!-- Mostrar mensaje si no hay reportes -->
  <div v-if="!props.reportes">
    <p class="text-center text-gray-500">No se han cargado los reportes.</p>
  </div>

</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const props = defineProps({
  reportes: Array
})

const filtroNombre = ref('')
const filtroAccion = ref('')
const filtroFecha = ref('')

//const coincideAccion = (r.tipo || '').toLowerCase().includes(filtroAccion.value.toLowerCase()) //relleno si tipo no definido


const limpiarFiltros = () => {
  filtroNombre.value = ''
  filtroAccion.value = ''
  filtroFecha.value = ''
}

const reportesFiltrados = computed(() => {
  if (!props.reportes) return []

  return props.reportes.filter(r => {
    const coincideNombre = r.user?.name?.toLowerCase().includes(filtroNombre.value.toLowerCase())
    const coincideAccion = r.tipo.toLowerCase().includes(filtroAccion.value.toLowerCase())
    const coincideFecha = filtroFecha.value === '' || r.created_at.startsWith(filtroFecha.value)

    return coincideNombre && coincideAccion && coincideFecha
  })
})


function formatoFecha(fecha) {
  return new Date(fecha).toLocaleString()
}

function formatearDescripcion(texto) {
  try {
    const partes = texto.split(/Antes:\s*(\{.*?\})\s*,\s*Después:\s*(\{.*\})/);

    if (partes.length < 3) return texto;

    const antesObj = JSON.parse(partes[1]);
    const despuesObj = JSON.parse(partes[2]);

    const formato = (obj) =>
      Object.entries(obj)
        .map(([clave, valor]) => `&nbsp;&nbsp;- <strong>${clave}:</strong> ${valor}`)
        .join('<br>');

    return `
      <strong>Antes:</strong><br>${formato(antesObj)}<br><br>
      <strong>Después:</strong><br>${formato(despuesObj)}
    `;
  } catch (e) {
    return texto; // Si algo falla, muestra el texto original
  }
}

const rutaExportarPDF = computed(() => {
  const params = new URLSearchParams();

  if (filtroNombre.value) params.append('usuario', filtroNombre.value);
  if (filtroAccion.value) params.append('accion', filtroAccion.value);
  if (filtroFecha.value) params.append('fecha', filtroFecha.value);

  return `/admin/reportes/exportar?${params.toString()}`;
});

</script>

<style scoped>
th {
  font-weight: 600;
}
</style>
