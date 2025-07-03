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
            
            <div class="flex justify-center items-center">
              <button 
                @click="receta = recetaVacia(); mostrarModalCrear = true" 
                class="bg-green-600 text-white rounded px-4 py-2 mt-4"
              >
                Crear receta
              </button>
            </div>
      
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

          <!-- Botón Mis Favoritos -->
          <button
            @click="abrirModalFavoritos"
            class="text-center text-black font-bold text-lg border-2 border-black px-4 py-2 rounded-xl hover:bg-orange-100 transition"
          >
            ❤️ Mis favoritos
          </button>

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

  <transition name="fade-modal">
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
          
          <button
            @click="toggleFavorito(recetaSeleccionada.id)"
            class="absolute buttom-2 right-4 bg-white hover:bg-red-300 rounded-full w-13 h-13 flex items-center justify-center transition-transform duration-200 active:scale-110"
          >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            class="w-12 h-12 transition-colors duration-200"
            :class="recetaSeleccionada.es_favorito ? 'fill-red-500' : 'fill-gray-300'"
            viewBox="0 -1 24 24"
          >
            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 
                    4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 
                    2.09C13.09 3.81 14.76 3 16.5 3 19.58 
                    3 22 5.42 22 8.5c0 3.78-3.4 
                    6.86-8.55 11.54L12 21.35z"
              />
          </svg>

          </button>
          
        </div>
        <!-- modaldefavoritos -->
        <button @click="cerrarModal" class="absolute top-4 right-4 text-white bg-red-500 hover:bg-red-600 rounded-full w-9 h-9 flex items-center justify-center font-bold">
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
  </transition>

    <!-- Modal de favoritos -->
        <div
          v-if="mostrarModalFavoritos"
          class="fixed inset-0 z-40 bg-black/50 flex items-center justify-center"
        >
          <div class="bg-gray-100 w-11/12 max-w-6xl rounded-xl shadow-xl relative p-8">
            <h2 class="text-2xl font-bold text-center mb-6">FAVORITOS</h2>
        
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              
              <div
                v-for="receta in favoritosPaginados"
                :key="receta.id"
                @click="verDetalleDesdeFavoritos(receta)"
                class="border-2 p-4 text-center cursor-pointer hover:bg-orange-100 transition rounded-xl"
              >
                <h3 class="font-bold text-lg">{{ receta.nombre }}</h3>
                <p class="text-sm">⏱️ {{ receta.tiempo_preparacion }} min</p>
                <p class="text-sm">🔥 {{ receta.dificultad }}</p>
                <div class="mt-2 text-2xl">❤️</div>
              </div>
            </div>
        
            <!-- Navegación entre páginas -->
            <div class="flex justify-center mt-6">
              <div class="inline-flex space">
              <!-- Botón anterior -->
                <button
                  :disabled="paginaFavoritos === 1"
                  @click="cambiarPaginaFavoritos(paginaFavoritos - 1)"
                  class="px-3 py-1 border rounded disabled:opacity-50"
                >
                  ⬅️
                </button>
                <!-- Botones numerados -->
                <button
                  v-for="n in totalPaginas"
                  :key="n"
                  @click="cambiarPaginaFavoritos(n)"
                  :class="[
                    'px-3 py-1 border rounded',
                    paginaFavoritos === n ? 'bg-black text-white' : 'bg-white'
                  ]"
                >
                  {{ n }}
                </button>
                
                <!-- Botón siguiente -->
                <button
                  :disabled="paginaFavoritos === totalPaginas"
                  @click="cambiarPaginaFavoritos(paginaFavoritos + 1)"
                  class="px-3 py-1 border rounded disabled:opacity-50"
                >
                  ➡️
                </button>    
              </div>
            </div>
            
        
            <!-- Botón cerrar -->
            <button
              @click="mostrarModalFavoritos = false"
              class="absolute top-4 right-4 text-white bg-red-500 hover:bg-red-600 rounded-full w-9 h-9 flex items-center justify-center font-bold"
            >
              ×
            </button>
          </div>
        </div>
  </div> 
  
  <!-- Modal -->
  <div v-if="mostrarModalCrear" class="fixed inset-0 bg-black/50 flex justify-center items-center z-50 text-black">
    <div class="bg-white p-6 rounded-lg w-full max-w-xl">
      <h2 class="text-lg font-semibold mb-4">Crear Receta</h2>

      <input v-model="receta.nombre" placeholder="Nombre" class="w-full mb-2 border p-2 rounded" />
      <textarea v-model="receta.instrucciones" placeholder="Instrucciones" class="w-full mb-2 border p-2 rounded"></textarea>
      <input v-model="receta.tiempo_preparacion" placeholder="Tiempo (minutos)" class="w-full mb-2 border p-2 rounded" />
      <select v-model="receta.dificultad" class="w-full mb-2 border p-2 rounded">
        <option value="" disabled>Dificultad</option>
        <option value="fácil">Fácil</option>
        <option value="media">Media</option>
        <option value="difícil">Difícil</option>
      </select>

      <h3 class="font-semibold mt-4">Ingredientes</h3>
      <div v-for="(ing, index) in receta.ingredientes" :key="index" class="flex gap-2 mb-2">
        <input v-model="ing.nombre" placeholder="Nombre" class="w-1/3 border p-2 rounded" />
        <select v-model="ing.categoria" class="w-1/3 border p-2 rounded ">
          <option disabled value="">Categoría</option>
          <option value="Vegetal">Vegetal</option>
          <option value="Animal">Animal</option>
          <option value="Procesados">Procesados</option>
        </select>
        <input v-model="ing.cantidad" placeholder="Cantidad" class="w-1/3 border p-2 rounded" />
      </div>
      <button @click="receta.ingredientes.push({ nombre: '', categoria: '', cantidad: '' })" class="text-blue-500 text-sm">
        + Añadir ingrediente
      </button>

      <div class="flex justify-end gap-2 mt-4">
        <button @click="cerrarModalCrear" class="bg-red-500 text-white px-4 py-2 rounded">Cancelar</button>
        <button @click="enviarSolicitud" class="bg-green-600 text-white px-4 py-2 rounded">Enviar solicitud</button>
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
//favoritos
const toggleFavorito = async (id) => {
  try {
    const response = await axios.post(`/favoritos/${id}`);
    const estado = response.data.status;
    console.log(response.data.status)

    if (recetaSeleccionada.value && recetaSeleccionada.value.id === id) {
      recetaSeleccionada.value.es_favorito = estado === 'added';
    }
  } catch (error) {
    console.error('Error al agregar/eliminar favorito:', error);
  }
};

//favoritos modal
const mostrarModalFavoritos = ref(false)
const favoritos = ref([])
const paginaFavoritos = ref(1)

const favoritosPorPagina = 6

const abrirModalFavoritos = async () => {
  
  //console.log('Cambiando a página:', nuevaPagina)
  
  try {
    const response = await axios.get('/api/favoritos')  // Este endpoint lo crearemos en el backend
    favoritos.value = response.data
    mostrarModalFavoritos.value = true
    paginaFavoritos.value = 1
  } catch (error) {
    console.error('Error al cargar favoritos:', error)
  }
}
const favoritosPaginados = computed(() => {
  const inicio = (paginaFavoritos.value - 1) * favoritosPorPagina
  return favoritos.value.slice(inicio, inicio + favoritosPorPagina)
})

const totalPaginas = computed(() =>
  Math.ceil(favoritos.value.length / favoritosPorPagina)
)

// modal favoritos ver detalle
const verDetalleDesdeFavoritos = async (receta) => {
  try {
    const response = await axios.get(`/api/recetas/${receta.id}`);
    recetaSeleccionada.value = response.data;
    //mostrarModalFavoritos.value = false;
    mostrarModal.value = true;
  } catch (error) {
    console.error('Error al cargar detalle de receta:', error);
  }
};

const cambiarPaginaFavoritos = (nuevaPagina) => {
  if (nuevaPagina >= 1 && nuevaPagina <= totalPaginas.value) {
    paginaFavoritos.value = nuevaPagina
  }
}

//crear receta cliente

const mostrarModalCrear = ref(false)

const recetaVacia = () => ({
  nombre: '',
  instrucciones: '',
  tiempo_preparacion: '',
  dificultad: '',
  ingredientes: [
    { nombre: '', categoria: '', cantidad: '' }
  ]
})

const receta = ref(recetaVacia())


const enviarSolicitud = () => {
  router.post('/cliente/solicitudes-receta', receta.value, {
    onSuccess: () => {
      mostrarModalCrear.value = false
      receta.value = recetaVacia()
      alert('Receta enviada para aprobación')
    },
    onError: (errors) => {
      console.error('Errores de validación:', errors)
      alert('Hubo un error al enviar la receta')
    }
  })
}


const cerrarModalCrear = () => {
  mostrarModalCrear.value = false
  receta.value = recetaVacia()
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

.fade-modal-enter-active, .fade-modal-leave-active {
  transition: opacity 0.3s ease, transform 0.1s ease;
}
.fade-modal-enter-from, .fade-modal-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
.fade-modal-enter-to, .fade-modal-leave-from {
  opacity: 1;
  transform: scale(1);
}

</style>
