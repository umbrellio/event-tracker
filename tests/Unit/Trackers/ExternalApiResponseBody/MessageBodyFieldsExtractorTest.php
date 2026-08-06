<?php

declare(strict_types=1);

namespace Tests\Unit\Trackers\ExternalApiResponseBody;

use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;
use Umbrellio\EventTracker\Trackers\ExternalApiResponseBody\MessageBodyFieldsExtractor;

class MessageBodyFieldsExtractorTest extends TestCase
{
    /**
     * @test
     */
    public function extractsNestedFieldsByDotNotation(): void
    {
        $extractor = new MessageBodyFieldsExtractor();
        $message = new Request('POST', '/', [], json_encode(['error' => ['code' => 'incorrect_jurisdiction']]));

        $result = $extractor->extract($message, ['code' => 'error.code']);

        $this->assertSame(['code' => 'incorrect_jurisdiction'], $result);
    }

    /**
     * @test
     */
    public function returnsDefaultValueForMissingField(): void
    {
        $extractor = new MessageBodyFieldsExtractor();
        $message = new Request('POST', '/', [], json_encode(['data' => []]));

        $result = $extractor->extract($message, ['code' => 'error.code']);

        $this->assertSame(['code' => 'unknown'], $result);
    }

    /**
     * @test
     */
    public function returnsConfiguredDefaultValueForMissingField(): void
    {
        $extractor = new MessageBodyFieldsExtractor();
        $message = new Request('POST', '/', [], json_encode(['data' => []]));

        $result = $extractor->extract($message, ['code' => 'error.code'], ['default_value' => 'none']);

        $this->assertSame(['code' => 'none'], $result);
    }

    /**
     * @test
     */
    public function returnsDefaultValueForMalformedJson(): void
    {
        $extractor = new MessageBodyFieldsExtractor();
        $message = new Request('POST', '/', [], 'not a json');

        $result = $extractor->extract($message, ['code' => 'error.code']);

        $this->assertSame(['code' => 'unknown'], $result);
    }

    /**
     * @test
     */
    public function returnsDefaultsWhenMessageIsNull(): void
    {
        $extractor = new MessageBodyFieldsExtractor();

        $result = $extractor->extract(null, ['code' => 'error.code']);

        $this->assertSame(['code' => 'unknown'], $result);
    }

    /**
     * @test
     */
    public function returnsEmptyArrayWhenNoFieldsConfigured(): void
    {
        $extractor = new MessageBodyFieldsExtractor();
        $message = new Request('POST', '/', [], json_encode(['method' => 'x']));

        $this->assertSame([], $extractor->extract($message, []));
    }

    /**
     * @test
     */
    public function rewindsBodyStreamAfterReading(): void
    {
        $extractor = new MessageBodyFieldsExtractor();
        $body = json_encode(['method' => 'pointg/sessions/create']);
        $message = new Request('POST', '/', [], $body);

        $extractor->extract($message, ['method' => 'method']);

        $this->assertSame($body, $message->getBody()->getContents());
    }

    /**
     * @test
     */
    public function skipsBodyReadingWhenStreamIsNotSeekable(): void
    {
        $extractor = new MessageBodyFieldsExtractor();

        $body = $this->createMock(StreamInterface::class);
        $body->method('isSeekable')->willReturn(false);
        $body->expects($this->never())->method('__toString');

        $message = $this->createMock(MessageInterface::class);
        $message->method('getBody')->willReturn($body);

        $result = $extractor->extract($message, ['method' => 'method']);

        $this->assertSame(['method' => 'unknown'], $result);
    }

    /**
     * @test
     */
    public function skipsBodyReadingWhenBodyExceedsMaxBytes(): void
    {
        $extractor = new MessageBodyFieldsExtractor();
        $message = new Request('POST', '/', [], json_encode(['method' => str_repeat('a', 100)]));

        $result = $extractor->extract($message, ['method' => 'method'], ['max_body_bytes' => 10]);

        $this->assertSame(['method' => 'unknown'], $result);
    }
}
