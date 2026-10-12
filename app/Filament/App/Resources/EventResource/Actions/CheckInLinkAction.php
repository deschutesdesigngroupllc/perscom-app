<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\EventResource\Actions;

use App\Models\Event;
use App\Services\EventAttendanceService;
use chillerlan\QRCode\QRCode;
use Filament\Actions\Action;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\View\View;

class CheckInLinkAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Check-In Link');
        $this->icon(Heroicon::OutlinedQrCode);
        $this->color('gray');

        $this->modalHeading('Member Check-In');
        $this->modalDescription('Share this link or display the QR code. Members can check in from one hour before the event starts until one hour after it ends.');
        $this->modalSubmitAction(false);
        $this->modalCancelActionLabel('Close');

        $this->authorize('update');

        $this->modalContent(function (Event $record): View {
            $url = EventAttendanceService::checkInUrl($record);
            [$opens, $closes] = EventAttendanceService::checkInWindow($record) ?? [null, null];

            return view('filament.app.events.check-in-link', [
                'url' => $url,
                'qrCode' => (new QRCode)->render($url),
                'opens' => $opens,
                'closes' => $closes,
            ]);
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'checkInLink';
    }
}
