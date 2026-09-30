# Luma PHP SDK for RunAPI

[![Packagist](https://img.shields.io/packagist/v/runapi-ai/luma)](https://packagist.org/packages/runapi-ai/luma)
[![License](https://img.shields.io/github/license/runapi-ai/luma-php)](https://github.com/runapi-ai/luma-php/blob/main/LICENSE)

The Luma PHP SDK is the language-specific package for Luma
on RunAPI. Use this package when your application needs Composer installs,
associative-array request bodies, task status lookup, and consistent RunAPI
errors in PHP.

This README is the PHP package guide for the public `luma-php` split
repository. For model details, use https://runapi.ai/models/luma; for API
reference, use https://runapi.ai/docs/api/luma/modify-video; for SDK docs, use
https://runapi.ai/docs/resources/sdks.

## Install

```bash
composer require runapi-ai/luma
```

## Quick start

```php
<?php

require __DIR__ . "/vendor/autoload.php";

use RunApi\Luma\LumaClient;

$client = new LumaClient(); // reads RUNAPI_API_KEY



$task = $client->modifyVideo->create([
    'model' => 'luma-modify-video',
    'prompt' => 'A precise product render on white marble',
    'source_video_url' => 'https://cdn.runapi.ai/public/samples/video.mp4',
    'watermark' => 'sample',
]);

$status = $client->modifyVideo->get($task->id);

$result = $client->modifyVideo->run([
    'model' => 'luma-modify-video',
    'prompt' => 'A serene mountain lake at dawn',
    'source_video_url' => 'https://cdn.runapi.ai/public/samples/video.mp4',
    'watermark' => 'sample',
]);

echo $result->videos[0]->url . PHP_EOL;
```

Use `create()` to submit a task and return quickly, `get()` to fetch the latest
task state, and `run()` when a script should create and poll until completion.
In web request handlers, prefer `create()` plus webhook or later `get()`
polling so a worker is not held open.


RunAPI-generated file URLs are temporary. Download and store generated files
in your own durable storage within the retention window; do not treat returned
URLs as long-term assets.

## Language notes

Pass request parameters as associative arrays with snake_case keys. The
available resources are `modifyVideo`. Keep `RUNAPI_API_KEY` in the environment
or your secret manager; never commit API keys or callback secrets.

## Links

- Model page: https://runapi.ai/models/luma
- SDK docs: https://runapi.ai/docs/resources/sdks
- Product docs: https://runapi.ai/docs/api/luma/modify-video
- Pricing and rate limits: https://runapi.ai/models/luma
- Full catalog: https://runapi.ai/models
- GitHub repository: https://github.com/runapi-ai/luma-php
- Multi-language SDK repository: https://github.com/runapi-ai/luma-sdk

## License

Licensed under the Apache License, Version 2.0.
