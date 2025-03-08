<?php

declare(strict_types=1);

namespace App\Notifications\Tenant;

use App\Mail\Tenant\NewAlert as NewAlertMailable;
use App\Models\Alert;
use App\Models\Enums\AlertChannel;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Slack\BlockKit\Blocks\ActionsBlock;
use Illuminate\Notifications\Slack\BlockKit\Blocks\SectionBlock;
use Illuminate\Notifications\Slack\SlackMessage;
use Illuminate\Support\Collection;
use League\HTMLToMarkdown\HtmlConverter;

class NewAlert extends Notification implements ShouldQueue
{
    use Queueable;

    public bool $deleteWhenMissingModels = true;

    public function __construct(protected Alert $alert)
    {
        //
    }

    /**
     * @return string[]
     */
    public function via(): array
    {
        return Collection::wrap($this->alert->channels)
            ->reject(fn (AlertChannel $channel): bool => $channel === AlertChannel::SLACK)
            ->map(fn (AlertChannel $channel) => $channel->value)
            ->values()
            ->toArray();
    }

    public function toMail(Tenant $notifiable): NewAlertMailable
    {
        return (new NewAlertMailable($this->alert))->to($notifiable->email);
    }

    public function toSlack(): SlackMessage
    {
        $converter = new HtmlConverter([
            'strip_tags' => true,
            'remove_nodes' => true,
        ]);

        return (new SlackMessage)
            ->text($this->alert->title)
            ->headerBlock($this->alert->title)
            ->sectionBlock(function (SectionBlock $block) use ($converter): void {
                $block->text($converter->convert($this->alert->message));
            })
            ->when(filled($this->alert->url), function (SlackMessage $message): void {
                $message->actionsBlock(function (ActionsBlock $block): void {
                    $block->button($this->alert->link_text)
                        ->url($this->alert->url)
                        ->primary();
                });
            });
    }
}
