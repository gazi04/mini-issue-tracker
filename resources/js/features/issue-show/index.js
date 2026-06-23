import initTags from './tags';
import initMembers from './members';
import initComments from './comments';

export default function initIssueShow() {
    const root = document.querySelector('[data-issue-root]');
    if (!root) {
        return;
    }

    initTags(root.dataset.tagsUrl);
    initMembers(root.dataset.membersUrl);
    initComments(root.dataset.commentsUrl);
}
