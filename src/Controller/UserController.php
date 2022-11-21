<?php

declare(strict_types=1);

namespace App\Controller;

use App\Ldap\GroupForm;
use App\Ldap\LdapService;
use App\Ldap\User;
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

final class UserController
{
    public function __construct(public ViewRenderer          $viewRenderer,
                                public LdapService           $ldapService,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public SessionInterface      $session,
                                public ValidatorInterface    $validator,
                                public AssetManager          $assetManager,
                                public FlashInterface        $flash
    )
    {
        $this->viewRenderer = $viewRenderer
            ->withControllerName('user')
            ->withLayout('@views/layout/main');
    }

    public function list(WebControllerService $webService): ResponseInterface
    {
        return $this->viewRenderer->render('list', [
            'urlGenerator' => $this->urlGenerator,
            'users' => User::getAll()
        ]);
    }

    public function edit(ServerRequestInterface $request, WebControllerService $webService): ResponseInterface
    {
        $user = new User();

        $dn = $this->getDnByRequest($request);
        if ($dn !== null && !$user->loadByEntryByDn($dn)) {
            return $this->webService->getNotFoundResponse();
        }

        if ($request->getMethod() === Method::POST) {
            /** @var array<string, array> $body */
            $body = $request->getParsedBody();
            if ($user->load($body) && $this->validator->validate($user)->isValid()) {
                $user->updateEntry();
                $this->flash->add('success', ['body' => 'User successfully saved!']);

                if ($user->isNewRecord()) {
                    return $this->webService->getRedirectResponse('user-list', ['saved' => 1]);
                }
                return $this->webService->getRedirectResponse('user-edit', ['dn' => $user->getDn(), 'saved' => 1]);
            }
        }

        return $this->viewRenderer->render('edit', [
            'urlGenerator' => $this->urlGenerator,
            'dn' => $user->getDn(),
            'user' => $user,
        ]);
    }


    private function getDnByRequest(ServerRequestInterface $request): string|null
    {
        if (!empty($request->getQueryParams()['dn']) && is_string($request->getQueryParams()['dn'])) {
            return (string)$request->getQueryParams()['dn'];
        }

        return null;
    }

}
