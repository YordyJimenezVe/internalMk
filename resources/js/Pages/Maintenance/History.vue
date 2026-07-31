<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { router, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

library.add(fas);

const props = defineProps({
  maintenances: Object,
  filters: Object,
});

const page = usePage();
const user = computed(() => page.props.auth.user);

const isSuperUser = computed(() => {
    // Handling different case variants and both 'rol' column and spatie roles if available
    const roles = (user.value?.roles || []).map(r => (typeof r === 'string' ? r : r.name || '').toLowerCase());
    const directRol = (user.value?.rol || '').toLowerCase();
    const superRoles = ['superusuario', 'administrador'];
    
    return superRoles.includes(directRol) || roles.some(name => superRoles.includes(name));
});

const columns = [
    { key: 'id', label: 'ID', sortable: true },
    { key: 'partida_id', label: 'Partida', sortable: true },
    { key: 'tipo', label: 'Tipo Mantenimiento', sortable: true },
    { key: 'partida.tipo', label: 'Categoría', sortable: true },
    { key: 'partida.marca', label: 'Marca', sortable: true },
    { key: 'partida.modelo', label: 'Modelo', sortable: true },
    { key: 'mecanico', label: 'Mecánico', sortable: true },
    { key: 'costo', label: 'Costo Acumulado', sortable: true },
    { key: 'status', label: 'Estado', sortable: true },
];

const deleteMaintenance = id => {
  if (!confirm('¿Estás seguro de que quieres Borrar este Mantenimiento terminado? Esta acción solo está permitida para Superusuarios.')) {
    return;
  }
  router.delete(route('deleteMaintenance', id));
};

const editMaintenance = id => {
    router.visit(route('editMaintenance', { id }));
};

const showMaintenance = id => {
    router.visit(route('maintenance.show', { id }));
};

</script>

<template>
    <AppLayout title="Historial de Mantenimiento">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                Historial de Mantenimientos Terminados
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="mb-4 flex justify-between items-center">
                    <Link :href="route('maintenance')" class="text-indigo-600 hover:text-indigo-800 font-bold flex items-center">
                        <FontAwesomeIcon icon="fa-solid fa-arrow-left" class="mr-2" />
                        Volver a Mantenimientos Activos
                    </Link>
                    <span class="text-sm text-gray-500 italic" v-if="!isSuperUser">
                        * Los mantenimientos terminados solo pueden ser editados por Superusuarios.
                    </span>
                </div>

                <DataTable 
                    :rows="maintenances" 
                    :columns="columns" 
                    :filters="filters"
                    routeName="maintenance.history"
                    title="Historial de Mantenimientos"
                    exportType="maintenance"
                >
                    <!-- Custom Cells -->
                    <template #cell-mecanico="{ row }">
                        {{ row.nombre_mecanico }} {{ row.apellido_mecanico }}
                    </template>
                    <template #cell-costo="{ row }">
                        {{ row.costo || '0.00' }}
                    </template>
                    <template #cell-status="{ row }">
                        <span class="px-2 py-1 rounded text-xs font-bold uppercase bg-green-100 text-green-800">
                            {{ row.status }}
                        </span>
                    </template>
                     <template #cell-partida.tipo="{ row }">
                        {{ row.partida ? row.partida.tipo : 'N/A' }}
                    </template>
                    <template #cell-partida.marca="{ row }">
                        {{ row.partida ? row.partida.marca : 'N/A' }}
                    </template>
                    <template #cell-partida.modelo="{ row }">
                        {{ row.partida ? row.partida.modelo : 'N/A' }}
                    </template>

                    <!-- Actions -->
                    <template #actions="{ row }">
                        <div class="flex justify-end gap-2">
                            <button @click="showMaintenance(row.id)" class="text-gray-600 hover:text-gray-900 transition-colors" title="Ver Detalles">
                                <FontAwesomeIcon icon="fa-solid fa-eye" />
                            </button>
                            
                            <template v-if="isSuperUser">
                                <button @click="editMaintenance(row.id)" class="text-blue-600 hover:text-blue-900 transition-colors" title="Editar">
                                    <FontAwesomeIcon icon="fa-solid fa-edit" />
                                </button>
                                <button @click="deleteMaintenance(row.id)" class="text-red-600 hover:text-red-900 transition-colors" title="Eliminar">
                                    <FontAwesomeIcon icon="fa-solid fa-trash" />
                                </button>
                            </template>
                        </div>
                    </template>
                </DataTable>
            </div>
        </div>
    </AppLayout>
</template>
