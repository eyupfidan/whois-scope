<script setup>
import { useI18n } from '../i18n';
import { useRoute, useRouter } from 'vue-router';

const { t, locale, locales, setLocale } = useI18n();
const route = useRoute();
const router = useRouter();

function changeLocale(code) {
    setLocale(code);
    router.push({ name: route.name === 'docs' ? 'docs' : 'home', params: { locale: code } });
}
</script>

<template>
    <div class="relative">
        <select
            :value="locale"
            class="appearance-none rounded-lg border border-slate-200 bg-slate-50 pl-3 pr-8 py-1.5 text-xs font-medium text-slate-700 cursor-pointer hover:border-sky-300 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition"
            :aria-label="t('nav.language')"
            @change="changeLocale($event.target.value)"
        >
            <option v-for="item in locales" :key="item.code" :value="item.code">
                {{ item.label }}
            </option>
        </select>
        <svg class="pointer-events-none absolute right-2 top-1/2 -translate-y-1/2 h-3.5 w-3.5 text-slate-400" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.24 4.5a.75.75 0 01-1.08 0l-4.24-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
        </svg>
    </div>
</template>
