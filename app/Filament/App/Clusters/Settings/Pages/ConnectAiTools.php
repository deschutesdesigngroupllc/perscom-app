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
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
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

    protected ?string $subheading = 'Connect Claude, ChatGPT, Gemini, Grok and other AI tools to PERSCOM through the Model Context Protocol (MCP).';

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
                Tabs::make('Tools')
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Claude')
                            ->schema([
                                $this->steps('claude_steps', [
                                    'Open Claude on the web, desktop or mobile and go to Settings, then Connectors.',
                                    'Choose Add Custom Connector, name it PERSCOM and paste the server URL.',
                                    'Select Connect and sign in to PERSCOM when prompted.',
                                ]),
                                $this->note('claude_note', 'Organization owners on Team and Enterprise plans add custom connectors for everyone from the organization settings.'),
                            ]),
                        Tab::make('Claude Code')
                            ->schema([
                                $this->note('claude_code_note', 'Run this in your terminal, then use /mcp inside Claude Code to sign in.'),
                                $this->code('claude_code', sprintf('claude mcp add --transport http perscom %s', $url), Grammar::Shellscript),
                            ]),
                        Tab::make('ChatGPT')
                            ->schema([
                                $this->steps('chatgpt_steps', [
                                    'In ChatGPT, open Settings, then Apps, then Advanced settings, and turn on Developer Mode.',
                                    'Choose Create, name the app PERSCOM and paste the server URL.',
                                    'Choose OAuth for authentication, save, and sign in to PERSCOM when prompted.',
                                ]),
                                $this->note('chatgpt_note', 'Custom apps are only offered on some ChatGPT plans, and the settings menu may be named Connectors in older versions.'),
                            ]),
                        Tab::make('Gemini')
                            ->schema([
                                $this->note('gemini_note', 'Add PERSCOM to Gemini CLI in ~/.gemini/settings.json, or .gemini/settings.json in a project. Gemini CLI opens a browser to sign in the first time PERSCOM is used, or run /mcp auth perscom.'),
                                $this->code('gemini', $this->configJson(['httpUrl' => $url]), Grammar::Json),
                            ]),
                        Tab::make('Grok')
                            ->schema([
                                $this->steps('grok_steps', [
                                    'Go to grok.com/connectors and choose New Connector, then Custom.',
                                    'Name it PERSCOM, paste the server URL and sign in to PERSCOM when prompted.',
                                ]),
                                $this->note('grok_api_note', 'Custom connectors require a paid Grok plan. To use PERSCOM from the xAI API instead, add it as a remote MCP tool with a PERSCOM API key.'),
                                $this->code('grok_api', (string) json_encode([
                                    'type' => 'mcp',
                                    'server_label' => 'perscom',
                                    'server_url' => $url,
                                    'authorization' => 'YOUR_PERSCOM_API_KEY',
                                ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), Grammar::Json),
                            ]),
                        Tab::make('Cursor')
                            ->schema([
                                $this->note('cursor_note', 'Add this to .cursor/mcp.json in your project, or ~/.cursor/mcp.json for every project. Cursor asks you to sign in the first time PERSCOM is used.'),
                                $this->code('cursor', $this->configJson(['url' => $url]), Grammar::Json),
                            ]),
                        Tab::make('VS Code')
                            ->schema([
                                $this->note('vscode_note', 'Add this to .vscode/mcp.json, or run MCP: Add Server from the Command Palette and choose HTTP. GitHub Copilot asks you to sign in the first time PERSCOM is used.'),
                                $this->code('vscode', (string) json_encode(['servers' => ['perscom' => ['type' => 'http', 'url' => $url]]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), Grammar::Json),
                            ]),
                        Tab::make('Other Tools')
                            ->schema([
                                $this->note('other_note', 'Any tool that supports remote MCP servers over Streamable HTTP can connect with the server URL. Tools that sign in with OAuth discover PERSCOM\'s sign-in automatically.'),
                                $this->note('other_api_key', 'For tools that cannot sign in, create an API key with the view scopes you want to share and send it as a bearer token in the Authorization header.'),
                                TextEntry::make('api_keys')
                                    ->hiddenLabel()
                                    ->state('Manage API Keys')
                                    ->url(PassportTokenResource::getUrl())
                                    ->color('primary'),
                            ]),
                    ]),
            ]);
    }

    /**
     * @param  string[]  $steps
     */
    protected function steps(string $name, array $steps): TextEntry
    {
        return TextEntry::make($name)
            ->hiddenLabel()
            ->state($steps)
            ->listWithLineBreaks()
            ->bulleted();
    }

    protected function note(string $name, string $text): TextEntry
    {
        return TextEntry::make($name)
            ->hiddenLabel()
            ->state($text)
            ->color('gray');
    }

    protected function code(string $name, string $code, Grammar $grammar): CodeEntry
    {
        return CodeEntry::make($name)
            ->hiddenLabel()
            ->state($code)
            ->grammar($grammar)
            ->copyable();
    }

    /**
     * @param  array<string, mixed>  $server
     */
    protected function configJson(array $server): string
    {
        return (string) json_encode(['mcpServers' => ['perscom' => $server]], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
