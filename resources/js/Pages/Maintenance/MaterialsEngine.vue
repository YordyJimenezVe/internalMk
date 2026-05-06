<script setup>
const props = defineProps({
  materials: Object,
  accesorios: Object,
  isView: {
    type: Boolean,
    default: false
  }
});

const hasValue = (val) => {
    return val !== null && val !== undefined && val !== '' && val !== 0 && val !== '0' && val !== '0,00';
};
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div v-if="!isView || hasValue(props.materials.concha_biela)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="concha_biela">
                <i class="fas fa-money-bill-wave mr-2 text-green-500"></i>$ Concha de Biela
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="concha_biela" name="concha_biela" type="text" placeholder="$ Precio" :value="props.materials.concha_biela">
        </div>
        <div v-if="!isView || hasValue(props.materials.concha_bancada)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="concha_bancada">
                <i class="fas fa-money-bill-wave mr-2 text-green-500"></i>$ Concha de Bancada
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="concha_bancada" name="concha_bancada" type="text" placeholder="$ Precio" :value="props.materials.concha_bancada">
        </div>
        <div v-if="!isView || hasValue(props.materials.anillos)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="anillos">
               <i class="fas fa-ring mr-2 text-green-500"></i>$ Anillos
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="anillos" name="anillos" type="text" placeholder="$ Precio" :value="props.materials.anillos">
        </div>
        <div v-if="!isView || hasValue(props.materials.empacadura_camara)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="empacadura_camara">
                <i class="fas fa-layer-group mr-2 text-green-500"></i>$ Empacadura Cámara
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" name="empacadura_camara" type="text" placeholder="$ Precio" :value="props.materials.empacadura_camara">
        </div>
        <div v-if="!isView || hasValue(props.materials.empacadura_carter)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="empacadura_carter">
                <i class="fas fa-sheet-plastic mr-2 text-green-500"></i>$ Empacadura del Carter
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="empacadura_carter" name="empacadura_carter" type="text" placeholder="$ Precio" :value="props.materials.empacadura_carter">
        </div>
        <div v-if="!isView || hasValue(props.materials.kit_empacaduras)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="kit_empacaduras">
                <i class="fas fa-briefcase mr-2 text-green-500"></i>$ Kit de Empacaduras
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" name="kit_empacaduras" type="text" placeholder="$ Precio" :value="props.materials.kit_empacaduras">
        </div>
        <div v-if="!isView || hasValue(props.materials.baño_quimico)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="baño_quimico">
                <i class="fas fa-flask-vial mr-2 text-green-500"></i>$ Baño Químico
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="baño_quimico" name="baño_quimico" type="text" placeholder="$ Costo" :value="props.materials.baño_quimico">
        </div>
        <div v-if="!isView || hasValue(props.materials.goma_valvula)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="goma_valvula">
                <i class="fas fa-circle-dot mr-2 text-green-500"></i>$ Goma Válvula
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" name="goma_valvula" type="text" placeholder="$ Costo" :value="props.materials.goma_valvula">
        </div>
        <div v-if="!isView || hasValue(props.materials.planos)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="planos">
                <i class="fas fa-ruler-combined mr-2 text-green-500"></i>$ Planos
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="planos" name="planos" type="text" placeholder="$ Costo" :value="props.materials.planos">
        </div>
        <div v-if="!isView || hasValue(props.materials.valvulas)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="valvulas">
               <i class="fas fa-microchip mr-2 text-green-500"></i>$ Válvulas
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" name="valvulas" type="text" placeholder="$ Costo" :value="props.materials.valvulas">
        </div>
        <div v-if="!isView || hasValue(props.materials.rectificadora)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="rectificadora">
                <i class="fas fa-screwdriver-wrench mr-2 text-green-500"></i>$ Rectificación
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="rectificadora" name="rectificadora" type="text" placeholder="$ Precio" :value="props.materials.rectificadora">
        </div>
        <div v-if="!isView || hasValue(props.materials.asientos)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="asientos">
                <i class="fas fa-chair mr-2 text-green-500"></i>$ Asientos
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" name="asientos" type="text" placeholder="$ Precio" :value="props.materials.asientos">
        </div>
        <div v-if="!isView || hasValue(props.materials.camisas)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="camisas">
                <i class="fas fa-shirt mr-2 text-green-500"></i>$ Camisas Bloques
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="camisas" name="camisas" type="text" placeholder="$ Costo" :value="props.materials.camisas">
        </div>
        <div v-if="!isView || hasValue(props.materials.levas)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="levas">
                <i class="fas fa-gears mr-2 text-green-500"></i>$ Leva Camisas
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" name="levas" type="text" placeholder="$ Costo" :value="props.materials.levas">
        </div>
        <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-4 mt-4 pt-4 border-t border-slate-700/30">
            <div v-if="!isView || hasValue(props.accesorios?.valve_cover)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="valve_cover">
                <i class="fas fa-money-bill-wave mr-2 text-indigo-500"></i>$ Tapa Válvula
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="valve_cover" name="valve_cover" type="text" placeholder="$ Precio" :value="props.accesorios?.valve_cover">
            </div>
            <div v-if="!isView || hasValue(props.accesorios?.chain_cover)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="chain_cover">
                <i class="fas fa-money-bill-wave mr-2 text-indigo-500"></i>$ Tapa Cadena
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="chain_cover" name="chain_cover" type="text" placeholder="$ Precio" :value="props.accesorios?.chain_cover">
            </div>
            <div v-if="!isView || hasValue(props.accesorios?.carter)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="carter">
                <i class="fas fa-money-bill-wave mr-2 text-indigo-500"></i>$ Carter
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="carter" name="carter" type="text" placeholder="$ Precio" :value="props.accesorios?.carter">
            </div>
            <div v-if="!isView || hasValue(props.accesorios?.pescador)">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="pescador">
                <i class="fas fa-money-bill-wave mr-2 text-indigo-500"></i>$ Pescador
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="pescador" name="pescador" type="text" placeholder="$ Precio" :value="props.accesorios?.pescador">
            </div>
        </div>
        <div v-if="!isView || hasValue(props.materials.pistones)" class="md:col-span-2 mt-4">
            <label class="block uppercase tracking-wider text-gray-500 dark:text-slate-400 text-[10px] font-bold mb-1.5 ml-1" for="pistones">
                <i class="fas fa-hand-back-fist mr-2 text-green-500"></i>$ Pistón o Brazo
            </label>
            <input class="block w-full bg-gray-50 dark:bg-slate-900/50 text-gray-900 dark:text-white border border-gray-200 dark:border-slate-700 rounded-xl py-2 px-4 focus:outline-none focus:border-blue-500 transition-all placeholder-gray-400 dark:placeholder-slate-600 font-medium" id="pistones" name="pistones" type="text" placeholder="$ Precio" :value="props.materials.pistones">
        </div>
    </div>
</template>


<script>
export default {
  name:'MaterialEngine'
}
</script>