<?php

declare(strict_types=1);

namespace Umbrellio\EventTracker\Trackers\ExternalApiResponseBody;

use Psr\Http\Message\MessageInterface;
use Throwable;

class MessageBodyFieldsExtractor
{
    private const DEFAULT_VALUE = 'unknown';
    private const DEFAULT_MAX_BODY_BYTES = 65536;

    public function extract(?MessageInterface $message, array $fields, array $config = []): array
    {
        if (!$fields) {
            return [];
        }

        $default = $config['default_value'] ?? self::DEFAULT_VALUE;
        $data = $this->decodeBody($message, $config['max_body_bytes'] ?? self::DEFAULT_MAX_BODY_BYTES);

        $result = [];
        foreach ($fields as $tag => $path) {
            $value = data_get($data, $path, $default);
            $result[$tag] = $value ?? $default;
        }

        return $result;
    }

    private function decodeBody(?MessageInterface $message, int $maxBodyBytes): array
    {
        if (!$message) {
            return [];
        }

        $body = $message->getBody();

        /**
         * Non-seekable bodies belong to streamed requests/responses - reading them here
         * would consume data the application still needs to process.
         */
        if (!$body->isSeekable() || ($body->getSize() !== null && $body->getSize() > $maxBodyBytes)) {
            return [];
        }

        try {
            $decoded = json_decode($body->__toString(), true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            return [];
        } finally {
            $body->rewind();
        }

        return is_array($decoded) ? $decoded : [];
    }
}
