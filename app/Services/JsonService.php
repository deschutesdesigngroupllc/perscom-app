<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;
use Throwable;

class JsonService
{
    public function __construct(protected int $maxInlineSize = 10 * 1024)
    {
        //
    }

    /**
     * Process JSON or text content for safe logging.
     * Returns an array with decoded/truncated content and metadata.
     */
    public function processForLogging(string|false|null $rawContent): array
    {
        if ($rawContent === null || $rawContent === false) {
            return [
                'content' => null,
                'is_truncated' => false,
                'is_compressed' => false,
                'content_path' => null,
            ];
        }

        $isTruncated = false;
        $contentLength = strlen($rawContent);

        if ($contentLength > $this->maxInlineSize) {
            $isTruncated = true;
            $decodedContent = $this->truncateJson($rawContent);
        } else {
            $decodedContent = $this->decodeJson($rawContent);
        }

        return [
            'content' => $decodedContent,
            'is_truncated' => $isTruncated,
            'is_compressed' => false,
            'content_path' => null,
        ];
    }

    /**
     * Decode JSON safely; fallback to raw text if invalid.
     */
    public function decodeJson(string $content): mixed
    {
        if (Str::isJson($content)) {
            try {
                return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            } catch (Throwable) {
                return $content;
            }
        }

        return mb_check_encoding($content, 'UTF-8') ? $content : null;
    }

    /**
     * Truncate large JSON content while preserving valid structure.
     */
    public function truncateJson(string $content): mixed
    {
        if (! Str::isJson($content)) {
            return mb_substr($content, 0, $this->maxInlineSize);
        }

        try {
            $decoded = json_decode($content, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return mb_substr($content, 0, $this->maxInlineSize);
        }

        $truncate = function ($data, int $depth = 0) use (&$truncate) {
            if ($depth > 6) {
                return '[...]';
            }

            if (is_array($data)) {
                $json = json_encode($data);
                if (strlen($json) > $this->maxInlineSize) {
                    if (array_is_list($data)) {
                        $data = array_slice($data, 0, 5);
                        $data[] = '[...]';
                    } else {
                        $data = array_slice($data, 0, 5, true);
                        $data['__truncated'] = true;
                    }
                }

                foreach ($data as $key => $value) {
                    if (is_array($value)) {
                        $data[$key] = $truncate($value, $depth + 1);
                    }
                }
            }

            return $data;
        };

        $result = $truncate($decoded);
        $result['__truncated'] = true;

        return $result;
    }
}
