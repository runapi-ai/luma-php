<?php

declare(strict_types=1);

namespace RunApi\Luma;

use RunApi\Core\BaseClient;
use RunApi\Core\ClientOptions;
use RunApi\Luma\Resources\ModifyVideo;

/**
 * Provides Luma prompt-driven video modification.
 *
 * Exposes typed model resources plus the universal files and account resources.
 */
final class LumaClient extends BaseClient
{
    /**
     * Modify video operations.
     */
    public readonly ModifyVideo $modifyVideo;

    /**
     * Create a Luma client with optional API key, base URL, and transport overrides.
     */
    public function __construct(ClientOptions $options = new ClientOptions())
    {
        parent::__construct($options);
        $this->modifyVideo = ModifyVideo::fromHttp($this->http);
    }
}
