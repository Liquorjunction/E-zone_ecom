const PROSE_SELECTOR = '.prose-content';

function isElement(node) {
    return node && node.nodeType === Node.ELEMENT_NODE;
}

function normalizeQuillLists(root) {
    const orderedLists = root.querySelectorAll('ol');

    orderedLists.forEach((ol) => {
        const directItems = Array.from(ol.children).filter((child) => child.tagName === 'LI');
        if (!directItems.length) {
            return;
        }

        const listTypes = new Set(
            directItems.map((li) => li.getAttribute('data-list') || 'ordered')
        );

        if (listTypes.size === 1 && listTypes.has('bullet')) {
            const ul = document.createElement('ul');
            ul.className = ol.className;

            directItems.forEach((li) => {
                li.removeAttribute('data-list');
                ul.appendChild(li);
            });

            ol.replaceWith(ul);
            return;
        }

        directItems.forEach((li) => {
            const type = li.getAttribute('data-list');
            if (type === 'bullet') {
                li.classList.add('prose-li-bullet');
            }
            if (type === 'ordered') {
                li.classList.add('prose-li-ordered');
            }
            if (type === 'check' || type === 'checked' || type === 'unchecked') {
                li.classList.add('prose-li-check');
            }
        });
    });
}

function normalizeTables(root) {
    const tables = root.querySelectorAll('table');
    tables.forEach((table) => {
        const parent = table.parentElement;
        if (parent && parent.classList.contains('prose-table-wrap')) {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'prose-table-wrap';
        table.replaceWith(wrapper);
        wrapper.appendChild(table);
    });
}

function normalizeMedia(root) {
    const images = root.querySelectorAll('img');
    images.forEach((img) => {
        if (!img.getAttribute('loading')) {
            img.setAttribute('loading', 'lazy');
        }
        if (!img.getAttribute('decoding')) {
            img.setAttribute('decoding', 'async');
        }
    });

    const iframes = root.querySelectorAll('iframe');
    iframes.forEach((iframe) => {
        const parent = iframe.parentElement;
        if (parent && parent.classList.contains('prose-media-wrap')) {
            return;
        }

        const wrapper = document.createElement('div');
        wrapper.className = 'prose-media-wrap';
        iframe.replaceWith(wrapper);
        wrapper.appendChild(iframe);
    });
}

function normalizeLinks(root) {
    const links = root.querySelectorAll('a[href]');

    links.forEach((link) => {
        const href = link.getAttribute('href') || '';
        const isExternal = /^https?:\/\//i.test(href) && !href.includes(window.location.host);

        if (isExternal) {
            link.setAttribute('target', '_blank');
            link.setAttribute('rel', 'noopener noreferrer');
        }
    });
}

function cleanupEmptyParagraphs(root) {
    const paragraphs = root.querySelectorAll('p');
    paragraphs.forEach((p) => {
        if (p.children.length === 1 && p.firstElementChild.tagName === 'BR') {
            p.remove();
        }
    });
}

function normalizeSingleRoot(root) {
    if (!isElement(root)) {
        return;
    }

    normalizeQuillLists(root);
    normalizeTables(root);
    normalizeMedia(root);
    normalizeLinks(root);
    cleanupEmptyParagraphs(root);

    root.setAttribute('data-prose-processed', 'true');
}

function normalizeAllProse() {
    document.querySelectorAll(PROSE_SELECTOR).forEach(normalizeSingleRoot);
}

function observeDynamicProse() {
    const observer = new MutationObserver((mutations) => {
        mutations.forEach((mutation) => {
            mutation.addedNodes.forEach((node) => {
                if (!isElement(node)) {
                    return;
                }

                if (node.matches && node.matches(PROSE_SELECTOR)) {
                    normalizeSingleRoot(node);
                }

                node.querySelectorAll && node.querySelectorAll(PROSE_SELECTOR).forEach(normalizeSingleRoot);
            });
        });
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

document.addEventListener('DOMContentLoaded', () => {
    normalizeAllProse();
    observeDynamicProse();
});
