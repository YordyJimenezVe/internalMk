<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    bitacoras: Object, // Paginator
    filters: Object,
});

const columns = [
    { key: 'id', label: 'ID', sortable: false, class: 'w-20' },
    { key: 'user_name', label: 'Usuario', sortable: false, class: 'w-48' },
    { key: 'action', label: 'Acción', sortable: false, class: 'min-w-[150px] max-w-[250px] !whitespace-normal' },
    { key: 'description', label: 'Descripción', sortable: false, class: 'min-w-[250px] max-w-[400px] !whitespace-normal' },
    { key: 'created_at', label: 'Fecha', sortable: false, class: 'w-48' },
];
</script>

<template>
    <AppLayout title="Bitácora">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Bitácora de Actividades
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <DataTable 
                    :rows="bitacoras" 
                    :columns="columns" 
                    :filters="filters" 
                    routeName="bitacora.index"
                    title="Registros de Actividad"
                    exportType="bitacora"
                >
                    <!-- Custom cell for Usuario -->
                    <template #cell-user_name="{ row }">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8">
                                <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 font-bold text-xs uppercase">
                                    {{ row.users?.name?.substring(0, 2) || '??' }}
                                </span>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                    {{ row.users?.name || 'Sistema' }}
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Custom cell for Acción -->
                    <template #cell-action="{ row }">
                        <span 
                            class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full whitespace-normal text-center"
                            :class="{
                                'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400': row.action.toUpperCase().includes('CREA'),
                                'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400': row.action.toUpperCase().includes('ACTUALIZA'),
                                'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400': row.action.toUpperCase().includes('ELIMINA'),
                                'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400': row.action.toUpperCase().includes('RESTAURA'),
                                'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-400': !['CREA', 'ACTUALIZA', 'ELIMINA', 'RESTAURA'].some(a => row.action.toUpperCase().includes(a))
                            }"
                        >
                            {{ row.action }}
                        </span>
                    </template>

                    <!-- Custom cell for Descripción -->
                    <template #cell-description="{ row }">
                        <div class="text-sm text-gray-900 dark:text-gray-100 whitespace-normal break-words py-1">
                            {{ row.description }}
                        </div>
                    </template>

                    <!-- Custom cell for Fecha -->
                    <template #cell-created_at="{ row }">
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            {{ new Date(row.created_at).toLocaleString('es-ES', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>