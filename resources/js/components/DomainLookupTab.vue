<script setup>
import { ref } from 'vue';
import { lookupDomain, normalizeDomainInput } from '../api/whois';
import WhoisResultCard from './WhoisResultCard.vue';
import { useI18n } from '../i18n';
import { useToast } from '../composables/useToast';
import { useApiError } from '../composables/useApiError';

const { t } = useI18n();
const toast = useToast();
const { resolve } = useApiError();

const domain = ref('');
const format = ref('summary');
const loading = ref(false);
const result = ref(null);
const historyKey = 'whois-lookup-history';
const history = ref(loadHistory());

function loadHistory() {
    try {
        const storedHistory = JSON.parse(localStorage.getItem(historyKey) ?? '[]');
        return Array.isArray(storedHistory) ? storedHistory : [];
    } catch {
        return [];
    }
}

function saveToHistory(value) {
    history.value = [
        { domain: value, lookedUpAt: new Date().toISOString() },
        ...history.value.filter((item) => item.domain !== value),
    ].slice(0, 10);
    localStorage.setItem(historyKey, JSON.stringify(history.value));
}

function clearHistory() {
    history.value = [];
    localStorage.removeItem(historyKey);
}

function lookupAgain(value) {
    domain.value = value;
    submit();
}

async function submit() {
    const value = normalizeDomainInput(domain.value);
    if (! value) {
        return;
    }

    domain.value = value;
    loading.value = true;
    result.value = null;

    try {
        const response = await lookupDomain(value, 'full');
        result.value = response.data;
        saveToHistory(value);
    } catch (err) {
        toast.error(resolve(err));
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="space-y-6">
        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <label for="domain" class="block text-sm font-medium text-slate-700 mb-1.5">{{ t('domain.label') }}</label>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input
                        id="domain"
                        v-model="domain"
                        type="text"
                        :placeholder="t('domain.placeholder')"
                        class="flex-1 rounded-lg border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition"
                        autocomplete="off"
                        spellcheck="false"
                        dir="ltr"
                    />
                    <button
                        type="submit"
                        class="rounded-lg bg-sky-600 px-6 py-3 font-semibold text-white hover:bg-sky-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors shrink-0"
                        :disabled="loading || !domain.trim()"
                    >
                        {{ loading ? t('domain.loading') : t('domain.submit') }}
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-4 text-sm">
                <span class="text-slate-600 font-medium">{{ t('domain.format') }}:</span>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input v-model="format" type="radio" value="summary" class="text-sky-600 focus:ring-sky-500" />
                    <span>{{ t('domain.summary') }}</span>
                </label>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input v-model="format" type="radio" value="full" class="text-sky-600 focus:ring-sky-500" />
                    <span>{{ t('domain.full') }}</span>
                </label>
            </div>
        </form>

        <WhoisResultCard v-if="result" :data="result" :format="format" />

        <section v-if="history.length" class="rounded-xl border border-slate-200 bg-slate-50/70 p-4 sm:p-5">
            <div class="mb-3 flex items-center justify-between gap-4">
                <div>
                    <h2 class="font-semibold text-slate-900">{{ t('history.title') }}</h2>
                    <p class="mt-0.5 text-xs text-slate-500">{{ t('history.description') }}</p>
                </div>
                <button type="button" class="text-xs font-medium text-slate-500 hover:text-red-600 transition-colors" @click="clearHistory">
                    {{ t('history.clear') }}
                </button>
            </div>
            <div class="flex flex-wrap gap-2">
                <button
                    v-for="item in history"
                    :key="item.domain"
                    type="button"
                    class="group inline-flex items-center gap-2 rounded-full border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm hover:border-sky-300 hover:text-sky-700 transition-colors"
                    :title="t('history.lookupAgain')"
                    @click="lookupAgain(item.domain)"
                >
                    <svg class="h-3.5 w-3.5 text-slate-400 group-hover:text-sky-500" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M3.5 10a6.5 6.5 0 1 0 2-4.7M3.5 3.5v4h4" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span dir="ltr">{{ item.domain }}</span>
                </button>
            </div>
        </section>
    </div>
</template>
