<script setup>
const appName = import.meta.env.VITE_APP_NAME ?? 'Whois';
</script>

<template>
    <header class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="mx-auto max-w-5xl px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-sky-600 text-white font-bold text-sm">W</span>
                <span class="font-bold text-lg text-slate-900">{{ appName }}</span>
            </div>
            <nav class="hidden sm:flex items-center gap-6 text-sm text-slate-600">
                <a href="/api/v1/whois/example.com" target="_blank" rel="noopener" class="hover:text-sky-600 transition-colors">API</a>
            </nav>
        </div>
    </header>
</template>
