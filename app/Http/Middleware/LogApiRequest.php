<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\ApiLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Context;
use Illuminate\Support\Str;
use JsonException;
use Laravel\Passport\Guards\TokenGuard;
use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\Exceptions\InvalidConfiguration;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    /**
     * @throws InvalidConfiguration
     * @throws JsonException
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! tenancy()->initialized) {
            return $response;
        }

        /** @var TokenGuard $passport */
        $passport = Auth::guard('passport');
        $client = $passport->client();

        $name = match (true) {
            optional($client)->firstParty() => 'api',
            ! optional($client)->firstParty() => 'oauth',
            default => 'api'
        };

        $causer = match (true) {
            optional($client)->firstParty() => Auth::guard('api')->user(),
            ! optional($client)->firstParty() => $client,
            default => null
        };

        $currentActivityModel = ActivitylogServiceProvider::determineActivityModel();
        config()->set('activitylog.activity_model', ApiLog::class);

        /** @var ApiLog $activity */
        $activity = activity($name)->withProperties([
            'client' => $client->id ?? null,
            'request_headers' => iterator_to_array($request->headers->getIterator()),
            'body' => $this->captureRequestBody($request),
        ])->causedBy($causer)->log($request->getPathInfo());

        $activity->setMeta([
            'ip' => $request->getClientIp(),
            'endpoint' => $request->getPathInfo(),
            'method' => $request->getMethod(),
            'request_id' => Context::get('request_id'),
            'trace_id' => Context::get('trace_id'),
        ]);

        $request->attributes->add([
            'activity_id' => $activity->getKey(),
        ]);

        config()->set('activitylog.activity_model', $currentActivityModel);

        return $response;
    }

    /**
     * @throws JsonException
     */
    private function captureRequestBody(Request $request): mixed
    {
        $contentType = $request->header('Content-Type', '');

        if (Str::startsWith($contentType, 'multipart/form-data')) {
            return $this->processMultipartData($request->all());
        }

        $content = $request->getContent();

        if (Str::isJson($content)) {
            return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        }

        if (! mb_check_encoding($content, 'UTF-8')) {
            return null;
        }

        return $content;
    }

    private function processMultipartData(mixed $data): mixed
    {
        if ($data instanceof UploadedFile) {
            return [
                'type' => 'file',
                'name' => $data->getClientOriginalName(),
                'size' => $data->getSize(),
                'mime_type' => $data->getMimeType(),
            ];
        }

        if (is_array($data)) {
            return array_map(function ($value) {
                return $this->processMultipartData($value);
            }, $data);
        }

        return $data;
    }
}
