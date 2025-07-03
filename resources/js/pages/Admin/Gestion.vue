<template>
  <div class="min-h-screen flex bg-gray-100">
    <!-- Barra lateral -->
    
    <aside class="w-64 bg-orange-500 text-white shadow-xl p-6"> 
      <!-- Título --> 
      <h2 class="text-2xl font-extrabold text-white flex items-center gap-2 mb-6">
        <span class="text-3xl animate-spin-slow">⚙️</span>
        <span class="tracking-wide drop-shadow">Panel Admin</span>
      </h2>
      <hr class="border-white border-opacity-30 mb-8" />
    
      <!-- Botones -->
      <nav class="flex flex-col gap-4">
        <button
          @click="vista = 'usuarios'"
          :class="`flex items-center gap-2 px-4 py-3 rounded-xl border border-white transition font-semibold transform
            ${vista === 'usuarios' 
              ? 'bg-white text-orange-500 translate-x-2 shadow-lg' 
              : 'text-white hover:bg-white hover:text-orange-500'}`"
        >
          <span class="text-xl">👤</span> Usuarios
        </button>
    
        <button
          @click="vista = 'recetas'"
          :class="`flex items-center gap-2 px-4 py-3 rounded-xl border border-white transition font-semibold transform
            ${vista === 'recetas' 
              ? 'bg-white text-orange-500 translate-x-2 shadow-lg' 
              : 'text-white hover:bg-white hover:text-orange-500'}`"
        >
          <span class="text-xl">🍽</span> Recetas
        </button>
    
        <button
          @click="vista = 'reportes'"
          :class="`flex items-center gap-2 px-4 py-3 rounded-xl border border-white transition font-semibold transform
            ${vista === 'reportes' 
              ? 'bg-white text-orange-500 translate-x-2 shadow-lg' 
              : 'text-white hover:bg-white hover:text-orange-500'}`"
        >
          <span class="text-xl">📊</span> Reportes
        </button>
      </nav>
    </aside>
    


    <!-- Contenido principal -->
    <div class="flex-1 flex flex-col">
      <!-- Encabezado -->
      <header class="flex justify-between items-center p-6 bg-white shadow">
        <!-- Título -->
        <Link href="/dashboard">
          <div>
            <h1 class="text-5xl font-black text-orange-500 drop-shadow-md leading-tight group-hover:underline">
              RECETA YA
            </h1>
            <p class="text-base text-gray-500 font-semibold tracking-wider uppercase ml-1">
              GESTIÓN
            </p>
          </div>
        </Link>
        
        <!-- Botón MI CUENTA y panel -->
        <div class="relative">
          <button
            id="boton-perfil"
            @click="mostrarPerfil = !mostrarPerfil"
            class="bg-green-600 text-white px-4 py-2 rounded-xl font-semibold hover:bg-green-700"
          >
            MI CUENTA
          </button>
      
          <transition name="fade">
            <div
              v-if="mostrarPerfil"
              id="panel-usuario"
              class="absolute right-0 mt-2 w-64 bg-white text-gray-800 shadow-xl rounded-xl p-4 z-50"
            >
              <!-- Imagen de perfil -->
              <div class="flex justify-center mb-4">
                <img src="/images/avatar.png" alt="Perfil" class="w-20 h-20 rounded-full border-4 border-orange-500" />
              </div>
      
              <p class="text-center text-sm text-gray-500 font-semibold uppercase">{{ usuario.rol }}</p>
              <p class="text-center text-lg font-bold mb-4">{{ usuario.name }}</p>
      
              <button @click="logout" class="w-full py-2 text-center text-sm text-red-600 hover:underline">
                Cerrar sesión
              </button>
            </div>
          </transition>
        </div>
      </header>

      <!-- Espacio que cambia -->
      <main class="p-8 bg-gray-50 flex-1 bg-slate-100">
        <template v-if="vista === 'usuarios'">
          <Usuarios :usuarios="usuarios" />
        </template>
        <template v-else-if="vista === 'recetas'">
          <Recetas :recetas="recetas" />
        </template>
        <template v-else-if="vista === 'reportes'">
          <Reportes :reportes="reportes" />
        </template>
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import Usuarios from './Usuarios.vue'
import Recetas from './Recetas.vue'
import Reportes from './Reportes.vue'

const vista = ref('reportes') // Valor inicial de la vista
const mostrarPerfil = ref(false)

const page = usePage()
const usuario = computed(() => page.props.auth?.user || { name: 'Invitado' })
const usuarios = computed(() => page.props.usuarios)
const recetas = computed(() => page.props.recetas)

const reportes = computed(() => page.props.reportes)

const botonClase = (valor) => {
  return vista.value === valor
    ? 'bg-orange-500 text-white font-semibold py-2 rounded-xl'
    : 'text-gray-700 py-2 py-2 text-left hover:bg-orange-100 rounded-xl'
}

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

const recetasPendientes = computed(() => recetas.value.filter(r => r.aprobada === 0))



</script>

<style>
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}
@keyframes spin-slow {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
.animate-spin-slow {
  animation: spin-slow 4s linear infinite;
}
</style>