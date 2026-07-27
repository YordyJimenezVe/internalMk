<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import _ from 'lodash'; // Assuming lodash is installed
import { ref } from 'vue';
const props = defineProps({
  partidas: Object,
  data: Object,
  employee: Object,
  tasa_bcv: Number,
  costo_declarado: [Number, String],
});
const showCedulaModal = ref(false);
const showSerialZoomModal = ref(false);

const formatSerial = (serial) => {
    if (!serial) return '';
    const upper = serial.toUpperCase();
    if (upper === 'S/S' || upper === 'SIN SERIAL' || upper === 'NO APARENTE SERIAL' || upper === 'NO APARENTA SERIAL') {
        return 'NO APARENTA SERIAL';
    }
    return serial;
};
</script>

<template>
    <AppLayout title="Registro de Facturación">
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center">
                    <i class="fa-solid fa-file-invoice-dollar mr-2 text-indigo-500"></i>Registrar Factura
                </h2>
                <a :href="route('billing')" class="inline-flex items-center px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-bold rounded-xl transition-all">
                    <i class="fa-solid fa-list-check mr-2 text-indigo-500"></i>Ver Historial
                </a>
            </div>
        </template>

        <div class="py-12 bg-gray-50 dark:bg-gray-900 min-h-screen transition-colors duration-300">
            <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-2xl rounded-3xl border border-gray-100 dark:border-gray-700/50 p-8">
                    <form ref="form" @submit.prevent="submitForm" class="space-y-8">
                        
                        <!-- Metadata and Inventory Section -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pb-8 border-b border-gray-100 dark:border-gray-700">
                            <div class="space-y-6">
                                <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-4 flex items-center">
                                    <i class="fa-solid fa-clock mr-2"></i>Fecha y Hora
                                </h3>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-1 ml-1" for="fecha">Fecha</label>
                                        <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl py-3 px-4 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-semibold text-sm" name="fecha" type="date" v-model="fecha" required>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-1 ml-1" for="hora">Hora</label>
                                        <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl py-3 px-4 leading-tight focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-all font-semibold text-sm" name="hora" type="time" v-model="hora" required>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-4 flex items-center">
                                    <i class="fa-solid fa-box-open mr-2 text-indigo-500"></i>Ítem de Inventario
                                </h3>
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-1 ml-1" for="partida_id">Referencia de Producto</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <i class="fa-solid fa-barcode text-indigo-400"></i>
                                        </div>
                                        <select disabled class="appearance-none block w-full bg-indigo-50/50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 border border-indigo-100 dark:border-indigo-800 rounded-xl py-3 pl-11 pr-4 leading-tight font-black uppercase text-sm" id="partida_id" v-model="partida" required>
                                            <option :key="data.id" :value="data.id">{{data.tipo}} - {{data.marca}} {{data.modelo}} ({{data.codInv}})</option>
                                        </select>
                                    </div>
                                    <input type="hidden" name="partida_id" :value="data.id">
                                </div>
                            </div>
                        </div>

                        <!-- Financial Calculations Section -->
                        <div class="space-y-6">
                            <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-4 flex items-center">
                                <i class="fa-solid fa-calculator mr-2 text-emerald-500"></i>Detalles Financieros
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                                <!-- Producto Divisas -->
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700/50 space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest flex items-center gap-1">
                                        <i class="fa-solid fa-tag"></i> Precio Divisa
                                    </label>
                                    <input class="w-full bg-transparent text-gray-900 dark:text-white text-xl font-black border-none focus:ring-0 p-0" name="priceDivisa" type="text" v-model="priceDivisa" readonly required>
                                </div>

                                <!-- Valor Tasa -->
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700/50 space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest flex items-center gap-1">
                                        <i class="fa-solid fa-earth-americas"></i> Tasa BCV
                                    </label>
                                    <input class="w-full bg-transparent text-emerald-600 dark:text-emerald-400 text-xl font-black border-none focus:ring-0 p-0" name="value_divisa" type="text" v-model="valueDivisa" placeholder="0.00" required>
                                </div>

                                <!-- Big (Base) -->
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700/50 space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest flex items-center gap-1">
                                        <i class="fa-solid fa-coins"></i> Base Imponible
                                    </label>
                                    <input class="w-full bg-transparent text-gray-900 dark:text-white text-xl font-black border-none focus:ring-0 p-0" name="big" type="text" v-model="big" readonly required>
                                </div>

                                <!-- Iva -->
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700/50 space-y-2">
                                    <label class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest flex items-center gap-1">
                                        <i class="fa-solid fa-percent"></i> IVA (16%)
                                    </label>
                                    <input class="w-full bg-transparent text-rose-500 text-xl font-black border-none focus:ring-0 p-0" name="iva" type="text" v-model="iva" readonly required>
                                </div>
                            </div>

                            <!-- Payment Breakdown -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 p-8 bg-indigo-50/30 dark:bg-indigo-900/10 rounded-[2.5rem] border border-indigo-100/50 dark:border-indigo-500/10">
                                <!-- Pago Bs -->
                                <div>
                                    <label class="block text-xs font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-widest mb-3" for="bs">
                                        <i class="fa-solid fa-wallet mr-1"></i>Pago en Bs
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400 font-bold">Bs.</span>
                                        <input class="appearance-none block w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-indigo-100 dark:border-indigo-800 rounded-2xl py-4 pl-12 pr-4 leading-tight focus:outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all text-xl font-black" name="bs" type="text" placeholder="0,00" v-model="bsAmount">
                                    </div>
                                </div>

                                <!-- Pago Divisas -->
                                <div>
                                    <label class="block text-xs font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-widest mb-3" for="divisa">
                                        <i class="fa-solid fa-dollar-sign mr-1"></i>Pago en Divisas
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400 font-bold">$</span>
                                        <input class="appearance-none block w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-white border border-indigo-100 dark:border-indigo-800 rounded-2xl py-4 pl-8 pr-4 leading-tight focus:outline-none focus:ring-4 focus:ring-indigo-500/10 transition-all text-xl font-black" name="divisa" type="text" v-model="pagoDivisa" placeholder="0,00" required>
                                    </div>
                                </div>

                                <!-- IGTF -->
                                <div>
                                    <label class="block text-xs font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-widest mb-3" for="igtf">
                                        <i class="fa-solid fa-money-bill-wave mr-1"></i>IGTF (3%)
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-indigo-400 font-bold">Bs.</span>
                                        <input class="appearance-none block w-full bg-white dark:bg-gray-800 text-rose-500 border border-indigo-100 dark:border-indigo-800 rounded-2xl py-4 pl-12 pr-4 leading-tight font-black text-xl" name="igtf" type="text" v-model="igtf" readonly required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Totals Banner -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4">
                                <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-8 rounded-[2rem] text-white shadow-xl shadow-indigo-500/20">
                                    <span class="text-[10px] font-black uppercase tracking-[0.3em] opacity-80">Monto Total Facturado</span>
                                    <div class="text-4xl font-black mt-2 flex items-baseline gap-2">
                                        {{ totalAmount }} <span class="text-lg opacity-80">Bs</span>
                                    </div>
                                    <input type="hidden" name="precio_total" v-model="totalAmount">
                                </div>
                                <div class="bg-gray-900 dark:bg-black p-8 rounded-[2rem] text-white shadow-xl">
                                    <span class="text-[10px] font-black uppercase tracking-[0.3em] text-indigo-400">Total a Cancelar (incl. IGTF)</span>
                                    <div class="text-4xl font-black mt-2 text-indigo-400 flex items-baseline gap-2">
                                        {{ montoCancelado }} <span class="text-lg opacity-80">Bs</span>
                                    </div>
                                    <input type="hidden" name="monto_cancelado" v-model="montoCancelado">
                                </div>
                            </div>
                        </div>

                        <!-- Client and Logistics Section -->
                        <div class="space-y-6 pt-8 border-t border-gray-100 dark:border-gray-700">
                             <h3 class="font-black text-xs uppercase tracking-[0.2em] text-gray-400 dark:text-gray-500 mb-4 flex items-center">
                                <i class="fa-solid fa-user-tag mr-2 text-indigo-500"></i>Información del Cliente y Control
                            </h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-4">
                                    <div v-if="data.tipo && (data.tipo.toUpperCase().includes('MOTOR') || data.tipo.toUpperCase().includes('CAJA'))">
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="observaciones">Detalles de Despacho (Cómo sale)</label>
                                        <select class="block w-full bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-sm" name="observaciones" required>
                                            <option value="" disabled selected>SELECCIONE UNA OPCIÓN</option>
                                            <template v-if="data.tipo.toUpperCase().includes('MOTOR')">
                                                <option value="MOTOR COMPLETO">MOTOR COMPLETO</option>
                                                <option value="MOTOR 7/8">MOTOR 7/8</option>
                                                <option value="MOTOR 3/4">MOTOR 3/4</option>
                                            </template>
                                            <template v-else-if="data.tipo.toUpperCase().includes('CAJA')">
                                                <option value="CAJA COMPLETA">CAJA COMPLETA</option>
                                                <option value="CAJA SIN TURBINA">CAJA SIN TURBINA</option>
                                            </template>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="client_name">Nombre de Cliente</label>
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" name="client_name" type="text" v-model="clientName" placeholder="EJ: JUAN PÉREZ">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="client_cedula">Cédula / RIF</label>
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" name="client_cedula" type="text" v-model="clientCedula" placeholder="EJ: V-12345678">
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="client_phone">Teléfono</label>
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" name="client_phone" type="text" v-model="clientPhone" placeholder="EJ: 0412-1234567">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="client_email">Correo Electrónico</label>
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" name="client_email" type="email" v-model="clientEmail" placeholder="EJ: CLIENTE@CORREO.COM">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="client_address">Dirección</label>
                                        <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold" name="client_address" type="text" v-model="clientAddress" placeholder="Dirección del cliente">
                                    </div>
                                    <div v-if="data.observation" class="p-4 bg-amber-50 dark:bg-amber-900/20 rounded-2xl border border-amber-100 dark:border-amber-900/30">
                                        <label class="block text-[10px] font-black text-amber-850 dark:text-amber-400 uppercase tracking-widest mb-1 flex items-center gap-1">
                                            <i class="fa-solid fa-comment-dots text-amber-500"></i> Observación del Asesor
                                        </label>
                                        <p class="text-xs text-amber-700 dark:text-amber-300 font-bold uppercase">{{ data.observation }}</p>
                                    </div>
                                    <div class="p-4 bg-indigo-50/50 dark:bg-indigo-900/20 rounded-2xl border border-indigo-100 dark:border-indigo-900/30">
                                        <label class="block text-[10px] font-black text-indigo-700 dark:text-indigo-400 uppercase tracking-widest mb-1 flex items-center gap-1">
                                            <i class="fa-solid fa-barcode text-indigo-500"></i> Serial del Motor / Caja
                                        </label>
                                        <p class="text-sm font-black text-indigo-900 dark:text-indigo-300 uppercase">{{ formatSerial(data.serial) || 'NO CARGADO' }}</p>
                                    </div>
                                </div>

                                <div class="space-y-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1 text-indigo-600 dark:text-indigo-400" for="numero_factura">Nro. de Factura</label>
                                            <input class="appearance-none block w-full bg-white dark:bg-gray-800 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 rounded-xl py-3 px-4 shadow-sm focus:ring-2 focus:ring-indigo-500 transition-all font-black text-sm" name="numero_factura" type="text" placeholder="0001">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase mb-2 ml-1" for="numero_control">Nro. de Control</label>
                                            <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-900 text-gray-700 dark:text-white border border-gray-100 dark:border-gray-700 rounded-xl py-3 px-4 focus:ring-2 focus:ring-indigo-500 transition-all font-bold text-sm" name="numero_control" type="text" placeholder="CONT-001">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <!-- Client ID Card Preview -->
                                        <div v-if="data.client_cedula_url" class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center gap-3">
                                            <label class="block w-full text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest text-center">Documento de Identidad</label>
                                            <div @click="showCedulaModal = true" class="relative group cursor-zoom-in w-full max-w-[200px] flex items-center justify-center">
                                                <img :src="data.client_cedula_url" class="rounded-xl shadow-lg border-2 border-white dark:border-gray-800 transition-transform group-hover:scale-[1.02] max-w-full max-h-40 object-contain" alt="Cédula de Identidad">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center rounded-xl transition-opacity">
                                                    <i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i>
                                                </div>
                                            </div>
                                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter italic">Ampliar Cédula</p>
                                        </div>

                                        <!-- Serial Image Preview -->
                                        <div v-if="data.serial_image_url" class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-gray-700 flex flex-col items-center gap-3">
                                            <label class="block w-full text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest text-center">Foto del Serial</label>
                                            <div @click="showSerialZoomModal = true" class="relative group cursor-zoom-in w-full max-w-[200px] flex items-center justify-center">
                                                <img :src="data.serial_image_url" class="rounded-xl shadow-lg border-2 border-white dark:border-gray-800 transition-transform group-hover:scale-[1.02] max-w-full max-h-40 object-contain" alt="Foto del Serial">
                                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center rounded-xl transition-opacity">
                                                    <i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i>
                                                </div>
                                            </div>
                                            <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tighter italic">Ampliar Serial</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Hidden Inputs and Submit -->
                        <div class="pt-10 flex flex-col md:flex-row items-center justify-between gap-6 border-t border-gray-100 dark:border-gray-700">
                            <div class="flex items-center gap-2 text-gray-400 dark:text-gray-500 text-xs">
                                <i class="fa-solid fa-shield-halved"></i>
                                <span>Verifique todos los montos antes de proceder</span>
                            </div>
                            
                            <input type="hidden" v-model="pagoDivisaHidden">
                            <input type="hidden" v-model="pagoBsHidden">
                            <input type="hidden" name="billing_request_id" :value="data.billing_request_id">
                            
                            <button type="submit" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-black py-4 px-12 rounded-[2rem] shadow-2xl shadow-indigo-600/30 transition-all transform hover:scale-[1.03] active:scale-95 flex items-center justify-center gap-3">
                                <i class="fa-solid fa-floppy-disk text-xl"></i>
                                PROCESAR FACTURACIÓN
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- ID Card Modal -->
        <div v-if="showCedulaModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-10">
            <div class="fixed inset-0 bg-gray-950/90 backdrop-blur-md transition-opacity" @click="showCedulaModal = false"></div>
            
            <div class="relative max-w-5xl w-full animate-in zoom-in-95 duration-300">
                <button @click="showCedulaModal = false" class="absolute -top-16 right-0 text-white/70 hover:text-white transition-colors flex items-center gap-2 font-black uppercase tracking-widest text-xs">
                    <span>Cerrar</span>
                    <i class="fa-solid fa-circle-xmark text-3xl"></i>
                </button>
                
                <div class="bg-white dark:bg-gray-900 p-3 rounded-[2.5rem] shadow-2xl border-4 border-white/10 overflow-hidden">
                    <img :src="data.client_cedula_url" class="w-full h-auto rounded-[2rem] shadow-inner" alt="Cédula Ampliada">
                </div>
            </div>
        </div>

        <!-- Serial Zoom Modal -->
        <div v-if="showSerialZoomModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-10">
            <div class="fixed inset-0 bg-gray-950/90 backdrop-blur-md transition-opacity" @click="showSerialZoomModal = false"></div>
            
            <div class="relative max-w-5xl w-full animate-in zoom-in-95 duration-300">
                <button @click="showSerialZoomModal = false" class="absolute -top-16 right-0 text-white/70 hover:text-white transition-colors flex items-center gap-2 font-black uppercase tracking-widest text-xs">
                    <span>Cerrar</span>
                    <i class="fa-solid fa-circle-xmark text-3xl"></i>
                </button>
                
                <div class="bg-white dark:bg-gray-900 p-3 rounded-[2.5rem] shadow-2xl border-4 border-white/10 overflow-hidden">
                    <img :src="data.serial_image_url" class="w-full h-auto rounded-[2rem] shadow-inner" alt="Serial Ampliado">
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script>
export default {
    data() {
        return {
            formatValue: '',
            bsAmount:'',
            priceDivisa:'',
            pagoDivisaHidden:'',
            pagoBsHidden:'',
            valueDivisa:'',
            pagoDivisa: '',
            montoCancelado:'',
            igtf: '',
            iva: '',
            partida:'',
            big: '',
            totalAmount: '',
            clientName: '',
            clientCedula: '',
            clientPhone: '',
            clientAddress: '',
            clientEmail: '',
            fecha: '',
            hora: '',
            formData: {
                partida: {
                    value: '0',
                    label: 'seleccione'
                },
                big: '',
                igtf: '',
                iva: '',
                numero_factura: '',
                numero_control: '',
                precio_total: '',
                // numero_nota_credito: '',
                // numero_factura_afect: '',
            },
        };
    },
    watch: {
        bsAmount(newValue){
            this.bsAmount=this.thousandsSeparator(newValue)
        },
        pagoDivisa(newValue) {
            this.calculatePaymentDetails();
        },
        valueDivisa(newValue) {
            this.calculateInvoiceDetails();
        }
    },
    mounted() {
        // Convertimos a número, redondeamos a 2 decimales y aseguramos que sea string para tus regex
        this.valueDivisa = parseFloat(this.tasa_bcv).toFixed(2);

        // El precio sugerido de facturación (costo_declarado) se asigna a priceDivisa
        const declaredUSD = parseFloat(this.$page.props.costo_declarado || 0).toFixed(2);
        this.priceDivisa = declaredUSD;

        // El pago real en divisa por el cual se vende el motor (ej: $3000) se asigna a pagoDivisa
        const initialUSD = this.data['price'] || this.$page.props.costo_declarado || 0;
        this.pagoDivisa = parseFloat(initialUSD).toFixed(2);

        // Realizar cálculos iniciales
        this.calculateInvoiceDetails();

        this.clientName = this.data['client_name'] || '';
        this.clientCedula = this.data['client_cedula'] || '';
        this.clientPhone = this.data['client_phone'] || '';
        this.clientAddress = this.data['client_address'] || '';
        this.clientEmail = this.data['client_email'] || '';
        this.partida = this.data.id;

        // Set current date and time
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        this.fecha = `${year}-${month}-${day}`;
        this.hora = now.toTimeString().split(' ')[0].substring(0, 5);
    },
    methods: {
        submitForm() {
            const data = {};
            for (const element of this.$refs.form.elements) {
                data[element.name] = element.value;
            }

            this.$inertia.post('/billing/store', data);
        },
        thousandsSeparator(param){
            return param
            .replace(/\D/g, "") // Remover caracteres no numéricos
            .replace(/([0-9])([0-9]{2})$/, "$1,$2") // agregar , luego de 2 digitos
            .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, "."); // Agegar decimal
        },
        normalizeUSD(usdValue) {
            let cleanStr = String(usdValue);
            if (cleanStr.includes(',') && cleanStr.includes('.')) {
                if (cleanStr.indexOf('.') < cleanStr.indexOf(',')) {
                    cleanStr = cleanStr.replace(/\./g, "").replace(",", ".");
                } else {
                    cleanStr = cleanStr.replace(/,/g, "");
                }
            } else if (cleanStr.includes(',')) {
                if (cleanStr.match(/,\d{2}$/)) {
                    cleanStr = cleanStr.replace(/\./g, "").replace(",", ".");
                } else {
                    cleanStr = cleanStr.replace(/,/g, "");
                }
            }
            return parseFloat(cleanStr) || 0;
        },
        calculateInvoiceDetails() {
            if (!this.priceDivisa) return;

            const usdFloat = this.normalizeUSD(this.priceDivisa);
            const rateFloat = parseFloat(this.valueDivisa) || 0;

            const usdCents = Math.round(usdFloat * 100);
            const rateCents = Math.round(rateFloat * 100);

            // 1. BIG (Base Imponible en Bs)
            const bigScale10000 = usdCents * rateCents;
            const bigCents = Math.round(bigScale10000 / 100);
            this.big = this.thousandsSeparator(String(bigCents));

            // 2. IVA (16%)
            const ivaCents = Math.round(bigCents * 0.16);
            this.iva = this.thousandsSeparator(String(ivaCents));

            // 3. Monto Total Facturado (BIG + IVA)
            const generalCents = bigCents + ivaCents;
            this.totalAmount = this.thousandsSeparator(String(generalCents));

            // Recalculamos los detalles del pago ya que cambió el total facturado
            this.calculatePaymentDetails();
        },
        calculatePaymentDetails() {
            if (!this.pagoDivisa) return;

            const usdFloat = this.normalizeUSD(this.pagoDivisa);
            const rateFloat = parseFloat(this.valueDivisa) || 0;

            const usdCents = Math.round(usdFloat * 100);
            const rateCents = Math.round(rateFloat * 100);

            // 4. IGTF (3% del pago en USD convertido a Bs)
            const scale10000 = usdCents * rateCents;
            const igtfCents = Math.round((3 * scale10000) / 10000);
            this.igtf = this.thousandsSeparator(String(igtfCents));

            // 5. Monto Cancelado (Monto Total + IGTF)
            const totalAmountCents = parseInt(String(this.totalAmount).replace(/[,.]/g, "")) || 0;
            const totalCents = totalAmountCents + igtfCents;
            this.montoCancelado = this.thousandsSeparator(String(totalCents));
        }
    }
};
</script>