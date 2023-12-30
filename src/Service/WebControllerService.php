<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Service;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Stringable;
use Yiisoft\Http\Header;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;

final class WebControllerService
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private UrlGeneratorInterface    $urlGenerator)
    {
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
            ->withHeader(Header::LOCATION, $this->urlGenerator->generate($url, [], $arguments));
    }

    public function getNotFoundResponse(): ResponseInterface
    {
        return $this->responseFactory
            ->createResponse(Status::NOT_FOUND);
    }

    public function getParamAsString(string $name, ServerRequestInterface $request): string
    {
        if (!empty($request->getQueryParams()[$name]) &&
            is_string($request->getQueryParams()[$name])
        ) {
            return (string)$request->getQueryParams()[$name];
        }

        return '';
    }
}
