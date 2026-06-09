<script setup>
import { computed } from 'vue';
import { useI18n } from '../i18n';

const props = defineProps({
    data: { type: Object, required: true },
    format: { type: String, default: 'summary' },
    compact: { type: Boolean, default: false },
});

const { t, locale } = useI18n();

const fields = computed(() => {
    const labels = {
        domain: 'fields.domain',
        registration_status: 'fields.registration_status',
        registrar: 'fields.registrar',
        owner: 'fields.owner',
        created_at: 'fields.created_at',
        updated_at: 'fields.updated_at',
        expires_at: 'fields.expires_at',
        whois_server: 'fields.whois_server',
        dnssec: 'fields.dnssec',
    };

    const entries = [];

    for (const [key, labelKey] of Object.entries(labels)) {
        if (props.data[key] != null && props.data[key] !== '') {
            entries.push({ key, label: t(labelKey), value: props.data[key] });
        }
    }

    return entries;
});

function formatRegistrationStatus(value) {
    const map = {
        registered: t('bulk.registered'),
        available: t('bulk.available'),
        unknown: t('bulk.unknown'),
    };

    return map[value] ?? value;
}

const dateLocale = computed(() => {
    const map = { en: 'en-US', tr: 'tr-TR', es: 'es-ES', zh: 'zh-CN', ar: 'ar-SA', pt: 'pt-BR', fr: 'fr-FR' };
    return map[locale.value] ?? 'en-US';
});

function formatDate(value) {
    if (!value) {
        return '—';
    }

    try {
        return new Intl.DateTimeFormat(dateLocale.value, {
            dateStyle: 'medium',
            timeStyle: 'short',
        }).format(new Date(value));
    } catch {
        return value;
    }
}
</script>

<template>
    <div :class="compact ? '' : 'rounded-xl border border-slate-200 overflow-hidden'">
        <dl class="divide-y divide-slate-100">
            <div
                v-for="field in fields"
                :key="field.key"
                class="grid grid-cols-1 sm:grid-cols-3 gap-1 px-4 py-3 even:bg-slate-50/50"
            >
                <dt class="text-sm font-medium text-slate-500">{{ field.label }}</dt>
                <dd class="sm:col-span-2 text-sm text-slate-900 break-all" dir="ltr">
                    <template v-if="field.key === 'registration_status'">{{ formatRegistrationStatus(field.value) }}</template>
                    <template v-else-if="field.key.includes('_at')">{{ formatDate(field.value) }}</template>
                    <template v-else>{{ field.value }}</template>
                </dd>
            </div>

            <div v-if="data.states?.length" class="grid grid-cols-1 sm:grid-cols-3 gap-1 px-4 py-3 even:bg-slate-50/50">
                <dt class="text-sm font-medium text-slate-500">{{ t('fields.states') }}</dt>
                <dd class="sm:col-span-2 flex flex-wrap gap-1.5">
                    <span
                        v-for="state in data.states"
                        :key="state"
                        class="inline-block rounded-full bg-amber-100 text-amber-800 text-xs px-2.5 py-1"
                    >
                        {{ state }}
                    </span>
                </dd>
            </div>

            <div v-if="format === 'full' && data.name_servers?.length" class="grid grid-cols-1 sm:grid-cols-3 gap-1 px-4 py-3 even:bg-slate-50/50">
                <dt class="text-sm font-medium text-slate-500">{{ t('fields.name_servers') }}</dt>
                <dd class="sm:col-span-2">
                    <ul class="space-y-1 text-sm font-mono text-slate-800">
                        <li v-for="ns in data.name_servers" :key="ns">{{ ns }}</li>
                    </ul>
                </dd>
            </div>
        </dl>

        <div v-if="format === 'full' && data.raw" class="border-t border-slate-200">
            <div class="px-4 py-2 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                {{ t('fields.raw') }}
            </div>
            <pre class="px-4 py-3 text-xs font-mono whitespace-pre-wrap overflow-x-auto max-h-80 overflow-y-auto bg-slate-900 text-slate-100" dir="ltr">{{ data.raw }}</pre>
        </div>
    </div>
</template>
