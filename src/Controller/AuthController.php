<?php

declare(strict_types=1);

namespace App\Controller;

use App\Ldap\ConnectionDetails;
use App\Ldap\LdapService;
use App\Ldap\LoginForm;
use App\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class AuthController
{

    public function __construct(public ViewRenderer          $viewRenderer,
                                public LdapService           $ldapService,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public SessionInterface      $session,
                                public AssetManager          $assetManager,
                                public FlashInterface        $flash
    )
    {
        $this->viewRenderer = $viewRenderer->withControllerName('auth')->withLayout('@views/layout/main-nomenu');
    }

    public function login(ServerRequestInterface $request,
                          FlashInterface         $flash,
                          LdapService            $ldapService,
                          ValidatorInterface     $validator): ResponseInterface
    {
        $loginForm = new LoginForm($ldapService);

        $connectionDetails = ConnectionDetails::createFromSession($this->session);
        $loginForm->loadConnectionDetails($connectionDetails);

        /** @var array<string, string|array>|null $body */
        $body = $request->getParsedBody();
        if (
            $request->getMethod() === Method::POST
            && $loginForm->load(is_array($body) ? $body : [])
            && $validator->validate($loginForm)->isValid()
        ) {
            $loginForm->getConnectionDetails()->storeInSession($this->session);

            return $this->webService->getRedirectResponse('home', []);
        }

        return $this->viewRenderer->render('login', [
            'urlGenerator' => $this->urlGenerator,
            'formModel' => $loginForm
        ]);
    }

    public function logout(ServerRequestInterface $request,
                           FlashInterface         $flash): ResponseInterface
    {
        ConnectionDetails::removeFromSession($this->session);

        //$flash->add('success', ['body' => 'Disconnected!']);
        return $this->webService->getRedirectResponse('login', []);
    }
}
