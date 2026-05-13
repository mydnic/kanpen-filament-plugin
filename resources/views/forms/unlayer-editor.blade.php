<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            design: $wire.entangle('{{ $getStatePath() }}'),
            html: $wire.entangle('{{ $getHtmlStatePath() }}'),
            editor: null,
            syncing: false,
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

            confirmImport() {
                this.importError = '';
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
                this.importJson = '';
                this.$dispatch('close-modal', { id: 'unlayer-import-{{ $getId() }}' });
            },
        }"
    >
        {{-- Toolbar --}}
        <div class="flex justify-end mb-2">
            <x-filament::button
                size="sm"
                color="gray"
                icon="heroicon-o-arrow-up-tray"
                x-on:click="importJson = ''; importError = ''; $dispatch('open-modal', { id: 'unlayer-import-{{ $getId() }}' })"
            >
                Import JSON
            </x-filament::button>
        </div>

        {{-- Canvas (wire:ignore scoped to the editor only) --}}
        <div wire:ignore>
            <div
                id="{{ $getId() }}-canvas"
                style="height: 700px; width: 100%; border-radius: 0.5rem;"
            ></div>
        </div>

        {{-- Import modal --}}
        <x-filament::modal id="unlayer-import-{{ $getId() }}" width="2xl">
            <x-slot name="heading">Import Unlayer JSON</x-slot>

            <x-slot name="description">
                Paste a design JSON from
                <a href="https://unlayer.com/templates" target="_blank" class="underline">unlayer.com/templates</a>
                or any exported Unlayer design.
            </x-slot>

            <x-filament::input.wrapper>
                <textarea
                    x-model="importJson"
                    rows="12"
                    placeholder='{ "counters": {}, "body": { ... } }'
                    class="block w-full font-mono text-xs border-none bg-transparent px-3 py-2 text-gray-950 placeholder:text-gray-400 focus:ring-0 dark:text-white dark:placeholder:text-gray-500"
                ></textarea>
            </x-filament::input.wrapper>

            <p x-show="importError" x-text="importError" class="mt-2 text-sm text-danger-600 dark:text-danger-400"></p>

            <x-slot name="footerActions">
                <x-filament::button x-on:click="confirmImport()">
                    Import
                </x-filament::button>

                <x-filament::button
                    color="gray"
                    x-on:click="$dispatch('close-modal', { id: 'unlayer-import-{{ $getId() }}' })"
                >
                    Cancel
                </x-filament::button>
            </x-slot>
        </x-filament::modal>
    </div>
</x-dynamic-component>
