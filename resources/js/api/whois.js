const API_BASE = '/api/v1/whois';

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

    if (!response.ok) {
        const error = new Error(data.message ?? 'Bir hata oluştu.');
        error.status = response.status;
        error.data = data;
        throw error;
    }

    return data;
}

export function lookupDomain(domain, format = 'summary') {
    const params = new URLSearchParams({ format });
    return request(`${API_BASE}/${encodeURIComponent(domain)}?${params}`);
}

export function bulkLookup(domains, format = 'summary') {
    return request(`${API_BASE}/bulk`, {
        method: 'POST',
        body: JSON.stringify({ domains, format }),
    });
}
