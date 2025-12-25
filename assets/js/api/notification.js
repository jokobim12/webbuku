document.addEventListener('DOMContentLoaded', () => {
    const notifBtn = document.getElementById('notif-btn');
    const notifDropdown = document.getElementById('notif-dropdown');
    const notifList = document.getElementById('notif-list');
    const notifBadge = document.getElementById('notif-badge');
    const markReadBtn = document.getElementById('mark-read-btn');

    if (!notifBtn) return; // Not logged in

    let isOpen = false;

    // Toggle Dropdown
    notifBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        isOpen = !isOpen;
        if (isOpen) {
            notifDropdown.classList.remove('hidden');
            fetchNotifications(); // Refresh on open
        } else {
            notifDropdown.classList.add('hidden');
        }
    });

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (!notifDropdown.contains(e.target) && !notifBtn.contains(e.target)) {
            notifDropdown.classList.add('hidden');
            isOpen = false;
        }
    });

    // Mark as read
    markReadBtn.addEventListener('click', () => {
        fetch('/api/read_notifications.php')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    fetchNotifications();
                    markReadBtn.classList.add('hidden');
                }
            });
    });

    // Initial Load
    fetchNotifications();

    // Poll every 60s
    setInterval(fetchNotifications, 60000);

    function fetchNotifications() {
        fetch('/api/get_notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    updateBadge(data.unread_count);
                    renderList(data.notifications);
                    
                    if (data.unread_count > 0) {
                        markReadBtn.classList.remove('hidden');
                    } else {
                        markReadBtn.classList.add('hidden');
                    }
                }
            })
            .catch(console.error);
    }

    function updateBadge(count) {
        if (count > 0) {
            notifBadge.classList.remove('hidden');
        } else {
            notifBadge.classList.add('hidden');
        }
    }

    function renderList(notifications) {
        if (notifications.length === 0) {
            notifList.innerHTML = '<div class="p-4 text-center text-gray-500 text-sm">Tidak ada notifikasi baru</div>';
            return;
        }

        notifList.innerHTML = notifications.map(n => {
            const bgClass = n.is_read == 0 ? 'bg-emerald-50 dark:bg-emerald-900/10' : '';
            let content = '';
            
            switch(n.type) {
                case 'like':
                    content = `beterima kasih, karena Anda telah menyukai <span class="font-medium text-gray-900 dark:text-gray-200">${n.book_title}</span>`;
                    break;
                case 'comment':
                    content = `mengomentari <span class="font-medium text-gray-900 dark:text-gray-200">${n.book_title}</span>`;
                    break;
                case 'follow':
                    content = `mulai mengikuti Anda`;
                    break;
            }

            return `
                <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-50 dark:border-gray-700 last:border-0 ${bgClass}">
                    <div class="flex gap-3">
                        <img src="${n.actor_avatar}" class="w-8 h-8 rounded-full object-cover mt-1">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-semibold text-gray-900 dark:text-white">${n.actor_name}</span> ${content}
                            </p>
                            <span class="text-xs text-gray-400 mt-1 block">${n.time}</span>
                        </div>
                    </div>
                </div>
            `;
        }).join('');
    }
});
