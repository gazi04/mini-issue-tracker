import { getJson } from '../lib/http';
import { escapeHtml } from '../lib/dom';

export default function initIssueSearch() {
    const input = document.getElementById('issue-search');
    const list = document.getElementById('issue-list');
    const pagination = document.getElementById('issue-pagination');
    if (!input || !list) {
        return;
    }

    let timer;

    input.addEventListener('input', () => {
        clearTimeout(timer);
        timer = setTimeout(async () => {
            const url = `${input.dataset.searchUrl}?q=${encodeURIComponent(input.value)}`;
            const { data } = await getJson(url);

            pagination?.classList.add('hidden');
            list.innerHTML = data.length ? data.map(rowMarkup).join('') : '<p class="py-3 text-gray-500">No issues match.</p>';
        }, 300);
    });
}

function rowMarkup(issue) {
    const tags = issue.tags
        .map(
            (tag) =>
                `<span class="text-xs px-2 py-0.5 rounded text-white" style="background-color: ${tag.color ?? '#6b7280'}">${escapeHtml(tag.name)}</span>`
        )
        .join(' ');

    return `
        <div class="flex items-center justify-between py-3">
            <div>
                <a href="${issue.url}" class="font-medium text-indigo-600 hover:underline">${escapeHtml(issue.title)}</a>
                <p class="text-xs text-gray-500">${escapeHtml(issue.project)}</p>
                <div class="mt-1 flex flex-wrap gap-1">${tags}</div>
            </div>
            <div class="text-right space-y-1">
                <span class="block text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">${escapeHtml(issue.status)}</span>
                <span class="block text-xs text-gray-500">${escapeHtml(issue.priority)}</span>
            </div>
        </div>`;
}
