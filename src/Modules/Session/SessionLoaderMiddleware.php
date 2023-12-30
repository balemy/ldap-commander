<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\Session;

use Balemy\LdapCommander\LDAP\Services\LdapService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Yiisoft\Http\Header;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Session\SessionInterface;

final class SessionLoaderMiddleware implements MiddlewareInterface
{
    public static ?Session $currentSession = null;

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private SessionInterface         $session,
        private UrlGeneratorInterface    $urlGenerator,
        private SessionList              $sessionList,
        private LdapService              $ldapService,
        private FlashInterface           $flash
    )
    {
        ;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $session = $this->sessionList->getSessionByHttpSession($this->session);

        if ($session === null) {
            return $this->responseFactory
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->urlGenerator->generate('login'));
        }

        try {
            $this->ldapService->connectWithDetails($session->connectionDetails);
        } catch (\Exception $ex) {
            $this->flash->add('danger', $ex->getMessage());

            return $this->responseFactory
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->urlGenerator->generate('login'));
        }


        static::$currentSession = $session;

        return $handler->handle($request);
    }
}
