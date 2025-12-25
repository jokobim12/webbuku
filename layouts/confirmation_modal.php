<!-- Global Confirmation Modal -->
<div id="globalConfirmModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity opacity-0" id="globalModalBackdrop"></div>

    <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <!-- Modal Panel -->
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" id="globalModalPanel">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10" id="globalModalIconBg">
                            <svg class="h-6 w-6 text-red-600" id="globalModalIcon" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-bold leading-6 text-gray-900" id="globalModalTitle">Konfirmasi</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="globalModalMessage">Apakah anda yakin ingin melakukan tindakan ini?</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="button" id="globalModalConfirmBtn" class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                        Ya, Lanjutkan
                    </button>
                    <button type="button" id="globalModalCancelBtn" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.ConfirmModal = {
        element: document.getElementById('globalConfirmModal'),
        backdrop: document.getElementById('globalModalBackdrop'),
        panel: document.getElementById('globalModalPanel'),
        title: document.getElementById('globalModalTitle'),
        message: document.getElementById('globalModalMessage'),
        confirmBtn: document.getElementById('globalModalConfirmBtn'),
        cancelBtn: document.getElementById('globalModalCancelBtn'),
        iconBg: document.getElementById('globalModalIconBg'),
        icon: document.getElementById('globalModalIcon'),
        onConfirm: null,

        show: function(title, message, callback, type = 'danger') {
            this.title.textContent = title;
            this.message.textContent = message;
            this.onConfirm = callback;

            // Type styling
            if (type === 'danger') {
                this.iconBg.className = 'mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10';
                this.icon.className = 'h-6 w-6 text-red-600';
                this.confirmBtn.className = 'inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors';
                this.confirmBtn.textContent = 'Hapus';
            } else {
                // Info/Logout style
                this.iconBg.className = 'mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 sm:mx-0 sm:h-10 sm:w-10';
                this.icon.className = 'h-6 w-6 text-emerald-600';
                this.icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />';
                this.confirmBtn.className = 'inline-flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 sm:ml-3 sm:w-auto transition-colors';
                this.confirmBtn.textContent = 'Ya, Lanjutkan';
            }

            // Show
            this.element.classList.remove('hidden');
            // Animation handling
            setTimeout(() => {
                this.backdrop.classList.remove('opacity-0');
                this.panel.classList.remove('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');
                this.panel.classList.add('opacity-100', 'translate-y-0', 'sm:scale-100');
            }, 10);
        },

        close: function() {
            // Animation
            this.backdrop.classList.add('opacity-0');
            this.panel.classList.remove('opacity-100', 'translate-y-0', 'sm:scale-100');
            this.panel.classList.add('opacity-0', 'translate-y-4', 'sm:translate-y-0', 'sm:scale-95');

            setTimeout(() => {
                this.element.classList.add('hidden');
                this.onConfirm = null;
            }, 300);
        }
    };

    // Event Listeners
    document.getElementById('globalModalCancelBtn').addEventListener('click', () => window.ConfirmModal.close());
    document.getElementById('globalModalConfirmBtn').addEventListener('click', () => {
        if (window.ConfirmModal.onConfirm) window.ConfirmModal.onConfirm();
        window.ConfirmModal.close();
    });

    // Handle Logout
    function confirmLogout(event, url) {
        event.preventDefault();
        window.ConfirmModal.show(
            'Konfirmasi Keluar',
            'Apakah anda yakin ingin keluar dari akun anda?',
            () => {
                window.location.href = url;
            },
            'info'
        );
    }
</script>
