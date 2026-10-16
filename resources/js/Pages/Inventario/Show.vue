<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
  inventario: Object,
  qrCode: String,
  barcode: String,
  barcodeData: String,
  tasa_bcv: Number,
  available_cameras: {
    type: Array,
    default: () => [],
  },
});

const page = usePage();
const userRoles = computed(() => {
    const roles = page.props.auth.user?.roles || [];
    return roles.map(r => (typeof r === 'string' ? r : r.name || '').toLowerCase());
});
const userPermissions = computed(() => {
    const permissions = page.props.auth.user?.permissions || [];
    return permissions.map(p => (typeof p === 'string' ? p : p.name || '').toLowerCase());
});

const isReadOnlyUser = computed(() => {
    const directRol = (page.props.auth.user?.rol || '').toLowerCase();
    if (userRoles.value.includes('administrador consulta') || directRol === 'administrador consulta') return true;
    const hasManagePermission = userPermissions.value.some(p => ['manage billing', 'manage partida', 'manage users', 'manage roles'].includes(p));
    const hasWriteRole = ['superusuario', 'administrador', 'facturacion', 'vendedor', 'inventario'].includes(directRol) || 
                         userRoles.value.some(r => ['superusuario', 'administrador', 'facturacion', 'vendedor', 'inventario'].includes(r));
    return !hasManagePermission && !hasWriteRole;
});

const canRequestBilling = computed(() => {
    const directRol = (page.props.auth.user?.rol || '').toLowerCase();
    return ['superusuario', 'administrador', 'facturacion', 'vendedor'].includes(directRol) || 
           userRoles.value.some(r => ['superusuario', 'administrador', 'facturacion', 'vendedor'].includes(r));
});

const showQrCodes = computed(() => !isReadOnlyUser.value);

const vehicleType = ref('Cargando...');
const vehicleExample = ref('');
const loadingVehicleType = ref(true);

onMounted(() => {
    axios.get(route('inventario.vehicle_type'), {
        params: {
            marca: props.inventario.marca,
            modelo: props.inventario.modelo,
            ano: props.inventario.año
        }
    }).then(response => {
        vehicleType.value = response.data.tipo_vehiculo;
        vehicleExample.value = response.data.ejemplo;
    }).catch(error => {
        console.error('Error al obtener el tipo de vehículo:', error);
        vehicleType.value = 'N/A';
        vehicleExample.value = '';
    }).finally(() => {
        loadingVehicleType.value = false;
    });
});

const alreadyRequested = computed(() => {
    return (props.inventario.billing_requests && props.inventario.billing_requests.length > 0) || 
           (props.inventario.bill && props.inventario.bill.length > 0);
});

const baseImponible = computed(() => {
    return parseFloat(props.inventario.costo_importacion_unitario || 0) + parseFloat(props.inventario.costo_taller || 0);
});



const isMotor = computed(() => {
    const t = (props.inventario.tipo || '').toUpperCase();
    const item = (props.inventario.item || '').toUpperCase();
    return t.includes('MOTOR') || item.includes('MOTOR');
});

const isCamara = computed(() => {
    const t = (props.inventario.tipo || '').toUpperCase();
    const item = (props.inventario.item || '').toUpperCase();
    return t.includes('CÁMARA') || t.includes('CAMARA') || t.includes('CULATA') ||
           item.includes('CÁMARA') || item.includes('CAMARA') || item.includes('CULATA') ||
           !!props.inventario.is_camara;
});

const isCaja = computed(() => {
    const t = (props.inventario.tipo || '').toUpperCase();
    const item = (props.inventario.item || '').toUpperCase();
    return t.includes('CAJA') || item.includes('CAJA') || !!props.inventario.is_caja;
});

const cameraSide = computed(() => {
    return props.inventario.camera_side || null;
});

const getDefaultDispatchDetail = () => {
    const tipo = props.inventario.tipo ? props.inventario.tipo.toUpperCase() : '';
    const obs = props.inventario.observation ? props.inventario.observation.trim().toUpperCase() : '';

    if (isCamara.value) {
        const validOptions = [
            'CÁMARA COMPLETA - LADO DERECHO (R)',
            'CÁMARA COMPLETA - LADO IZQUIERDO (L)',
            'SOLO CAPARAZÓN - LADO DERECHO (R)',
            'SOLO CAPARAZÓN - LADO IZQUIERDO (L)',
            'CÁMARA COMPLETA',
            'SOLO CAPARAZÓN'
        ];
        if (validOptions.includes(obs)) {
            return obs;
        }

        const side = props.inventario.camera_side || (obs.includes('DERECH') || obs === 'R' ? 'R' : (obs.includes('IZQUIERD') || obs === 'L' ? 'L' : null));
        const isCaparazon = obs.includes('CAPARAZON') || obs.includes('CAPARAZÓN');

        if (side === 'R') {
            return isCaparazon ? 'SOLO CAPARAZÓN - LADO DERECHO (R)' : 'CÁMARA COMPLETA - LADO DERECHO (R)';
        }
        if (side === 'L') {
            return isCaparazon ? 'SOLO CAPARAZÓN - LADO IZQUIERDO (L)' : 'CÁMARA COMPLETA - LADO IZQUIERDO (L)';
        }
        return isCaparazon ? 'SOLO CAPARAZÓN' : 'CÁMARA COMPLETA';
    }

    if (isCaja.value) {
        if (obs) return obs;
        return 'CAJA COMPLETA';
    }

    if (tipo.includes('MOTOR 7/8')) return 'MOTOR 7/8';
    if (tipo.includes('MOTOR COMPLETO')) return 'MOTOR COMPLETO';
    if (tipo.includes('MOTOR 3/4')) return 'MOTOR 3/4';
    if (tipo.includes('MOTOR 5/8')) return 'MOTOR COMPLETO';
    if (tipo.includes('MOTOR')) return 'MOTOR COMPLETO';
    return obs || '';
};

const getInitialSerial = (serial) => {
    if (!serial) return '';
    const upper = serial.toUpperCase();
    if (['S/S', 'SIN SERIAL', 'NO APARENTE SERIAL', 'NO APARENTA SERIAL'].includes(upper)) {
        return '';
    }
    return serial;
};

const todayStr = new Date().toISOString().split('T')[0];

const form = useForm({
    partida_id: props.inventario.id,
    second_partida_id: null,
    price: '',
    tasa_bcv: props.tasa_bcv || '',
    fecha_tasa_bcv: todayStr,
    client_name: '',
    client_cedula: '',
    client_phone: '',
    client_address: '',
    client_email: '',
    quantity: 1,
    client_cedula_file: null,
    serial_file: null,
    serial: (isCamara.value || isCaja.value) ? '' : getInitialSerial(props.inventario.serial),
    observation: getDefaultDispatchDetail(),
});

// Pair camera management
const isPairCamera = ref(false);
const secondCodInput = ref('');
const selectedSecondCamera = ref(null);
const secondSearchError = ref('');
const searchingSecond = ref(false);

const suggestedCameras = computed(() => {
    const list = [...(props.available_cameras || [])];
    const currentModel = (props.inventario.modelo || '').toUpperCase();
    const currentSide = props.inventario.camera_side;
    const targetSide = currentSide === 'R' ? 'L' : (currentSide === 'L' ? 'R' : null);

    return list.sort((a, b) => {
        const aModel = (a.modelo || '').toUpperCase() === currentModel;
        const bModel = (b.modelo || '').toUpperCase() === currentModel;
        const aSide = targetSide && a.side === targetSide;
        const bSide = targetSide && b.side === targetSide;

        if (aModel && aSide && !(bModel && bSide)) return -1;
        if (bModel && bSide && !(aModel && aSide)) return 1;
        if (aModel && !bModel) return -1;
        if (bModel && !aModel) return 1;
        return 0;
    });
});

const updatePairObservation = (secondCam) => {
    if (!secondCam) return;
    const currentCod = props.inventario.formatted_cod || props.inventario.codInv;
    const currentSide = props.inventario.camera_side_label || props.inventario.camera_side || 'L/R';
    const secondCod = secondCam.formatted_cod || secondCam.codInv;
    const secondSide = secondCam.side_label || secondCam.side || 'L/R';
    
    form.observation = `PAR DE CÁMARAS (L + R): [Cod: ${currentCod} - LADO ${currentSide}] + [Cod: ${secondCod} - LADO ${secondSide}]`;
};

const onTogglePairCamera = () => {
    if (isPairCamera.value) {
        form.quantity = 2;
        const perfectMatch = suggestedCameras.value.find(c => {
            const sameModel = (c.modelo || '').toUpperCase() === (props.inventario.modelo || '').toUpperCase();
            const oppSide = props.inventario.camera_side === 'R' ? c.side === 'L' : c.side === 'R';
            return sameModel && oppSide;
        });
        if (perfectMatch) {
            form.second_partida_id = perfectMatch.id;
            selectedSecondCamera.value = perfectMatch;
            updatePairObservation(perfectMatch);
        }
    } else {
        clearSecondCamera();
        form.quantity = 1;
        form.observation = getDefaultDispatchDetail();
    }
};

const onSelectSecondCameraFromDropdown = () => {
    secondSearchError.value = '';
    if (!form.second_partida_id) {
        selectedSecondCamera.value = null;
        return;
    }
    const found = (props.available_cameras || []).find(c => c.id === form.second_partida_id);
    if (found) {
        selectedSecondCamera.value = found;
        secondCodInput.value = found.codInv || '';
        updatePairObservation(found);
    }
};

const clearSecondCamera = () => {
    form.second_partida_id = null;
    selectedSecondCamera.value = null;
    secondCodInput.value = '';
    secondSearchError.value = '';
    if (isPairCamera.value) {
        form.observation = getDefaultDispatchDetail();
    }
};

const searchSecondCameraByCode = async () => {
    const raw = String(secondCodInput.value || '').trim();
    if (!raw) {
        secondSearchError.value = 'Por favor ingresa un código de inventario.';
        return;
    }
    secondSearchError.value = '';
    searchingSecond.value = true;

    const cleanNum = raw.replace(/\D/g, '');
    const localFound = (props.available_cameras || []).find(c => {
        return String(c.id) === raw || 
               String(c.codInv) === raw || 
               (cleanNum && String(c.codInv) === cleanNum) ||
               (c.formatted_cod && c.formatted_cod.includes(raw));
    });

    if (localFound) {
        form.second_partida_id = localFound.id;
        selectedSecondCamera.value = localFound;
        updatePairObservation(localFound);
        searchingSecond.value = false;
        return;
    }

    try {
        const res = await axios.get(route('billing.checkItem', raw));
        if (res.data?.success && res.data?.item) {
            const it = res.data.item;
            if (it.id === props.inventario.id) {
                secondSearchError.value = 'No puedes vincular la misma cámara como su propio par.';
                selectedSecondCamera.value = null;
                form.second_partida_id = null;
            } else if (!it.is_available) {
                secondSearchError.value = `La cámara #${it.codInv} no está disponible (Estatus: ${it.status}).`;
                selectedSecondCamera.value = null;
                form.second_partida_id = null;
            } else {
                form.second_partida_id = it.id;
                selectedSecondCamera.value = {
                    id: it.id,
                    codInv: it.codInv,
                    formatted_cod: it.formatted_cod,
                    marca: it.marca,
                    modelo: it.modelo,
                    tipo: it.tipo,
                    side: it.camera_side,
                    side_label: it.camera_side_label,
                    observation: it.observation
                };
                updatePairObservation(selectedSecondCamera.value);
            }
        } else {
            secondSearchError.value = 'No se encontró ninguna cámara disponible con ese código.';
        }
    } catch (e) {
        secondSearchError.value = e.response?.data?.message || 'No se encontró ningún ítem con el código especificado.';
    } finally {
        searchingSecond.value = false;
    }
};

const fetchingRate = ref(false);
const fetchRateByDate = async (selectedDate) => {
    if (!selectedDate) return;
    fetchingRate.value = true;
    try {
        const response = await fetch(`/api/exchange-rate/by-date?date=${selectedDate}`);
        const data = await response.json();
        if (data && data.rate) {
            form.tasa_bcv = data.rate;
        }
    } catch (e) {
        console.error('Error al consultar tasa por fecha:', e);
    } finally {
        fetchingRate.value = false;
    }
};

watch(() => form.fecha_tasa_bcv, (newDate) => {
    if (newDate) {
        fetchRateByDate(newDate);
    }
});

const montoCalculadoBs = computed(() => {
    const usd = parseFloat(form.price) || 0;
    const rate = parseFloat(form.tasa_bcv) || 0;
    if (usd > 0 && rate > 0) {
        return (usd * rate).toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    return '0,00';
});

const serialDisplayText = computed(() => {
    const s = props.inventario.serial;
    const hasPhoto = !!props.inventario.serial_image_url;
    if (s && !['S/S', 'SIN SERIAL', 'NO APARENTE SERIAL', 'NO APARENTA SERIAL'].includes(s.toUpperCase())) {
        return s;
    }
    if (hasPhoto) {
        return 'VER FOTO ADJUNTA';
    }
    return 'NO APARENTA SERIAL';
});

// Camera and Preview state
const isCameraOpen = ref(false);
const videoElement = ref(null);
const canvasElement = ref(null);
const stream = ref(null);
const imagePreviewUrl = ref(null);
const serialPreviewUrl = ref(null);
const cameraError = ref(null);
const cameraTarget = ref('cedula'); // 'cedula' or 'serial'
const showSerialZoomModal = ref(false);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        // Limit to 2MB
        if (file.size > 2 * 1024 * 1024) {
            alert('La imagen no debe superar los 2MB');
            return;
        }
        form.client_cedula_file = file;
        if (imagePreviewUrl.value) {
            URL.revokeObjectURL(imagePreviewUrl.value);
        }
        imagePreviewUrl.value = URL.createObjectURL(file);
    }
};

const removeImage = () => {
    form.client_cedula_file = null;
    if (imagePreviewUrl.value) {
        URL.revokeObjectURL(imagePreviewUrl.value);
        imagePreviewUrl.value = null;
    }
};

const handleSerialFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        // Limit to 2MB
        if (file.size > 2 * 1024 * 1024) {
            alert('La imagen no debe superar los 2MB');
            return;
        }
        form.serial_file = file;
        if (serialPreviewUrl.value) {
            URL.revokeObjectURL(serialPreviewUrl.value);
        }
        serialPreviewUrl.value = URL.createObjectURL(file);
    }
};

const removeSerialImage = () => {
    form.serial_file = null;
    if (serialPreviewUrl.value) {
        URL.revokeObjectURL(serialPreviewUrl.value);
        serialPreviewUrl.value = null;
    }
};

const startCamera = async (target = 'cedula') => {
    cameraTarget.value = target;
    isCameraOpen.value = true;
    cameraError.value = null;
    try {
        stream.value = await navigator.mediaDevices.getUserMedia({
            video: { facingMode: 'environment', width: { ideal: 1280 }, height: { ideal: 720 } }
        });
        if (videoElement.value) {
            videoElement.value.srcObject = stream.value;
        }
    } catch (err) {
        console.error("Error al acceder a la cámara:", err);
        cameraError.value = "No se pudo acceder a la cámara. Asegúrate de dar los permisos necesarios o que ningún otro programa la esté usando.";
    }
};

const capturePhoto = () => {
    if (videoElement.value && canvasElement.value) {
        const context = canvasElement.value.getContext('2d');
        canvasElement.value.width = videoElement.value.videoWidth;
        canvasElement.value.height = videoElement.value.videoHeight;
        context.drawImage(videoElement.value, 0, 0);
        
        canvasElement.value.toBlob((blob) => {
            if (blob) {
                const filename = cameraTarget.value === 'cedula' ? "capture_cedula.jpg" : "capture_serial.jpg";
                const file = new File([blob], filename, { type: "image/jpeg" });
                
                if (cameraTarget.value === 'cedula') {
                    form.client_cedula_file = file;
                    if (imagePreviewUrl.value) {
                        URL.revokeObjectURL(imagePreviewUrl.value);
                    }
                    imagePreviewUrl.value = URL.createObjectURL(file);
                } else {
                    form.serial_file = file;
                    if (serialPreviewUrl.value) {
                        URL.revokeObjectURL(serialPreviewUrl.value);
                    }
                    serialPreviewUrl.value = URL.createObjectURL(file);
                }
                closeCamera();
            }
        }, 'image/jpeg', 0.9);
    }
};

const closeCamera = () => {
    if (stream.value) {
        stream.value.getTracks().forEach(track => track.stop());
        stream.value = null;
    }
    isCameraOpen.value = false;
    cameraError.value = null;
};

const submitBilling = () => {
    if (isPairCamera.value && !form.second_partida_id) {
        alert('Has marcado la venta del par de cámaras. Por favor selecciona o busca la segunda cámara complementaria antes de enviar.');
        return;
    }

    form.transform((data) => ({
        ...data,
        second_partida_id: isPairCamera.value ? form.second_partida_id : null,
        quantity: isPairCamera.value ? 2 : (data.quantity || 1),
        client_name: data.client_name ? data.client_name.toUpperCase() : '',
        client_cedula: data.client_cedula ? data.client_cedula.toUpperCase() : '',
        client_phone: data.client_phone ? data.client_phone.trim() : '',
        client_email: data.client_email ? data.client_email.trim() : '',
        client_address: data.client_address ? data.client_address.toUpperCase() : '',
        serial: data.serial ? data.serial.toUpperCase() : '',
        observation: data.observation ? data.observation.toUpperCase() : '',
        price: data.price ? data.price.toString().replace(/\./g, '').replace(',', '.') : ''
    })).post(route('billing.requests.store'), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            form.reset('client_name', 'client_cedula', 'client_phone', 'client_email', 'client_address', 'client_cedula_file', 'quantity', 'serial_file', 'serial', 'observation', 'second_partida_id'); isPairCamera.value = false; selectedSecondCamera.value = null;
            if (imagePreviewUrl.value) {
                URL.revokeObjectURL(imagePreviewUrl.value);
                imagePreviewUrl.value = null;
            }
            if (serialPreviewUrl.value) {
                URL.revokeObjectURL(serialPreviewUrl.value);
                serialPreviewUrl.value = null;
            }
            router.visit(route('inventario'));
        },
    });
};
</script>

<template>
    <AppLayout title="Detalle de Registro">
        <template #header>
            <h2 class="font-bold text-2xl text-gray-800 dark:text-white leading-tight flex items-center justify-center gap-2 flex-wrap">
                <i class="fa-solid fa-box text-indigo-500"></i>
                <span>Registro: {{ props.inventario.label_title || (props.inventario.marca + ' ' + props.inventario.modelo) }}</span>
                <span v-if="props.inventario.camera_side" :class="{
                    'bg-blue-600 text-white': props.inventario.camera_side === 'L',
                    'bg-amber-500 text-black': props.inventario.camera_side === 'R'
                }" class="px-2.5 py-0.5 rounded-lg text-xs font-black uppercase tracking-wider ml-2 shadow-sm">
                    LADO {{ props.inventario.camera_side === 'L' ? 'IZQUIERDO (L)' : 'DERECHO (R)' }}
                </span>
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl rounded-2xl border border-gray-100 dark:border-gray-700 p-8">
                    
                    <!-- Detail Layout -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                        
                        <!-- Left: Info -->
                        <div class="lg:col-span-2">
                             <h3 class="text-xl font-bold text-gray-800 dark:text-white mb-6 flex items-center">
                                <i class="fa-solid fa-circle-info mr-2 text-indigo-500"></i>Información General
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-list-check mr-1"></i>Tipo de Registro
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.tipo }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-copyright mr-1"></i>Marca
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.marca }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-car mr-1"></i>Modelo
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.modelo }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2 flex items-center justify-between">
                                        <span><i class="fa-solid fa-barcode mr-1"></i>Serial</span>
                                        <span v-if="props.inventario.serial_image_url" class="text-[10px] text-indigo-500 font-bold flex items-center gap-1 cursor-pointer hover:underline" @click="showSerialZoomModal = true">
                                            <i class="fa-solid fa-camera"></i> Ver Foto
                                        </span>
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                                        <span :class="{'text-indigo-600 dark:text-indigo-400 font-bold': props.inventario.serial_image_url && (!props.inventario.serial || ['S/S', 'SIN SERIAL', 'NO APARENTE SERIAL', 'NO APARENTA SERIAL'].includes(props.inventario.serial.toUpperCase()))}">
                                            {{ serialDisplayText }}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-calendar-check mr-1"></i>Año
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.año }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-truck-pickup mr-1"></i>Tipo de Vehículo
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 font-semibold border border-gray-100 dark:border-gray-700 min-h-[46px] flex flex-col justify-center">
                                        <span v-if="loadingVehicleType" class="text-xs text-gray-400 flex items-center">
                                            <i class="fa-solid fa-circle-notch fa-spin mr-2"></i>Consultando API...
                                        </span>
                                        <div v-else>
                                            <span class="font-bold">{{ vehicleType }}</span>
                                            <span v-if="vehicleExample" class="text-xs text-gray-400 block mt-0.5 font-normal">
                                                Ejemplo: {{ vehicleExample }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-barcode mr-1"></i>Inventario
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-indigo-600 dark:text-indigo-400 rounded-xl py-3 px-4 font-bold border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.codInv }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-file-invoice mr-1"></i>Expediente
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.expediente }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-check-to-slot mr-1"></i>Condición
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-white rounded-xl py-3 px-4 border border-gray-100 dark:border-gray-700">
                                        {{ props.inventario.condicion }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-signal mr-1"></i>Estatus
                                    </label>
                                    <div class="flex">
                                        <span :class="{
                                            'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300': props.inventario.status === 'DISPONIBLE',
                                            'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300': props.inventario.status === 'VENDIDO',
                                            'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300': props.inventario.status === 'EN TALLER',
                                            'bg-slate-100 text-slate-800 dark:bg-slate-900/40 dark:text-slate-300': props.inventario.status === 'INOPERATIVO-DESARMADO',
                                            'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300': props.inventario.status === 'USO INTERNO',
                                        }" class="px-4 py-2 rounded-xl text-sm font-bold uppercase border border-transparent">
                                            {{ props.inventario.status }}
                                        </span>
                                    </div>
                                </div>
                                <div v-if="props.inventario.origen === 'IMPORTADO' || props.inventario.costo_importacion_unitario > 0">
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-money-bill-transfer mr-1"></i>Costo de Importación + Mantenimiento
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-300 rounded-xl py-3.5 px-5 border border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                        <div class="flex flex-col">
                                            <span class="text-[9px] text-gray-400 uppercase tracking-wide">Costo Total (Bs.)</span>
                                            <span class="text-xl font-black text-indigo-600 dark:text-indigo-400">Bs. {{ baseImponible.toLocaleString('es-VE', {minimumFractionDigits: 2}) }}</span>
                                        </div>
                                        
                                        <div class="flex items-center gap-6">
                                            <!-- Detailed Breakdown -->
                                            <div v-if="props.inventario.costo_taller > 0" class="flex flex-col text-right text-[11px] text-gray-500 font-semibold gap-0.5">
                                                <span>Importación: Bs. {{ parseFloat(props.inventario.costo_importacion_unitario || 0).toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
                                                <span>Taller: Bs. {{ parseFloat(props.inventario.costo_taller || 0).toLocaleString('es-VE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}</span>
                                            </div>

                                            <!-- Conversion to Dolar -->
                                            <div v-if="props.tasa_bcv > 0" class="flex flex-col items-end border-l border-gray-200 dark:border-gray-700 pl-4">
                                                <span class="text-[9px] text-indigo-500 dark:text-indigo-400 uppercase font-black">Conversión BCV (USD)</span>
                                                <span class="text-xs font-black text-indigo-600 dark:text-indigo-400">
                                                    $ {{ (baseImponible / parseFloat(props.tasa_bcv)).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) }}
                                                </span>
                                                <span class="text-[8px] text-gray-400 font-medium">Tasa: Bs. {{ parseFloat(props.tasa_bcv).toFixed(2) }}</span>
                                                <span v-if="props.inventario.fecha_tasa_bcv" class="text-[8px] text-indigo-500 font-bold">Fecha Tasa: {{ props.inventario.fecha_tasa_bcv }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-money-bill-transfer mr-1"></i>Costo de Adquisición
                                    </label>
                                    <div class="w-full bg-gray-50 dark:bg-gray-700/50 text-gray-800 dark:text-gray-300 rounded-xl py-3 px-4 font-bold border border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                        <span>${{ parseFloat(props.inventario.costo || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}</span>
                                    </div>
                                </div>
                                <div>
                                    <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                        <i class="fa-solid fa-tag mr-1"></i>Precio de Venta Sugerido
                                    </label>
                                    <div class="w-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 rounded-xl py-3 px-4 text-xl font-black border border-indigo-100 dark:border-indigo-900/30">
                                        ${{ parseFloat(props.inventario.price_sale || 0).toLocaleString('en-US', {minimumFractionDigits: 2}) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Observation Display -->
                            <div v-if="props.inventario.observation" class="mt-6 p-6 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                                <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                    <i class="fa-solid fa-comment-dots mr-1 text-indigo-500"></i>Observaciones
                                </label>
                                <div class="text-sm font-semibold text-gray-800 dark:text-white leading-relaxed whitespace-pre-line">
                                    {{ props.inventario.observation }}
                                </div>
                            </div>

                            <!-- Serial Image Display -->
                            <div v-if="props.inventario.serial_image_url" class="mt-6 p-6 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700">
                                <label class="block uppercase tracking-wide text-gray-500 dark:text-gray-400 text-xs font-bold mb-2">
                                    <i class="fa-solid fa-camera mr-1 text-indigo-500"></i>Foto de Serial de Motor/Caja
                                </label>
                                <div class="relative group cursor-zoom-in w-full max-w-[300px] mt-2 block">
                                    <img :src="props.inventario.serial_image_url" class="rounded-xl shadow-lg border-2 border-white dark:border-gray-800 transition-transform group-hover:scale-[1.02] w-full h-auto" alt="Serial de Motor/Caja" @click="showSerialZoomModal = true">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center rounded-xl transition-opacity" @click="showSerialZoomModal = true">
                                        <i class="fa-solid fa-magnifying-glass-plus text-white text-2xl"></i>
                                    </div>
                                </div>
                            </div>

                            <!-- Codes Row -->
                            <div v-if="showQrCodes" class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-8 p-6 bg-gray-50 dark:bg-gray-900/30 rounded-2xl border border-gray-100 dark:border-gray-700">
                                <div class="text-center flex flex-col items-center">
                                    <h4 class="font-bold mb-4 text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400">Código QR</h4>
                                    <div class="inline-block bg-white p-4 rounded-2xl shadow-inner border border-gray-200 dark:border-gray-700 relative">
                                        <div v-html="props.qrCode"></div>
                                        <!-- Side / Type Indicator -->
                                        <div v-if="props.inventario.camera_side" class="mt-3 inline-flex items-center justify-center gap-2 px-3.5 py-1.5 rounded-xl bg-gray-950 text-white font-black text-xs tracking-wider uppercase shadow-md border border-gray-800">
                                            <span class="text-amber-400 text-sm font-black">{{ props.inventario.camera_side }}</span>
                                            <span class="text-[10px] text-gray-200">LADO {{ props.inventario.camera_side === 'L' ? 'IZQUIERDO' : 'DERECHO' }}</span>
                                        </div>
                                        <div v-else-if="isCamara" class="mt-3 inline-flex items-center px-3 py-1 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-800 dark:text-purple-300 text-[10px] font-black uppercase">
                                            CÁMARA / CULATA
                                        </div>
                                        <div v-else-if="isCaja" class="mt-3 inline-flex items-center px-3 py-1 rounded-xl bg-cyan-100 dark:bg-cyan-900/40 text-cyan-800 dark:text-cyan-300 text-[10px] font-black uppercase">
                                            CAJA / TRANSMISIÓN
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center flex flex-col items-center justify-between">
                                    <div>
                                        <h4 class="font-bold mb-4 text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400">Código de Barras</h4>
                                        <div class="inline-block bg-white p-4 rounded-xl shadow-inner border border-gray-200 dark:border-gray-700">
                                            <div v-html="props.barcode"></div>
                                            <p class="text-[10px] font-mono text-black mt-2 tracking-[0.2em]">{{ props.barcodeData }}</p>
                                        </div>
                                    </div>
                                    <div class="mt-6">
                                        <a :href="route('printInventario', props.inventario.id)" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-lg hover:shadow-indigo-500/30">
                                            <i class="fa-solid fa-print mr-2"></i>Imprimir Etiqueta
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Actions/Billing -->
                        <div class="lg:col-span-1">
                             <div class="bg-indigo-600 dark:bg-indigo-900/40 p-8 rounded-3xl shadow-2xl dark:shadow-none text-white sticky top-8 border border-indigo-400 dark:border-indigo-800">
                                <h3 class="text-xl font-bold mb-6 flex items-center">
                                    <i class="fa-solid fa-paper-plane mr-2"></i>Facturación
                                </h3>
                                
                                <div v-if="props.inventario.status === 'INOPERATIVO-DESARMADO'" class="bg-white/10 dark:bg-black/20 p-6 rounded-2xl border border-white/20 dark:border-indigo-800/50 flex flex-col items-center text-center space-y-4">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-ban text-3xl opacity-55"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg">Inoperativo / Desarmado</p>
                                        <p class="text-sm opacity-80 mt-1">Este ítem se encuentra inoperativo o desarmado. No está disponible para la venta o facturación.</p>
                                    </div>
                                </div>

                                <div v-else-if="props.inventario.status === 'USO INTERNO'" class="bg-white/10 dark:bg-black/20 p-6 rounded-2xl border border-white/20 dark:border-indigo-800/50 flex flex-col items-center text-center space-y-4">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-building-user text-3xl opacity-55"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg">Uso Interno</p>
                                        <p class="text-sm opacity-80 mt-1">Este ítem está asignado para uso interno de la empresa. No está disponible para la venta o facturación.</p>
                                    </div>
                                </div>

                                <div v-else-if="props.inventario.status === 'GARANTIA' || props.inventario.status === 'GARANTÍA'" class="bg-white/10 dark:bg-black/20 p-6 rounded-2xl border border-white/20 dark:border-indigo-800/50 flex flex-col items-center text-center space-y-4">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-shield-halved text-3xl opacity-55"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg">En Garantía</p>
                                        <p class="text-sm opacity-80 mt-1">Este ítem se encuentra bajo proceso de garantía y servicio técnico.</p>
                                    </div>
                                </div>

                                <div v-else-if="alreadyRequested || props.inventario.status === 'VENDIDO'" class="bg-white/10 dark:bg-black/20 p-6 rounded-2xl border border-white/20 dark:border-indigo-800/50 flex flex-col items-center text-center space-y-4">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-lock text-3xl opacity-50"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg">Vendido o Solicitado</p>
                                        <p class="text-sm opacity-80 mt-1">Este ítem ya ha sido procesado o tiene una solicitud pendiente de revisión.</p>
                                    </div>
                                </div>

                                <form v-else-if="canRequestBilling" @submit.prevent="submitBilling" class="space-y-5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">Precio Final ($)</label>
                                            <input v-model="form.price" type="text" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none font-bold" placeholder="0.00" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80 flex items-center justify-between">
                                                <span>Tasa BCV (Bs./$)</span>
                                                <span v-if="fetchingRate" class="text-[10px] lowercase text-amber-300 font-normal">buscando...</span>
                                            </label>
                                            <input v-model="form.tasa_bcv" type="number" step="0.01" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none font-bold" placeholder="0.00">
                                        </div>
                                    </div>
                                    
                                    <div>
                                        <label class="block text-xs font-bold mb-2 uppercase opacity-80">Fecha Tasa BCV</label>
                                        <input v-model="form.fecha_tasa_bcv" type="date" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none">
                                    </div>

                                    <div v-if="parseFloat(form.price) > 0 && parseFloat(form.tasa_bcv) > 0" class="p-3 bg-emerald-500/20 rounded-xl border border-emerald-500/40 flex items-center justify-between text-xs font-bold">
                                        <span class="opacity-90 uppercase">Monto Estimado en Bs.:</span>
                                        <span class="text-emerald-300 text-sm font-black">Bs. {{ montoCalculadoBs }}</span>
                                    </div>
                                    <div class="grid grid-cols-1 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">Cédula / RIF</label>
                                            <input v-model="form.client_cedula" type="text" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none" placeholder="V-12345678" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">Razón Social / Nombre</label>
                                            <input v-model="form.client_name" type="text" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none" placeholder="Nombre completo" required>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold mb-2 uppercase opacity-80">Capture de Cédula / RIF (Opcional)</label>
                                        
                                        <!-- Image Preview with Deletion Option -->
                                        <div v-if="imagePreviewUrl" class="relative mt-2 p-2 bg-white/10 border border-white/20 rounded-2xl overflow-hidden flex flex-col items-center">
                                            <img :src="imagePreviewUrl" class="max-h-48 w-full object-contain rounded-xl" alt="Preview de Cédula">
                                            <button type="button" @click="removeImage" class="mt-2 w-full flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-600 text-white font-bold py-2 rounded-xl transition-all active:scale-[0.98]">
                                                <i class="fa-solid fa-trash"></i> Eliminar Foto
                                            </button>
                                        </div>
                                        
                                        <!-- Upload / Shutter Options -->
                                        <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1">
                                            <!-- File Upload Wrapper -->
                                            <label class="flex flex-col items-center justify-center p-3 bg-white/10 hover:bg-white/20 border border-dashed border-white/30 hover:border-white/50 rounded-2xl cursor-pointer transition-all text-center">
                                                <i class="fa-solid fa-upload text-xl mb-1 opacity-80"></i>
                                                <span class="text-[10px] font-bold uppercase tracking-wider">Cargar Archivo</span>
                                                <input type="file" accept="image/*" class="hidden" @change="handleFileChange">
                                            </label>
                                            
                                            <!-- Camera Button -->
                                            <button type="button" @click="startCamera('cedula')" class="flex flex-col items-center justify-center p-3 bg-white/10 hover:bg-white/20 border border-dashed border-white/30 hover:border-white/50 rounded-2xl transition-all text-center">
                                                <i class="fa-solid fa-camera text-xl mb-1 opacity-80"></i>
                                                <span class="text-[10px] font-bold uppercase tracking-wider">Tomar Foto</span>
                                            </button>
                                        </div>
                                        <p v-if="!imagePreviewUrl" class="mt-2 text-[10px] text-white/50 text-center">Formatos permitidos: JPG, PNG (Máx 2MB)</p>
                                    </div>

                                    <!-- Serial fields ONLY for Motors (No serial for Cajas or Cámaras) -->
                                    <template v-if="isMotor">
                                        <div>
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">
                                                Serial de Motor (Opcional)
                                            </label>
                                            <input v-model="form.serial" type="text" @input="form.serial = form.serial.toUpperCase()" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none uppercase font-bold" placeholder="Ej: 4G63-XXXXXX">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">
                                                Foto de Serial de Motor (Opcional)
                                            </label>
                                            
                                            <!-- Serial Image Preview with Deletion Option -->
                                            <div v-if="serialPreviewUrl" class="relative mt-2 p-2 bg-white/10 border border-white/20 rounded-2xl overflow-hidden flex flex-col items-center">
                                                <img :src="serialPreviewUrl" class="max-h-48 w-full object-contain rounded-xl" alt="Preview de Serial">
                                                <button type="button" @click="removeSerialImage" class="mt-2 w-full flex items-center justify-center gap-2 bg-rose-500 hover:bg-rose-600 text-white font-bold py-2 rounded-xl transition-all active:scale-[0.98]">
                                                    <i class="fa-solid fa-trash"></i> Eliminar Foto
                                                </button>
                                            </div>
                                            
                                            <!-- Upload / Shutter Options -->
                                            <div v-else class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-1">
                                                <label class="flex flex-col items-center justify-center p-3 bg-white/10 hover:bg-white/20 border border-dashed border-white/30 hover:border-white/50 rounded-2xl cursor-pointer transition-all text-center">
                                                    <i class="fa-solid fa-upload text-xl mb-1 opacity-80"></i>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider">Cargar Archivo</span>
                                                    <input type="file" accept="image/*" class="hidden" @change="handleSerialFileChange">
                                                </label>
                                                
                                                <button type="button" @click="startCamera('serial')" class="flex flex-col items-center justify-center p-3 bg-white/10 hover:bg-white/20 border border-dashed border-white/30 hover:border-white/50 rounded-2xl transition-all text-center">
                                                    <i class="fa-solid fa-camera text-xl mb-1 opacity-80"></i>
                                                    <span class="text-[10px] font-bold uppercase tracking-wider">Tomar Foto</span>
                                                </button>
                                            </div>
                                            <p v-if="!serialPreviewUrl" class="mt-2 text-[10px] text-white/50 text-center">Formatos permitidos: JPG, PNG (Máx 2MB)</p>
                                        </div>
                                    </template>
                                    <template v-else-if="isCaja">
                                        <div class="p-3.5 bg-white/10 border border-white/20 rounded-2xl flex items-start gap-3 text-xs">
                                            <i class="fa-solid fa-boxes text-amber-300 text-base mt-0.5"></i>
                                            <div>
                                                <p class="font-bold text-white uppercase tracking-wide">Caja de Cambios Sin Serial Numérico</p>
                                                <p class="text-white/70 text-[11px] mt-0.5">Las cajas se identifican únicamente por su código de inventario ({{ props.inventario.codInv }}). No requieren serial numérico ni foto para la facturación.</p>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else-if="isCamara">
                                        <div class="p-3.5 bg-white/10 border border-white/20 rounded-2xl flex items-start gap-3 text-xs">
                                            <i class="fa-solid fa-layer-group text-blue-300 text-base mt-0.5"></i>
                                            <div>
                                                <p class="font-bold text-white uppercase tracking-wide">Cámara / Culata Sin Serial Numérico</p>
                                                <p class="text-white/70 text-[11px] mt-0.5">Las cámaras se identifican con su código de inventario ({{ props.inventario.codInv }}) y lado. No requieren serial numérico ni foto.</p>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- Venta del Par de Cámaras (Ambos lados L + R) -->
                                    <div v-if="isCamara" class="p-4 bg-purple-950/40 border border-purple-400/40 rounded-2xl backdrop-blur-sm space-y-3">
                                        <div class="flex items-center justify-between gap-3">
                                            <div class="flex items-center gap-2.5">
                                                <div class="h-8 w-8 rounded-xl bg-purple-500/30 border border-purple-400/40 flex items-center justify-center text-purple-200">
                                                    <i class="fa-solid fa-link text-xs"></i>
                                                </div>
                                                <div>
                                                    <p class="text-xs font-bold text-white uppercase tracking-wide">¿Facturar el Par de Cámaras? (Juego L + R)</p>
                                                    <p class="text-[10px] text-purple-200/80">Activa esta opción si el cliente compra ambos lados.</p>
                                                </div>
                                            </div>
                                            <label class="relative inline-flex cursor-pointer items-center">
                                                <input type="checkbox" v-model="isPairCamera" @change="onTogglePairCamera" class="peer sr-only">
                                                <div class="peer h-6 w-11 rounded-full bg-white/20 after:absolute after:top-[2px] after:left-[2px] after:h-5 after:w-5 after:rounded-full after:bg-white after:transition-all after:content-[''] peer-checked:bg-purple-500 peer-checked:after:translate-x-full peer-focus:outline-none"></div>
                                            </label>
                                        </div>

                                        <!-- Panel de selección de la segunda cámara -->
                                        <div v-if="isPairCamera" class="pt-3 border-t border-purple-400/20 space-y-3">
                                            <div>
                                                <label class="block text-[10px] font-bold mb-1.5 uppercase tracking-wider text-purple-200">
                                                    Seleccionar Segunda Cámara del Inventario Disponible
                                                </label>
                                                <select 
                                                    v-model="form.second_partida_id"
                                                    @change="onSelectSecondCameraFromDropdown"
                                                    class="block w-full bg-slate-900 border border-purple-400/50 rounded-xl py-2.5 px-3 text-xs text-white focus:ring-2 focus:ring-purple-400 outline-none"
                                                >
                                                    <option :value="null">-- Seleccionar cámara del listado disponible --</option>
                                                    <option v-for="cam in suggestedCameras" :key="cam.id" :value="cam.id">
                                                        {{ cam.label }}
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="flex items-center gap-2">
                                                <div class="relative flex-1">
                                                    <input 
                                                        v-model="secondCodInput"
                                                        @keyup.enter="searchSecondCameraByCode"
                                                        type="text"
                                                        placeholder="O escribe el código de inventario (ej: 628)"
                                                        class="block w-full bg-white/10 border border-white/20 rounded-xl py-2 px-3 text-xs text-white placeholder-white/40 focus:ring-2 focus:ring-purple-400 outline-none"
                                                    >
                                                </div>
                                                <button 
                                                    type="button" 
                                                    @click="searchSecondCameraByCode"
                                                    :disabled="searchingSecond"
                                                    class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 cursor-pointer"
                                                >
                                                    <i v-if="searchingSecond" class="fa-solid fa-spinner fa-spin"></i>
                                                    <i v-else class="fa-solid fa-search"></i>
                                                    Buscar
                                                </button>
                                            </div>

                                            <!-- Feedback de cámara seleccionada -->
                                            <div v-if="selectedSecondCamera" class="p-3 bg-emerald-950/60 border border-emerald-400/50 rounded-xl flex items-start justify-between text-xs text-emerald-100">
                                                <div class="flex items-start gap-2">
                                                    <i class="fa-solid fa-circle-check text-emerald-400 text-sm mt-0.5"></i>
                                                    <div>
                                                        <div class="font-bold text-white flex items-center gap-2">
                                                            <span>Segunda Cámara Vinculada:</span>
                                                            <span class="px-1.5 py-0.2 rounded text-[10px] font-black uppercase" :class="selectedSecondCamera.side === 'L' ? 'bg-blue-600 text-white' : 'bg-amber-500 text-black'">
                                                                LADO {{ selectedSecondCamera.side_label || selectedSecondCamera.side || 'L/R' }}
                                                            </span>
                                                        </div>
                                                        <p class="mt-0.5 text-white/80">
                                                            [Cod: {{ selectedSecondCamera.formatted_cod || selectedSecondCamera.codInv }}] {{ selectedSecondCamera.marca }} {{ selectedSecondCamera.modelo }} {{ selectedSecondCamera.observation ? '· ' + selectedSecondCamera.observation : '' }}
                                                        </p>
                                                        <p class="text-[10px] text-emerald-300 mt-1 font-semibold">
                                                            ✓ Al procesar la factura, ambas cámaras pasarán automáticamente a estatus VENDIDO.
                                                        </p>
                                                    </div>
                                                </div>
                                                <button type="button" @click="clearSecondCamera" class="text-white/60 hover:text-rose-400 transition ml-2">
                                                    <i class="fa-solid fa-xmark"></i>
                                                </button>
                                            </div>

                                            <div v-else-if="secondSearchError" class="p-2.5 bg-rose-900/60 border border-rose-400/40 rounded-xl text-xs text-rose-200 flex items-center gap-2">
                                                <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                                                <span>{{ secondSearchError }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="flex flex-col justify-end">
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">Teléfono (Opcional)</label>
                                            <input v-model="form.client_phone" type="text" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none" placeholder="0412-1234567">
                                        </div>
                                        <div class="flex flex-col justify-end">
                                            <label class="block text-xs font-bold mb-2 uppercase opacity-80">Correo Electrónico (Opcional)</label>
                                            <input v-model="form.client_email" type="email" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none" placeholder="cliente@correo.com">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold mb-2 uppercase opacity-80">Dirección (Opcional)</label>
                                        <textarea v-model="form.client_address" rows="2" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none resize-none" placeholder="Dirección completa del cliente"></textarea>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-bold mb-2 uppercase opacity-80">
                                            {{ isCamara ? 'Detalle y Lado de Despacho' : 'Detalles de Despacho (Cómo sale)' }}
                                        </label>
                                        <select v-if="isMotor || isCaja || isCamara" v-model="form.observation" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white focus:ring-2 focus:ring-white outline-none font-bold text-sm" required>
                                            <option value="" disabled class="text-gray-800">SELECCIONE UNA OPCIÓN</option>
                                            <template v-if="isMotor">
                                                <option value="MOTOR COMPLETO" class="text-gray-800">MOTOR COMPLETO</option>
                                                <option value="MOTOR 7/8" class="text-gray-800">MOTOR 7/8</option>
                                                <option value="MOTOR 3/4" class="text-gray-800">MOTOR 3/4</option>
                                            </template>
                                            <template v-else-if="isCaja">
                                                <option value="CAJA COMPLETA" class="text-gray-800">CAJA COMPLETA</option>
                                                <option value="CAJA SIN TURBINA" class="text-gray-800">CAJA SIN TURBINA</option>
                                            </template>
                                            <template v-else-if="isCamara">
                                                <option value="CÁMARA COMPLETA - LADO DERECHO (R)" class="text-gray-800">CÁMARA COMPLETA - LADO DERECHO (R)</option>
                                                <option value="CÁMARA COMPLETA - LADO IZQUIERDO (L)" class="text-gray-800">CÁMARA COMPLETA - LADO IZQUIERDO (L)</option>
                                                <option value="SOLO CAPARAZÓN - LADO DERECHO (R)" class="text-gray-800">SOLO CAPARAZÓN - LADO DERECHO (R)</option>
                                                <option value="SOLO CAPARAZÓN - LADO IZQUIERDO (L)" class="text-gray-800">SOLO CAPARAZÓN - LADO IZQUIERDO (L)</option>
                                                <option value="CÁMARA COMPLETA" class="text-gray-800">CÁMARA COMPLETA (UN SOLO LADO)</option>
                                                <option value="SOLO CAPARAZÓN" class="text-gray-800">SOLO CAPARAZÓN (UN SOLO LADO)</option>
                                                <option v-if="form.observation && !['CÁMARA COMPLETA - LADO IZQUIERDO (L)', 'CÁMARA COMPLETA - LADO DERECHO (R)', 'SOLO CAPARAZÓN - LADO IZQUIERDO (L)', 'SOLO CAPARAZÓN - LADO DERECHO (R)', 'CÁMARA COMPLETA', 'SOLO CAPARAZÓN'].includes(form.observation)" :value="form.observation" class="text-gray-800">
                                                    {{ form.observation }} (REGISTRADO)
                                                </option>
                                            </template>
                                        </select>
                                        <textarea v-else v-model="form.observation" @input="form.observation = form.observation.toUpperCase()" rows="2" class="block w-full bg-white/10 border border-white/20 rounded-xl py-3 px-4 text-white placeholder-white/40 focus:ring-2 focus:ring-white outline-none resize-none uppercase" placeholder="Ej: Observación de la venta..."></textarea>
                                    </div>

                                    <!-- Warranty disclaimer for non-motors -->
                                    <div v-if="!isMotor" class="p-3.5 bg-amber-500/20 border border-amber-400/40 rounded-2xl flex items-center gap-3 text-xs text-amber-100">
                                        <i class="fa-solid fa-shield-halved text-amber-300 text-lg flex-shrink-0"></i>
                                        <div>
                                            <span class="font-bold uppercase tracking-wider block text-[11px] text-amber-200">Garantía Exclusiva para Motores</span>
                                            <span class="text-[11px] text-white/80">Este ítem ({{ isCamara ? 'Cámara' : (isCaja ? 'Caja' : 'Repuesto') }}) no genera Póliza de Garantía. Solo se emitirá la factura fiscal/comercial.</span>
                                        </div>
                                    </div>
                                    
                                    <button type="submit" :disabled="form.processing" class="w-full bg-white text-indigo-700 font-bold py-4 px-6 rounded-2xl shadow-xl transition-all transform hover:scale-[1.05] active:scale-95 disabled:opacity-50 flex items-center justify-center text-lg">
                                        <i class="fa-solid fa-circle-check mr-2"></i>{{ form.processing ? 'Enviando...' : 'Solicitar Venta' }}
                                    </button>
                                </form>
                                <div v-else class="bg-white/10 dark:bg-black/20 p-6 rounded-2xl border border-white/20 dark:border-indigo-800/50 flex flex-col items-center text-center space-y-4">
                                    <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center">
                                        <i class="fa-solid fa-lock text-3xl opacity-50"></i>
                                    </div>
                                    <div>
                                        <p class="font-bold text-lg">Solo Lectura</p>
                                        <p class="text-sm opacity-80 mt-1">No tiene permisos para solicitar la facturación de este ítem.</p>
                                    </div>
                                </div>
                             </div>
                        </div>
                    </div>

                    <!-- Maintenance History (Full width bottom) -->
                    <div class="mt-16 border-t border-gray-100 dark:border-gray-700 pt-12">
                        <div class="flex items-center justify-between mb-8">
                            <h3 class="text-2xl font-black text-gray-800 dark:text-white flex items-center">
                                <i class="fa-solid fa-wrench mr-3 text-indigo-500"></i>Bitácora de Mantenimiento
                            </h3>
                        </div>

                        <div v-if="props.inventario.maintenances && props.inventario.maintenances.length > 0" class="overflow-x-auto rounded-2xl border border-gray-100 dark:border-gray-700">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Fecha</th>
                                        <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Servicio</th>
                                        <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Estado</th>
                                        <th class="px-6 py-4 text-left text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Técnico</th>
                                        <th class="px-6 py-4 text-right text-xs font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-100 dark:divide-gray-700">
                                    <tr v-for="maintenance in props.inventario.maintenances" :key="maintenance.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ maintenance.fecha }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-indigo-600 dark:text-indigo-400 font-black">{{ maintenance.tipo }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 text-xs font-black rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                                {{ maintenance.status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ maintenance.nombre_mecanico }} {{ maintenance.apellido_mecanico }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <a :href="route('maintenance.show', maintenance.id)" class="bg-gray-100 dark:bg-gray-700 px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 font-bold hover:bg-indigo-500 hover:text-white transition-all">Ver Detalle</a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div v-else class="text-center py-16 bg-gray-50 dark:bg-gray-900/30 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                             <div class="bg-gray-100 dark:bg-gray-800 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fa-solid fa-folder-open text-gray-400 text-2xl"></i>
                             </div>
                             <p class="text-gray-500 dark:text-gray-400 font-bold">Sin registros de mantenimiento</p>
                             <p class="text-sm text-gray-400 mt-1">Este equipo no ha sido ingresado al taller hasta el momento.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <!-- Camera Modal Overlay -->
        <div v-if="isCameraOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/90 backdrop-blur-md">
            <div class="relative bg-gray-905 bg-slate-900 border border-slate-800 rounded-[2.5rem] overflow-hidden max-w-lg w-full p-6 flex flex-col items-center shadow-2xl">
                <div class="w-full flex justify-between items-center mb-4">
                    <h4 class="text-white font-black text-lg uppercase tracking-tight flex items-center">
                        <i class="fa-solid fa-camera mr-2 text-indigo-500"></i>Tomar Foto de {{ cameraTarget === 'cedula' ? 'Cédula' : 'Serial de Motor/Caja' }}
                    </h4>
                    <button type="button" @click="closeCamera" class="text-gray-400 hover:text-white transition-colors">
                        <i class="fa-solid fa-times text-lg"></i>
                    </button>
                </div>
                
                <!-- Camera Feed / Error -->
                <div class="relative w-full aspect-[4/3] rounded-3xl bg-black overflow-hidden border border-slate-800 flex items-center justify-center shadow-inner">
                    <video ref="videoElement" autoplay playsinline class="w-full h-full object-cover"></video>
                    <div v-if="cameraError" class="absolute inset-0 flex flex-col items-center justify-center p-6 text-center text-rose-500 bg-black/90">
                        <i class="fa-solid fa-triangle-exclamation text-3xl mb-2"></i>
                        <p class="font-bold text-sm">{{ cameraError }}</p>
                    </div>
                </div>
                
                <canvas ref="canvasElement" class="hidden"></canvas>
                
                <!-- Shutter & Controls -->
                <div class="w-full flex justify-center items-center gap-6 mt-6">
                    <button type="button" @click="closeCamera" class="px-6 py-3 bg-slate-800 hover:bg-slate-700 text-gray-300 font-bold rounded-2xl transition-colors">
                        Cancelar
                    </button>
                    <button type="button" @click="capturePhoto" :disabled="cameraError" class="h-16 w-16 bg-white hover:bg-gray-100 rounded-full flex items-center justify-center text-gray-900 hover:scale-105 active:scale-95 transition-all shadow-xl disabled:opacity-50 disabled:pointer-events-none" title="Capturar Foto">
                        <div class="h-12 w-12 rounded-full border-4 border-slate-900 bg-white"></div>
                    </button>
                    <div class="w-[92px]"></div> <!-- Spacer to center the shutter button -->
                </div>
            </div>
        </div>

        <!-- Serial Zoom Modal -->
        <div v-if="showSerialZoomModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4 sm:p-10">
            <div class="fixed inset-0 bg-gray-950/90 backdrop-blur-md transition-opacity" @click="showSerialZoomModal = false"></div>
            <div class="relative max-w-4xl w-full max-h-screen bg-transparent rounded-2xl overflow-hidden flex flex-col items-center justify-center z-10 animate-in zoom-in-95 duration-200">
                <img :src="props.inventario.serial_image_url" class="max-w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl border-4 border-slate-900" alt="Serial de Motor/Caja Ampliado">
                <button @click="showSerialZoomModal = false" class="mt-4 px-6 py-2 bg-slate-900/80 hover:bg-slate-950 text-white font-bold rounded-xl transition-all shadow-lg flex items-center gap-2 border border-slate-800">
                    <i class="fa-solid fa-times"></i> Cerrar Vista
                </button>
            </div>
        </div>
    </AppLayout>
</template>