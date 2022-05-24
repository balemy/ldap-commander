<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Ldap\ConnectionDetails;
use App\Ldap\LdapService;
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

final class LDAPConnect implements MiddlewareInterface
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private LdapService              $ldapService,
        private SessionInterface         $session,
        private UrlGeneratorInterface    $urlGenerator,
        private FlashInterface           $flash

    )
    {

    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $connectionDetails = ConnectionDetails::createFromSession($this->session);
        try {
            $this->ldapService->connect($connectionDetails);
        } catch (\Exception $ex) {
            $this->flash->add('danger', $ex->getMessage());
            return $this->responseFactory
                ->createResponse(Status::FOUND)
                ->withHeader(Header::LOCATION, $this->urlGenerator->generate('login'));
        }

        return $handler->handle($request);


    }
}
