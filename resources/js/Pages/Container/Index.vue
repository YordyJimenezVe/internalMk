<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { router, Link } from '@inertiajs/vue3';

defineProps({
  containers: Object, // Changed to Object to support Pagination
  filters: Object,
});

const columns = [
    { key: 'expediente', label: 'Expediente', sortable: true },
    { key: 'fecha', label: 'Fecha', sortable: true },
    { key: 'hora', label: 'Hora', sortable: true },
    { key: 'motores', label: 'Motores', sortable: true },
    { key: 'cajas', label: 'Cajas', sortable: true },
    { key: 'camaras', label: 'Cámaras', sortable: true },
    { key: 'accesorios', label: 'Accesorios', sortable: true },
    { key: 'total', label: 'Total', sortable: true }, // Computed column
];

// Formatting helper
const formatoMiles = (number) => {
  if (!number) return '0';
  return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

const eliminarContainer = id => {
  if (!confirm('¿Estás seguro de que quieres eliminar este Contenedor?')) {
    return;
  }
  router.delete(route('deleteContainer', id));
};
</script>

<template>
    <AppLayout title="Contenedor">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Contenedor
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-4 flex justify-end">
                     <Link :href="route('createcontainer')" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg shadow-md transition-colors flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Registrar Nuevo Contenedor
                    </Link>
                </div>

                <DataTable 
                    :rows="containers" 
                    :columns="columns" 
                    :filters="filters"
                    routeName="container"
                    title="Listado de Contenedores"
                    exportType="container"
                >
                    <!-- Custom Cell Formatting -->
                    <template #cell-motores="{ row }">
                        {{ formatoMiles(row.motores) }}
                    </template>
                    <template #cell-cajas="{ row }">
                        {{ formatoMiles(row.cajas) }}
                    </template>
                    <template #cell-camaras="{ row }">
                        {{ formatoMiles(row.camaras) }}
                    </template>
                    <template #cell-accesorios="{ row }">
                        {{ formatoMiles(row.accesorios) }}
                    </template>
                    <template #cell-total="{ row }">
                        <span class="font-bold text-gray-800 dark:text-gray-200">
                             {{ formatoMiles((row.motores || 0) + (row.cajas || 0) + (row.camaras || 0) + (row.accesorios || 0)) }}
                        </span>
                    </template>

                    <!-- Actions -->
                    <template #actions="{ row }">
                        <div class="flex justify-end gap-3 items-center">
                            <Link :href="route('showContainer', row.id)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors tooltip" title="Ver Detalle">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </Link>
                            <Link :href="route('editContainer', row.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors tooltip" title="Editar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </Link>
                            <button @click="eliminarContainer(row.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors tooltip" title="Eliminar">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>