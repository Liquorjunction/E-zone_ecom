<div id="universal-popup"
    class="fixed inset-0 flex items-center justify-center bg-black/60 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300 ease-out"
    style="z-index: 99999;">

    <div id="universal-popup-overlay" class="absolute inset-0"></div>

    <div id="universal-popup-card"
        class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm mx-4 p-8 sm:p-8 flex flex-col items-center transform scale-95 opacity-0 transition-all duration-300 ease-out border-2">

        <button id="universal-popup-x"
            type="button"
            aria-label="Close"
            class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-primary/30 rounded-full p-1.5 transition-all duration-200 hover:bg-gray-100 group">
            <svg class="w-5 h-5 transition-transform duration-200 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        <div class="mb-5 transform transition-all duration-500 ease-out animate-bounce-subtle">
            <span id="universal-popup-icon"></span>
        </div>

        <div id="universal-popup-message"
            class="text-center text-lg sm:text-lg font-semibold mb-6 sm:mb-6 text-gray-800 leading-relaxed px-2 max-h-64 overflow-y-auto scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-transparent hover:scrollbar-thumb-gray-400">
        </div>

        <div id="universal-popup-actions" class="w-full flex flex-col gap-3">
            <!-- Single OK button (default) -->
            <button id="universal-popup-close"
                type="button"
                class="w-full max-w-30 mx-auto px-3 py-1 sm:px-4 sm:py-2 bg-primary text-white rounded-full font-semibold shadow-lg hover:shadow-xl hover:scale-105 focus:ring-4 focus:ring-primary/30 focus:outline-none border border-primary/20 transition-all duration-200 active:scale-95">
                OK
            </button>

            <!-- Confirmation buttons (conditional) -->
            <div id="universal-popup-confirm-buttons" class="hidden w-full flex gap-3">
                <button id="universal-popup-cancel"
                    type="button"
                    class="flex-1 px-3 py-1 sm:px-4 sm:py-2 bg-gray-200 text-gray-700 rounded-full font-semibold shadow-md hover:shadow-lg hover:scale-105 focus:ring-4 focus:ring-gray-300/30 focus:outline-none border border-gray-300 transition-all duration-200 active:scale-95 hover:bg-gray-300">
                    Cancel
                </button>
                <button id="universal-popup-confirm"
                    type="button"
                    class="flex-1 px-3 py-1 sm:px-4 sm:py-2 bg-primary text-white rounded-full font-semibold shadow-lg hover:shadow-xl hover:scale-105 focus:ring-4 focus:ring-primary/30 focus:outline-none border border-primary/20 transition-all duration-200 active:scale-95">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
