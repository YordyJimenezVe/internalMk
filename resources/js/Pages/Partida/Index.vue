<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { library } from '@fortawesome/fontawesome-svg-core';
import { fas } from '@fortawesome/free-solid-svg-icons';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import { Link, router } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

const currentUrl = window.location.pathname;
library.add(fas);
const props = defineProps({
    partidas: Object,
    filters: Object,
    links: Object,
    tipos: Object,
});

const eliminarPartida = id => {
    if (!confirm('¿Estás seguro de que quieres eliminar esta Partida?')) {
        return;
    }
    router.delete(`/partida/delete/${id}`);
};

const searchInput = ref(null);
const pageStartIndex = ref(0);
const query = ref('');
const statusFilter = ref('DISPONIBLE'); // Default filter

// --- Scanner Auto-Fix & Smart Direct Navigation ---
watch(query, (newVal) => {
    if (!newVal) return;

    // 1. Detect QR Code Scan (e.g. "httpÑ--localhostÑ8000-partida-show-653")
    // Fix: Redirect immediately to the ID found at the end
    if (newVal.startsWith('http') || newVal.startsWith('httpÑ')) {
        const matches = newVal.match(/(\d+)$/); // Extract ID at the end
        if (matches) {
            query.value = ''; // Clear search to avoid double submission
            router.visit(route('showPartida', matches[1]));
            return;
        }
    }

    // 2. Detect Barcode Scan with Typo (e.g. "TRHU'616'128" -> "TRHU-616-128")
    // Fix: Replace ' with - automatically for better search experience AND SUBMIT
    if (newVal.includes("'")) {
        const fixedVal = newVal.replace(/'/g, '-');
        query.value = fixedVal;
        
        // Auto-submit if it looks like a complete barcode (e.g. lengthy enough)
        if (fixedVal.length > 5) {
             router.visit(route('partida', { search: fixedVal }));
        }
    }
});

const updatePageStartIndex = () => {
    if (props.partidas && props.partidas.current_page && props.partidas.per_page) {
        pageStartIndex.value = (props.partidas.current_page - 1) * props.partidas.per_page;
    } else {
        pageStartIndex.value = 0;
    }
};


const calculateGlobalIndex = () => {
    if (props.partidas && props.partidas.data) {
        props.partidas.data.forEach((partida, index) => {
            if (partida.global_index === undefined) {
                partida.global_index = pageStartIndex.value + index + 1; // Solo si no tiene índice
            }
        });
    }
};

const fetchPartidas = async () => {
    const currentUrl = window.location.pathname;
    const routeName = currentUrl === '/autopart' ? 'autopart' : 'partida';
    
    // Check if we are in 'partida' to apply filter, otherwise typical behavior
    const options = { 
        search: query.value,
        page: 1,
    };
    
    if (routeName === 'partida') {
        options.status = statusFilter.value;
    }

    await router.visit(route(routeName, options), {
        onSuccess: () => {
            updatePageStartIndex();
            calculateGlobalIndex();
        },
        onError: (error) => {
            console.error("Error al cargar partidas:", error);
        },
    });
};

const registrarPartida = () => {
    if (currentUrl === '/partida') {
        router.visit(route('createPartida'));
    } else if (currentUrl === '/autopart') {
        router.visit(route('createAutopart'));
    } else {
        router.visit(route('createCamara'));
    }
};

const editarPartida = id => {
    if (currentUrl === '/autopart') {
        router.visit(route('editAutopart', { id }));
    } else if (currentUrl === '/camara') {
        router.visit(route('editCamara', { id }));
    } else {
        router.visit(route('editPartida', { id }));
    }
};

const goBill = id => {
    router.visit(route('createBilling', { id }));
};

const exportExcel = termino => {
    const routeSegments = window.location.pathname.split('/');
    const caso = routeSegments[1];
    window.location.href = '/report/reporteExcel/partidas/' + caso + termino;
};

const exportPdf = termino => {
    const routeSegments = window.location.pathname.split('/');
    const caso = routeSegments[1];
    window.location.href = '/report/reportePdf/partidas/' + caso + '/' + termino;
};

const getSpanishLabel = label => {
    const translations = {
        "&laquo; Previous": "Anterior &laquo;",
        "Next &raquo;": "Siguiente &raquo;",
    };
    return translations[label] || label;
};

const handlePageLinkClick = (url) => {
    if (url) {
        router.visit(url, {
            onSuccess: () => {
                updatePageStartIndex();
                calculateGlobalIndex();
            }
        });
    }
};

const goToPage = (page) => {
    pageStartIndex.value = (page - 1) * props.partidas.per_page;
};

onMounted(() => {
    updatePageStartIndex();
    const searchParam = new URLSearchParams(window.location.search);
    const searchTerm = searchParam.get('search');
    if (searchTerm) {
        query.value = searchTerm;
    }
    calculateGlobalIndex();

    // Auto-focus on mount
    if (searchInput.value) {
        searchInput.value.focus();
    }

    // Auto-focus on mount
    if (searchInput.value) {
        searchInput.value.focus();
    }
});
</script>

<style>
@media screen and (max-width: 768px) {
  .hide-on-mobile {
    display: none;
  }
}
</style>

<template>
  <AppLayout title="Partida">
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        Partida
      </h2>
    </template>

    <div class="py-12">
      <div class="flex justify-center">
        <button @click="registrarPartida()"
          class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-2 text-cente mb-4">Registrar Nueva
          Partida <i class="fa-solid fa-plus mr-2"></i></button>
      </div>
      <div class="max-w-8xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white ">
          <div class=" justify-end mb-6">
            <input 
                ref="searchInput"
                type="search" 
                v-model="query" 
                class="mt-6 mx-2" 
                placeholder="Escanea aquí (o escribe)"
                autofocus
                @keyup.enter="fetchPartidas" 
            />
            <select v-model="statusFilter" @change="fetchPartidas" class="mt-6 mx-2 border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
                <option value="DISPONIBLE">Disponibles</option>
                <option value="VENDIDO">Vendidos</option>
                <option value="ALL">Todos</option>
            </select>
            <button type="button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
              @click="fetchPartidas">Buscar</button>
          </div>

          <table width="100%">
            <thead>
              <tr>
                <th>Item</th>
                <th class="hide-on-mobile">Contenedor</th>
                <th class="hide-on-mobile">Inventario</th>
                <th>Expediente</th>
                <th>Tipo</th>
                <th>Marca / Modelo</th>
                <th v-if="currentUrl=='/autopart'">Categoría</th>
                <th v-if="currentUrl!=='/autopart'">Año</th>
                <th v-if="currentUrl=='/autopart'">Cantidad</th>
                <th>Opciones</th>
              </tr>
            </thead>
            <tbody style="text-align: center;">
              <tr v-for="(partida, index) in partidas.data" :key="partida.id">
                <td>{{ partida.global_index }}</td>
                <td class="hide-on-mobile"> {{ partida.container.cod }}</td>
                <td> {{ partida.codInv }}</td>
                <td class="hide-on-mobile"> {{ partida.expediente }}</td>
                <td> {{ partida.tipo }}</td>
                <td class="hide-on-mobile"> {{ partida.marca }} {{ partida.modelo }}</td>
                <td v-if="currentUrl=='/autopart'" class="hide-on-mobile"> {{ partida.categorie }}</td>
                <td v-if="currentUrl!=='/autopart'"> {{ partida.año }}</td>
                <td v-if="currentUrl=='/autopart'" class="hide-on-mobile"> {{ partida.cantidad }}</td>
                <td>
                  <button v-if="partida" @click="router.visit(route('showPartida', partida.id))"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon
                      icon="fa-solid fa-eye" /></button>
                  <button v-if="partida" @click="editarPartida(partida.id)"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon
                      icon="fa-solid fa-edit" /></button>
                  <button v-if="partida" @click="goBill(partida.id)"
                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon
                      icon="fa-solid fa-cart-shopping" /></button>
                  <button v-if="partida" @click="eliminarPartida(partida.id)"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mx-2"><FontAwesomeIcon
                      icon="fa-solid fa-trash" /></button>
                </td>
              </tr>
            </tbody>
          </table>
          <div>
          </div>
          <div class="px-1 pt-10 mb-6">
            <Link v-for=" (link, index) in partidas.links" :key="index" :href="link.url || '#'"  class="px-1 border-2" :class="{
              'is-current': link.active,
              'has-text-grey-light': !link.url,
            }" v-html="getSpanishLabel(link.label)" />

          </div>
          <span>Página {{ partidas.current_page }} de {{ partidas.last_page }}</span>
          <div class="mt-4">
            <span>Cantidad de Motores: {{ tipos[0]['motores'] || 0 }} de Cajas {{ tipos[0]['cajas_automaticas'] || 0 }} de
              autopartes {{ tipos[0]['autopartes'] || 0 }} de cámaras {{ tipos[0]['camaras'] || 0 }}</span>
          </div>
        </div>
        <div class="mt-6">
                        <a @click="exportExcel(query)" class="px-2 py-1 bg-indigo-600 text-white rounded-md mx-2">ExportExcel</a>
                      
                        <a @click="exportPdf(query)" class="px-2 py-1 bg-red-600 text-white rounded-md mx-2">ExportPdf</a>
                  </div>
            </div>
        </div>
    </AppLayout>
</template>