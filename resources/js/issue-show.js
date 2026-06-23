import { getJson, postJson, deleteJson } from './http';

export default function initIssueShow() {
    const root = document.querySelector('[data-issue-root]');
    if (!root) {
        return;
    }

    const tagsUrl = root.dataset.tagsUrl;
    const membersUrl = root.dataset.membersUrl;
    const commentsUrl = root.dataset.commentsUrl;

    initTags(tagsUrl);
    initMembers(membersUrl);
    initComments(commentsUrl);
}

function initTags(tagsUrl) {
    const container = document.getElementById('issue-tags');
    const select = document.getElementById('tag-select');
    const attachBtn = document.getElementById('attach-tag-btn');
    if (!container || !attachBtn) {
        return;
    }

    const render = (tags) => {
        container.innerHTML = tags
            .map(
                (tag) => `
                <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded text-white" style="background-color: ${tag.color ?? '#6b7280'}">
                    ${escapeHtml(tag.name)}
                    <button type="button" data-detach-tag="${tag.id}" class="font-bold hover:text-gray-200">&times;</button>
                </span>`
            )
            .join('');
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

function initMembers(membersUrl) {
    const list = document.getElementById('issue-members');
    const select = document.getElementById('member-select');
    const attachBtn = document.getElementById('attach-member-btn');
    if (!list || !attachBtn) {
        return;
    }

    const render = (members) => {
        list.innerHTML = members
            .map(
                (member) => `
                <li class="flex items-center justify-between text-sm">
                    <span>${escapeHtml(member.name)}</span>
                    <button type="button" data-detach-member="${member.id}" class="text-red-600 hover:underline">Remove</button>
                </li>`
            )
            .join('');
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

function initComments(commentsUrl) {
    const form = document.getElementById('comment-form');
    const listEl = document.getElementById('comment-list');
    const loadMoreBtn = document.getElementById('load-more-comments');
    if (!form || !listEl) {
        return;
    }

    let nextPageUrl = commentsUrl;

    const commentMarkup = (comment) => `
        <li class="border-b pb-2">
            <p class="text-sm font-medium text-gray-800">${escapeHtml(comment.author_name)}</p>
            <p class="text-sm text-gray-600">${escapeHtml(comment.body)}</p>
        </li>`;

    const loadPage = async () => {
        if (!nextPageUrl) {
            return;
        }
        const page = await getJson(nextPageUrl);
        listEl.insertAdjacentHTML('beforeend', page.data.map(commentMarkup).join(''));
        nextPageUrl = page.next_page_url;
        loadMoreBtn.classList.toggle('hidden', !nextPageUrl);
    };

    loadMoreBtn.addEventListener('click', loadPage);

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors(form);

        const payload = {
            author_name: form.author_name.value,
            body: form.body.value,
        };

        try {
            const { data } = await postJson(form.action, payload);
            listEl.insertAdjacentHTML('afterbegin', commentMarkup(data));
            form.reset();
        } catch (error) {
            if (error.status === 422) {
                showErrors(form, error.data.errors);
            }
        }
    });

    loadPage();
}

function clearErrors(form) {
    form.querySelectorAll('[data-error]').forEach((el) => (el.textContent = ''));
}

function showErrors(form, errors) {
    Object.entries(errors ?? {}).forEach(([field, messages]) => {
        const el = form.querySelector(`[data-error="${field}"]`);
        if (el) {
            el.textContent = messages[0];
        }
    });
}

function escapeHtml(value) {
    const div = document.createElement('div');
    div.textContent = value ?? '';
    return div.innerHTML;
}

export { escapeHtml };
