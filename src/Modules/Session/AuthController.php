<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\Session;

use Balemy\LdapCommander\LDAP\ConnectionDetails;
use Balemy\LdapCommander\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class AuthController
{

    public function __construct(public ViewRenderer          $viewRenderer,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public SessionInterface      $session,
                                public AssetManager          $assetManager,
    )
    {
        $this->viewRenderer = $viewRenderer->withViewPath(__DIR__ . '/Views/')->withLayout('@views/layout/main-nomenu');
    }

    public function login(ServerRequestInterface $request, ValidatorInterface $validator, FormHydrator $formHydrator, SessionList $sessionList): ResponseInterface
    {
        $loginForm = new LoginForm($sessionList);

        if ($request->getMethod() === Method::POST &&
            $formHydrator->populate($loginForm, $request->getParsedBody()) && $loginForm->isValid()) {

            $this->session->set('SessionId', $loginForm->getSessionId());

            return $this->webService->getRedirectResponse('home', []);
        }

        return $this->viewRenderer->render('login', [
            'urlGenerator' => $this->urlGenerator,
            'loginForm' => $loginForm,
        ]);
    }

    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        $this->session->remove('SessionId');
        return $this->webService->getRedirectResponse('login', []);
    }
}
