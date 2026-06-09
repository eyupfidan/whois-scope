<script setup>
import { ref } from 'vue';
import DomainLookupTab from '../components/DomainLookupTab.vue';
import BulkLookupTab from '../components/BulkLookupTab.vue';
import { useI18n } from '../i18n';

const { t } = useI18n();
const activeTab = ref('domain');

const tabs = [
    { id: 'domain', labelKey: 'tabs.domain' },
    { id: 'bulk', labelKey: 'tabs.bulk' },
];
</script>

<template>
    <section class="bg-gradient-to-br from-sky-600 via-blue-700 to-indigo-800 text-white">
        <div class="mx-auto max-w-5xl px-4 py-14 sm:py-20 text-center">
            <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">
                {{ t('hero.title') }}
            </h1>
            <p class="mt-4 text-sky-100 text-lg max-w-2xl mx-auto">
                {{ t('hero.subtitle') }}
            </p>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 -mt-8 pb-16">
        <div class="rounded-2xl bg-white shadow-xl shadow-slate-200/60 border border-slate-200 overflow-hidden">
            <div class="flex border-b border-slate-200 bg-slate-50">
                <button
                    v-for="tab in tabs"
                    :key="tab.id"
                    type="button"
                    class="flex-1 px-4 py-4 text-sm font-semibold transition-colors"
                    :class="activeTab === tab.id
                        ? 'bg-white text-sky-700 border-b-2 border-sky-600 -mb-px'
                        : 'text-slate-500 hover:text-slate-700 hover:bg-slate-100'"
                    @click="activeTab = tab.id"
                >
                    {{ t(tab.labelKey) }}
                </button>
            </div>

            <div class="p-6 sm:p-8">
                <DomainLookupTab v-if="activeTab === 'domain'" />
                <BulkLookupTab v-else />
            </div>
        </div>

        <div class="mt-12 grid gap-6 sm:grid-cols-3">
            <article class="rounded-xl bg-white p-6 border border-slate-200 shadow-sm">
                <div class="h-10 w-10 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center text-lg font-bold">⚡</div>
                <h3 class="mt-4 font-semibold text-slate-900">{{ t('features.fast.title') }}</h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ t('features.fast.text') }}</p>
            </article>
            <article class="rounded-xl bg-white p-6 border border-slate-200 shadow-sm">
                <div class="h-10 w-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg font-bold">🔒</div>
                <h3 class="mt-4 font-semibold text-slate-900">{{ t('features.secure.title') }}</h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ t('features.secure.text') }}</p>
            </article>
            <article class="rounded-xl bg-white p-6 border border-slate-200 shadow-sm">
                <div class="h-10 w-10 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center text-lg font-bold">🌐</div>
                <h3 class="mt-4 font-semibold text-slate-900">{{ t('features.tld.title') }}</h3>
                <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ t('features.tld.text') }}</p>
            </article>
        </div>
    </section>
</template>
