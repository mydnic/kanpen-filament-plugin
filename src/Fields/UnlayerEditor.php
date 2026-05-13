<?php

namespace Mydnic\KanpenFilamentPlugin\Fields;

use Filament\Forms\Components\Field;

class UnlayerEditor extends Field
{
    protected string $view = 'kanpen-filament-plugin::forms.unlayer-editor';

    protected string $htmlField = 'content_html';

    public function htmlField(string $name): static
    {
        $this->htmlField = $name;

        return $this;
    }

    public function getHtmlStatePath(): string
    {
        $parts = explode('.', $this->getStatePath());
        array_pop($parts);
        $parts[] = $this->htmlField;

        return implode('.', $parts);
    }
}
