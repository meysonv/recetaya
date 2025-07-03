<template>
  <div>
    <h2 class="text-xl font-bold mb-4 text-black">Getionar Usuarios</h2>
    <table class="min-w-full bg-white shadow rounded">
      <thead>
        <tr class="bg-orange-500 text-white">
          <th class="p-2">ID</th>
          <th class="p-2">Nombre</th>
          <th class="p-2">Email</th>
          <th class="p-2">Rol</th>
          <th class="p-2">Recetas</th>
          <th class="p-2">Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="usuario in usuarios" :key="usuario.id" class="border-t">
          <td class="p-2 text-center text-black">{{ usuario.id }}</td>
          <td class="p-2 text-center text-black">{{ usuario.name }}</td>
          <td class="p-2 text-center text-black">{{ usuario.email }}</td>
          <td class="p-2 text-center text-black">{{ usuario.rol }}</td>
          <td class="p-2 text-center text-black">{{ usuario.recetas_count }}</td>
          <td class="p-2 text-center text-black">

            <button
              @click="abrirModal(usuario)"
              class="bg-yellow-400 px-2 py-1 rounded hover:bg-yellow-500"
            >
              Editar
            </button>            
            
            <button
              class="bg-red-500 px-2 py-1 text-white rounded ml-2 hover:bg-red-700"
              @click="eliminarUsuario(usuario.id)"
            >
              Eliminar
            </button>

          </td>
        </tr>
      </tbody>
    </table>
  </div>

<transition name="fade">
  <div
    v-if="mostrarModal"
    class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
  >
    <div class="bg-white p-6 rounded-xl w-96 shadow-lg">
      <h2 class="text-lg font-bold mb-4 text-black">Editar Usuario</h2>

      <div class="mb-2">
        <label class="block text-sm text-black">Nombre</label>
        <input v-model="usuarioEditando.name" class="w-full border rounded p-2 text-black" />
      </div>

      <div class="mb-2">
        <label class="block text-sm text-black">Email</label>
        <input v-model="usuarioEditando.email" class="w-full border rounded p-2 text-black" />
      </div>

      <div class="mb-4">
        <label class="block text-sm text-black">Rol</label>
        <select v-model="usuarioEditando.rol" class="w-full border rounded p-2 text-black">
          <option value="admin">Admin</option>
          <option value="cliente">Cliente</option>
        </select>
      </div>

      

      <div class="flex justify-end gap-2">
        <button @click="cerrarModal" class="px-4 py-2 bg-red-600 rounded hover:bg-red-700">Cancelar</button>
        <button @click="actualizarUsuario" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Guardar</button>
      </div>
    </div>
  </div>
</transition>


</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'


defineProps({
  usuarios: Array
})

const eliminarUsuario = (id) => {
  if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
    router.delete(`/admin/usuario/${id}`)
  }
}

const mostrarModal = ref(false)
const usuarioEditando = ref({
  id: null,
  name: '',
  email: '',
  rol: ''
})

const abrirModal = (usuario) => {
  usuarioEditando.value = { ...usuario }
  mostrarModal.value = true
}

const cerrarModal = () => {
  mostrarModal.value = false
}

const actualizarUsuario = () => {
  //console.log("Datos que se enviarán al backend:", usuarioEditando.value) check datos controlador
  router.put(`/admin/usuarios/${usuarioEditando.value.id}`, usuarioEditando.value, {
    onSuccess: () => cerrarModal()
  })
}

</script>