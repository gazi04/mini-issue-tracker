import Alpine from 'alpinejs';

import initIssueShow from './features/issue-show';
import initIssueFilter from './features/issue-filter';
import initTagCreate from './features/tag-create';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initIssueShow();
    initIssueFilter();
    initTagCreate();
});
