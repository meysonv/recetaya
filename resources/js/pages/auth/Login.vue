<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Head, useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-slate-100 px-4">
    <Head title="Iniciar sesión" />

    <div class="w-full max-w-md bg-white p-8 rounded-2xl shadow-xl">
      <h1 class="text-3xl font-bold text-center text-orange-600 mb-6">Inicia sesión</h1>

      <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
        {{ status }}
      </div>

      <form @submit.prevent="submit" class="flex flex-col gap-5">
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
            autocomplete="current-password"
            class="w-full px-4 py-2 border border-gray-300 rounded-md text-gray-900 bg-white"
          />
          <InputError :message="form.errors.password" />
        </div>

        <div class="flex items-center justify-between">
          <label for="remember" class="flex items-center space-x-2 text-sm text-gray-700">
            <Checkbox id="remember" v-model="form.remember" />
            <span>Recordarme</span>
          </label>

          <TextLink v-if="canResetPassword" :href="route('password.request')" class="text-sm text-orange-500 hover:underline">
            ¿Olvidaste tu contraseña?
          </TextLink>
        </div>

        <Button type="submit" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 rounded-lg transition" :disabled="form.processing">
          <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin inline mr-2" />
          Iniciar sesión
        </Button>
      </form>

      <p class="text-center text-sm text-gray-600 mt-4">
        ¿No tienes cuenta?
        <TextLink :href="route('register')" class="text-orange-500 hover:underline">Regístrate</TextLink>
      </p>
    </div>
  </div>
</template>
