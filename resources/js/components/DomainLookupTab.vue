<script setup>
import { ref } from 'vue';
import { lookupDomain } from '../api/whois';
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

async function submit() {
    const value = domain.value.trim();
    if (! value) {
        return;
    }

    loading.value = true;
    result.value = null;

    try {
        const response = await lookupDomain(value, format.value);
        result.value = response.data;
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
    </div>
</template>
