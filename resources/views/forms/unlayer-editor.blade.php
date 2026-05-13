<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        wire:ignore
        x-data="{
            design: $wire.entangle('{{ $getStatePath() }}'),
            html: $wire.entangle('{{ $getHtmlStatePath() }}'),
            editor: null,
            syncing: false,
            importModalOpen: false,
            importJson: '',
            importError: '',

            init() {
                this.loadScript().then(() => this.setupEditor());
            },

            loadScript() {
                return new Promise(resolve => {
                    if (window.unlayer) return resolve();
                    const s = document.createElement('script');
                    s.src = 'https://editor.unlayer.com/embed.js';
                    s.onload = resolve;
                    document.head.appendChild(s);
                });
            },

            setupEditor() {
                this.editor = unlayer.createEditor({
                    id: '{{ $getId() }}-canvas',
                    displayMode: 'email',
                    appearance: { theme: 'modern_light' },
                    features: { colorPicker: { presets: [] } },
                });

                if (this.design) {
                    try { this.editor.loadDesign(JSON.parse(this.design)); } catch(e) {}
                }

                let timer;
                this.editor.addEventListener('design:updated', () => {
                    clearTimeout(timer);
                    timer = setTimeout(() => this.exportDesign(), 1500);
                });

                this.$watch('design', val => {
                    if (this.syncing || !val || !this.editor) return;
                    try { this.editor.loadDesign(JSON.parse(val)); } catch(e) {}
                });
            },

            exportDesign() {
                if (!this.editor) return;
                this.syncing = true;
                this.editor.exportHtml(data => {
                    this.design = JSON.stringify(data.design);
                    this.html = data.html;
                    setTimeout(() => { this.syncing = false; }, 50);
                });
            },

            openImportModal() {
                this.importJson = '';
                this.importError = '';
                this.importModalOpen = true;
            },

            confirmImport() {
                let parsed;
                try {
                    parsed = JSON.parse(this.importJson);
                } catch (e) {
                    this.importError = 'Invalid JSON. Please paste a valid Unlayer design.';
                    return;
                }
                if (!parsed.counters || !parsed.body) {
                    this.importError = 'This does not look like a valid Unlayer design JSON.';
                    return;
                }
                this.editor.loadDesign(parsed);
                this.exportDesign();
                this.importModalOpen = false;
            },
        }"
    >
        {{-- Toolbar --}}
        <div class="flex justify-end mb-2">
            <button
                type="button"
                x-on:click="openImportModal()"
                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-600 bg-white border border-gray-300 shadow-sm hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                </svg>
                Import JSON
            </button>
        </div>

        {{-- Editor canvas --}}
        <div
            id="{{ $getId() }}-canvas"
            style="height: 700px; width: 100%; border-radius: 0.5rem;"
        ></div>

        {{-- Import modal --}}
        <div
            x-show="importModalOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
        >
            {{-- Backdrop --}}
            <div
                class="absolute inset-0 bg-black/50"
                x-on:click="importModalOpen = false"
            ></div>

            {{-- Dialog --}}
            <div class="relative z-10 w-full max-w-2xl rounded-xl bg-white shadow-xl dark:bg-gray-900 p-6">
                <h2 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Import Unlayer JSON</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                    Paste a design JSON from <a href="https://unlayer.com/templates" target="_blank" class="underline">unlayer.com/templates</a> or any exported Unlayer design.
                </p>

                <textarea
                    x-model="importJson"
                    rows="10"
                    placeholder='{ "counters": {}, "body": { ... } }'
                    class="w-full rounded-lg border border-gray-300 bg-gray-50 p-3 font-mono text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200"
                ></textarea>

                <p x-show="importError" x-text="importError" class="mt-2 text-sm text-danger-600"></p>

                <div class="mt-4 flex justify-end gap-3">
                    <button
                        type="button"
                        x-on:click="importModalOpen = false"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        x-on:click="confirmImport()"
                        class="rounded-lg px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-500"
                    >
                        Import
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
