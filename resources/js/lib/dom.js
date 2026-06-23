export function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

export function clearErrors(form) {
    form.querySelectorAll('[data-error]').forEach((el) => (el.textContent = ''));
}

export function showErrors(form, errors) {
    Object.entries(errors ?? {}).forEach(([field, messages]) => {
        const el = form.querySelector(`[data-error="${field}"]`);
        if (el) {
            el.textContent = messages[0];
        }
    });
}
