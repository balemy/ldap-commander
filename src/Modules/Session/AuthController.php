<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\Session;

use Balemy\LdapCommander\ApplicationParameters;
use Balemy\LdapCommander\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Aliases\Aliases;
use Yiisoft\Assets\AssetManager;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final class AuthController
{

    public function __construct(
        public ViewRenderer $viewRenderer,
        public WebControllerService $webService,
        public UrlGeneratorInterface $urlGenerator,
        public SessionInterface $session,
        public FlashInterface $flash,
        public AssetManager $assetManager,
    ) {
        $this->viewRenderer = $viewRenderer->withViewPath(__DIR__ . '/Views/')->withLayout('@views/layout/plain');
    }

    public function login(
        ServerRequestInterface $request,
        FormHydrator $formHydrator,
        ConfiguredSessionList $sessionList,
        Aliases $aliases,
        ApplicationParameters $applicationParameters,
    ): ResponseInterface {
        $loginForm = new LoginForm($sessionList);

        if (count($sessionList->getAll()) === 0) {
            $this->flash->add('danger', ['body' => 'Configuration File missing!']);
        }

        if ($request->getMethod() === Method::POST &&
            $formHydrator->populateFromPostAndValidate($loginForm, $request) && $loginForm->isValid()) {
            $this->session->set('SessionId', $loginForm->getSessionId());

            return $this->webService->getRedirectResponse('home', []);
        }

        return $this->viewRenderer->render('login', [
            'urlGenerator' => $this->urlGenerator,
            'loginForm' => $loginForm,
            'sessionList' => $loginForm->getSessionTitles(),
            'aliases' => $aliases,
            'applicationParameters' => $applicationParameters,
        ]);
    }

    public function logout(ServerRequestInterface $request): ResponseInterface
    {
        $this->session->remove('SessionId');
        return $this->webService->getRedirectResponse('login', []);
    }
}
