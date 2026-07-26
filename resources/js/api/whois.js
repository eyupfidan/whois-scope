const API_BASE = '/api/v1/whois';

export class ApiError extends Error {
    constructor(message, status, code = null) {
        super(message);
        this.name = 'ApiError';
        this.status = status;
        this.code = code;
    }
}

export function normalizeDomainInput(input) {
    const value = String(input).trim();

    if (! value) {
        return '';
    }

    try {
        const url = new URL(
            /^[a-z][a-z\d+.-]*:\/\//i.test(value) ? value : `http://${value}`,
        );
        const hostname = url.hostname.toLowerCase().replace(/\.$/, '');

        return hostname.startsWith('www.') ? hostname.slice(4) : hostname;
    } catch {
        return value;
    }
}

async function request(url, options = {}) {
    const response = await fetch(url, {
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            ...(options.headers ?? {}),
        },
        ...options,
    });

    const data = await response.json().catch(() => ({}));

    if (! response.ok) {
        throw new ApiError(
            data.message ?? 'Request failed',
            response.status,
            data.code ?? null,
        );
    }

    return data;
}

export function lookupDomain(domain, format = 'summary') {
    domain = normalizeDomainInput(domain);
    const params = new URLSearchParams({ format });

    return request(`${API_BASE}/${encodeURIComponent(domain)}?${params}`);
}

export function bulkLookup(domains, format = 'summary') {
    return request(`${API_BASE}/bulk`, {
        method: 'POST',
        body: JSON.stringify({ domains, format }),
    });
}
