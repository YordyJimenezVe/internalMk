<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { router } from '@inertiajs/vue3'
import { ref, computed  } from 'vue'; // Import watch function


const props = defineProps({
  partidas: Array,
});

let termino = 'aloja'; 
const searchQuery = ref(''); //Should really load it from the query string
const filter = computed(() => {
  const searchTerms = searchQuery.value.toLowerCase();
  termino=searchQuery.value.toLowerCase();

  return props.partidas.filter((partida) => {
    // Iterar a través de todas las propiedades del objeto
    for (const key in partida) {
      // Omita las propiedades que no sean cadenas y el campo "id" (opcional)
      if (typeof partida[key] !== 'string' || key === 'id') {
        continue;
      }

      if (partida[key].toLowerCase().includes(searchTerms)) {
        return true; // Found a match in any string property
      }
    }

    return false; // No match found in any string property
  });
});
</script>


<template>
    <AppLayout title="Partida">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Histórico
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white ">
                    <div class="flex justify-end mb-6">
                        <input type="search" v-model="searchQuery" class="mt-6" placeholder="Buscar Término">
                    </div>
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>Indice</th>
                                <th>Contenedor</th>
                                <th>Inventario</th>
                                <th>Expediente</th>
                                <th>Tipo</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Año</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center;">
                            <tr v-for="partida in filter" :key="partida.id"> 
                                <td> {{ partidas.indexOf(partida) + 1 }}</td>
                                <td> {{ partida.container.id }}-{{ partida.container.fecha }}</td>
                                <td> {{ partida.codInv }}</td>
                                <td> {{ partida.expediente }}</td>
                                <td> {{ partida.tipo }}</td>
                                <td> {{ partida.marca }}</td>
                                <td> {{ partida.modelo }}</td>
                                <td> {{ partida.año }}</td>
                                <td>
                                    <button v-if="partida" @click="visualizarPartida(partida.id)" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mx-2">Visualizar</button>
                                </td>
                            </tr> 
                        </tbody>
                    </table>
                    <div class="mb-6">
                        <a @click="exportExcel(termino)" class="px-2 py-1 bg-indigo-600 text-white rounded-md mx-2">ExportExcel</a>
                      
                        <a @click="exportPdf(termino)" class="px-2 py-1 bg-red-600 text-white rounded-md mx-2">ExportPdf</a>
                  </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
export default {
    data: function () {
      return {
        termino:this.termino
      }
      // Luego usas this.algo para obtener su valor. o {{ algo }} en el lado html
     },
    methods: {
      visualizarPartida(id) {
        // Realizar la solicitud a la ruta 'addCenterDist' usando Inertia.visit
      router.visit(route('showPartida', { id }));
      },
      exportExcel(termino) {
          window.location.href= '/report/reporteExcel/history/'+termino;
      },
      exportPdf(termino) {
          window.location.href= '/report/reportePdf/history/'+termino;
      },
    }
  };
  </script>