<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\ServerConfig\ReferentialIntegrity;

use Balemy\LdapCommander\Ldap\LdapService;
use Balemy\LdapCommander\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Method;
use Yiisoft\Hydrator\Hydrator;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class Controller
{
    public function __construct(public ViewRenderer          $viewRenderer,
                                public LdapService           $ldapService,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public ValidatorInterface    $validator,
                                public FlashInterface        $flash
    )
    {
        $this->viewRenderer = $viewRenderer->withViewPath(__DIR__ . '/Views/');
    }

    public function edit(ServerRequestInterface $request, WebControllerService $webService, RefIntService $refIntService): ResponseInterface
    {
        $formModel = new RefForm();
        $refIntService->populate($formModel);

        if ($request->getMethod() === Method::POST) {
            /** @var array<string, array> $body */
            $body = $request->getParsedBody();
            if ((new Hydrator())->hydrate($formModel, $body['RefForm']) &&
                $this->validator->validate($formModel)->isValid()) {
                $refIntService->saveByForm($formModel);
                $this->flash->add('success', ['body' => 'Group successfully saved!']);
                return $this->webService->getRedirectResponse('server-config-refint-edit', ['saved' => 1]);
            }
        }

        return $this->viewRenderer->render('edit', [
            'urlGenerator' => $this->urlGenerator,
            'formModel' => $formModel,
        ]);
    }
}
