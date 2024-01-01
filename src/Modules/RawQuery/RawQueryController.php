<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\RawQuery;

use Balemy\LdapCommander\ApplicationParameters;
use Balemy\LdapCommander\Modules\Session\Session;
use Balemy\LdapCommander\Modules\UserManager\UserForm;
use Balemy\LdapCommander\Modules\UserManager\UserFormSchema;
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

final class RawQueryController
{
    public function __construct(public ViewRenderer          $viewRenderer,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public SessionInterface      $session,
                                public ValidatorInterface    $validator,
                                public FormHydrator          $formHydrator,
                                public AssetManager          $assetManager,
                                public FlashInterface        $flash,
                                public ApplicationParameters $applicationParameters,
    )
    {
        $this->viewRenderer = $viewRenderer->withViewPath(__DIR__ . '/Views/');
    }

    /**
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function index(ServerRequestInterface $request, FormHydrator $formHydrator): ResponseInterface
    {
        $queryForm = new QueryForm();
        $results = [];

        if ($request->getMethod() === Method::POST &&
            $formHydrator->populate($queryForm, $request->getParsedBody()) && $this->validator->validate($queryForm)->isValid()) {

            $session = Session::getCurrentSession();
            $query = trim($queryForm->query);
            $results = $session->lrConnection->query()->rawFilter($query)->select(['dn'])->paginate();
        }

        return $this->viewRenderer->render('index', [
            'urlGenerator' => $this->urlGenerator,
            'queryForm' => $queryForm,
            'results' => $results
        ]);
    }

}
