<script setup>
import { ref } from 'vue';
import AppHeader from './components/AppHeader.vue';
import DomainLookupTab from './components/DomainLookupTab.vue';
import BulkLookupTab from './components/BulkLookupTab.vue';
import AppFooter from './components/AppFooter.vue';

const activeTab = ref('domain');

const tabs = [
    { id: 'domain', label: 'Domain Whois' },
    { id: 'bulk', label: 'Toplu Whois' },
];
</script>

<template>
    <div class="min-h-screen flex flex-col">
        <AppHeader />

        <main class="flex-1">
            <section class="bg-gradient-to-br from-sky-600 via-blue-700 to-indigo-800 text-white">
                <div class="mx-auto max-w-5xl px-4 py-14 sm:py-20 text-center">
                    <h1 class="text-3xl sm:text-4xl font-bold tracking-tight">
                        Hızlı &amp; Güvenli Whois Sorgulama
                    </h1>
                    <p class="mt-4 text-sky-100 text-lg max-w-2xl mx-auto">
                        Domain kayıt bilgilerini saniyeler içinde sorgulayın. Tekil ve toplu whois desteği.
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
                            {{ tab.label }}
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
                        <h3 class="mt-4 font-semibold text-slate-900">Hızlı Sorgulama</h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                            Önbellek desteği ile tekrarlayan sorgularda anında yanıt alın.
                        </p>
                    </article>
                    <article class="rounded-xl bg-white p-6 border border-slate-200 shadow-sm">
                        <div class="h-10 w-10 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center text-lg font-bold">🔒</div>
                        <h3 class="mt-4 font-semibold text-slate-900">Güvenli API</h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                            Rate limit koruması ile kötüye kullanıma karşı güvenli erişim.
                        </p>
                    </article>
                    <article class="rounded-xl bg-white p-6 border border-slate-200 shadow-sm">
                        <div class="h-10 w-10 rounded-lg bg-violet-100 text-violet-600 flex items-center justify-center text-lg font-bold">🌐</div>
                        <h3 class="mt-4 font-semibold text-slate-900">Geniş TLD Desteği</h3>
                        <p class="mt-2 text-sm text-slate-600 leading-relaxed">
                            .com, .net, .tr ve yüzlerce uzantı için whois sorgusu.
                        </p>
                    </article>
                </div>
            </section>
        </main>

        <AppFooter />
    </div>
</template>
