<?php

declare(strict_types=1);


namespace App\Web\Telegram\Webhook;

use App\Web\Telegram\Handler;
use HttpSoft\Message\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Yiisoft\Log\Logger;

final readonly class Action
{
    public function __construct(
        private ResponseFactory $responseFactory,
        private Handler $handler,
        private LoggerInterface $logger
    )
    {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $body = (string)$request->getBody();
        $update = json_decode($body, true);

        try {
            $this->handler->handle($update);

            $response = $this->responseFactory->createResponse(200);
            $response->getBody()->write('OK');

            return $response;
        } catch (\Throwable $e) {
            $this->logger->error($e->getMessage());
            $response = $this->responseFactory->createResponse(500);
            $response->getBody()->write('Bot api error');
            return $response;
        }
    }
}
