import Alpine from 'alpinejs';

import initIssueShow from './issue-show';
import initIssueSearch from './issue-search';
import initTagCreate from './tags';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initIssueShow();
    initIssueSearch();
    initTagCreate();
});
