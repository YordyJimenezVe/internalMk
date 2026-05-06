<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Recuperar Contraseña" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div class="mb-6 text-sm text-gray-400 text-center leading-relaxed font-medium">
            ¿Olvidaste tu contraseña? No hay problema. Solo dinos tu dirección de correo electrónico y te enviaremos un enlace para restablecerla.
        </div>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-400 text-center bg-green-400/10 p-3 rounded-lg border border-green-400/20">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel for="email" value="Correo Electrónico" class="text-gray-300 mb-1.5" />
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <TextInput
                        id="email"
                        v-model="form.email"
                        type="email"
                        class="block w-full pl-10 bg-slate-800/50 border-slate-700 text-white focus:border-blue-500 focus:ring-blue-500/20 rounded-xl transition-all"
                        placeholder="ejemplo@correo.com"
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-2 text-red-400" :message="form.errors.email" />
            </div>

            <div class="flex items-center justify-between gap-4 pt-2">
                <Link :href="route('login')" class="text-sm text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-arrow-left me-2"></i>Volver al Inicio
                </Link>
                <PrimaryButton 
                    class="justify-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-bold py-3 px-6 rounded-xl shadow-lg transform active:scale-[0.98] transition-all" 
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">ENVIAR ENLACE</span>
                    <i v-else class="fas fa-circle-notch fa-spin"></i>
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
