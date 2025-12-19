<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3'
</script>


<template>
    <AppLayout title="Reportes">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Reportes
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Quick Links -->
                        <div class="border p-4 rounded-lg bg-gray-50">
                            <h3 class="font-bold text-lg mb-4 text-gray-800">Accesos Rápidos</h3>
                            <div class="flex flex-col space-y-2">
                                <button @click="bitacora()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-center">
                                    <i class="fa-solid fa-list mr-2"></i>Bitácora de Eventos
                                </button>
                                <button @click="history()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded text-center">
                                    <i class="fa-solid fa-history mr-2"></i>Histórico
                                </button>
                            </div>
                        </div>

                        <!-- Report Generator -->
                        <div class="border p-4 rounded-lg bg-white shadow-sm border-indigo-100">
                             <h3 class="font-bold text-lg mb-4 text-indigo-700">Generador de Reportes</h3>
                             
                             <div class="mb-4">
                                <label class="block text-gray-700 font-bold mb-2">Tipo de Reporte</label>
                                <select v-model="form.tipo" class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="partidas">Inventario (Partidas)</option>
                                    <option value="facturas">Ventas (Facturación)</option>
                                </select>
                             </div>

                             <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-gray-700 font-bold mb-2">Desde</label>
                                    <input v-model="form.start_date" type="date" class="w-full border-gray-300 rounded shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-gray-700 font-bold mb-2">Hasta</label>
                                    <input v-model="form.end_date" type="date" class="w-full border-gray-300 rounded shadow-sm">
                                </div>
                             </div>

                             <div v-if="form.tipo === 'partidas'" class="mb-4">
                                <label class="block text-gray-700 font-bold mb-2">Estatus</label>
                                <select v-model="form.status" class="w-full border-gray-300 rounded shadow-sm">
                                    <option value="DISPONIBLE">Solo Disponibles</option>
                                    <option value="VENDIDO">Solo Vendidos</option>
                                    <option value="ALL">Todo (General)</option>
                                </select>
                             </div>

                             <div class="mb-4">
                                <label class="block text-gray-700 font-bold mb-2">Formato</label>
                                <div class="flex space-x-4">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.format" value="excel" class="form-radio text-indigo-600">
                                        <span class="ml-2">Excel (.xlsx)</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.format" value="pdf" class="form-radio text-red-600">
                                        <span class="ml-2">PDF</span>
                                    </label>
                                </div>
                             </div>

                             <button @click="generateReport" class="w-full bg-indigo-600 hover:bg-indigo-800 text-white font-bold py-3 px-4 rounded transition duration-150">
                                <i class="fa-solid fa-download mr-2"></i>Descargar Reporte
                             </button>
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
        history() {
             router.visit(route('history.index'));
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