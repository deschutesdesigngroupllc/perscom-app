<?php

declare(strict_types=1);

namespace App\Filament\App\Widgets;

use App\Filament\App\Clusters\Settings\Pages\ConnectAiTools;
use App\Filament\App\Pages\Dashboard;
use App\Models\PassportToken;
use App\Services\UserSettingsService;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class ConnectAiAgentWidget extends Widget
{
    public const string DISMISSED_KEY = 'connect_ai_agent';

    protected static ?int $sort = -10;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected string $view = 'filament.app.widgets.connect-ai-agent-widget';

    public static function canView(): bool
    {
        return ConnectAiTools::canAccess()
            && ! static::isDismissed()
            && ! static::hasConnectedAiTool();
    }

    public static function isDismissed(): bool
    {
        return in_array(static::DISMISSED_KEY, (array) UserSettingsService::get('dismissed_widgets', []), true);
    }

    /**
     * MCP clients request the mcp:use scope when they connect, so an active token carrying it means
     * this person has already linked an AI tool.
     */
    public static function hasConnectedAiTool(): bool
    {
        return PassportToken::query()
            ->where('user_id', Auth::id())
            ->where('revoked', false)
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->whereJsonContains('scopes', 'mcp:use')
            ->exists();
    }

    public function dismiss(): void
    {
        $dismissed = (array) UserSettingsService::get('dismissed_widgets', []);

        UserSettingsService::save('dismissed_widgets', array_values(array_unique([...$dismissed, static::DISMISSED_KEY])));

        $this->redirect(Dashboard::getUrl(), navigate: true);
    }

    public function getServerUrl(): string
    {
        return route('mcp.server');
    }

    public function getSetupUrl(): string
    {
        return ConnectAiTools::getUrl();
    }

    /**
     * @return array<int, string>
     */
    public function getExamplePrompts(): array
    {
        return [
            'Give me a breakdown of our personnel by unit and rank.',
            'Which members have not been seen in the last 30 days?',
            'Summarize the awards and promotions handed out this month.',
            'Who joined in the last 90 days, and what training have they completed?',
        ];
    }
}
