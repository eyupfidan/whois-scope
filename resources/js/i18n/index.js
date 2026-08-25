import { ref, computed } from 'vue';
import en from './locales/en';
import tr from './locales/tr';
import es from './locales/es';
import zh from './locales/zh';
import ar from './locales/ar';
import pt from './locales/pt';
import fr from './locales/fr';

const messages = { en, tr, es, zh, ar, pt, fr };

export const locales = [
    { code: 'en', label: 'English' },
    { code: 'es', label: 'Español' },
    { code: 'zh', label: '中文' },
    { code: 'ar', label: 'العربية' },
    { code: 'pt', label: 'Português' },
    { code: 'fr', label: 'Français' },
    { code: 'tr', label: 'Türkçe' },
];

const pathLocale = typeof window !== 'undefined' ? window.location.pathname.split('/')[1] : null;
const locale = ref(pathLocale && messages[pathLocale] ? pathLocale : 'en');

function resolve(obj, path) {
    return path.split('.').reduce((acc, key) => acc?.[key], obj);
}

export function setLocale(code) {
    if (! messages[code]) {
        return;
    }

    locale.value = code;
    localStorage.setItem('locale', code);
    document.documentElement.lang = code;
    document.documentElement.dir = code === 'ar' ? 'rtl' : 'ltr';
}

export function useI18n() {
    const t = (key, replacements = {}) => {
        let text = resolve(messages[locale.value], key)
            ?? resolve(messages.en, key)
            ?? key;

        for (const [placeholder, value] of Object.entries(replacements)) {
            text = text.replace(`:${placeholder}`, String(value));
        }

        return text;
    };

    return {
        locale,
        locales,
        setLocale,
        t,
        isRtl: computed(() => locale.value === 'ar'),
    };
}

setLocale(locale.value);
