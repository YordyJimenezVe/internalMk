<template>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-dark-gradient">
        <div class="animate__animated animate__fadeInDown">
            <slot name="logo" />
        </div>

        <div class="w-full sm:max-w-md mt-6 px-8 py-8 glass-card shadow-2xl overflow-hidden sm:rounded-2xl animate__animated animate__fadeInUp">
            <slot />
        </div>
    </div>
</template>

<style scoped>
.bg-dark-gradient {
    background: radial-gradient(circle at top right, #1e293b, #0f172a);
    background-color: #0f172a;
}

.glass-card {
    background: rgba(30, 41, 59, 0.7);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}
</style>
