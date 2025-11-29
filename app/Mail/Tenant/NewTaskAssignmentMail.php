<?php

declare(strict_types=1);

namespace App\Mail\Tenant;

use App\Models\TaskAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class NewTaskAssignmentMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(protected TaskAssignment $taskAssignment, protected string $url)
    {
        //
    }

    public function build(): static
    {
        return $this->markdown('emails.tenant.new-task-assignment')
            ->subject('New Task Assignment')
            ->with([
                'task' => $this->taskAssignment->task?->title,
                'due' => $this->taskAssignment->due_at ? Carbon::parse($this->taskAssignment->due_at)->toDayDateTimeString() : 'No Due Date',
                'expires' => $this->taskAssignment->expires_at ? Carbon::parse($this->taskAssignment->expires_at)->toDayDateTimeString() : 'No Expiration Date',
                'assigned' => $this->taskAssignment->assigned_by?->name,
                'url' => $this->url,
                'date' => Carbon::parse($this->taskAssignment->created_at)->toFormattedDateString(),
            ]);
    }
}
