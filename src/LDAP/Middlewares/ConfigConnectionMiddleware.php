<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\LDAP\Middlewares;

use Balemy\LdapCommander\Handler\NotFoundHandler;
use Balemy\LdapCommander\Modules\Session\ConfiguredSessionList;
use Balemy\LdapCommander\Modules\Session\Session;
use Balemy\LdapCommander\Modules\SlapdConfig\Services\SlapdConfigService;
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
use Yiisoft\Yii\View\ViewRenderer;

final class ConfigConnectionMiddleware implements MiddlewareInterface
{
    public static ?Session $currentSession = null;

    public function __construct(
        private ResponseFactoryInterface      $responseFactory,
        private UrlGeneratorInterface         $urlGenerator,
        private FlashInterface                $flash,
        private ViewRenderer                  $viewRenderer,

    )
    {
        $this->viewRenderer = $viewRenderer->withControllerName('site');
    }

    public function process(ServerRequestInterface  $request,
                            RequestHandlerInterface $handler,
    ): ResponseInterface
    {
        try {
            (new SlapdConfigService());
        } catch (\Exception $ex) {

            return $this->viewRenderer
                ->render('config-connection-failed', ['message' => $ex->getMessage(), 'urlGenerator' => $this->urlGenerator])
                ->withStatus(Status::BAD_GATEWAY);
        }


        return $handler->handle($request);
    }
}
