<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\AutomationResource\Pages;

use App\Filament\App\Resources\AutomationResource;
use App\Services\AutomationService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;
use UnitEnum;

class CreateAutomation extends CreateRecord
{
    protected static string $resource = AutomationResource::class;

    public function mount(): void
    {
        parent::mount();

        $templateKey = request()->query('template');
        if ($templateKey !== null) {
            $this->fillFromTemplate($templateKey);
        }
    }

    public function fillFromTemplate(string $templateKey): void
    {
        $templates = AutomationService::getTemplates();
        $template = $templates[$templateKey] ?? null;

        if ($template === null) {
            return;
        }

        $formData = $this->prepareTemplateData($template['data']);
        $this->form->fill($formData);
    }

    /**
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('useTemplate')
                ->label('Use Template')
                ->icon(Heroicon::OutlinedDocumentDuplicate)
                ->color('gray')
                ->visible(fn (): bool => AutomationService::getTemplates() !== [])
                ->schema([
                    Radio::make('template')
                        ->label('Select a Template')
                        ->options(fn (): array => collect(AutomationService::getTemplates())
                            ->mapWithKeys(fn (array $template, string $key): array => [
                                $key => $template['name'],
                            ])
                            ->toArray())
                        ->descriptions(fn (): array => collect(AutomationService::getTemplates())
                            ->mapWithKeys(fn (array $template, string $key): array => [
                                $key => new HtmlString($this->buildTemplateDescription($template)),
                            ])
                            ->toArray())
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $templates = AutomationService::getTemplates();
                    $template = $templates[$data['template']] ?? null;

                    if ($template === null) {
                        return;
                    }

                    $formData = $this->prepareTemplateData($template['data']);

                    $this->form->fill($formData);
                }),
        ];
    }

    /**
     * Build the description string for a template, including prerequisites.
     *
     * @param  array{name: string, description: string, category: string, prerequisites?: list<string>, data: array<string, mixed>}  $template
     */
    private function buildTemplateDescription(array $template): string
    {
        $description = $template['description'];

        $prerequisites = $template['prerequisites'] ?? [];
        if ($prerequisites !== []) {
            $description .= '<br><br>Prerequisites:<br>• '.implode('<br>• ', $prerequisites);
        }

        return $description;
    }

    /**
     * Prepare template data for form filling.
     *
     * Converts enums to their values for proper form compatibility.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function prepareTemplateData(array $data): array
    {
        $prepared = [];

        foreach ($data as $key => $value) {
            if ($value instanceof BackedEnum) {
                $prepared[$key] = $value->value;
            } elseif ($value instanceof UnitEnum) {
                $prepared[$key] = $value->name;
            } elseif (is_array($value)) {
                $prepared[$key] = $this->prepareTemplateData($value);
            } else {
                $prepared[$key] = $value;
            }
        }

        return $prepared;
    }
}
