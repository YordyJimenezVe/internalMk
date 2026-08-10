<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
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

// Countdown to Saturday Aug 15, 2026 at 00:01:00 UTC
const targetDate = new Date('2026-08-15T00:01:00Z').getTime();
const timeLeft = ref({
    days: '00',
    hours: '00',
    minutes: '00',
    seconds: '00',
    isExpired: false,
});

let timer = null;

const updateCountdown = () => {
    const now = Date.now();
    const difference = targetDate - now;

    if (difference <= 0) {
        timeLeft.value = {
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            isExpired: true,
        };
        if (timer) clearInterval(timer);
        return;
    }

    const days = Math.floor(difference / (1000 * 60 * 60 * 24));
    const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((difference % (1000 * 60)) / 1000);

    timeLeft.value = {
        days: String(days).padStart(2, '0'),
        hours: String(hours).padStart(2, '0'),
        minutes: String(minutes).padStart(2, '0'),
        seconds: String(seconds).padStart(2, '0'),
        isExpired: false,
    };
};

onMounted(() => {
    updateCountdown();
    timer = setInterval(updateCountdown, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
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

        <!-- Hosting Expiration Notice with Countdown -->
        <div class="mb-6 p-4 bg-rose-500/10 border border-rose-500/30 text-rose-400 rounded-xl text-left text-xs font-medium space-y-3">
            <div class="flex items-center gap-3">
                <i class="fas fa-triangle-exclamation text-xl text-rose-500 animate-pulse shrink-0"></i>
                <div>
                    <p class="font-extrabold uppercase tracking-wide text-rose-500 text-xs">⚠️ AVISO DEL PROVEEDOR DE HOSPEDAJE</p>
                    <p class="text-gray-300 text-[11px] leading-tight mt-0.5">
                        La suscripción del plan para <span class="text-white font-bold">maikelcars.com</span> está por expirar. Realice el pago de renovación para evitar la suspensión.
                    </p>
                </div>
            </div>

            <!-- Countdown Display -->
            <div class="bg-slate-900/80 border border-rose-500/20 rounded-lg p-2.5 flex items-center justify-between text-center">
                <div class="flex-1">
                    <span class="block text-base font-black text-rose-400 font-mono tracking-wider">{{ timeLeft.days }}</span>
                    <span class="text-[9px] uppercase tracking-wider text-gray-400 font-semibold">Días</span>
                </div>
                <span class="text-rose-500/50 font-bold text-sm -mt-3">:</span>
                <div class="flex-1">
                    <span class="block text-base font-black text-rose-400 font-mono tracking-wider">{{ timeLeft.hours }}</span>
                    <span class="text-[9px] uppercase tracking-wider text-gray-400 font-semibold">Horas</span>
                </div>
                <span class="text-rose-500/50 font-bold text-sm -mt-3">:</span>
                <div class="flex-1">
                    <span class="block text-base font-black text-rose-400 font-mono tracking-wider">{{ timeLeft.minutes }}</span>
                    <span class="text-[9px] uppercase tracking-wider text-gray-400 font-semibold">Min</span>
                </div>
                <span class="text-rose-500/50 font-bold text-sm -mt-3">:</span>
                <div class="flex-1">
                    <span class="block text-base font-black text-rose-400 font-mono tracking-wider">{{ timeLeft.seconds }}</span>
                    <span class="text-[9px] uppercase tracking-wider text-gray-400 font-semibold">Seg</span>
                </div>
            </div>
            
            <p v-if="timeLeft.isExpired" class="text-rose-400 text-center font-bold text-xs uppercase animate-pulse">
                El periodo de hospedaje ha vencido (Sábado 15 - 00:01 UTC)
            </p>
            <p v-else class="text-gray-400 text-[10px] text-center font-normal">
                Vence el <span class="text-gray-200 font-semibold">Sábado 15 a las 00:01 UTC</span>
            </p>
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
