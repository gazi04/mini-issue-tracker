import { getHtml } from '../lib/http';

export default function initIssueFilter() {
    const form = document.getElementById('issue-filters');
    const results = document.getElementById('issue-results');
    if (!form || !results) {
        return;
    }

    const buildUrl = () => {
        const params = new URLSearchParams(new FormData(form));
        const query = params.toString();
        return query ? `${form.action}?${query}` : form.action;
    };

    const load = async (url, { push = true } = {}) => {
        results.innerHTML = await getHtml(url);
        if (push) {
            history.pushState({}, '', url);
        }
    };

    let timer;
    form.querySelector('#issue-search')?.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(() => load(buildUrl()), 300);
    });

    form.querySelectorAll('select').forEach((select) => {
        select.addEventListener('change', () => load(buildUrl()));
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        load(buildUrl());
    });

    // Keep paginator links inside the results AJAX.
    results.addEventListener('click', (event) => {
        const link = event.target.closest('nav a[href]');
        if (!link) {
            return;
        }
        event.preventDefault();
        load(link.href);
    });

    window.addEventListener('popstate', () => load(location.href, { push: false }));
}
