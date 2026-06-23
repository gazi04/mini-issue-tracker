function csrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? '';
}

async function request(url, method, body) {
    const response = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'X-Requested-With': 'XMLHttpRequest',
        },
        body: body === undefined ? undefined : JSON.stringify(body),
    });

    const data = response.status === 204 ? null : await response.json();

    if (!response.ok) {
        throw { status: response.status, data };
    }

    return data;
}

export const getJson = (url) => request(url, 'GET');
export const postJson = (url, body) => request(url, 'POST', body);
export const deleteJson = (url) => request(url, 'DELETE');
