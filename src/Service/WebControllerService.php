<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Stringable;
use Yiisoft\Http\Header;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;

final class WebControllerService
{
    private ResponseFactoryInterface $responseFactory;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(ResponseFactoryInterface $responseFactory, UrlGeneratorInterface $urlGenerator)
    {
        $this->responseFactory = $responseFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string $url
     * @param array $arguments
     * @return ResponseInterface
     * @psalm-param array<string,null|Stringable|scalar> $arguments
     */
    public function getRedirectResponse(string $url, array $arguments = []): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse(Status::FOUND)
            ->withHeader(Header::LOCATION, $this->urlGenerator->generate($url, $arguments));
    }

    public function getNotFoundResponse(): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse(Status::NOT_FOUND);
    }
}
