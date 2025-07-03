<template>
  <div>

    <h2 class="text-xl font-bold mb-4 text-black">Gestor de Recetas</h2>

    <!-- Botón crear receta -->
    <div class="flex justify-between mb-4 items-center">
      
      <!-- Buscador -->
      <div class="">
        <input
          type="text"
          v-model="busqueda"
          placeholder="Buscador"
          class="border-2 bg-orange-300 rounded text-black text-center text-2xl font-bold uppercase"
        />
      </div>
      <div class="items-end flex gap-2">
        <button @click="abrirModalAprobacion" class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">
          Aprobar recetas
        </button>

        <button @click="abrirModalCrear" class="px-4 py-2 bg-black text-white rounded hover:bg-gray-800">
          Crear receta
        </button>

      </div>

    </div>

    <table class="min-w-full bg-white shadow rounded mb-6">
      <thead>
        <tr class="bg-orange-500 text-white">
          <th class="p-2">ID</th>
          <th class="p-2">Nombre</th>
          <th class="p-2">Usuario</th>
          <th class="p-2">Fecha</th>
          <th class="p-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="receta in recetasFiltradas" :key="receta.id" class="border-t text-center text-black">
          <td class="p-2">{{ receta.id }}</td>
          <td class="p-2">{{ receta.nombre }}</td>
          <td class="p-2">{{ receta.user.name }}</td>
          <td class="p-2">{{ formatFecha(receta.created_at) }}</td>
          <td class="p-2">
            <button @click="abrirModal(receta)" class="bg-yellow-400 px-2 py-1 rounded">Editar</button>
            <button @click="eliminarReceta(receta.id)" class="bg-red-500 text-white px-2 py-1 rounded ml-2">Eliminar</button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Modal editar recetas -->
    <div v-if="mostrarModal" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
      <div class="bg-white p-6 rounded-xl w-full max-w-lg shadow-xl">
        <h3 class="text-lg font-bold mb-4 text-black">Editar Receta</h3>

        <form @submit.prevent="guardarCambios">
          <div class="mb-3">
            <label class="block text-sm font-semibold text-black">Nombre</label>
            <input v-model="form.nombre" type="text" class="w-full border rounded p-2 text-black" required />
          </div>

          <div class="mb-3">
            <label class="block text-sm font-semibold text-black">Instrucciones</label>
            <textarea v-model="form.instrucciones" class="w-full h-32 border rounded p-2 text-black" required></textarea>
          </div>

          <div class="mb-3">
            <label class="block text-sm font-semibold text-black">Tiempo (minutos)</label>
            <input v-model="form.tiempo_preparacion" type="number" class="w-full border rounded p-2 text-black" required min="1" />
          </div>

          <div class="mb-3">
            <label class="block text-sm font-semibold text-black">Dificultad</label>
            <select v-model="form.dificultad" class="w-full border rounded p-2 text-black" required>
              <option value="Fácil">Fácil</option>
              <option value="Media">Media</option>
              <option value="Difícil">Difícil</option>
            </select>
          </div>

          <div class="flex justify-end mt-4">
            <button type="button" @click="cerrarModal" class="mr-2 px-4 py-2 bg-red-500 rounded text-white rounded hover:bg-red-700">Cancelar</button>
            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Crear Receta -->
<div v-if="mostrarModalCrear" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 text-black">
  <div class="bg-white p-6 rounded-xl w-full max-w-xl shadow-xl overflow-y-auto max-h-[90vh]">
    <h3 class="text-lg font-bold mb-4">Crear Nueva Receta</h3>

    <form @submit.prevent="crearReceta">
      <div class="mb-3">
        <label class="block text-sm font-semibold">Nombre</label>
        <input v-model="nuevaReceta.nombre" type="text" class="w-full border rounded p-2" required />
      </div>

      <div class="mb-3">
        <label class="block text-sm font-semibold">Instrucciones</label>
        <textarea v-model="nuevaReceta.instrucciones" class="w-full h-32 border rounded p-2" required></textarea>
      </div>

      <div class="mb-3">
        <label class="block text-sm font-semibold">Tiempo (minutos)</label>
        <input v-model="nuevaReceta.tiempo_preparacion" type="number" class="w-full border rounded p-2" required />
      </div>

      <div class="mb-3">
        <label class="block text-sm font-semibold">Dificultad</label>
        <select v-model="nuevaReceta.dificultad" class="w-full border rounded p-2" required>
          <option value="fácil">Fácil</option>
          <option value="media">Media</option>
          <option value="difícil">Difícil</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="block text-sm font-semibold mb-1">Ingredientes</label>
        <div v-for="(ing, i) in nuevaReceta.ingredientes" :key="i" class="grid grid-cols-3 gap-2 mb-2">
          <input v-model="ing.nombre" type="text" placeholder="Nombre" class="border rounded p-1" required />
          <select v-model="ing.categoria" class="flex-1 border rounded p-2" required>
            <option disabled value="">Categoría</option>
            <option value="Animal">Animal</option>
            <option value="Vegetal">Vegetal</option>
            <option value="Procesados">Procesados</option>
          </select>
          <input v-model="ing.cantidad" type="text" placeholder="Cantidad" class="border rounded p-1" required />
        </div>
        <button type="button" @click="agregarIngrediente" class="text-blue-600 text-sm hover:underline">+ Añadir ingrediente</button>
      </div>

      <div class="flex justify-end mt-4">
        <button type="button" @click="cerrarModalCrear" class="mr-2 px-4 py-2 text-white bg-red-500 rounded hover:bg-red-700">Cancelar</button>
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal de aprobación de recetas -->
<div v-if="mostrarModalAprobacion" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded-xl w-full max-w-4xl shadow-xl overflow-y-auto max-h-[90vh] text-black">
    <h3 class="text-lg font-bold mb-4">Recetas pendientes de aprobación</h3>

    <div v-if="recetasPendientes.length === 0" class="text-center text-gray-500">
      No hay recetas pendientes.
    </div>

    <table v-else class="min-w-full bg-white rounded shadow mb-6">
      <thead>
        <tr class="bg-orange-500 text-white text-sm">
          <th class="p-2">ID</th>
          <th class="p-2">Nombre</th>
          <th class="p-2">Usuario</th>
          <th class="p-2">Acción</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="receta in recetasPendientes" :key="receta.id" class="border-t text-center">
          <td class="p-2">{{ receta.id }}</td>
          <td class="p-2">{{ receta.nombre }}</td>
          <td class="p-2">{{ receta.user.name }}</td>
          <td class="p-2">
            <div class="items-center flex gap-2 justify-center">

            <button @click="verDetalles(receta)" class="bg-blue-500 text-white px-5 py-1 rounded">
              Ver
            </button>

            <button @click="aprobarReceta(receta.id)" class="bg-green-600 text-white px-2 py-1 rounded hover:bg-green-700">Aprobar</button>
          
            <button @click="rechazarReceta(receta.id)" class="bg-red-600 text-white px-2 py-1 rounded hover:bg-red-700">Rechazar</button>


            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <div class="flex justify-end">
      <button @click="cerrarModalAprobacion" class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-700">Cerrar</button>
    </div>
  </div>
</div>

<Modal v-if="mostrarModalDetalles" @close="mostrarModalDetalles = false">
  <template #header>
    <h2 class="text-lg font-bold">Detalles de la receta</h2>
  </template>

  <template #body>
    <div v-if="recetaSeleccionada">
      <p><strong>Nombre:</strong> {{ recetaSeleccionada.nombre }}</p>
      <p><strong>Usuario:</strong> {{ recetaSeleccionada.user.name }}</p>
      <p><strong>Instrucciones:</strong> {{ recetaSeleccionada.instrucciones }}</p>
      <p><strong>Tiempo de preparación:</strong> {{ recetaSeleccionada.tiempo_preparacion }} min</p>
      <p><strong>Dificultad:</strong> {{ recetaSeleccionada.dificultad }}</p>
      <div class="mt-2">
        <p class="font-semibold">Ingredientes:</p>
        <ul>
          <li v-for="ing in recetaSeleccionada.ingredientes" :key="ing.id">
            - {{ ing.nombre }} ({{ ing.pivot.cantidad }}) [{{ ing.categoria }}]
          </li>
        </ul>
      </div>
    </div>
  </template>

  <template #footer>
    <button @click="mostrarModalDetalles = false" class="bg-red-500 text-white px-3 py-1 rounded">Cerrar</button>
  </template>
</Modal>

<!-- Modal Detalles sin componente externo -->
<!-- Modal Detalles sin componente externo -->
<div v-if="mostrarModalDetalles" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded-xl w-full max-w-lg shadow-xl text-black">
    <h2 class="text-lg font-bold mb-4">Detalles de la receta</h2>

    <div v-if="recetaSeleccionada">
      <p><strong>Nombre:</strong> {{ recetaSeleccionada.nombre }}</p>
      <p><strong>Usuario:</strong> {{ recetaSeleccionada.user.name }}</p>
      <p><strong>Instrucciones:</strong> {{ recetaSeleccionada.instrucciones }}</p>
      <p><strong>Tiempo de preparación:</strong> {{ recetaSeleccionada.tiempo_preparacion }} min</p>
      <p><strong>Dificultad:</strong> {{ recetaSeleccionada.dificultad }}</p>
      <div class="mt-2">
        <p class="font-semibold">Ingredientes:</p>
        <ul>
          <li v-for="ing in recetaSeleccionada.ingredientes" :key="ing.id">
            - {{ ing.nombre }} ({{ ing.pivot.cantidad }}) [{{ ing.categoria }}]
          </li>
        </ul>
      </div>
    </div>

    <div class="flex justify-end mt-4">
      <button @click="mostrarModalDetalles = false" class="bg-red-500 text-white px-4 py-2 rounded">
        Cerrar
      </button>
    </div>
  </div>
</div>

</template>

<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
// Al inicio del <script setup>


// ✅ AGREGAR ESTA LÍNEA:
const mostrarModalDetalles = ref(false)

const props = defineProps({
  recetas: Array
})

const busqueda = ref('')

// const recetasFiltradas = computed(() => {
//   if (!busqueda.value) return props.recetas

//   return props.recetas.filter((receta) => {
//     const termino = busqueda.value.toLowerCase()
//     return (
//       receta.id.toString().includes(termino) ||
//       receta.nombre.toLowerCase().includes(termino) ||
//       receta.user.name.toLowerCase().includes(termino)
//     )
//   })
// })

const recetasFiltradas = computed(() => {
  if (!busqueda.value) {
    return props.recetas.filter(r => r.aprobada); // solo aprobadas
  }

  return props.recetas
    .filter(r => r.aprobada) // solo aprobadas
    .filter((receta) => {
      const termino = busqueda.value.toLowerCase();
      return (
        receta.id.toString().includes(termino) ||
        receta.nombre.toLowerCase().includes(termino) ||
        receta.user.name.toLowerCase().includes(termino)
      );
    });
});


const mostrarModal = ref(false)
const recetaSeleccionada = ref(null)

const form = ref({
  nombre: '',
  instrucciones: '',
  tiempo_preparacion: '',
  dificultad: ''
})

const abrirModal = (receta) => {
  recetaSeleccionada.value = receta
  form.value = {
    nombre: receta.nombre,
    instrucciones: receta.instrucciones,
    tiempo_preparacion: receta.tiempo_preparacion,
    dificultad: receta.dificultad
  }
  mostrarModal.value = true
}

const cerrarModal = () => {
  mostrarModal.value = false
  recetaSeleccionada.value = null
}

const guardarCambios = () => {
  router.put(`/admin/recetas/${recetaSeleccionada.value.id}`, form.value, {
    onSuccess: () => {
      cerrarModal()
    }
  })
}

const eliminarReceta = (id) => {
  if (confirm('¿Estás seguro de eliminar esta receta?')) {
    router.delete(`/admin/recetas/${id}`)
  }
}

const formatFecha = (fecha) => {
  return new Date(fecha).toLocaleDateString()
}

const mostrarModalCrear = ref(false)
const nuevaReceta = ref({
  nombre: '',
  instrucciones: '',
  tiempo_preparacion: '',
  dificultad: '',
  ingredientes: [{ nombre: '', categoria: '', cantidad: '' }],
})

const abrirModalCrear = () => {
  mostrarModalCrear.value = true
  nuevaReceta.value = {
    nombre: '',
    instrucciones: '',
    tiempo_preparacion: '',
    dificultad: '',
    ingredientes: [{ nombre: '', categoria: '', cantidad: '' }],
  }
}

const cerrarModalCrear = () => {
  mostrarModalCrear.value = false
}

const agregarIngrediente = () => {
  nuevaReceta.value.ingredientes.push({ nombre: '', categoria: '', cantidad: '' })
}

const crearReceta = () => {
  router.post('/admin/recetas', nuevaReceta.value, {
    onSuccess: () => {
      cerrarModalCrear()
    }
  })
}

const mostrarModalAprobacion = ref(false)
const recetasPendientes = ref([])

const abrirModalAprobacion = async () => {
  try {
    const response = await axios.get('/api/recetas?pendientes=1')
    recetasPendientes.value = response.data
    mostrarModalAprobacion.value = true
  } catch (error) {
    console.error('Error al cargar recetas pendientes:', error)
  }
}

const cerrarModalAprobacion = () => {
  mostrarModalAprobacion.value = false
}

const aprobarReceta = async (id) => {
  try {
    await axios.put(`/admin/recetas/${id}/aprobar`)
    recetasPendientes.value = recetasPendientes.value.filter(r => r.id !== id)
  } catch (error) {
    console.error('Error al aprobar receta:', error)
  }
}

const verDetalles = (receta) => {
  recetaSeleccionada.value = receta
  mostrarModalDetalles.value = true
}

const rechazarReceta = async (id) => {
  if (!confirm("¿Estás seguro de rechazar esta receta? Esta acción no se puede deshacer.")) return

  try {
    await axios.delete(`/admin/recetas/${id}/rechazar`)
    recetasPendientes.value = recetasPendientes.value.filter(r => r.id !== id)
  } catch (error) {
    console.error('Error al rechazar receta:', error)
  }
}


</script>
