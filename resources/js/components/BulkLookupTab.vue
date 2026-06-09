<script setup>
import { ref, computed, watch } from 'vue';
import { bulkLookup } from '../api/whois';
import WhoisResultCard from './WhoisResultCard.vue';
import { useI18n } from '../i18n';

const { t } = useI18n();

const input = ref('');
const format = ref('summary');
const loading = ref(false);
const error = ref(null);
const response = ref(null);
const expanded = ref(new Set());

const domainCount = computed(() => {
    return input.value
        .split(/[\n,;]+/)
        .map((d) => d.trim())
        .filter(Boolean).length;
});

function parseDomains() {
    return [...new Set(
        input.value
            .split(/[\n,;]+/)
            .map((d) => d.trim())
            .filter(Boolean),
    )];
}

function isOpen(domain) {
    return expanded.value.has(domain);
}

function toggle(domain) {
    const next = new Set(expanded.value);

    if (next.has(domain)) {
        next.delete(domain);
    } else {
        next.add(domain);
    }

    expanded.value = next;
}

function expandAll() {
    if (! response.value) {
        return;
    }

    expanded.value = new Set(response.value.results.map((r) => r.domain));
}

function collapseAll() {
    expanded.value = new Set();
}

function previewText(item) {
    if (item.status === 'error') {
        return item.message;
    }

    const parts = [item.data?.registrar, item.data?.expires_at].filter(Boolean);

    return parts.join(' · ') || '—';
}

watch(response, (value) => {
    if (! value?.results.length) {
        expanded.value = new Set();

        return;
    }

    expanded.value = new Set([value.results[0].domain]);
});

async function submit() {
    const domains = parseDomains();
    if (domains.length === 0) {
        return;
    }

    loading.value = true;
    error.value = null;
    response.value = null;
    expanded.value = new Set();

    try {
        response.value = await bulkLookup(domains, format.value);
    } catch (err) {
        error.value = err.status === 429
            ? t('errors.rateLimit')
            : (err.message ?? t('errors.generic'));
    } finally {
        loading.value = false;
    }
}
</script>

<template>
    <div class="space-y-6">
        <form class="space-y-4" @submit.prevent="submit">
            <div>
                <label for="domains" class="block text-sm font-medium text-slate-700 mb-1.5">
                    {{ t('bulk.label') }}
                    <span class="text-slate-400 font-normal">{{ t('bulk.hint') }}</span>
                </label>
                <textarea
                    id="domains"
                    v-model="input"
                    rows="6"
                    :placeholder="t('bulk.placeholder')"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition font-mono text-sm"
                    spellcheck="false"
                    dir="ltr"
                />
                <p v-if="domainCount > 0" class="mt-1.5 text-xs text-slate-500">
                    {{ t('bulk.count', { count: domainCount }) }}
                </p>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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

                <button
                    type="submit"
                    class="rounded-lg bg-sky-600 px-6 py-3 font-semibold text-white hover:bg-sky-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
                    :disabled="loading || domainCount === 0"
                >
                    {{ loading ? t('bulk.loading') : t('bulk.submit') }}
                </button>
            </div>
        </form>

        <div v-if="error" class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm">
            {{ error }}
        </div>

        <div v-if="response" class="space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <p class="text-sm text-slate-600">
                    {{ t('bulk.successCount', {
                        success: response.results.filter((r) => r.status === 'success').length,
                        total: response.results.length,
                    }) }}
                </p>
                <div class="flex gap-2 text-xs">
                    <button
                        type="button"
                        class="rounded-md border border-slate-200 px-2.5 py-1 text-slate-600 hover:bg-slate-50 transition-colors"
                        @click="expandAll"
                    >
                        {{ t('bulk.expandAll') }}
                    </button>
                    <button
                        type="button"
                        class="rounded-md border border-slate-200 px-2.5 py-1 text-slate-600 hover:bg-slate-50 transition-colors"
                        @click="collapseAll"
                    >
                        {{ t('bulk.collapseAll') }}
                    </button>
                </div>
            </div>

            <div class="rounded-xl border border-slate-200 divide-y divide-slate-200 overflow-hidden">
                <div
                    v-for="item in response.results"
                    :key="item.domain"
                    :class="item.status === 'error' ? 'bg-red-50/30' : 'bg-white'"
                >
                    <button
                        type="button"
                        class="w-full px-4 py-3 flex items-center gap-3 text-start hover:bg-slate-50/80 transition-colors"
                        :class="item.status === 'error' ? 'hover:bg-red-50' : ''"
                        :aria-expanded="isOpen(item.domain)"
                        @click="toggle(item.domain)"
                    >
                        <svg
                            class="h-4 w-4 shrink-0 text-slate-400 transition-transform duration-200"
                            :class="{ 'rotate-90': isOpen(item.domain) }"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd" />
                        </svg>

                        <span class="font-semibold text-sm text-slate-800 shrink-0" dir="ltr">{{ item.domain }}</span>

                        <span
                            class="text-xs uppercase tracking-wide px-2 py-0.5 rounded-full shrink-0"
                            :class="item.status === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-200 text-red-800'"
                        >
                            {{ item.status === 'success' ? t('bulk.success') : t('bulk.error') }}
                        </span>

                        <span
                            v-if="! isOpen(item.domain)"
                            class="text-xs text-slate-500 truncate min-w-0 flex-1"
                            dir="ltr"
                        >
                            {{ previewText(item) }}
                        </span>
                    </button>

                    <div
                        v-show="isOpen(item.domain)"
                        class="border-t border-slate-100 px-4 pb-4 pt-3"
                    >
                        <p v-if="item.status === 'error'" class="text-sm text-red-700">
                            {{ item.message }}
                        </p>
                        <WhoisResultCard
                            v-else
                            :data="item.data"
                            :format="response.format"
                            compact
                        />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
