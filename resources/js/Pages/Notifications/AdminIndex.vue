<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';

const props = defineProps({
    sentNotifications: Array,
    settings: Array,
    roles: Array,
    users: Array,
});

const activeTab = ref('settings');

// Form para cambiar configuración de un interruptor de alerta
const toggleForm = useForm({
    enabled: false,
});

const handleToggle = (setting) => {
    toggleForm.enabled = !setting.enabled;
    toggleForm.post(route('admin.notifications.toggle_setting', setting.id), {
        preserveScroll: true,
    });
};

// Form para mandar un aviso/comunicado manual
const broadcastForm = useForm({
    target_type: 'ALL',
    target_role: props.roles[0] || '',
    target_user: props.users[0]?.id || '',
    title: '',
    message: '',
    icon: 'fa-bullhorn',
    color: 'indigo',
});

const submitBroadcast = () => {
    broadcastForm.post(route('admin.notifications.broadcast'), {
        onSuccess: () => {
            broadcastForm.reset('title', 'message');
        },
        preserveScroll: true,
    });
};

const iconsList = [
    { name: 'Megáfono', value: 'fa-bullhorn' },
    { name: 'Campana', value: 'fa-bell' },
    { name: 'Alerta / Peligro', value: 'fa-triangle-exclamation' },
    { name: 'Camión', value: 'fa-truck-arrow-right' },
    { name: 'Calculadora', value: 'fa-calculator' },
    { name: 'Mensaje', value: 'fa-envelope' },
];

const colorsList = [
    { name: 'Índigo / Informativo', value: 'indigo', border: 'border-indigo-500', bg: 'bg-indigo-50 dark:bg-indigo-950/20', text: 'text-indigo-600 dark:text-indigo-400' },
    { name: 'Esmeralda / Éxito', value: 'emerald', border: 'border-emerald-500', bg: 'bg-emerald-50 dark:bg-emerald-950/20', text: 'text-emerald-600 dark:text-emerald-400' },
    { name: 'Ámbar / Advertencia', value: 'amber', border: 'border-amber-500', bg: 'bg-amber-50 dark:bg-amber-950/20', text: 'text-amber-600 dark:text-amber-400' },
    { name: 'Rosa / Crítico', value: 'rose', border: 'border-rose-500', bg: 'bg-rose-50 dark:bg-rose-950/20', text: 'text-rose-600 dark:text-rose-400' },
];
</script>

<template>
    <AppLayout title="Panel de Notificaciones">
        <template #header>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-wider">
                        Alertas & Avisos del Sistema
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                        Administra el flujo de notificaciones inteligentes y emite comunicados a los roles correspondientes.
                    </p>
                </div>
                
                <!-- Tab Controls -->
                <div class="flex bg-slate-100 dark:bg-slate-800 p-1 rounded-2xl border border-slate-200/50 dark:border-slate-700/50 shadow-inner">
                    <button 
                        @click="activeTab = 'settings'" 
                        class="px-4 py-2 text-xs font-black rounded-xl transition-all duration-205 flex items-center gap-2 uppercase tracking-widest"
                        :class="activeTab === 'settings' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-md' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    >
                        <i class="fa-solid fa-toggle-on text-sm"></i>
                        Ajustes
                    </button>
                    <button 
                        @click="activeTab = 'broadcast'" 
                        class="px-4 py-2 text-xs font-black rounded-xl transition-all duration-205 flex items-center gap-2 uppercase tracking-widest"
                        :class="activeTab === 'broadcast' ? 'bg-white dark:bg-slate-900 text-indigo-600 dark:text-indigo-400 shadow-md' : 'text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    >
                        <i class="fa-solid fa-bullhorn text-sm"></i>
                        Comunicados
                    </button>
                </div>
            </div>
        </template>

        <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Alert Banner success -->
            <div v-if="$page.props.flash?.success" class="mb-6 p-4 rounded-3xl bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-500/30 flex items-center gap-3 text-emerald-700 dark:text-emerald-300">
                <i class="fa-solid fa-circle-check text-xl"></i>
                <span class="text-xs font-bold">{{ $page.props.flash.success }}</span>
            </div>

            <!-- Tab content: Settings -->
            <div v-if="activeTab === 'settings'" class="grid grid-cols-1 gap-6">
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl p-6">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-6">
                        Configuración de Canales Automatizados
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div 
                            v-for="setting in settings" 
                            :key="setting.id"
                            class="p-5 bg-slate-50 dark:bg-slate-850 rounded-3xl border border-slate-100 dark:border-slate-800 hover:shadow-lg transition-all duration-200 flex flex-col justify-between"
                        >
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-xs font-black uppercase tracking-wider py-1 px-3.5 rounded-full" :class="
                                        setting.key === 'notify_outflow' ? 'bg-amber-100 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400' :
                                        setting.key === 'notify_return' ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' :
                                        setting.key === 'notify_pending_conciliation' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-950/30 dark:text-indigo-400' :
                                        'bg-rose-100 text-rose-700 dark:bg-rose-950/30 dark:text-rose-400'
                                    ">
                                        {{ setting.name }}
                                    </span>
                                    
                                    <!-- Toggle Switch -->
                                    <button 
                                        @click="handleToggle(setting)"
                                        class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                                        :class="setting.enabled ? 'bg-indigo-600' : 'bg-slate-300 dark:bg-slate-700'"
                                    >
                                        <span 
                                            class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="setting.enabled ? 'translate-x-5' : 'translate-x-0'"
                                        />
                                    </button>
                                </div>
                                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ setting.description }}</p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-200/50 dark:border-slate-800 flex justify-between items-center text-[10px]">
                                <span class="text-slate-400">Canal de envío</span>
                                <span class="font-bold text-slate-700 dark:text-slate-300 uppercase tracking-widest">Base de Datos / Campana</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tab content: Broadcast & Logs -->
            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Manual Broadcast Form -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl p-6 lg:col-span-1 h-fit">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-6">
                        Anuncio Manual
                    </h3>
                    
                    <form @submit.prevent="submitBroadcast" class="space-y-4">
                        <!-- Destinatario -->
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Destinatarios</label>
                            <select 
                                v-model="broadcastForm.target_type"
                                class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white p-3 font-semibold focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option value="ALL">Todos los Usuarios</option>
                                <option value="ROLE">Por Rol Específico</option>
                                <option value="USER">Por Usuario Específico</option>
                            </select>
                        </div>

                        <!-- Target Role Option -->
                        <div v-if="broadcastForm.target_type === 'ROLE'">
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Seleccionar Rol</label>
                            <select 
                                v-model="broadcastForm.target_role"
                                class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white p-3 font-semibold focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option v-for="role in roles" :key="role" :value="role">{{ role }}</option>
                            </select>
                        </div>

                        <!-- Target User Option -->
                        <div v-if="broadcastForm.target_type === 'USER'">
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Seleccionar Usuario</label>
                            <select 
                                v-model="broadcastForm.target_user"
                                class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white p-3 font-semibold focus:border-indigo-500 focus:ring-indigo-500"
                            >
                                <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }} ({{ user.rol || 'Sin Rol' }})</option>
                            </select>
                        </div>

                        <!-- Asunto / Título -->
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Título del Aviso</label>
                            <input 
                                type="text"
                                v-model="broadcastForm.title"
                                placeholder="Ej: Mantenimiento del Servidor"
                                class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white p-3 focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            />
                        </div>

                        <!-- Contenido -->
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Cuerpo del Mensaje</label>
                            <textarea 
                                v-model="broadcastForm.message"
                                placeholder="Escribe los detalles del anuncio aquí..."
                                rows="4"
                                class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white p-3 focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            ></textarea>
                        </div>

                        <!-- Estilo e Icono -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Icono ilustrativo</label>
                                <select 
                                    v-model="broadcastForm.icon"
                                    class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white p-3 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option v-for="ico in iconsList" :key="ico.value" :value="ico.value">{{ ico.name }}</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tema / Nivel</label>
                                <select 
                                    v-model="broadcastForm.color"
                                    class="w-full text-xs rounded-2xl border-slate-200 dark:border-slate-800 dark:bg-slate-950 dark:text-white p-3 focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option v-for="col in colorsList" :key="col.value" :value="col.value">{{ col.name }}</option>
                                </select>
                            </div>
                        </div>

                        <button 
                            type="submit" 
                            :disabled="broadcastForm.processing"
                            class="w-full py-3.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg hover:shadow-indigo-500/20 transition-all duration-150 disabled:opacity-50"
                        >
                            Emitir Comunicado
                        </button>
                    </form>
                </div>

                <!-- Sent Notifications Log List -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-100 dark:border-slate-800 shadow-2xl p-6 lg:col-span-2">
                    <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-widest border-b border-slate-100 dark:border-slate-800 pb-3 mb-6">
                        Historial de Notificaciones
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-800">
                                    <th class="py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider">Detalles Alerta</th>
                                    <th class="py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider">Destinatario</th>
                                    <th class="py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider">Leído</th>
                                    <th class="py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider text-right">Enviado</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                                <tr v-if="sentNotifications.length === 0">
                                    <td colspan="4" class="py-12 text-center text-xs text-slate-400 italic">No se han emitido notificaciones en el sistema</td>
                                </tr>
                                <tr v-for="notif in sentNotifications" :key="notif.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50">
                                    <td class="py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full" :class="
                                                notif.color === 'rose' ? 'bg-rose-100 text-rose-600 dark:bg-rose-950/30 dark:text-rose-400' :
                                                notif.color === 'emerald' ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-950/30 dark:text-emerald-400' :
                                                notif.color === 'amber' ? 'bg-amber-100 text-amber-600 dark:bg-amber-950/30 dark:text-amber-400' :
                                                'bg-indigo-100 text-indigo-600 dark:bg-indigo-950/30 dark:text-indigo-400'
                                            ">
                                                <i :class="['fa-solid', notif.icon || 'fa-bell', 'text-xs']"></i>
                                            </div>
                                            <div>
                                                <div class="text-xs font-black text-slate-900 dark:text-white uppercase">{{ notif.title }}</div>
                                                <div class="text-[10px] text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-1 max-w-[280px]">{{ notif.message }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 text-xs font-bold text-slate-700 dark:text-slate-300">
                                        {{ notif.user_name }}
                                        <div class="text-[9px] text-slate-400 dark:text-slate-500 font-normal mt-0.5">{{ notif.user_email }}</div>
                                    </td>
                                    <td class="py-4">
                                        <span 
                                            class="py-1 px-3 rounded-full text-[9px] font-black uppercase tracking-wider"
                                            :class="notif.read_at ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/30 dark:text-emerald-400' : 'bg-amber-50 text-amber-700 dark:bg-amber-950/30 dark:text-amber-400'"
                                        >
                                            {{ notif.read_at ? 'Leído' : 'Pendiente' }}
                                        </span>
                                    </td>
                                    <td class="py-4 text-right text-xs text-slate-500 dark:text-slate-400">
                                        {{ new Date(notif.created_at).toLocaleString() }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
