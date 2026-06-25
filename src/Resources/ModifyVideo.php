<?php

declare(strict_types=1);

namespace RunApi\Luma\Resources;

use RunApi\Core\Http\HttpClient;
use RunApi\Core\Models\TaskCreateResponse;
use RunApi\Core\RequestOptions;
use RunApi\Core\Resources\TypedConfiguredResource;
use RunApi\Luma\Models\CompletedVideoTaskResponse;
use RunApi\Luma\Models\VideoTaskResponse;
use RunApi\Luma\Types;

/**
 * Applies prompt-guided edits to an existing video. The source video's motion is preserved while visual changes described in the prompt are applied.
 */
readonly class ModifyVideo extends TypedConfiguredResource
{
    /**
     * Submits a modify-video task and returns immediately with a task id.
     *
     * @param array{
     *   model: string,
     *   source_video_url: string,
     *   callback_url?: string,
     *   prompt?: string
     * } $params
     */
    public function create(array $params, ?RequestOptions $options = null): TaskCreateResponse
    {
        return parent::create($params, $options);
    }

    /**
     * Fetches the current status of a modify-video task by id.
     */
    public function get(string $id, ?RequestOptions $options = null): VideoTaskResponse
    {
        $response = parent::get($id, $options);

        /** @var VideoTaskResponse $response */
        return $response;
    }

    /**
     * Submits a modify-video task and polls until it completes.
     *
     * @param array{
     *   model: string,
     *   source_video_url: string,
     *   callback_url?: string,
     *   prompt?: string
     * } $params
     */
    public function run(array $params, ?RequestOptions $options = null): CompletedVideoTaskResponse
    {
        $response = parent::run($params, $options);

        /** @var CompletedVideoTaskResponse $response */
        return $response;
    }

    /**
     * Create the resource using the shared RunAPI HTTP transport.
     */
    public static function fromHttp(HttpClient $http): self
    {
        return new self(
            $http,
            '/api/v1/luma/modify_video',
            'luma/modify-video',
            VideoTaskResponse::class,
            CompletedVideoTaskResponse::class,
            Types::MODIFY_VIDEO_MODELS,
            'modify-video',
            VideoTaskResponse::class,
            CompletedVideoTaskResponse::class,
        );
    }
}
