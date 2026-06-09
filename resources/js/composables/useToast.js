import { reactive } from 'vue';

const state = reactive({
    toasts: [],
});

let nextId = 0;

export function useToast() {
    function show(message, type = 'error', duration = 5000) {
        const id = nextId++;
        state.toasts.push({ id, message, type });

        setTimeout(() => dismiss(id), duration);

        return id;
    }

    function dismiss(id) {
        const index = state.toasts.findIndex((toast) => toast.id === id);

        if (index >= 0) {
            state.toasts.splice(index, 1);
        }
    }

    return {
        toasts: state.toasts,
        show,
        dismiss,
        error: (message) => show(message, 'error'),
        success: (message) => show(message, 'success'),
    };
}
