<script setup>
import { computed, ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

const props = defineProps({
    rows: Object, // Paginator object from Laravel
    columns: Array, // [{ key: 'name', label: 'Name', sortable: true }]
    filters: Object, // { search: '...', etc }
    routeName: String, // 'container.index' to keep filters on reload
    title: String,
    exportType: {
        type: String,
        default: null // 'container', 'partida', etc. for export URL
    }
});

const search = ref(props.filters?.search || '');
const currentSort = ref(props.filters?.sort || 'id');
const currentDirection = ref(props.filters?.direction || 'desc');

// Debounce search update
const updateSearch = debounce((value) => {
    router.get(route(props.routeName), { 
        ...props.filters, 
        search: value, 
        page: 1 
    }, { preserveState: true, replace: true });
}, 300);

watch(search, (value) => updateSearch(value));

const sortBy = (key) => {
    if (key === currentSort.value) {
        currentDirection.value = currentDirection.value === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.value = key;
        currentDirection.value = 'asc';
    }
    
    router.get(route(props.routeName), {
        ...props.filters,
        sort: currentSort.value,
        direction: currentDirection.value
    }, { preserveState: true });
};

const exportData = (type) => {
    // Collect all active filters from props
    const activeFilters = { ...props.filters };
    
    // Ensure search filter matches the current input
    activeFilters.search = search.value;

    const queryParams = new URLSearchParams(activeFilters).toString();
    const term = search.value || 'todos';
    const caso = search.value ? 'busqueda' : (activeFilters.status ? 'filtro' : 'todos');
    
    const routePrefix = type === 'excel' ? 'reporteExcel' : 'reportePdf';
    const url = route(routePrefix, { tipo: props.exportType, caso: caso, termino: term }) + '?' + queryParams;
    
    window.open(url, '_blank');
};
</script>

<template>
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden border border-gray-200 dark:border-gray-700">
        <!-- Header / Toolbar -->
        <div class="p-4 flex flex-col md:flex-row justify-between items-center border-b border-gray-200 dark:border-gray-700 gap-4">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">{{ title }}</h2>
            
            <div class="flex items-center gap-2 w-full md:w-auto">
                <!-- Search -->
                <div class="relative w-full md:w-64">
                    <input 
                        v-model="search"
                        type="text" 
                        placeholder="Buscar..." 
                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-indigo-500 focus:border-indigo-500"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Export Buttons -->
                <div v-if="exportType" class="flex gap-2">
                    <button @click="exportData('excel')" class="p-2 text-green-600 hover:bg-green-50 dark:hover:bg-green-900/30 rounded-lg transition-colors" title="Exportar Excel">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </button>
                    <button @click="exportData('pdf')" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Exportar PDF">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                    <tr>
                        <th 
                            v-for="col in columns" 
                            :key="col.key"
                            scope="col" 
                            class="px-6 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                            @click="col.sortable ? sortBy(col.key) : null"
                        >
                            <div class="flex items-center gap-1">
                                {{ col.label }}
                                <span v-if="col.sortable && currentSort === col.key">
                                    <span v-if="currentDirection === 'asc'">↑</span>
                                    <span v-else>↓</span>
                                </span>
                            </div>
                        </th>
                        <th v-if="$slots.actions" class="px-6 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="row in rows?.data || []" :key="row.id" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td v-for="col in columns" :key="col.key" :class="['px-6 py-4 font-medium text-gray-900 dark:text-white whitespace-nowrap', col.class || '']">
                            <!-- Helper slot for custom cell formatting -->
                            <slot :name="'cell-' + col.key" :row="row">
                                {{ row[col.key] }}
                            </slot>
                        </td>
                        <td v-if="$slots.actions" class="px-6 py-4 text-right">
                            <slot name="actions" :row="row"></slot>
                        </td>
                    </tr>
                    <tr v-if="(rows?.data || []).length === 0">
                        <td :colspan="columns.length + ($slots.actions ? 1 : 0)" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                            No se encontraron resultados
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700 flex flex-col md:flex-row justify-between items-center text-gray-500 dark:text-gray-400 font-medium">
            <span class="text-sm">
                Mostrando {{ rows?.from || 0 }} a {{ rows?.to || 0 }} de {{ rows?.total || 0 }} resultados
            </span>
            <div class="flex gap-1 mt-2 md:mt-0">
                <!-- Using Inertia Links for Pagination -->
                 <component
                    :is="link.url ? 'Link' : 'span'"
                    v-for="(link, index) in rows?.links || []"
                    :key="index"
                    :href="link.url"
                    class="px-3 py-1 rounded-md text-sm transition-colors"
                    :class="{
                        'bg-indigo-600 text-white': link.active,
                        'hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer': !link.active && link.url,
                        'text-gray-300 dark:text-gray-600': !link.url
                    }"
                    v-html="link.label"
                 />
            </div>
        </div>
    </div>
</template>

<script>
import { Link } from '@inertiajs/vue3';
export default {
    components: { Link }
}
</script>
