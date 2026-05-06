<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3'
</script>


<template>
    <AppLayout title="Reportes">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center">
                <i class="fa-solid fa-chart-line mr-2 text-indigo-500"></i>Centro de Reportes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <!-- Quick Links -->
                        <div class="p-6 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-lg mb-6 text-gray-800 dark:text-white flex items-center">
                                <i class="fa-solid fa-bolt mr-2 text-amber-500"></i>Accesos Rápidos
                            </h3>
                            <div class="flex flex-col space-y-4">
                                <button @click="bitacora()" class="group bg-white dark:bg-gray-800 hover:bg-emerald-600 dark:hover:bg-emerald-600 text-gray-700 dark:text-gray-200 hover:text-white dark:hover:text-white font-bold py-4 px-6 rounded-xl border border-gray-100 dark:border-gray-700 transition-all flex items-center transform hover:scale-[1.02]">
                                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/30 rounded-lg mr-4 group-hover:bg-emerald-500 transition-colors">
                                        <i class="fa-solid fa-list-check text-emerald-600 dark:text-emerald-400 group-hover:text-white"></i>
                                    </div>
                                    <div class="text-left">
                                        <div class="text-sm uppercase tracking-wider opacity-60">Movimientos</div>
                                        Bitácora de Eventos
                                    </div>
                                </button>

                            </div>
                        </div>

                        <!-- Report Generator -->
                        <div class="p-6 rounded-2xl bg-white dark:bg-gray-800 shadow-sm border border-indigo-50 dark:border-indigo-900/30">
                             <h3 class="font-bold text-lg mb-6 text-indigo-700 dark:text-indigo-400 flex items-center">
                                 <i class="fa-solid fa-file-export mr-2"></i>Generador de Reportes
                             </h3>
                             
                             <div class="space-y-6">
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-folder-open mr-1"></i>Tipo de Reporte
                                    </label>
                                    <select v-model="form.tipo" class="w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 outline-none transition-all">
                                        <option value="partidas">Inventario (Partidas)</option>
                                        <option value="facturas">Ventas (Facturación)</option>
                                        <option value="maintenance">Mantenimientos</option>
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                            <i class="fa-solid fa-calendar mr-1"></i>Desde
                                        </label>
                                        <input v-model="form.start_date" type="date" class="w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                            <i class="fa-solid fa-calendar mr-1"></i>Hasta
                                        </label>
                                        <input v-model="form.end_date" type="date" class="w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 outline-none">
                                    </div>
                                </div>

                                <div v-if="form.tipo === 'partidas'">
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-filter mr-1"></i>Estatus
                                    </label>
                                    <select v-model="form.status" class="w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 outline-none">
                                        <option value="DISPONIBLE">Solo Disponibles</option>
                                        <option value="VENDIDO">Solo Vendidos</option>
                                        <option value="ALL">Todo (General)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-file-signature mr-1"></i>Formato de Selección
                                    </label>
                                    <div class="flex space-x-4 mt-2">
                                        <button @click.prevent="form.format = 'excel'" :class="form.format === 'excel' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'" class="flex-1 py-3 px-4 rounded-xl font-bold transition-all flex items-center justify-center">
                                            <i class="fa-solid fa-file-excel mr-2"></i>Excel
                                        </button>
                                        <button @click.prevent="form.format = 'pdf'" :class="form.format === 'pdf' ? 'bg-rose-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300'" class="flex-1 py-3 px-4 rounded-xl font-bold transition-all flex items-center justify-center">
                                            <i class="fa-solid fa-file-pdf mr-2"></i>PDF
                                        </button>
                                    </div>
                                </div>

                                <button @click="generateReport" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-4 rounded-2xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all transform hover:scale-[1.02] active:scale-95 flex items-center justify-center">
                                    <i class="fa-solid fa-cloud-arrow-down mr-2 text-lg"></i>Generar Docuemnto
                                </button>
                             </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    data() {
        return {
            form: {
                tipo: 'partidas', // partidas (inventario), facturas (ventas)
                start_date: '',
                end_date: '',
                status: 'DISPONIBLE', // For Inventory: DISPONIBLE, VENDIDO, ALL
                format: 'excel',
            }
        }
    },
    methods: {
        bitacora() {
             router.visit(route('bitacora.index'));
        },
        generateReport() {
            // Construct the URL with query parameters
            const params = new URLSearchParams({
                fecha_inicio: this.form.start_date,
                fecha_fin: this.form.end_date,
                status: this.form.status,
            }).toString();

            // Determine route based on format and type
            // Current routes: /report/reporteExcel/{tipo}/{caso}/{termino?}
            // We can treat 'caso' as the category or filter, and use query params for dates
            // Defaulting 'caso' to 'general' or empty if not used for specific filtering
            
            const routeName = this.form.format === 'excel' ? 'reporteExcel' : 'reportePdf';
            const url = route(routeName, {
                tipo: this.form.tipo,
                caso: 'general', // Placeholder
            }) + '?' + params;

            window.open(url, '_blank');
        }
  }
  };
  </script>