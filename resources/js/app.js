// Import SortableJS and make it globally available for Alpine.js
import Sortable from 'sortablejs';
import Quill from 'quill';
window.Sortable = Sortable;
window.Quill = Quill;
window.dispatchEvent(new Event('quill:ready'));

// Keep session alive by pinging server every 10 minutes
setInterval(() => {
    fetch('/keep-alive', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        credentials: 'same-origin'
    }).catch(() => {
        // Silently handle errors
    });
}, 600000); // 10 minutes
