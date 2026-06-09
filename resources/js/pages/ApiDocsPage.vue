<script setup>
import { computed } from 'vue';
import { useI18n } from '../i18n';

const { t } = useI18n();

const baseUrl = computed(() => window.location.origin + '/api/v1/whois');
</script>

<template>
    <div class="mx-auto max-w-4xl px-4 py-10 sm:py-14">
        <div class="mb-10">
            <h1 class="text-3xl font-bold text-slate-900">{{ t('docs.title') }}</h1>
            <p class="mt-2 text-slate-600">{{ t('docs.subtitle') }}</p>
        </div>

        <div class="space-y-10">
            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ t('docs.baseUrl') }}</h2>
                <pre class="rounded-lg bg-slate-900 text-sky-300 px-4 py-3 text-sm font-mono overflow-x-auto">{{ baseUrl }}</pre>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ t('docs.auth') }}</h2>
                <p class="text-slate-600 text-sm leading-relaxed">{{ t('docs.authText') }}</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ t('docs.rateLimits') }}</h2>
                <div class="rounded-xl border border-slate-200 overflow-hidden text-sm">
                    <table class="w-full">
                        <tbody class="divide-y divide-slate-100">
                            <tr class="bg-slate-50">
                                <td class="px-4 py-3 font-medium text-slate-700">{{ t('docs.rateSingle') }}</td>
                                <td class="px-4 py-3 font-mono text-slate-600">GET /{domain}</td>
                                <td class="px-4 py-3 text-slate-600">60 {{ t('docs.perMinute') }}</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium text-slate-700">{{ t('docs.rateBulk') }}</td>
                                <td class="px-4 py-3 font-mono text-slate-600">POST /bulk</td>
                                <td class="px-4 py-3 text-slate-600">10 {{ t('docs.perMinute') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-2">{{ t('docs.caching') }}</h2>
                <p class="text-slate-600 text-sm leading-relaxed">{{ t('docs.cachingText', { ttl: '3600' }) }}</p>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ t('docs.formats') }}</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-xl border border-slate-200 p-4">
                        <code class="text-sm font-semibold text-sky-700">summary</code>
                        <p class="mt-2 text-sm text-slate-600">{{ t('docs.formatSummary') }}</p>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-4">
                        <code class="text-sm font-semibold text-sky-700">full</code>
                        <p class="mt-2 text-sm text-slate-600">{{ t('docs.formatFull') }}</p>
                    </div>
                </div>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-6">{{ t('docs.endpoints') }}</h2>

                <article class="rounded-xl border border-slate-200 overflow-hidden mb-6">
                    <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center gap-3">
                        <span class="rounded px-2 py-0.5 text-xs font-bold bg-emerald-100 text-emerald-800">GET</span>
                        <code class="text-sm font-mono text-slate-800">/api/v1/whois/{domain}</code>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <h3 class="font-semibold text-slate-900">{{ t('docs.singleTitle') }}</h3>
                            <p class="text-sm text-slate-600 mt-1">{{ t('docs.singleDesc') }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">{{ t('docs.queryParams') }}</h4>
                            <pre class="rounded-lg bg-slate-900 text-slate-100 px-4 py-3 text-xs font-mono overflow-x-auto">format=summary|full  (default: summary)</pre>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">{{ t('docs.example') }}</h4>
                            <pre class="rounded-lg bg-slate-900 text-sky-300 px-4 py-3 text-xs font-mono overflow-x-auto">curl "{{ baseUrl }}/google.com?format=summary"</pre>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">{{ t('docs.response') }}</h4>
                            <pre class="rounded-lg bg-slate-900 text-emerald-300 px-4 py-3 text-xs font-mono overflow-x-auto">{
  "data": {
    "domain": "google.com",
    "registrar": "MarkMonitor Inc.",
    "created_at": "1997-09-15T04:00:00+00:00",
    "expires_at": "2028-09-14T04:00:00+00:00",
    "states": ["client delete prohibited"]
  }
}</pre>
                        </div>
                    </div>
                </article>

                <article class="rounded-xl border border-slate-200 overflow-hidden">
                    <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center gap-3">
                        <span class="rounded px-2 py-0.5 text-xs font-bold bg-amber-100 text-amber-800">POST</span>
                        <code class="text-sm font-mono text-slate-800">/api/v1/whois/bulk</code>
                    </div>
                    <div class="p-4 space-y-4">
                        <div>
                            <h3 class="font-semibold text-slate-900">{{ t('docs.bulkTitle') }}</h3>
                            <p class="text-sm text-slate-600 mt-1">{{ t('docs.bulkDesc') }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">{{ t('docs.requestBody') }}</h4>
                            <pre class="rounded-lg bg-slate-900 text-slate-100 px-4 py-3 text-xs font-mono overflow-x-auto">{
  "domains": ["google.com", "example.com"],
  "format": "summary"
}</pre>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">{{ t('docs.example') }}</h4>
                            <pre class="rounded-lg bg-slate-900 text-sky-300 px-4 py-3 text-xs font-mono overflow-x-auto">curl -X POST "{{ baseUrl }}/bulk" \
  -H "Content-Type: application/json" \
  -d '{"domains":["google.com"],"format":"full"}'</pre>
                        </div>
                        <div>
                            <h4 class="text-xs font-semibold uppercase tracking-wide text-slate-500 mb-2">{{ t('docs.response') }}</h4>
                            <pre class="rounded-lg bg-slate-900 text-emerald-300 px-4 py-3 text-xs font-mono overflow-x-auto">{
  "format": "summary",
  "results": [
    { "domain": "google.com", "status": "success", "data": { ... } },
    { "domain": "invalid", "status": "error", "message": "Invalid domain: invalid" }
  ]
}</pre>
                        </div>
                    </div>
                </article>
            </section>

            <section>
                <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ t('docs.errors') }}</h2>
                <div class="rounded-xl border border-slate-200 divide-y divide-slate-100 text-sm">
                    <div class="px-4 py-3 flex gap-4">
                        <code class="font-mono text-red-600 shrink-0">422</code>
                        <span class="text-slate-600">{{ t('docs.error422') }}</span>
                    </div>
                    <div class="px-4 py-3 flex gap-4">
                        <code class="font-mono text-red-600 shrink-0">429</code>
                        <span class="text-slate-600">{{ t('docs.error429') }}</span>
                    </div>
                    <div class="px-4 py-3 flex gap-4">
                        <code class="font-mono text-red-600 shrink-0">502</code>
                        <span class="text-slate-600">{{ t('docs.error502') }}</span>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
