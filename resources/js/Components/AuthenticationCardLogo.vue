<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <Link :href="'/'" class="flex flex-col items-center gap-2 no-underline group">
        <div class="logo-outer">
            <div class="logo-inner">
                <i class="fas fa-car-side text-2xl text-blue-500 group-hover:scale-110 transition-transform"></i>
            </div>
        </div>
        <span class="brand-name">MAIKEL <span class="text-blue-500">CARS</span></span>
        <span class="brand-subtitle text-xs text-gray-400 font-medium tracking-widest uppercase">Sistema Interno</span>
    </Link>
</template>

<style scoped>
.logo-outer {
    width: 64px;
    height: 64px;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(30, 64, 175, 0.2));
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(59, 130, 246, 0.3);
    padding: 2px;
}

.logo-inner {
    width: 100%;
    height: 100%;
    background: #1e293b;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.3);
}

.brand-name {
    font-family: 'Orbitron', sans-serif;
    color: white;
    font-size: 1.5rem;
    font-weight: bold;
    letter-spacing: 2px;
}

.brand-subtitle {
     letter-spacing: 5px;
     margin-top: -5px;
}
</style>
