<?php

namespace Balemy\LdapCommander\Modules\SlapdConfig\Controllers;

use Balemy\LdapCommander\LDAP\Services\LdapService;
use Balemy\LdapCommander\Modules\Session\Session;
use Balemy\LdapCommander\Modules\SlapdConfig\Models\AccessControl;
use Balemy\LdapCommander\Modules\SlapdConfig\Models\BindUser;
use Balemy\LdapCommander\Modules\SlapdConfig\Models\MemberOf;
use Balemy\LdapCommander\Modules\SlapdConfig\Services\SlapdConfigService;
use Balemy\LdapCommander\Service\WebControllerService;
use LdapRecord\Models\Entry;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

class ModuleConfigController
{
    public function __construct(public ViewRenderer          $viewRenderer,
                                public LdapService           $ldapService,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public SessionInterface      $session,
                                public ValidatorInterface    $validator,
                                public AssetManager          $assetManager,
                                public SlapdConfigService    $configService,
                                public FlashInterface        $flash
    )
    {
        $this->viewRenderer = $viewRenderer->withViewPath(dirname(__DIR__) . '/Views/module-config/');
    }

    public function index(WebControllerService $webService): ResponseInterface
    {
        return $this->viewRenderer->render('index', [
            'urlGenerator' => $this->urlGenerator,
        ]);
    }

    public function memberOf(ServerRequestInterface $request, WebControllerService $webService): ResponseInterface
    {
        $model = MemberOf::getModel($this->configService);

        if ($request->getMethod() === Method::POST &&
            /** @psalm-suppress PossiblyInvalidArgument */
            $model->load($request->getParsedBody()) && $this->validator->validate($model)->isValid()) {

            try {
                $model->save();
                $this->flash->add('success', ['body' => 'Successfully saved!']);
                return $this->webService->getRedirectResponse('module-config', ['saved' => 1]);
            } catch (\Exception $ex) {
                $this->flash->add('danger', ['body' => $ex->getMessage()]);
            }
        }

        return $this->viewRenderer->render('member-of', [
            'urlGenerator' => $this->urlGenerator,
            'model' => $model
        ]);
    }

}
