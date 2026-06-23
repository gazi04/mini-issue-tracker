import { postJson } from './http';
import { escapeHtml } from './issue-show';

export default function initTagCreate() {
    const form = document.getElementById('tag-create-form');
    const list = document.getElementById('tag-list');
    if (!form || !list) {
        return;
    }

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        const errorEl = form.querySelector('[data-error="name"]');
        errorEl.textContent = '';

        const payload = {
            name: form.name.value,
            color: form.color.value,
        };

        try {
            const { data } = await postJson(form.action, payload);

            const emptyState = list.querySelector('p');
            if (emptyState) {
                emptyState.remove();
            }

            list.insertAdjacentHTML(
                'beforeend',
                `<span class="text-xs px-2 py-1 rounded text-white" style="background-color: ${data.color ?? '#6b7280'}">${escapeHtml(data.name)}</span>`
            );
            form.reset();
            form.color.value = '#3b82f6';
        } catch (error) {
            if (error.status === 422) {
                errorEl.textContent = error.data.errors.name?.[0] ?? 'Invalid tag.';
            }
        }
    });
}
