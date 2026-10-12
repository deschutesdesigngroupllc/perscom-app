<?php

declare(strict_types=1);

namespace App\Filament\App\Clusters\Settings\Pages;

use App\Filament\App\Clusters\Settings;
use App\Filament\App\Resources\PassportTokenResource;
use BackedEnum;
use BezhanSalleh\FilamentShield\Support\Utils;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Phiki\Grammar\Grammar;
use UnitEnum;

class ConnectAiTools extends Page
{
    protected static ?string $cluster = Settings::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string|UnitEnum|null $navigationGroup = 'AI & Integrations';

    protected static ?string $navigationLabel = 'Connect AI Tools';

    protected static ?int $navigationSort = 11;

    protected static ?string $title = 'Connect AI Tools';

    protected ?string $subheading = 'Connect Claude, Cursor and other AI tools to PERSCOM through the Model Context Protocol (MCP).';

    public static function canAccess(): bool
    {
        return parent::canAccess()
            && Auth::user()->hasRole(Utils::getSuperAdminName())
            && ! App::isDemo();
    }

    public function content(Schema $schema): Schema
    {
        $url = route('mcp.server');

        return $schema
            ->components([
                Section::make('MCP Server')
                    ->description('This connection is read-only. AI tools can look up members, the organization structure and personnel records, but cannot change anything.')
                    ->schema([
                        TextEntry::make('url')
                            ->label('Server URL')
                            ->state($url)
                            ->copyable()
                            ->fontFamily('mono'),
                        TextEntry::make('access')
                            ->hiddenLabel()
                            ->state('Each person signs in with their own PERSCOM account. They only see what their account is allowed to see and what they approve when connecting.'),
                    ]),
                Section::make('Claude')
                    ->description('In Claude, open Settings, then Connectors, choose Add Custom Connector and paste the server URL above. Claude will ask you to sign in to PERSCOM.')
                    ->collapsible(),
                Section::make('Claude Code')
                    ->collapsible()
                    ->schema([
                        CodeEntry::make('claude_code')
                            ->hiddenLabel()
                            ->state(sprintf('claude mcp add --transport http perscom %s', $url))
                            ->grammar(Grammar::Shellscript)
                            ->copyable(),
                    ]),
                Section::make('Cursor')
                    ->description('Add this to .cursor/mcp.json in your project or ~/.cursor/mcp.json for every project.')
                    ->collapsible()
                    ->schema([
                        CodeEntry::make('cursor')
                            ->hiddenLabel()
                            ->state($this->configJson(['url' => $url]))
                            ->grammar(Grammar::Json)
                            ->copyable(),
                    ]),
                Section::make('Claude Desktop')
                    ->description('Add this to claude_desktop_config.json. It uses mcp-remote, which requires Node.js.')
                    ->collapsible()
                    ->schema([
                        CodeEntry::make('claude_desktop')
                            ->hiddenLabel()
                            ->state($this->configJson(['command' => 'npx', 'args' => ['mcp-remote', $url]]))
                            ->grammar(Grammar::Json)
                            ->copyable(),
                    ]),
                Section::make('Tools Without Sign In')
                    ->description('For tools that cannot sign in, create an API key with the view scopes you want to share and send it as a bearer token in the Authorization header.')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        TextEntry::make('api_keys')
                            ->hiddenLabel()
                            ->state('Manage API Keys')
                            ->url(PassportTokenResource::getUrl())
                            ->color('primary'),
                    ]),
            ]);
    }

    /**
     * @param  array<string, mixed>  $server
     */
    protected function configJson(array $server): string
    {
        return (string) json_encode(['mcpServers' => ['perscom' => $server]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
