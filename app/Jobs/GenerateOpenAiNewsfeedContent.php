<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Traits\HasEventPrompts;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class GenerateOpenAiNewsfeedContent implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Activity $activity, protected string $event, protected string $type = 'headline', protected string $prompt = '')
    {
        if (in_array(HasEventPrompts::class, class_uses_recursive($this->activity->subject))) {
            $this->prompt = $this->activity->subject->generatePromptForEvent($this->event, $this->type);
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->prompt) {
            $response = OpenAI::completions()->create([
                'model' => 'text-davinci-003',
                'prompt' => $this->prompt,
                'max_tokens' => config('openai.max_tokens'),
                'temperature' => config('openai.temperature'),
            ]);

            Log::debug('OpenAI newsfeed content response', [
                'type' => $this->type,
                'prompt' => $this->prompt,
                'response' => $response,
            ]);

            $result = collect($response->choices)->first();

            $this->activity->update([
                'properties' => $this->activity->properties->put($this->type, trim(preg_replace('/\s\s+/', ' ', $result->text))),
            ]);
        }
    }
}
