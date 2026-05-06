<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { onMounted } from 'vue';

const props = defineProps({
  partidas: Object,
  datas: Array,
  employee: Object,
});

</script>

<template >
    <AppLayout title="Mantenimiento">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center justify-center">
                <i class="fa-solid fa-screwdriver-wrench mr-2 text-indigo-500"></i>
                Registrar Mantenimiento 
                <span v-if="partidas" class="text-indigo-600 dark:text-indigo-400 ml-2"> - {{ partidas.marca }} {{ partidas.modelo }}</span>
            </h2>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-3xl border border-gray-100 dark:border-gray-700 p-8">
                    <form ref="form" @submit.prevent="submitForm" class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <!-- Selection -->
                            <div class="md:col-span-2">
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2 flex items-center" for="partida_id">
                                    <i class="fa-solid fa-box-open mr-2 text-indigo-500"></i>Ítem de Inventario
                                </label>
                                <select 
                                    class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-3 px-4 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all cursor-pointer font-semibold" 
                                    id="partida_id" 
                                    v-model="formData.partida"  
                                    @change="getPartida(formData)" 
                                    name="partida_id"  
                                    required
                                >
                                    <option v-for="data in datas" :key="data.id" :value="data.id" class="text-gray-900 dark:text-white bg-white dark:bg-gray-700">
                                        {{data.codInv}} - {{data.marca}} {{data.modelo}} ({{data.tipo}})
                                    </option>
                                </select>
                            </div>

                            <!-- Read-only Details -->
                            <template v-if="partidas">
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2 flex items-center">
                                        <i class="fa-solid fa-copyright mr-2"></i>Marca
                                    </label>
                                    <div class="w-full bg-gray-100 dark:bg-gray-900/50 border border-transparent rounded-xl py-3 px-4 text-gray-700 dark:text-gray-300 font-medium">
                                        {{ props.partidas.marca }}
                                    </div>
                                    <input type="hidden" name="marca" :value="props.partidas.marca">
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2 flex items-center">
                                        <i class="fa-solid fa-car mr-2"></i>Modelo
                                    </label>
                                    <div class="w-full bg-gray-100 dark:bg-gray-900/50 border border-transparent rounded-xl py-3 px-4 text-gray-700 dark:text-gray-300 font-medium">
                                        {{ props.partidas.modelo }}
                                    </div>
                                    <input type="hidden" name="modelo" :value="props.partidas.modelo">
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2 flex items-center">
                                        <i class="fa-solid fa-calendar mr-2"></i>Año
                                    </label>
                                    <div class="w-full bg-gray-100 dark:bg-gray-900/50 border border-transparent rounded-xl py-3 px-4 text-gray-700 dark:text-gray-300 font-medium">
                                        {{ props.partidas.año }}
                                    </div>
                                    <input type="hidden" name="año" :value="props.partidas.año">
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2 flex items-center">
                                        <i class="fa-solid fa-file-lines mr-2"></i>Expediente
                                    </label>
                                    <div class="w-full bg-gray-100 dark:bg-gray-900/50 border border-transparent rounded-xl py-3 px-4 text-gray-700 dark:text-gray-300 font-medium">
                                        {{ props.partidas.expediente }}
                                    </div>
                                    <input type="hidden" name="expediente" :value="props.partidas.expediente">
                                </div>
                                <input type="hidden" name="codInv" :value="props.partidas.codInv">
                            </template>

                            <!-- Maintenance Info -->
                            <div class="md:col-span-2">
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2 flex items-center" for="descripcion">
                                    <i class="fa-solid fa-align-left mr-2 text-indigo-500"></i>Descripción del Mantenimiento
                                </label>
                                <textarea 
                                    class="block w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-4 px-4 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all placeholder-gray-400" 
                                    name="descripcion" 
                                    placeholder="Detalle el trabajo a realizar..." 
                                    rows="4"
                                    required
                                ></textarea>
                            </div>

                            <div>
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2 flex items-center" for="tipo">
                                    <i class="fa-solid fa-gears mr-2 text-indigo-500"></i>Tipo de Mantenimiento
                                </label>
                                <select name="tipo" class="block w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-3 px-4 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" required>
                                    <option value="PREVENTIVO">PREVENTIVO</option>
                                    <option value="CORRECTIVO">CORRECTIVO</option>
                                    <option value="INSPECCIÓN">INSPECCIÓN</option>
                                    <option value="OTRO">OTRO</option>
                                </select>
                            </div>

                            <div>
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2 flex items-center" for="status">
                                    <i class="fa-solid fa-spinner mr-2 text-indigo-500"></i>Estado Inicial
                                </label>
                                <select class="block w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-3 px-4 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" name="status" required>
                                    <option value="EN ESPERA">EN ESPERA</option>
                                    <option value="EN PROCESO">EN PROCESO</option>
                                    <option value="TERMINADO">TERMINADO</option>
                                </select>
                            </div>

                            <div>
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2 flex items-center" for="fecha">
                                    <i class="fa-solid fa-calendar-day mr-2 text-indigo-500"></i>Fecha de Inicio
                                </label>
                                <input class="block w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-3 px-4 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" id="fecha" name="fecha" type="date" required>
                            </div>

                            <div>
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2 flex items-center" for="cedula_mecanico">
                                    <i class="fa-solid fa-id-card mr-2 text-indigo-500"></i>Cédula Responsable
                                </label>
                                <input v-model="formData.cedula_mecanico" @change="getEmployee(formData)" class="block w-full bg-white dark:bg-gray-700 text-gray-900 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-3 px-4 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all" name="cedula_mecanico" type="text" placeholder="Cédula del mecánico" required>
                            </div>

                            <template v-if="employee">
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2 flex items-center">
                                        <i class="fa-solid fa-user mr-2"></i>Nombre Mecánico
                                    </label>
                                    <div class="w-full bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 rounded-xl py-3 px-4 text-emerald-700 dark:text-emerald-400 font-bold">
                                        {{ props.employee.nombre }}
                                    </div>
                                    <input type="hidden" name="nombre_mecanico" :value="props.employee.nombre">
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2 flex items-center">
                                        <i class="fa-solid fa-user mr-2"></i>Apellido Mecánico
                                    </label>
                                    <div class="w-full bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 rounded-xl py-3 px-4 text-emerald-700 dark:text-emerald-400 font-bold">
                                        {{ props.employee.apellido }}
                                    </div>
                                    <input type="hidden" name="apellido_mecanico" :value="props.employee.apellido">
                                </div>
                            </template>
                        </div>

                        <div class="flex justify-center pt-8 border-t border-gray-100 dark:border-gray-700">
                            <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-12 rounded-2xl shadow-xl shadow-indigo-200 dark:shadow-none transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center gap-2 text-lg">
                                <i class="fa-solid fa-clipboard-check text-xl"></i>
                                Registrar Mantenimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    data() {
        return {
            formData: {
                partida: this.partidas ? this.partidas.id : '',
                cedula_mecanico: '',
            },
        };
    },
    mounted() {
        // Establecer fecha de hoy por defecto
        const today = new Date().toISOString().split('T')[0];
        const dateInput = document.getElementById('fecha');
        if (dateInput) dateInput.value = today;
    },
    watch: {
        partidas: {
            handler(newValue) {
                if (newValue) {
                    this.formData.partida = newValue.id;
                }
            },
            immediate: true,
            deep: true
        }
    },
    methods: {
        submitForm() {
            const data = {};
            for (const element of this.$refs.form.elements) {
                if (element.name) {
                    data[element.name] = element.value;
                }
            }
            this.$inertia.post('/maintenance/store', data);
        },

        async getPartida(e){
            try {
                this.$inertia.post('/maintenance/add', {
                    partida: this.formData.partida,
                    employee: this.formData.cedula_mecanico,
                }, { preserveScroll: true });
            } catch (error) {}
        },
        async getEmployee(e){
            try {
                this.$inertia.post('/maintenance/add', {
                    partida: this.formData.partida,
                    employee: this.formData.cedula_mecanico,
                }, { preserveScroll: true });
            } catch (error) {}
        },
    }
};
</script>