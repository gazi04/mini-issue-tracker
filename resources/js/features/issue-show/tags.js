import { postJson, deleteJson } from '../../lib/http';
import { tagMarkup } from './templates';

export default function initTags(tagsUrl) {
    const container = document.getElementById('issue-tags');
    const select = document.getElementById('tag-select');
    const attachBtn = document.getElementById('attach-tag-btn');
    if (!container || !attachBtn) {
        return;
    }

    const render = (tags) => {
        container.innerHTML = tags.map(tagMarkup).join('');
    };

    attachBtn.addEventListener('click', async () => {
        const { data } = await postJson(tagsUrl, { tag_id: Number(select.value) });
        render(data);
    });

    container.addEventListener('click', async (event) => {
        const btn = event.target.closest('[data-detach-tag]');
        if (!btn) {
            return;
        }
        const { data } = await deleteJson(`${tagsUrl}/${btn.dataset.detachTag}`);
        render(data);
    });
}
