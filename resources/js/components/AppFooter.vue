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
            <p class="mt-2">
                <a
                    href="https://opensource.org/licenses/MIT"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="text-sky-600 hover:text-sky-700 transition-colors"
                >
                    {{ t('footer.license') }}
                </a>
            </p>
        </div>
    </footer>
</template>
