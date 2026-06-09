<script setup>
import { useToast } from '../composables/useToast';

const { toasts, dismiss } = useToast();
</script>

<template>
    <div
        class="fixed top-20 end-4 z-50 flex flex-col gap-2 w-full max-w-sm pointer-events-none"
        aria-live="polite"
    >
        <TransitionGroup
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 translate-x-4"
            enter-to-class="opacity-100 translate-x-0"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100 translate-x-0"
            leave-to-class="opacity-0 translate-x-4"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto rounded-xl shadow-lg border px-4 py-3 flex items-start gap-3 text-sm"
                :class="toast.type === 'error'
                    ? 'bg-red-50 border-red-200 text-red-800'
                    : 'bg-emerald-50 border-emerald-200 text-emerald-800'"
                role="alert"
            >
                <span class="flex-1 leading-relaxed">{{ toast.message }}</span>
                <button
                    type="button"
                    class="shrink-0 opacity-60 hover:opacity-100 transition-opacity"
                    :aria-label="'Dismiss'"
                    @click="dismiss(toast.id)"
                >
                    ✕
                </button>
            </div>
        </TransitionGroup>
    </div>
</template>
