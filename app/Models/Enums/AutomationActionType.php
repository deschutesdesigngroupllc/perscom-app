<?php

declare(strict_types=1);

namespace App\Models\Enums;

use Filament\Support\Contracts\HasLabel;

enum AutomationActionType: string implements HasLabel
{
    case WEBHOOK = 'webhook';
    case MESSAGE = 'message';
    case MODEL_UPDATE = 'model_update';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::WEBHOOK => 'Send Webhook',
            self::MESSAGE => 'Send Message',
            self::MODEL_UPDATE => 'Update Resource',
        };
    }
}
