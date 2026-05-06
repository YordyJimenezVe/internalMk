<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template >
    <AppLayout title="Containers">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight">
                <i class="fa-solid fa-box-archive mr-2 text-indigo-500"></i>Registrar Containers
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
                    <form ref="form" @submit.prevent="submitForm" class="space-y-6" method="post">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Código -->
                            <div>
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2" for="cod">
                                    <i class="fa-solid fa-barcode mr-1"></i>Código
                                </label>
                                <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors" name="cod" type="text" placeholder="Ej: CONT-001" required>
                            </div>
                            <!-- Expediente -->
                            <div>
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2" for="expediente">
                                    <i class="fa-solid fa-file-invoice mr-1"></i>Expediente
                                </label>
                                <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors" name="expediente" type="text" placeholder="Ej: EXP-2024" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Fecha -->
                            <div>
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2" for="fecha">
                                    <i class="fa-solid fa-calendar mr-1"></i>Fecha
                                </label>
                                <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors" name="fecha" type="date" required>
                            </div>
                            <!-- Hora -->
                            <div>
                                <label class="block uppercase tracking-wide text-gray-700 dark:text-gray-300 text-xs font-bold mb-2" for="hora">
                                    <i class="fa-solid fa-clock mr-1"></i>Hora
                                </label>
                                <input class="appearance-none block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-3 px-4 leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-indigo-500 transition-colors" name="hora" type="time" required>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 dark:border-gray-700 pt-6">
                            <h3 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Totales en Contenedor</h3>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Motores -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-2" for="motores">Motores</label>
                                    <input class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-2 px-3 focus:outline-none focus:border-indigo-500" name="motores" type="number" placeholder="0" required>
                                </div>
                                <!-- Cajas -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-2" for="cajas">Cajas</label>
                                    <input class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-2 px-3 focus:outline-none focus:border-indigo-500" name="cajas" type="number" placeholder="0" required>
                                </div>
                                <!-- Cámaras -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-2" for="camaras">Cámaras</label>
                                    <input class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-2 px-3 focus:outline-none focus:border-indigo-500" name="camaras" type="number" placeholder="0" required>
                                </div>
                                <!-- Accesorios -->
                                <div>
                                    <label class="block text-gray-700 dark:text-gray-300 text-xs font-bold mb-2" for="accesorios">Accesorios</label>
                                    <input class="block w-full bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-white border border-gray-200 dark:border-gray-600 rounded-lg py-2 px-3 focus:outline-none focus:border-indigo-500" name="accesorios" type="number" placeholder="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-center pt-4">
                            <button type="submit" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-12 rounded-xl shadow-lg shadow-indigo-200 dark:shadow-none transition-all transform hover:scale-[1.02] active:scale-95">
                                <i class="fa-solid fa-plus mr-2"></i>Registrar Contenedor
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
<script>
export default {
  methods: {
    async submitForm() {
    try {
        console.log('e')
      const data = {};
      for (const element of this.$refs.form.elements) {
        data[element.name] = element.value;
      }

      const response = await this.$inertia.post('/container/store', data);
      // Handle successful response (e.g., display success message)
      console.log('Form submitted successfully:', response);
    } catch (error) {
      // Handle errors (e.g., display error message to user)
      console.error('Error submitting form:', error);
    }
  }
}
};
</script>