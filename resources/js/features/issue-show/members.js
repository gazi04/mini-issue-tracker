import { postJson, deleteJson } from '../../lib/http';
import { memberMarkup } from './templates';

export default function initMembers(membersUrl) {
    const list = document.getElementById('issue-members');
    const select = document.getElementById('member-select');
    const attachBtn = document.getElementById('attach-member-btn');
    if (!list || !attachBtn) {
        return;
    }

    const render = (members) => {
        list.innerHTML = members.map(memberMarkup).join('');
    };

    attachBtn.addEventListener('click', async () => {
        const { data } = await postJson(membersUrl, { user_id: Number(select.value) });
        render(data);
    });

    list.addEventListener('click', async (event) => {
        const btn = event.target.closest('[data-detach-member]');
        if (!btn) {
            return;
        }
        const { data } = await deleteJson(`${membersUrl}/${btn.dataset.detachMember}`);
        render(data);
    });
}
