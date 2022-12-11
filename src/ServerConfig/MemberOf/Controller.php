<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\ServerConfig\MemberOf;

use Balemy\LdapCommander\Ldap\LdapService;
use Balemy\LdapCommander\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Http\Method;
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
        $this->viewRenderer = $viewRenderer
            ->withControllerName('memberof')
            ->withViewPath('@views/server-config')
            ->withLayout('@views/layout/main');
    }

    public function edit(ServerRequestInterface $request, WebControllerService $webService, MemberOfService $memberOfService): ResponseInterface
    {
        $formModel = new MemberOfForm();
        $memberOfService->populate($formModel);

        if ($request->getMethod() === Method::POST) {
            /** @var array<string, array> $body */
            $body = $request->getParsedBody();
            if ($formModel->load($body) &&
                $this->validator->validate($formModel)->isValid()) {
                $memberOfService->saveByForm($formModel);
                $this->flash->add('success', ['body' => 'Successfully saved!']);
                return $this->webService->getRedirectResponse('server-config-memberof-edit', ['saved' => 1]);
            }
        }

        return $this->viewRenderer->render('edit', [
            'urlGenerator' => $this->urlGenerator,
            'formModel' => $formModel,
        ]);
    }
}
