import { useI18n } from '../i18n';

export function resolveApiError(error, t) {
    if (error?.status === 429 || error?.code === 'rate_limited') {
        return t('errors.rateLimit');
    }

    if (error?.code) {
        const key = `errors.codes.${error.code}`;
        const translated = t(key);

        if (translated !== key) {
            return translated;
        }
    }

    return t('errors.generic');
}

export function resolveErrorCode(code, t) {
    if (! code) {
        return t('errors.generic');
    }

    const key = `errors.codes.${code}`;
    const translated = t(key);

    return translated !== key ? translated : t('errors.generic');
}

export function useApiError() {
    const { t } = useI18n();

    return {
        resolve: (error) => resolveApiError(error, t),
        resolveCode: (code) => resolveErrorCode(code, t),
    };
}
