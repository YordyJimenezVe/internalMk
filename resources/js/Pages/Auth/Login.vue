<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar Sesión" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="status" class="mb-4 font-medium text-sm text-green-400">
            {{ status }}
        </div>

        <!-- Hosting Expiration Notice -->
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl text-left text-xs font-bold flex items-center gap-3">
            <i class="fas fa-triangle-exclamation text-lg text-rose-500 animate-pulse shrink-0"></i>
            <div>
                <p class="font-extrabold uppercase tracking-wide text-rose-500 mb-0.5">⚠️ AVISO DEL PROVEEDOR DE HOSPEDAJE</p>
                <p class="text-gray-300 font-medium">La suscripción del plan de negocios para <span class="text-white font-bold">maikelcars.com</span> vencerá en 07 días. Por favor, realice el pago de renovación a la brevedad para evitar la suspensión del servicio.</p>
            </div>
        </div>

        <h2 class="text-center text-white text-xl font-bold mb-6 opacity-90 tracking-tight">BIENVENIDO</h2>

        <form @submit.prevent="submit" class="space-y-5">
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

            <div>
                <div class="flex justify-between items-center mb-1.5">
                    <InputLabel for="password" value="Contraseña" class="text-gray-300" />
                    <Link v-if="canResetPassword" :href="route('password.request')" class="text-xs text-blue-400 hover:text-blue-300 hover:underline transition-colors focus:outline-none">
                        ¿Olvidaste tu contraseña?
                    </Link>
                </div>
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                        <i class="fas fa-lock"></i>
                    </span>
                    <TextInput
                        id="password"
                        v-model="form.password"
                        type="password"
                        class="block w-full pl-10 bg-slate-800/50 border-slate-700 text-white focus:border-blue-500 focus:ring-blue-500/20 rounded-xl transition-all"
                        placeholder="••••••••"
                        required
                        autocomplete="current-password"
                    />
                </div>
                <InputError class="mt-2 text-red-400" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between py-2">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox v-model:checked="form.remember" name="remember" class="rounded border-slate-700 bg-slate-800 text-blue-600 shadow-sm focus:ring-blue-500/20 cursor-pointer" />
                    <span class="ms-2 text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Recordarme</span>
                </label>
            </div>

            <div class="pt-2">
                <PrimaryButton 
                    class="w-full justify-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-bold py-3 rounded-xl shadow-lg transform active:scale-[0.98] transition-all" 
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">ENTRAR</span>
                    <i v-else class="fas fa-circle-notch fa-spin"></i>
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>

<style>
/* Custom transitions and scrollbar for the auth views if needed */
.animate__animated {
    --animate-duration: 0.8s;
}
</style>
