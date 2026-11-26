<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\Label;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LabelTest extends TestCase
{
    /**
     * @return array<string, array{0: string, 1: string}>
     */
    public static function labels(): array
    {
        return [
            'sentence case' => ['First name', 'First Name'],
            'minor words stay lowercase' => ['time in grade', 'Time in Grade'],
            'a leading minor word is capitalized' => ['of record', 'Of Record'],
            'acronyms' => ['api key url', 'API Key URL'],
            'already title case' => ['RSVP Deadline', 'RSVP Deadline'],
        ];
    }

    #[DataProvider('labels')]
    public function test_title_case(string $label, string $expected): void
    {
        $this->assertSame($expected, Label::titleCase($label));
    }

    public function test_names_become_title_case_labels(): void
    {
        $this->assertSame('Registration Deadline', Label::fromName('registration_deadline'));
        $this->assertSame('Last Seen At', Label::fromName('lastSeenAt'));
        $this->assertSame('Author', Label::fromName('author_id'));
        $this->assertSame('Document', Label::fromName('documentId'));
        $this->assertSame('ID', Label::fromName('id'));
    }

    public function test_unlabelled_filament_components_default_to_title_case(): void
    {
        $this->assertSame('First Name', TextInput::make('first_name')->getLabel());
        $this->assertSame('Email Address', TextEntry::make('user.email_address')->getLabel());
        $this->assertSame('Rank', TextColumn::make('rank.name')->getLabel());
        $this->assertSame('Is Active', TernaryFilter::make('is_active')->getLabel());
        $this->assertSame('Author', Select::make('author_id')->getLabel());
        $this->assertSame('Document', Select::make('document_id')->relationship('document', 'name')->getLabel());
        $this->assertSame('Position', Select::make('position_id')->relationship('position.unit', 'name')->getLabel());
    }

    public function test_explicit_and_built_in_labels_still_win(): void
    {
        $this->assertSame('Given name', TextInput::make('first_name')->label('Given name')->getLabel());
        $this->assertSame('Created', TextColumn::make('created_at')->getLabel());
    }
}
