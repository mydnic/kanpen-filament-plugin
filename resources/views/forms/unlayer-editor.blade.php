<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        wire:ignore
        x-data="{
            design: $wire.entangle('{{ $getStatePath() }}'),
            html: $wire.entangle('{{ $getHtmlStatePath() }}'),
            editor: null,
            syncing: false,

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
        }"
    >
        <div
            id="{{ $getId() }}-canvas"
            style="height: 700px; width: 100%; border-radius: 0.5rem;"
        ></div>
    </div>
</x-dynamic-component>
