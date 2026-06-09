/**
 * @param {Array<{ domain: string, status: string, data?: object, code?: string }>} results
 * @param {(key: string) => string} t
 */
export function downloadBulkResultsCsv(results, t) {
    const headers = [
        'domain',
        'status',
        'registrar',
        'created_at',
        'expires_at',
        'states',
        'owner',
        'whois_server',
    ];

    const rows = results.map((item) => {
        if (item.status === 'error') {
            return [
                item.domain,
                t('bulk.error'),
                '',
                '',
                '',
                '',
                '',
                '',
            ];
        }

        const data = item.data ?? {};

        return [
            item.domain,
            statusLabel(item.status, t),
            data.registrar ?? '',
            data.created_at ?? '',
            data.expires_at ?? '',
            Array.isArray(data.states) ? data.states.join('; ') : '',
            data.owner ?? '',
            data.whois_server ?? '',
        ];
    });

    const escape = (value) => {
        const string = String(value ?? '');

        if (/[",\n]/.test(string)) {
            return `"${string.replace(/"/g, '""')}"`;
        }

        return string;
    };

    const csv = [
        headers.join(','),
        ...rows.map((row) => row.map(escape).join(',')),
    ].join('\n');

    const blob = new Blob([`\uFEFF${csv}`], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `whois-bulk-${new Date().toISOString().slice(0, 10)}.csv`;
    link.click();
    URL.revokeObjectURL(url);
}

function statusLabel(status, t) {
    const map = {
        registered: t('bulk.registered'),
        available: t('bulk.available'),
        unknown: t('bulk.unknown'),
        error: t('bulk.error'),
    };

    return map[status] ?? status;
}

export function statusLabelForItem(status, t) {
    return statusLabel(status, t);
}
