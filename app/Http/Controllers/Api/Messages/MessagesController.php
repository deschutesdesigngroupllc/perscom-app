<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Messages;

use App\Http\Controllers\Api\AuthorizesRequests;
use App\Http\Requests\Api\MessageRequest;
use App\Models\Message;
use Orion\Http\Controllers\Controller;

class MessagesController extends Controller
{
    use AuthorizesRequests;

    protected $model = Message::class;

    protected $request = MessageRequest::class;

    /**
     * @return array<int, string>
     */
    public function includes(): array
    {
        return [
            'schedule',
        ];
    }

    /**
     * @return array<int, string>
     */
    public function sortableBy(): array
    {
        return ['id', 'message', 'channels', 'recipients', 'repeats', 'send_at', 'sent_at', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function searchableBy(): array
    {
        return ['id', 'message', 'channels', 'recipients', 'repeats', 'send_at', 'sent_at', 'created_at', 'updated_at'];
    }

    /**
     * @return array<int, string>
     */
    public function filterableBy(): array
    {
        return ['id', 'message', 'channels', 'recipients', 'repeats', 'send_at', 'sent_at', 'created_at', 'updated_at'];
    }
}
