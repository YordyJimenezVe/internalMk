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
  Facturas: Array,
});

let termino = 'aloja'; 
const deleteFactura = id => {
  if (!confirm('¿Estás seguro de que quieres Borrar esta Facturación?')) {
    return;
  }
  router.delete(`/billing/delete/${id}`);
};

const searchQuery = ref(''); //Should really load it from the query string

const filteredFacturas = computed(() => {
  const searchTerms = searchQuery.value.toLowerCase();
  termino=searchQuery.value.toLowerCase();

  return props.Facturas.filter((Factura) => {
    // Iterar a través de todas las propiedades del objeto
    for (const key in Factura) {
      // Omita las propiedades que no sean cadenas y el campo "id" (opcional)
      if (typeof Factura[key] !== 'string' || key === 'id') {
        continue;
      }

      if (Factura[key].toLowerCase().includes(searchTerms)) {
        return true; // Found a match in any string property
      }
    }

    return false; // No match found in any string property
  });
});
</script>


<template>
    <AppLayout title="Facturación">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Facturación
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
                                <th>Partida</th>
                                <th>Tipo</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Total Bs</th>
                                <th>Fecha Factura</th>
                                <th>Número de Factura</th>
                                <th>Opciones</th>
                            </tr>
                        </thead>
                        <tbody style="text-align: center;">
                          <tr v-for="factura in filteredFacturas" :key="factura.id"> 
                                <td> {{ factura.partida_id }}</td>
                                <td> {{ factura.partidas.tipo }}</td>
                                <td> {{ factura.partidas.marca }}</td>
                                <td> {{ factura.partidas.modelo }}</td>
                                <td> {{ factura.precio_total }}</td>
                                <td> {{ factura.fecha }}</td>
                                <td> {{ factura.numero_factura }}</td>
                                <td>
                                    <button v-if="factura" @click="visualizeFact(factura.id)" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon icon="fa-solid fa-eye" /></button>
                                    <button v-if="factura" @click="editBilling(factura.id)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon icon="fa-solid fa-edit" /></button>
                                    <button v-if="factura" @click="devolucionFactura(factura.id)" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon icon="fa-solid fa-repeat" /></button>
                                    <button v-if="factura" @click="deleteFactura( factura.id)" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon icon="fa-solid fa-trash" /></button>
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
    methods: {
        editBilling(id) {
        // Realizar la solicitud a la ruta 'editCenterDist' usando Inertia.visit
          router.visit(route('editBilling', { id }));
        },
        devolucionFactura(id) {
        // Realizar la solicitud a la ruta 'returnBilling' usando Inertia.visit para hacer una  devolución
          router.visit(route('returnBilling', { id }));
        },
        visualizeFact(id) {
          router.visit(route('viewFact', { id }));
        },
        exportExcel(termino) {
          window.location.href= '/report/reporteExcel/facturas/'+termino;
      },
      exportPdf(termino) {
          window.location.href= '/report/reportePdf/facturas/'+termino;
        },
    },
};
  </script>