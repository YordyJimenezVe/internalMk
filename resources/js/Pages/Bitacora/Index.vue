<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps } from 'vue';
import { ref, computed  } from 'vue'; // Import watch function
/* import the fontawesome core */
import { library } from '@fortawesome/fontawesome-svg-core'
 
/* import the fontawesome icon component */
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { fas } from '@fortawesome/free-solid-svg-icons';

library.add(fas)

const props = defineProps({
  bitacoras: Array,
});

let termino = 'aloja'; 

const searchQuery = ref(''); //Should really load it from the query string

const filteredBitacora = computed(() => {
  const searchTerms = searchQuery.value.toLowerCase();
  termino=searchQuery.value.toLowerCase();

  return props.bitacoras.filter((bitacora) => {
    // Iterar a través de todas las propiedades del objeto
    for (const key in bitacora) {
      // Omita las propiedades que no sean cadenas y el campo "id" (opcional)
      if (typeof bitacora[key] !== 'string' || key === 'id') {
        continue;
      }

      if (bitacora[key].toLowerCase().includes(searchTerms)) {
        return true; // Found a match in any string property
      }
    }

    return false; // No match found in any string property
  });
});
</script>


<template>
    <AppLayout title="Bitacora">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bitacora
            </h2>
        </template>

        <div class="py-12">
           
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white">
                    <div class="flex justify-end mb-6">
                        <input type="search" v-model="searchQuery" class="mt-6" placeholder="Buscar Término">
                    </div>
                    <table width="100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Usuario</th>
                                <th>Acción</th>
                                <th>Descripción</th>
                                <th>fecha</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center;">
                          <tr v-for="bitacora in filteredBitacora" :key="bitacora.id"> 
                                <td> {{ bitacora.id }}</td>
                                <td> {{ bitacora.users.name }}</td>
                                <td> {{ bitacora.action }}</td>
                                <td> {{ bitacora.description }}</td>
                                <td> {{ bitacora.created_at }}</td>
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
    methods: {
        exportExcel(termino) {
          window.location.href= '/report/reporteExcel/bitacora/'+termino;
        },
        exportPdf(termino) {
          window.location.href= '/report/reportePdf/bitacora/'+termino;
        },
    },
};
  </script>