<script setup>
import AppHeader from './components/AppHeader.vue';
import AppFooter from './components/AppFooter.vue';
import { RouterView } from 'vue-router';
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <AppHeader />
        <main class="flex-1">
            <RouterView />
        </main>
        <AppFooter />
    </div>
</template>
