<script setup>
import { useI18n } from '../i18n';

const { t } = useI18n();
const appName = import.meta.env.VITE_APP_NAME ?? 'WhoisScope';
</script>

<template>
    <footer class="border-t border-slate-200 bg-white py-8 mt-auto">
        <div class="mx-auto max-w-5xl px-4 text-center text-sm text-slate-500">
            <p>&copy; {{ new Date().getFullYear() }} {{ appName }}</p>
            <p class="mt-1">{{ t('footer.tagline') }}</p>
        </div>
    </footer>
</template>
