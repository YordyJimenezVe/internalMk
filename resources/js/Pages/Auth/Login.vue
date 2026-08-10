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

// Force preview mode for testing via query parameter ?expired=true or ?preview_expired=1
const checkPreviewParam = () => {
    if (typeof window !== 'undefined') {
        const params = new URLSearchParams(window.location.search);
        if (params.get('expired') === 'true' || params.get('expired') === '1' || params.get('preview_expired') === '1') {
            return true;
        }
    }
    return false;
};

let timer = null;

const updateCountdown = () => {
    const isForceExpired = checkPreviewParam();
    const now = Date.now();
    const difference = targetDate - now;

    if (difference <= 0 || isForceExpired) {
        timeLeft.value = {
            days: '00',
            hours: '00',
            minutes: '00',
            seconds: '00',
            isExpired: true,
        };
        if (timer && !isForceExpired) clearInterval(timer);
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
    <Head :title="timeLeft.isExpired ? 'HostGator - Cuenta Suspendida' : 'Iniciar Sesión'" />

    <AuthenticationCard>
        <template #logo>
            <!-- HostGator Brand Logo with Snappy Crocodile Mascot when expired -->
            <div v-if="timeLeft.isExpired" class="flex flex-col items-center gap-2">
                <div class="flex items-center gap-3 bg-slate-900/90 border border-amber-500/30 px-5 py-3 rounded-2xl shadow-xl">
                    <!-- HostGator Snappy Alligator Mascot SVG -->
                    <div class="relative w-12 h-12 flex items-center justify-center bg-gradient-to-br from-amber-400 to-yellow-500 rounded-xl shadow-md p-1.5 shrink-0">
                        <svg viewBox="0 0 100 100" class="w-full h-full drop-shadow-sm" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <!-- Snappy Alligator Head & Snout -->
                            <path d="M15 62C15 48 26 36 42 36C52 36 65 30 76 20C80 26 84 34 82 45C80 54 88 56 94 60C88 64 78 65 72 65C62 65 55 70 48 74C38 80 26 78 19 72C16 69 15 66 15 62Z" fill="#0284c7" />
                            <!-- Yellow Belly / Jaw -->
                            <path d="M19 72C26 78 38 80 48 74C55 70 62 65 72 65C78 65 88 64 94 60C90 68 78 74 68 76C55 79 40 82 28 80C22 79 19 75 19 72Z" fill="#facc15" />
                            <!-- Eye Ridge & White Eye -->
                            <circle cx="44" cy="34" r="10" fill="#0284c7" />
                            <circle cx="44" cy="34" r="7" fill="#ffffff" />
                            <circle cx="46" cy="34" r="3.5" fill="#0f172a" />
                            <circle cx="47.5" cy="32.5" r="1.2" fill="#ffffff" />
                            <!-- Alligator Snout Scales / Spikes -->
                            <path d="M28 36L32 28L36 36" fill="#0369a1" />
                            <path d="M20 44L24 37L27 45" fill="#0369a1" />
                            <!-- Snappy Teeth -->
                            <path d="M52 66L55 60L58 66" fill="#ffffff" />
                            <path d="M62 65L65 59L68 65" fill="#ffffff" />
                            <path d="M72 64L75 58L78 64" fill="#ffffff" />
                            <!-- Snappy Nostril & Smile -->
                            <circle cx="86" cy="55" r="2" fill="#0369a1" />
                        </svg>
                    </div>
                    <div class="text-left">
                        <div class="flex items-baseline">
                            <span class="text-2xl font-black tracking-tight text-white">Host</span>
                            <span class="text-2xl font-black tracking-tight text-amber-400">Gator</span>
                        </div>
                        <span class="text-[10px] tracking-wider uppercase text-gray-400 font-semibold block -mt-1">Web Hosting Services</span>
                    </div>
                </div>
            </div>
            <AuthenticationCardLogo v-else />
        </template>

        <!-- ========================================== -->
        <!-- STATE 1: HOSTING SUSPENDED / EXPIRED VIEW -->
        <!-- ========================================== -->
        <div v-if="timeLeft.isExpired" class="space-y-6 text-center animate__animated animate__fadeIn">
            <!-- Alert Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-rose-500/15 border border-rose-500/30 text-rose-400 text-xs font-bold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                <span>Servicio Suspendido</span>
            </div>

            <!-- Main Suspended Headline -->
            <div>
                <h2 class="text-xl font-extrabold text-white tracking-tight">
                    Esta Cuenta Ha Sido Suspendida
                </h2>
                <p class="text-gray-400 text-xs mt-1.5 leading-relaxed">
                    El periodo de alojamiento web contratado para este sitio ha llegado a su fecha de vencimiento.
                </p>
            </div>

            <!-- Domain Info Card -->
            <div class="bg-slate-900/90 border border-slate-700/70 rounded-xl p-3.5 text-left space-y-2">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-medium">Dominio Afectado:</span>
                    <span class="font-mono font-bold text-amber-400 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20">maikelcars.com</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-medium">Proveedor:</span>
                    <span class="text-gray-200 font-semibold flex items-center gap-1.5">
                        <i class="fas fa-server text-blue-400"></i> HostGator Web Hosting
                    </span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-400 font-medium">Fecha de Vencimiento:</span>
                    <span class="text-rose-400 font-semibold">15 Ago 2026 - 00:01 UTC</span>
                </div>
                <div class="flex justify-between items-center text-xs border-t border-slate-800 pt-2">
                    <span class="text-gray-400 font-medium">Código de Estado:</span>
                    <span class="font-mono text-[11px] text-gray-300">ERR_HOSTING_EXPIRED_RENEWAL</span>
                </div>
            </div>

            <!-- Instructions Notice -->
            <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-left text-xs text-amber-300 flex items-start gap-2.5">
                <i class="fas fa-circle-info text-amber-400 mt-0.5 text-sm shrink-0"></i>
                <p class="text-[11px] leading-relaxed text-amber-200/90">
                    Si usted es el administrador o titular de esta cuenta, por favor comuníquese con el <strong>proveedor de hosting (HostGator)</strong> o ingrese a su portal de cliente para regularizar el pago de renovación y restaurar el servicio de inmediato.
                </p>
            </div>

            <!-- HostGator Actions -->
            <div class="space-y-3 pt-1">
                <a
                    href="https://portal.hostgator.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-amber-500 to-yellow-500 hover:from-amber-400 hover:to-yellow-400 text-slate-950 font-extrabold py-3 px-4 rounded-xl shadow-lg shadow-amber-500/20 transform active:scale-[0.98] transition-all text-xs tracking-wider uppercase"
                >
                    <i class="fas fa-credit-card"></i>
                    <span>Portal de Clientes HostGator</span>
                    <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                </a>

                <a
                    href="https://www.hostgator.com/contact"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="w-full inline-flex items-center justify-center gap-2 bg-slate-800/80 hover:bg-slate-700/80 border border-slate-700 text-gray-300 hover:text-white font-semibold py-2.5 px-4 rounded-xl transition-all text-xs"
                >
                    <i class="fas fa-headset text-blue-400"></i>
                    <span>Contactar Soporte Técnico 24/7</span>
                </a>
            </div>

            <p class="text-gray-500 text-[10px]">
                HostGator LLC &bull; Todos los derechos reservados.
            </p>
        </div>

        <!-- ========================================== -->
        <!-- STATE 2: ACTIVE LOGIN & COUNTDOWN VIEW     -->
        <!-- ========================================== -->
        <template v-else>
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
                
                <p class="text-gray-400 text-[10px] text-center font-normal">
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
        </template>
    </AuthenticationCard>
</template>

<style>
/* Custom transitions and scrollbar for the auth views if needed */
.animate__animated {
    --animate-duration: 0.8s;
}
</style>
