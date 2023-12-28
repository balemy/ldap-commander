<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Controller;

use Balemy\LdapCommander\LDAP\ConnectionDetails;
use Balemy\LdapCommander\LDAP\LoginForm;
use Balemy\LdapCommander\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Assets\AssetManager;
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
        $this->viewRenderer = $viewRenderer->withControllerName('auth')->withLayout('@views/layout/main-nomenu');
    }

    public function login(ServerRequestInterface $request, ValidatorInterface $validator): ResponseInterface
    {
        $connections = ConnectionDetails::getAll();
        if (count($connections) === 0) {
            throw new \Exception('No connections configured!');
        }

        $connectionId = 0;

        if (isset($request->getQueryParams()['c'])) {
            $connectionId = intval($request->getQueryParams()['c']);
            if (!isset($connections[$connectionId])) {
                return $this->webService->getNotFoundResponse();
            }
        }
        $connectionDetails = $connections[$connectionId];

        $loginForm = new LoginForm();
        $loginForm->loadConnectionDetails($connectionDetails);

        /** @var array<string, string|array>|null $body */
        $body = $request->getParsedBody();
        if ($request->getMethod() === Method::POST) {

            // Loading may return false, when all fields are disabled
            $loginForm->loadSafeAttributes(is_array($body) ? $body : []);

            if ($validator->validate($loginForm)->isValid()) {
                $loginForm->storeInSession($this->session);
                return $this->webService->getRedirectResponse('home', []);
            }
        }

        if ($loginForm->isAttributeFixed('adminPassword')) {
            (new Hydrator())->hydrate($loginForm, ['adminPassword' => '******************']);
        }

        return $this->viewRenderer->render('login', [
            'urlGenerator' => $this->urlGenerator,
            'formModel' => $loginForm,
            'connectionId' => $connectionId
        ]);
    }

    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        LoginForm::removeFromSession($this->session);

        return $this->webService->getRedirectResponse('login', []);
    }
}
