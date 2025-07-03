<template>  
  <div class="min-h-screen bg-slate-100 text-gray-800 transition-all duration-500" :class="{ 'ml-64': barraActiva }">  
    <!-- Encabezado -->  
    <header
      class="relative w-full bg-cover bg-center h-64 flex flex-col items-center justify-center text-white"
      style="background-image: url('/images/banner.png');"
      >
      
      <div class="absolute top-4 left-4" v-if="!barraActiva">
        <button @click="barraActiva = true" class="text-white text-2xl">☰</button>
      </div>
      <h1 class="text-4xl font-extrabold drop-shadow-md">Receta YA</h1>
    
      <input
        type="text"
        placeholder="Escribe un ingrediente"
        v-model="busqueda"
        @keyup.enter="buscarRecetas"
        class="mt-4 w-2/3 px-4 py-2 rounded-xl shadow bg-white text-gray-800"
      />

      <!-- Botón MI CUENTA + tarjeta de perfil -->
      <div class="absolute top-4 right-4" @click="mostrarPerfil = !mostrarPerfil">
        <button 
          id="boton-perfil"
          class="px-4 py-2 border rounded-xl text-white bg-green-600 hover:bg-green-700 "
        >
          MI CUENTA
        </button>
      
        <!-- Tarjeta de usuario -->
        <transition name="fade">
          <div
            id="panel-usuario"
            v-if="mostrarPerfil"
            class="absolute right-0 mt-2 w-64 bg-white text-gray-800 shadow-xl rounded-xl p-4 z-50"
          >
            <!-- Imagen de perfil -->
            <div class="flex justify-center mb-4">
              <img
                src="/images/avatar.png"
                alt="Perfil"
                class="w-20 h-20 rounded-full border-4 border-orange-500"
              />
            </div>
      
            <!-- Información -->
            <p class="text-center text-sm text-gray-500 font-semibold uppercase">
              {{ usuario.rol }}
            </p>
            <p class="text-center text-lg font-bold mb-4">
              {{ usuario.name }}
            </p>

            <!-- 🔧 NUEVA OPCIÓN: Gestionar Registros -->
            <a
              href="/admin/gestion"
              class="block text-center text-blue-600 font-semibold mb-3 hover:underline"
            >
              Gestionar Registros
            </a>
      
            <!-- Cerrar sesión -->
            
            <button
              @click="logout"
              class="w-full py-2 text-center text-sm text-red-600 hover:underline"
            >
              Cerrar sesión
            </button>


          </div>
        </transition>
      </div>




    </header>

    <div class="flex">
      <!-- Sidebar (animada) -->
      <transition name="slide">
        <aside
          v-if="barraActiva"
          class="fixed z-40 left-0 top-0 h-full w-64 bg-white shadow-xl p-6 flex flex-col gap-4"
        >
          <div class="absolute top-4 left-4">
            <button @click="barraActiva = false" class="text-black text-2xl">☰</button>
          </div>
          <pre> </pre>
          <h2 class="text-lg font-semibold text-gray-700 mb-4 text-center">Filtrar por categoría</h2>
          <div class="flex flex-col gap-3 items-center">
            <button 
              v-for="categoria in categorias"
              :key="categoria"
              @click="filtrarPorCategoria(categoria)"
              :class="[
                'w-full px-4 py-2 rounded-xl text-sm font-semibold border transition',
                categoriaSeleccionada === categoria
                  ? 'bg-orange-500 text-white border-orange-600 shadow-md'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-orange-100'
              ]"
            >
              {{ categoria }}
            </button>
          </div>
          <button 
            @click="limpiarFiltros"
            class="mt-6 w-full px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-xl hover:bg-red-200 transition"
          >
            Limpiar filtros
          </button>

        </aside>
      </transition>

      <!-- Contenido principal -->
      <main class="flex-1 p-8 transition-all duration-500">
        <div v-if="recetas.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="receta in recetas"
            :key="receta.id"
            class="p-4 border border-gray-300 rounded-lg shadow-sm bg-white cursor-pointer hover:shadow-md transition-transform hover:-translate-y-0.5"
            @click="abrirModal(receta)"
          >
            <h2 class="text-lg font-bold text-orange-600 mb-2">{{ receta.nombre }}</h2>
            <p class="text-sm text-gray-700 mb-2 line-clamp-3">{{ receta.instrucciones }}</p>
            <ul class="text-sm text-gray-500 list-disc list-inside">
              <li v-for="ingrediente in receta.ingredientes" :key="ingrediente.id">
                {{ ingrediente.nombre }} — {{ ingrediente.pivot.cantidad }}
              </li>
            </ul>
          </div>
        </div>

        <div v-else class="mt-8 text-center text-gray-500">
          No se encontraron recetas con esos ingredientes
        </div>
      </main>
    </div>

    <!-- Modal de detalle -->
    <div
      v-if="mostrarModal && recetaSeleccionada"
      class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center"
    >
      <div class="bg-white w-4/5 max-w-5xl rounded-xl overflow-hidden relative shadow-xl">
        <div
          class="h-64 bg-cover bg-center text-white flex flex-col justify-end p-6"
          :style="`background-image: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.8)), url('/images/cocina.jpg')`"
        >
          <h2 class="text-3xl font-bold">{{ recetaSeleccionada.nombre }}</h2>
          <p class="text-sm font-bold">Tiempo: {{ recetaSeleccionada.tiempo_preparacion }} min</p>
          <p class="text-sm font-bold">Dificultad: {{ recetaSeleccionada.dificultad }}</p>
        </div>

        <button @click="cerrarModal" class="absolute top-4 right-4 text-white bg-red-500 hover:bg-red-600 rounded-full w-8 h-8 flex items-center justify-center font-bold">
          ×
        </button>

        <div class="grid grid-cols-2 gap-6 p-6">
          <div>
            <h3 class="text-lg font-bold mb-2 text-orange-600">📋 Instrucciones</h3>
            <p class="text-gray-700 whitespace-pre-wrap font-bold">{{ recetaSeleccionada.instrucciones }}</p>
          </div>
          <div>
            <h3 class="text-lg font-bold mb-2 text-orange-600">🥕 Ingredientes</h3>
            <ul class="text-gray-700 list-disc list-inside font-bold">
              <li v-for="ingrediente in recetaSeleccionada.ingredientes" :key="ingrediente.id">
                {{ ingrediente.nombre }} — {{ ingrediente.pivot.cantidad }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>  
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import axios from 'axios'
import { usePage } from '@inertiajs/vue3'
import { watch } from 'vue'
import { router } from '@inertiajs/vue3'

const barraActiva = ref(false)
const recetas = ref([])
const busqueda = ref('')
const categoriaSeleccionada = ref('')
const recetaSeleccionada = ref(null)
const mostrarModal = ref(false)

const page = usePage()
const usuario = computed(() => page.props.auth?.user)

const categorias = ['Vegetal', 'Animal', 'Procesados']

const buscarRecetas = async () => {
  try {
    const response = await axios.get('/api/recetas', {
      params: {
        ingrediente: busqueda.value,
        categoria: categoriaSeleccionada.value
      }
    })
    recetas.value = response.data
  } catch (error) {
    console.error('Error al buscar recetas:', error)
  }
}

const filtrarPorCategoria = async (categoria) => {
  categoriaSeleccionada.value = categoriaSeleccionada.value === categoria ? '' : categoria
  await buscarRecetas()
}

const limpiarFiltros = async () => {
  busqueda.value = ''
  categoriaSeleccionada.value = ''
  await buscarRecetas()
}

const abrirModal = (receta) => {
  recetaSeleccionada.value = receta
  mostrarModal.value = true
}

const cerrarModal = () => {
  recetaSeleccionada.value = null
  mostrarModal.value = false
}

onMounted(() => {
  buscarRecetas()
})

const mostrarPerfil = ref(false)

//const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

function clickOutside(event) {
  const panel = document.getElementById('panel-usuario')
  const boton = document.getElementById('boton-perfil')

  if (panel && !panel.contains(event.target) && !boton.contains(event.target)) {
    mostrarPerfil.value = false
  }
}

watch(mostrarPerfil, (nuevoValor) => {
  if (nuevoValor) {
    document.addEventListener('click', clickOutside)
  } else {
    document.removeEventListener('click', clickOutside)
  }
})

const logout = () => {
  router.post('/logout')
}

</script>

<style>
.slide-enter-active, .slide-leave-active {
  transition: transform 0.3s ease;
}
.slide-enter-from,
.slide-leave-to {
  transform: translateX(-100%);
}
</style>
