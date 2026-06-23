import { escapeHtml } from '../../lib/dom';

export function tagMarkup(tag) {
    return `
        <span class="inline-flex items-center gap-1 text-xs px-2 py-1 rounded text-white" style="background-color: ${tag.color ?? '#6b7280'}">
            ${escapeHtml(tag.name)}
            <button type="button" data-detach-tag="${tag.id}" class="font-bold hover:text-gray-200">&times;</button>
        </span>`;
}

export function memberMarkup(member) {
    return `
        <li class="flex items-center justify-between text-sm">
            <span>${escapeHtml(member.name)}</span>
            <button type="button" data-detach-member="${member.id}" class="text-red-600 hover:underline">Remove</button>
        </li>`;
}

export function commentMarkup(comment) {
    return `
        <li class="border-b pb-2">
            <p class="text-sm font-medium text-gray-800">${escapeHtml(comment.author_name)}</p>
            <p class="text-sm text-gray-600">${escapeHtml(comment.body)}</p>
        </li>`;
}
