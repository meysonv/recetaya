<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
    <Head title="Registro" />
    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">
      <h1 class="text-3xl font-bold text-center text-orange-600 mb-6">Crea una cuenta</h1>

      <form @submit.prevent="submit" class="flex flex-col gap-5">
        <div>
          <Label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nombre</Label>
          <Input
            id="name"
            type="text"
            v-model="form.name"
            required
            autocomplete="name"
            class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 bg-white"
          />
          <InputError :message="form.errors.name" />
        </div>

        <div>
          <Label for="email" class="block text-sm font-semibold text-gray-700 mb-1">Correo electrónico</Label>
          <Input
            id="email"
            type="email"
            v-model="form.email"
            required
            autocomplete="email"
            class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 bg-white"
          />
          <InputError :message="form.errors.email" />
        </div>

        <div>
          <Label for="password" class="block text-sm font-semibold text-gray-700 mb-1">Contraseña</Label>
          <Input
            id="password"
            type="password"
            v-model="form.password"
            required
            autocomplete="new-password"
            class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 bg-white"
          />
          <InputError :message="form.errors.password" />
        </div>

        <div>
          <Label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1">Confirmar contraseña</Label>
          <Input
            id="password_confirmation"
            type="password"
            v-model="form.password_confirmation"
            required
            autocomplete="new-password"
            class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 bg-white"
          />
          <InputError :message="form.errors.password_confirmation" />
        </div>

        <Button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 rounded-lg transition" :disabled="form.processing">
          <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin inline mr-2" />
          Crear cuenta
        </Button>
      </form>

      <p class="text-center text-sm text-gray-600 mt-4">
        ¿Ya tienes cuenta?
        <TextLink :href="route('login')" class="text-orange-500 hover:underline">Inicia sesión</TextLink>
      </p>
    </div>
  </div>
</template>