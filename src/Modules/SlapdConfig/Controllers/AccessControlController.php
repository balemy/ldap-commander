<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\SlapdConfig\Controllers;

use Balemy\LdapCommander\LDAP\Services\LdapService;
use Balemy\LdapCommander\Modules\Session\Session;
use Balemy\LdapCommander\Modules\SlapdConfig\Models\AccessControl;
use Balemy\LdapCommander\Modules\SlapdConfig\Services\SlapdConfigService;
use Balemy\LdapCommander\Service\WebControllerService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\FormModel\FormHydrator;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class AccessControlController
{
    public function __construct(public ViewRenderer          $viewRenderer,
                                public LdapService           $ldapService,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public SessionInterface      $session,
                                public FormHydrator          $formHydrator,
                                public ValidatorInterface    $validator,
                                public AssetManager          $assetManager,
                                public FlashInterface        $flash
    )
    {
        $this->viewRenderer = $viewRenderer->withViewPath(dirname(__DIR__) . '/Views/access-control/');
    }

    /**
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function index(ServerRequestInterface $request, SlapdConfigService $configService): ResponseInterface
    {
        $model = new AccessControl(
            dn: null,
            lrEntry: $configService->getDatabaseConfigEntry(),
            schemaService: Session::getCurrentSession()->getSchemaService()
        );
        if ($request->getMethod() === Method::POST &&
            /** @psalm-suppress PossiblyInvalidArgument */
            $model->load($request->getParsedBody()) && $this->validator->validate($model)->isValid()) {
            $model->save();
            $this->flash->add('success', ['body' => 'Successfully saved!']);

            return $this->webService->getRedirectResponse('access-control', ['saved' => 1]);
        }

        return $this->viewRenderer->render('index', [
            'urlGenerator' => $this->urlGenerator,
            'model' => $model,
            'assetManager' => $this->assetManager
        ]);
    }

}
