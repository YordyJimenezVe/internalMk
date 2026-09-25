<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    email: String,
    token: String,
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.update'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <Head title="Restablecer Contraseña" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <h2 class="text-center text-white text-xl font-bold mb-6 opacity-90 tracking-tight">CABLEAR CONTRASEÑA</h2>

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
                        required
                        autofocus
                        autocomplete="username"
                    />
                </div>
                <InputError class="mt-2 text-red-400" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Nueva Contraseña" class="text-gray-300 mb-1.5" />
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
                        autocomplete="new-password"
                    />
                </div>
                <InputError class="mt-2 text-red-400" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirmar Contraseña" class="text-gray-300 mb-1.5" />
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 group-focus-within:text-blue-500 transition-colors">
                        <i class="fas fa-shield-alt"></i>
                    </span>
                    <TextInput
                        id="password_confirmation"
                        v-model="form.password_confirmation"
                        type="password"
                        class="block w-full pl-10 bg-slate-800/50 border-slate-700 text-white focus:border-blue-500 focus:ring-blue-500/20 rounded-xl transition-all"
                        placeholder="••••••••"
                        required
                        autocomplete="new-password"
                    />
                </div>
                <InputError class="mt-2 text-red-400" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-2">
                <PrimaryButton 
                    class="w-full justify-center bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-500 hover:to-blue-600 text-white font-bold py-3 rounded-xl shadow-lg transform active:scale-[0.98] transition-all" 
                    :class="{ 'opacity-50 cursor-not-allowed': form.processing }" 
                    :disabled="form.processing"
                >
                   <span v-if="!form.processing">RESTABLECER CONTRASEÑA</span>
                   <i v-else class="fas fa-circle-notch fa-spin"></i>
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>
