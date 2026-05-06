<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    title: String,
    value: String,
    icon: Object, 
    color: String,
    trend: String,
    trendUp: Boolean,
    link: String,
});
</script>

<template>
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg rounded-xl transition-all duration-300 hover:shadow-2xl hover:scale-105 border border-gray-100 dark:border-gray-700 relative group">
        <div class="p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-opacity-20 transition-colors duration-300 flex items-center justify-center" :class="color">
                    <font-awesome-icon :icon="icon" class="h-6 w-6 transition-transform duration-300 group-hover:rotate-12" :class="color.replace('bg-', 'text-')" />
                </div>
                <div class="ml-4">
                    <p class="mb-2 text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ title }}</p>
                    <p class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ value }}</p>
                </div>
            </div>
        </div>
        
        <!-- Decoration circle -->
        <div class="absolute -top-6 -right-6 w-24 h-24 rounded-full bg-current opacity-5 pointer-events-none" :class="color.replace('bg-', 'text-')"></div>
        
        <!-- Action Area -->
        <div v-if="trend" class="absolute bottom-0 left-0 w-full px-6 py-2.5 bg-gray-50/50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700/50 flex items-center justify-between text-[10px] transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700/50">
             <Link v-if="link" :href="link" class="w-full h-full flex items-center justify-between group/link">
                <span class="font-bold flex items-center uppercase tracking-widest opacity-80" :class="trendUp ? 'text-green-600' : 'text-red-600'">
                    <i v-if="trendUp" class="fa-solid fa-arrow-trend-up mr-1.5 transition-transform group-hover/link:-translate-y-0.5"></i>
                    <i v-else class="fa-solid fa-arrow-trend-down mr-1.5 transition-transform group-hover/link:translate-y-0.5"></i>
                    {{ trend }}
                </span>
                <span class="inline-flex items-center font-black uppercase tracking-tighter transition-all px-2 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 group-hover/link:bg-indigo-600 group-hover/link:text-white dark:group-hover/link:bg-indigo-500 dark:group-hover/link:text-white">
                    VER DETALLE
                    <i class="fa-solid fa-angles-right ml-1.5 text-[8px] transition-transform group-hover/link:translate-x-1"></i>
                </span>
            </Link>
            <template v-else>
                <span class="font-bold flex items-center uppercase tracking-widest opacity-80" :class="trendUp ? 'text-green-600' : 'text-red-600'">
                    <i v-if="trendUp" class="fa-solid fa-arrow-trend-up mr-1.5"></i>
                    <i v-else class="fa-solid fa-arrow-trend-down mr-1.5"></i>
                    {{ trend }}
                </span>
                <span class="text-gray-400 font-bold uppercase tracking-tighter">MES ACTUAL</span>
            </template>
        </div>
    </div>
</template>
