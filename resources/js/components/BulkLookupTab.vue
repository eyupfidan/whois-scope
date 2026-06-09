<script setup>
import { ref, computed } from 'vue';
import { bulkLookup } from '../api/whois';
import WhoisResultCard from './WhoisResultCard.vue';

const input = ref('');
const format = ref('summary');
const loading = ref(false);
const error = ref(null);
const response = ref(null);

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

async function submit() {
    const domains = parseDomains();
    if (domains.length === 0) {
        return;
    }

    loading.value = true;
    error.value = null;
    response.value = null;

    try {
        response.value = await bulkLookup(domains, format.value);
    } catch (err) {
        error.value = err.status === 429
            ? 'Çok fazla istek gönderdiniz. Lütfen biraz bekleyin.'
            : err.message;
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
                    Domain listesi
                    <span class="text-slate-400 font-normal">(satır veya virgülle ayırın, max 50)</span>
                </label>
                <textarea
                    id="domains"
                    v-model="input"
                    rows="6"
                    placeholder="ornek.com&#10;google.com&#10;github.com"
                    class="w-full rounded-lg border border-slate-300 px-4 py-3 text-slate-900 placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 outline-none transition font-mono text-sm"
                    spellcheck="false"
                />
                <p v-if="domainCount > 0" class="mt-1.5 text-xs text-slate-500">{{ domainCount }} domain</p>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4 text-sm">
                    <span class="text-slate-600 font-medium">Format:</span>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input v-model="format" type="radio" value="summary" class="text-sky-600 focus:ring-sky-500" />
                        <span>Özet</span>
                    </label>
                    <label class="inline-flex items-center gap-2 cursor-pointer">
                        <input v-model="format" type="radio" value="full" class="text-sky-600 focus:ring-sky-500" />
                        <span>Tam</span>
                    </label>
                </div>

                <button
                    type="submit"
                    class="rounded-lg bg-sky-600 px-6 py-3 font-semibold text-white hover:bg-sky-700 disabled:opacity-60 disabled:cursor-not-allowed transition-colors"
                    :disabled="loading || domainCount === 0"
                >
                    {{ loading ? 'Sorgulanıyor…' : 'Toplu Sorgula' }}
                </button>
            </div>
        </form>

        <div v-if="error" class="rounded-lg bg-red-50 border border-red-200 px-4 py-3 text-red-700 text-sm">
            {{ error }}
        </div>

        <div v-if="response" class="space-y-4">
            <p class="text-sm text-slate-600">
                {{ response.results.filter((r) => r.status === 'success').length }} / {{ response.results.length }} başarılı
            </p>

            <div
                v-for="item in response.results"
                :key="item.domain"
                class="rounded-xl border overflow-hidden"
                :class="item.status === 'success' ? 'border-slate-200' : 'border-red-200 bg-red-50/50'"
            >
                <div
                    class="px-4 py-2 text-sm font-semibold flex items-center justify-between"
                    :class="item.status === 'success' ? 'bg-slate-50 text-slate-700' : 'bg-red-100 text-red-800'"
                >
                    <span>{{ item.domain }}</span>
                    <span
                        class="text-xs uppercase tracking-wide px-2 py-0.5 rounded-full"
                        :class="item.status === 'success' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-200 text-red-800'"
                    >
                        {{ item.status === 'success' ? 'Başarılı' : 'Hata' }}
                    </span>
                </div>

                <div v-if="item.status === 'error'" class="px-4 py-3 text-sm text-red-700">
                    {{ item.message }}
                </div>

                <div v-else class="p-4">
                    <WhoisResultCard :data="item.data" :format="response.format" compact />
                </div>
            </div>
        </div>
    </div>
</template>
