<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\DocumentResource\Actions;

use App\Models\Document;
use App\Models\User;
use Closure;
use Filament\Actions\Action;
use Illuminate\Database\Eloquent\Model;

class ViewDocumentAction extends Action
{
    protected Closure|Document|null $document = null;

    protected Closure|User|null $user = null;

    protected Closure|Model|null $attached = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->visible(fn (): bool => $this->getDocument() instanceof Document);
        $this->modalSubmitAction(false);
        $this->modalCancelActionLabel('Close');
        $this->modalHeading(fn () => $this->getDocument()->name ?? 'Document');
        $this->modalContent(fn (): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View => view('filament.app.view-document', [
            'document' => $this->getDocument(),
            'user' => $this->getUser(),
            'model' => $this->getAttached(),
        ]));
    }

    public static function getDefaultName(): ?string
    {
        return 'view_document';
    }

    public function document(Closure|Document|null $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function user(Closure|User|null $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function attached(Closure|Model|null $attached): static
    {
        $this->attached = $attached;

        return $this;
    }

    public function getDocument(): ?Document
    {
        return $this->evaluate($this->document);
    }

    public function getUser(): ?User
    {
        return $this->evaluate($this->user);
    }

    public function getAttached(): ?Model
    {
        return $this->evaluate($this->attached);
    }
}
