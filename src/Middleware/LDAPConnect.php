<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Middleware;

use Balemy\LdapCommander\Ldap\LdapService;
use Balemy\LdapCommander\Ldap\LoginForm;
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
        $login = LoginForm::createFromSession($this->session);
        if ($login !== null) {

            if (!empty($login->getAttributeValue('configUser'))) {
                try {
                    $this->ldapService->connectConfig($login);
                } catch (\Exception $ex) {
                    $this->flash->add('danger', 'Configuration Connect: ' . $ex->getMessage());
                }
            }


            try {
                $this->ldapService->connect($login);
            } catch (\Exception $ex) {
                $this->flash->add('danger', $ex->getMessage());
            }
            return $handler->handle($request);
        }

        return $this->responseFactory
            ->createResponse(Status::FOUND)
            ->withHeader(Header::LOCATION, $this->urlGenerator->generate('login'));
    }
}
