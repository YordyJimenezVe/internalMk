<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3'


const props = defineProps({
  containers: Array,
});

const formatoMiles = (number) => {
  const exp = /(\d)(?=(\d{3})+(?!\d))/g;
  const rep = '$1.';
  let arr = number.toString().split('.');
  arr[0] = arr[0].replace(exp,rep);
  return arr[1] ? arr.join('.'): arr[0];
}

const eliminarContainer = id => {
  if (!confirm('¿Estás seguro de que quieres eliminar este Contenedor?')) {
    return;
  }
  router.delete(`/container/delete/${id}`);
};
</script>


<template>
    <AppLayout title="Contenedor">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Contenedor
            </h2>
        </template>

        <div class="py-12">
            <div class="flex justify-center">
                <button @click="registrarContenedor()" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-2 text-cente mb-4">Registrar Nuevo Contenedor <i class="fa-solid fa-plus mr-2"></i></button>
            </div>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white ">
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>Indice</th>
                                <th>Expediente</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Total</th>
                                <th>Total Motores Mixtos</th>
                                <th>Total Cajas Mixtas</th>
                                <th>Total Cámaras Mixtas</th>
                                <th>Total Accesorios</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center;">
                            <tr v-for="container in containers" :key="container.id"> 
                                
                                <td> {{ containers.indexOf(container) + 1 }}</td>
                                <td> {{ container.expediente }}</td>
                                <td> {{ container.fecha }}</td>
                                <td> {{ container.hora }}</td>
                                <td> {{ formatoMiles(container.motores+container.cajas+container.camaras+container.accesorios) }}</td>
                                <td> {{ formatoMiles(container.motores) }}</td>
                                <td> {{ formatoMiles(container.cajas) }}</td>
                                <td> {{ formatoMiles(container.camaras) }}</td>
                                <td> {{ formatoMiles(container.accesorios) }}</td>
                                <td>
                                    <button v-if="container" @click="editarContainer(container.id)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-2">Editar</button>
                                    <button v-if="container" @click="eliminarContainer( container.id)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mx-2">Eliminar</button>
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
    editarContainer(id) {
      // Realizar la solicitud a la ruta 'editCenterDist' usando Inertia.visit
      router.visit(route('editContainer', { id }));
    },
    registrarContenedor() {
      // Realizar la solicitud a la ruta 'addCenterDist' usando Inertia.visit
      router.visit(route('createcontainer'));
    },
  }
  };
  </script>