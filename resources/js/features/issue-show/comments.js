import { getJson, postJson } from '../../lib/http';
import { clearErrors, showErrors } from '../../lib/dom';
import { commentMarkup } from './templates';

export default function initComments(commentsUrl) {
    const form = document.getElementById('comment-form');
    const listEl = document.getElementById('comment-list');
    const loadMoreBtn = document.getElementById('load-more-comments');
    if (!form || !listEl) {
        return;
    }

    let nextPageUrl = commentsUrl;

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
