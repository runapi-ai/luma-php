<?php

declare(strict_types=1);

namespace RunApi\Luma\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use RunApi\Core\ClientOptions;
use RunApi\Core\Errors\ValidationException;
use RunApi\Core\Tests\Fixtures\QueueHttpClient;
use RunApi\Luma\LumaClient;
use RunApi\Luma\Models\CompletedVideoTaskResponse;
use RunApi\Luma\Resources\ModifyVideo;

final class LumaClientTest extends TestCase
{
    public function testExposesTypedResources(): void
    {
        $client = new LumaClient(new ClientOptions(apiKey: 'k', httpClient: new QueueHttpClient([]), maxRetries: 0));

        self::assertInstanceOf(ModifyVideo::class, $client->modifyVideo);
    }

    public function testCreatePostsCompactedBodyToCorrectPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
        ]);
        $client = new LumaClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $task = $client->modifyVideo->create([
            'model' => 'luma-modify-video',
            'prompt' => 'A product render',
            'source_video_url' => 'https://cdn.runapi.ai/public/samples/video.mp4',
            'callback_url' => '',
            'seed' => null,
        ]);

        $body = json_decode((string) $transport->requests[0]->getBody(), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame('task_1', $task->id);
        self::assertSame('/api/v1/luma/modify_video', $transport->requests[0]->getUri()->getPath());
        self::assertSame('luma-modify-video', $body['model']);
        self::assertArrayNotHasKey('callback_url', $body);
        self::assertArrayNotHasKey('seed', $body);
    }

    public function testRunReturnsTypedCompletedResponseAndPreservesUnknownFields(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed","videos":[{"url":"https://file.runapi.ai/result"}],"extra_field":"kept"}'),
        ]);
        $client = new LumaClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $result = $client->modifyVideo->run([
            'model' => 'luma-modify-video',
            'prompt' => 'A product render',
            'source_video_url' => 'https://cdn.runapi.ai/public/samples/video.mp4',
        ]);

        self::assertInstanceOf(CompletedVideoTaskResponse::class, $result);
        self::assertSame('https://file.runapi.ai/result', $result->videos[0]->url);
        self::assertSame('kept', $result->toArray()['extra_field']);
        self::assertSame('/api/v1/luma/modify_video/task_1', $transport->requests[1]->getUri()->getPath());
    }

    public function testCompletedResponseRequiresResultFiles(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_1"}'),
            new Response(200, [], '{"id":"task_1","status":"completed"}'),
        ]);
        $client = new LumaClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('videos is required');

        $client->modifyVideo->run([
            'model' => 'luma-modify-video',
            'prompt' => 'A product render',
            'source_video_url' => 'https://cdn.runapi.ai/public/samples/video.mp4',
        ]);
    }


    public function testSecondaryResourceUsesItsOwnPath(): void
    {
        $transport = new QueueHttpClient([
            new Response(200, [], '{"id":"task_2"}'),
        ]);
        $client = new LumaClient(new ClientOptions(apiKey: 'k', httpClient: $transport, maxRetries: 0));

        $client->modifyVideo->create([
            'model' => 'luma-modify-video',
            'prompt' => 'A product render',
            'source_video_url' => 'https://cdn.runapi.ai/public/samples/video.mp4',
        ]);

        self::assertSame('/api/v1/luma/modify_video', $transport->requests[0]->getUri()->getPath());
    }
}
