<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps } from 'vue';
import { ref, computed  } from 'vue'; // Import watch function
/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'
 
/* import the fontawesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons';
import { router } from '@inertiajs/vue3';

library.add(fas)

const props = defineProps({
  maintenances: Array,
});

const deleteMaintenance = id => {
  if (!confirm('¿Estás seguro de que quieres Borrar este Mantenimiento?')) {
    return;
  }
  router.delete(`/maintenance/delete/${id}`);
};

const searchQuery = ref(''); //Should really load it from the query string

const filteredMaintenances = computed(() => {
  const searchTerms = searchQuery.value.toLowerCase();

  return props.maintenances.filter((maintenance) => {
    // Iterar a través de todas las propiedades del objeto
    for (const key in maintenance) {
      // Omita las propiedades que no sean cadenas y el campo "id" (opcional)
      if (typeof maintenance[key] !== 'string' || key === 'id') {
        continue;
      }

      if (maintenance[key].toLowerCase().includes(searchTerms)) {
        return true; // Found a match in any string property
      }
    }

    return false; // No match found in any string property
  });
});
</script>


<template>
    <AppLayout title="Mantenimiento">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Mantenimiento
            </h2>
        </template>

        <div class="py-12">
            <div class="flex justify-center">
                <button @click="addMaintenance()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-2 text-cente mb-4">Añadir Mantenimiento <FontAwesomeIcon icon="fa-solid fa-plus" /></button>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white ">
                    <div class="flex justify-end mb-6">
                        <input type="search" v-model="searchQuery" class="mt-6" placeholder="Buscar Término">
                    </div>
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Partida</th>
                                <th>Tipo</th>
                                <th>Categoría</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Mecánico</th>
                                <th>Costo Acumulado</th>
                                <th>Estado</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center;">
                            <tr v-for="maintenance in filteredMaintenances" :key="maintenance.id"> 
                                <td> {{ maintenance.id }}</td>
                                <td> {{ maintenance.partida_id }}</td>
                                <td> {{ maintenance.tipo }}</td>
                                <td> {{ maintenance.partida.tipo }}</td>
                                <td> {{ maintenance.partida.marca }}</td>
                                <td> {{ maintenance.partida.modelo }}</td>
                                <td> {{ maintenance.nombre_mecanico }}  {{ maintenance.apellido_mecanico }}</td>
                                <td> {{ maintenance.costo }}</td>
                                <td> {{ maintenance.estado }}</td>
                                <td>
                                    <button v-if="maintenance" @click="visualizeMaintenance(maintenance.id)" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon icon="fa-solid fa-eye" /></button>
                                    <button v-if="maintenance" @click="editMaintenance(maintenance.id)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon icon="fa-solid fa-edit" /></button>
                                    <button v-if="maintenance" @click="deleteMaintenance( maintenance.id)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon icon="fa-solid fa-trash" /></button>
                                </td>
                            </tr> 
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    methods: {
        editMaintenance(id) {
        // Realizar la solicitud a la ruta 'editCenterDist' usando Inertia.visit
        router.visit(route('editMaintenance', { id }));
        },
        addMaintenance() {
        // Realizar la solicitud a la ruta 'addCenterDist' usando Inertia.visit
        router.visit(route('createMaintenance'));
        },
        viewAsignCenterDist(id) {
        // Realizar la solicitud a la ruta 'addCenterDist' usando Inertia.visit
        router.visit(route('viewAsignCenterDist', { id }));
        },
    },
};
  </script>