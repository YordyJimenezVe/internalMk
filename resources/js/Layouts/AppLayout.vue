<script setup>
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';

defineProps({
    title: String,
});

const showingNavigationDropdown = ref(false);
const isSidebarCollapsed = ref(false);
const isDarkMode = ref(false);

const toggleSidebar = () => {
    isSidebarCollapsed.value = !isSidebarCollapsed.value;
    localStorage.setItem('sidebarCollapsed', isSidebarCollapsed.value);
};

const toggleDarkMode = () => {
    isDarkMode.value = !isDarkMode.value;
    if (isDarkMode.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('darkMode', 'true');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('darkMode', 'false');
    }
};

onMounted(() => {
    // Restore sidebar preference
    const collapsed = localStorage.getItem('sidebarCollapsed');
    if (collapsed === 'true') {
        isSidebarCollapsed.value = true;
    }

    // Restore dark mode preference
    const dark = localStorage.getItem('darkMode');
    if (dark === 'true' || (!dark && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDarkMode.value = true;
        document.documentElement.classList.add('dark');
    }

    // --- GLOBAL SCANNER LISTENER ---
    let scannerBuffer = '';
    let scannerTimeout = null;

    window.addEventListener('keydown', (e) => {
        if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
            return;
        }

        if (e.key === 'Enter') {
            if (scannerBuffer.length > 3) {
                e.preventDefault(); // Stop any default form submission
                let scannedData = scannerBuffer;
                scannerBuffer = '';

                if (scannedData.includes("'")) {
                    scannedData = scannedData.replace(/'/g, '-');
                }
                
                router.post(route('scan.process'), { code: scannedData });
            }
            return;
        }

        if (e.key.length === 1) {
            scannerBuffer += e.key;
            if (scannerTimeout) clearTimeout(scannerTimeout);
            scannerTimeout = setTimeout(() => {
                scannerBuffer = '';
            }, 200);
        }
    });
});

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};
</script>

<template>
    <div class="min-h-screen bg-gray-100 dark:bg-gray-900 font-sans text-gray-900 antialiased">
        <Head :title="title" />

        <Banner />

        <!-- Mobile Sidebar Backdrop -->
        <div v-if="showingNavigationDropdown" class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 md:hidden" @click="showingNavigationDropdown = false"></div>

        <!-- Sidebar -->
        <aside 
            :class="[
                'fixed top-0 left-0 z-50 h-screen transition-all duration-300 transform bg-white dark:bg-gray-800 shadow-xl border-r border-gray-200 dark:border-gray-700',
                showingNavigationDropdown ? 'translate-x-0' : '-translate-x-full md:translate-x-0',
                isSidebarCollapsed ? 'md:w-20' : 'md:w-64',
                'w-64'
            ]"
        >
            <!-- Logo area -->
            <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-gray-50 to-white dark:from-gray-800 dark:to-gray-800" :class="isSidebarCollapsed ? 'justify-center' : 'justify-between'">
                <Link :href="route('dashboard')" class="flex items-center space-x-2" v-if="!isSidebarCollapsed">
                    <ApplicationMark class="block h-9 w-auto text-indigo-600" />
                    <span class="text-lg font-bold text-gray-800 dark:text-white tracking-wide truncate">InternalMk</span>
                </Link>
                <Link :href="route('dashboard')" v-else>
                     <ApplicationMark class="block h-9 w-auto text-indigo-600" />
                </Link>

                <!-- Mobile close button -->
                <button @click="showingNavigationDropdown = false" class="md:hidden text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Toggle Button (Desktop only) -->
             <div class="hidden md:flex justify-end p-2 border-b border-gray-100 dark:border-gray-700">
                <button @click="toggleSidebar" class="p-1 rounded-md text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none transition">
                    <svg v-if="!isSidebarCollapsed" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"></path></svg>
                    <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path></svg>
                </button>
            </div>


            <!-- Navigation Links -->
            <div class="px-3 py-6 overflow-y-auto h-[calc(100vh-8rem)]">
                <nav class="space-y-1 flex flex-col">
                    
                    <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('dashboard') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Panel' : ''">
                        <font-awesome-icon icon="fa-solid fa-house" class="w-5 h-5 shrink-0" :class="[route().current('dashboard') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Panel de Control</span>
                    </NavLink>

                    <div class="pt-4 pb-2" v-if="!isSidebarCollapsed && ($page.props.auth.user.permissions?.includes('manage roles') || $page.props.auth.user.permissions?.includes('view partida') || $page.props.auth.user.permissions?.includes('view maintenance') || $page.props.auth.user.roles?.includes('Superusuario'))">
                        <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Gestión</p>
                    </div>
                     <div class="pt-4 pb-2 flex justify-center" v-else-if="isSidebarCollapsed && ($page.props.auth.user.permissions?.includes('manage roles') || $page.props.auth.user.permissions?.includes('view partida') || $page.props.auth.user.permissions?.includes('view maintenance') || $page.props.auth.user.roles?.includes('Superusuario'))">
                        <div class="w-4 h-1 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>

                    <NavLink v-if="$page.props.auth.user.permissions?.includes('manage roles') || $page.props.auth.user.roles?.includes('Superusuario')" :href="route('container')" :active="route().current('container')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('container') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Contenedor' : ''">
                        <font-awesome-icon icon="fa-solid fa-cube" class="w-5 h-5 shrink-0" :class="[route().current('container') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Contenedor</span>
                    </NavLink>

                    <NavLink v-if="$page.props.auth.user.permissions?.includes('view partida') || $page.props.auth.user.roles?.includes('Superusuario')" :href="route('inventario')" :active="route().current('inventario')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('inventario') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Inventario' : ''">
                        <font-awesome-icon icon="fa-solid fa-box" class="w-5 h-5 shrink-0" :class="[route().current('inventario') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Inventario</span>
                    </NavLink>


                     <NavLink v-if="$page.props.auth.user.permissions?.includes('view maintenance') || $page.props.auth.user.roles?.includes('Superusuario')" :href="route('maintenance')" :active="route().current('maintenance')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('maintenance') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Mantenimiento' : ''">
                        <font-awesome-icon icon="fa-solid fa-wrench" class="w-5 h-5 shrink-0" :class="[route().current('maintenance') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Mantenimiento</span>
                    </NavLink>

                    <a v-if="$page.props.auth.user.permissions?.includes('view partida') || $page.props.auth.user.roles?.includes('Superusuario')" href="/generar-qr-etiquetas" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white" :class="[isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Etiquetas QR' : ''">
                        <font-awesome-icon icon="fa-solid fa-qrcode" class="w-5 h-5 shrink-0 text-gray-400 group-hover:text-gray-500 dark:text-gray-400" :class="[isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Etiquetas QR</span>
                    </a>
                    
                    <div class="pt-4 pb-2" v-if="!isSidebarCollapsed && ($page.props.auth.user.permissions?.includes('manage users') || $page.props.auth.user.permissions?.includes('manage roles') || $page.props.auth.user.permissions?.includes('view bitacora') || $page.props.auth.user.permissions?.includes('view billing') || $page.props.auth.user.permissions?.includes('view reports') || $page.props.auth.user.permissions?.includes('access scan') || $page.props.auth.user.roles?.includes('Superusuario'))">
                         <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administración</p>
                    </div>
                     <div class="pt-4 pb-2 flex justify-center" v-else-if="isSidebarCollapsed && ($page.props.auth.user.permissions?.includes('manage users') || $page.props.auth.user.permissions?.includes('manage roles') || $page.props.auth.user.permissions?.includes('view bitacora') || $page.props.auth.user.permissions?.includes('view billing') || $page.props.auth.user.permissions?.includes('view reports') || $page.props.auth.user.permissions?.includes('access scan') || $page.props.auth.user.roles?.includes('Superusuario'))">
                        <div class="w-4 h-1 bg-gray-200 dark:bg-gray-700 rounded"></div>
                    </div>

                    <NavLink v-if="$page.props.auth.user.permissions?.includes('manage users') || $page.props.auth.user.roles?.includes('Superusuario')" :href="route('users.index')" :active="route().current('users.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('users.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Usuarios' : ''">
                        <font-awesome-icon icon="fa-solid fa-users" class="w-5 h-5 shrink-0" :class="[route().current('users.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Usuarios</span>
                    </NavLink>

                    <NavLink v-if="$page.props.auth.user.permissions?.includes('manage roles') || $page.props.auth.user.roles?.includes('Superusuario')" :href="route('roles.index')" :active="route().current('roles.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('roles.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Roles' : ''">
                        <font-awesome-icon icon="fa-solid fa-shield-halved" class="w-5 h-5 shrink-0" :class="[route().current('roles.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Roles y Permisos</span>
                    </NavLink>

                    <NavLink v-if="$page.props.auth.user.permissions?.includes('view bitacora') || $page.props.auth.user.roles?.includes('Superusuario')" :href="route('bitacora.index')" :active="route().current('bitacora.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('bitacora.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Bitácora' : ''">
                        <font-awesome-icon icon="fa-solid fa-book" class="w-5 h-5 shrink-0" :class="[route().current('bitacora.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Bitácora</span>
                    </NavLink>

                    <NavLink v-if="$page.props.auth.user.permissions?.includes('view billing') || $page.props.auth.user.roles?.includes('Superusuario') || $page.props.auth.user.roles?.includes('FACTURACION')" :href="route('billing.requests.index')" :active="route().current('billing.requests.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('billing.requests.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Solicitudes' : ''">
                        <font-awesome-icon icon="fa-solid fa-file-circle-exclamation" class="w-5 h-5 shrink-0" :class="[route().current('billing.requests.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Solicitudes</span>
                    </NavLink>

                    <NavLink v-if="$page.props.auth.user.permissions?.includes('view billing') || $page.props.auth.user.roles?.includes('Superusuario') || $page.props.auth.user.roles?.includes('FACTURACION')" :href="route('billing')" :active="route().current('billing')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('billing') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Historial' : ''">
                        <font-awesome-icon icon="fa-solid fa-file-invoice-dollar" class="w-5 h-5 shrink-0" :class="[route().current('billing') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" />
                        <span v-if="!isSidebarCollapsed">Historial de Ventas</span>
                    </NavLink>

                    <NavLink v-if="$page.props.auth.user.permissions?.includes('view reports') || $page.props.auth.user.roles?.includes('Superusuario')" :href="route('reports')" :active="route().current('reports')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('reports') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Reportes' : ''">
                        <svg class="w-6 h-6 shrink-0" :class="[route().current('reports') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                        <span v-if="!isSidebarCollapsed">Reportes</span>
                    </NavLink>

                    <NavLink v-if="$page.props.auth.user.permissions?.includes('access scan') || $page.props.auth.user.roles?.includes('Superusuario')" :href="route('scan.index')" :active="route().current('scan.index')" class="flex items-center px-3 py-3 text-sm font-medium rounded-lg transition-colors duration-150 ease-in-out group" :class="[route().current('scan.index') ? 'bg-indigo-50 text-indigo-700 dark:bg-gray-700 dark:text-white' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white', isSidebarCollapsed ? 'justify-center' : '']" :title="isSidebarCollapsed ? 'Escaneo' : ''">
                        <svg class="w-6 h-6 shrink-0" :class="[route().current('scan.index') ? 'text-indigo-600 dark:text-indigo-400' : 'text-gray-400 group-hover:text-gray-500 dark:text-gray-400', isSidebarCollapsed ? '' : 'mr-3']" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 17h.01M4.5 20h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 20z" />
                        </svg>
                        <span v-if="!isSidebarCollapsed">Escaneo</span>
                    </NavLink>
                </nav>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex flex-col flex-1 min-h-screen transition-all duration-300" :class="isSidebarCollapsed ? 'md:pl-20' : 'md:pl-64'">
            
            <!-- Navbar Header -->
            <header class="flex items-center justify-between h-16 px-6 py-4 bg-white border-b border-gray-200 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <div class="flex items-center">
                    <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-gray-500 hover:text-gray-700 focus:outline-none md:hidden">
                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <!-- Header Title Slot -->
                    <h2 v-if="$slots.header" class="ml-4 font-semibold text-xl text-gray-800 dark:text-white leading-tight">
                        <slot name="header" />
                    </h2>
                </div>
                
                <div class="flex items-center space-x-4">
                     <!-- Dark Mode Toggle -->
                     <button @click="toggleDarkMode" class="p-2 rounded-lg text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 focus:outline-none transition-colors duration-200" :title="isDarkMode ? 'Cambiar a modo luz' : 'Cambiar a modo oscuro'">
                        <font-awesome-icon :icon="isDarkMode ? 'fa-solid fa-sun' : 'fa-solid fa-moon'" class="w-5 h-5" />
                     </button>

                     <!-- Teams Dropdown -->
                     <div class="relative ms-3" v-if="$page.props.jetstream.hasTeamFeatures">
                        <Dropdown align="right" width="60">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white dark:bg-gray-800 dark:text-gray-300 hover:text-gray-700 dark:hover:text-white focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ $page.props.auth.user.current_team.name }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                        </svg>
                                    </button>
                                </span>
                            </template>
                             <template #content>
                                <div class="w-60">
                                    <div class="block px-4 py-2 text-xs text-gray-400">Gestionar Equipo</div>
                                    <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">Configuración del Equipo</DropdownLink>
                                    <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">Crear Nuevo Equipo</DropdownLink>
                                    <div class="border-t border-gray-200 dark:border-gray-700" />
                                    <div class="block px-4 py-2 text-xs text-gray-400">Cambiar de Equipo</div>
                                    <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                        <form @submit.prevent="switchToTeam(team)">
                                            <DropdownLink as="button">
                                                <div class="flex items-center">
                                                    <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                    <div>{{ team.name }}</div>
                                                </div>
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </div>
                            </template>
                        </Dropdown>
                     </div>

                     <!-- Settings Dropdown -->
                     <div class="relative ms-3">
                          <Dropdown align="right" width="48">
                            <template #trigger>
                                <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                    <img class="h-8 w-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                </button>
                                <span v-else class="inline-flex rounded-md">
                                    <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-300 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-white focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                        {{ $page.props.auth.user.name }}
                                        <svg class="ms-2 -me-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <div class="block px-4 py-2 text-xs text-gray-400">Gestionar Cuenta</div>
                                <DropdownLink :href="route('profile.show')">
                                    <div class="flex items-center">
                                        <font-awesome-icon icon="fa-solid fa-user" class="mr-2 w-4 h-4" />
                                        <span>Perfil</span>
                                    </div>
                                </DropdownLink>
                                <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">Tokens API</DropdownLink>
                                <div class="border-t border-gray-200 dark:border-gray-700" />
                                <form @submit.prevent="logout">
                                    <DropdownLink as="button">
                                        <div class="flex items-center text-red-600 dark:text-red-400">
                                            <font-awesome-icon icon="fa-solid fa-right-from-bracket" class="mr-2 w-4 h-4" />
                                            <span>Cerrar Sesión</span>
                                        </div>
                                    </DropdownLink>
                                </form>
                            </template>
                          </Dropdown>
                     </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900 p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
