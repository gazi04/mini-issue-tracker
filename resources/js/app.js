import Alpine from 'alpinejs';

import initIssueShow from './features/issue-show';
import initIssueSearch from './features/issue-search';
import initTagCreate from './features/tag-create';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initIssueShow();
    initIssueSearch();
    initTagCreate();
});
