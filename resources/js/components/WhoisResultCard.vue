<script setup>
import { computed } from 'vue';

const props = defineProps({
    data: { type: Object, required: true },
    format: { type: String, default: 'summary' },
    compact: { type: Boolean, default: false },
});

const fields = computed(() => {
    const labels = {
        domain: 'Domain',
        registrar: 'Kayıt firması',
        owner: 'Sahip',
        created_at: 'Oluşturulma',
        updated_at: 'Güncellenme',
        expires_at: 'Bitiş tarihi',
        whois_server: 'Whois sunucusu',
        dnssec: 'DNSSEC',
    };

    const entries = [];

    for (const [key, label] of Object.entries(labels)) {
        if (props.data[key] != null && props.data[key] !== '') {
            entries.push({ key, label, value: props.data[key] });
        }
    }

    return entries;
});

function formatDate(value) {
    if (!value) {
        return '—';
    }

    try {
        return new Intl.DateTimeFormat('tr-TR', {
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
                <dd class="sm:col-span-2 text-sm text-slate-900 break-all">
                    <template v-if="field.key.includes('_at')">{{ formatDate(field.value) }}</template>
                    <template v-else>{{ field.value }}</template>
                </dd>
            </div>

            <div v-if="data.states?.length" class="grid grid-cols-1 sm:grid-cols-3 gap-1 px-4 py-3 even:bg-slate-50/50">
                <dt class="text-sm font-medium text-slate-500">Durum</dt>
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
                <dt class="text-sm font-medium text-slate-500">Name server</dt>
                <dd class="sm:col-span-2">
                    <ul class="space-y-1 text-sm font-mono text-slate-800">
                        <li v-for="ns in data.name_servers" :key="ns">{{ ns }}</li>
                    </ul>
                </dd>
            </div>
        </dl>

        <div v-if="format === 'full' && data.raw" class="border-t border-slate-200">
            <div class="px-4 py-2 bg-slate-50 text-xs font-semibold uppercase tracking-wide text-slate-500">
                Ham Whois
            </div>
            <pre class="px-4 py-3 text-xs font-mono text-slate-700 whitespace-pre-wrap overflow-x-auto max-h-80 overflow-y-auto bg-slate-900 text-slate-100">{{ data.raw }}</pre>
        </div>
    </div>
</template>
