<script setup>
import AppLogo from './AppLogo.vue';
import LanguageSwitcher from './LanguageSwitcher.vue';
import { useI18n } from '../i18n';
import { RouterLink } from 'vue-router';

const { t } = useI18n();
const appName = import.meta.env.VITE_APP_NAME ?? 'WhoisScope';
</script>

<template>
    <header class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="mx-auto max-w-5xl px-4 h-16 flex items-center justify-between gap-4">
            <RouterLink to="/" class="flex items-center gap-2.5 hover:opacity-90 transition-opacity">
                <AppLogo />
                <span class="font-bold text-lg text-slate-900 tracking-tight">{{ appName }}</span>
            </RouterLink>

            <div class="flex items-center gap-3 sm:gap-5">
                <nav class="flex items-center gap-4 sm:gap-6 text-sm text-slate-600">
                    <RouterLink to="/" class="hover:text-sky-600 transition-colors hidden sm:inline">
                        {{ t('nav.home') }}
                    </RouterLink>
                    <RouterLink
                        to="/docs"
                        class="hover:text-sky-600 transition-colors font-medium"
                        active-class="text-sky-600"
                    >
                        {{ t('nav.apiDocs') }}
                    </RouterLink>
                </nav>
                <LanguageSwitcher />
            </div>
        </div>
    </header>
</template>
