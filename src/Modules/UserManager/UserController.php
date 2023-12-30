<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\UserManager;

use Balemy\LdapCommander\ApplicationParameters;
use Balemy\LdapCommander\LDAP\LdapService;
use Balemy\LdapCommander\LDAP\Services\SchemaService;
use Balemy\LdapCommander\Modules\GroupManager\Group;
use Balemy\LdapCommander\Modules\Session\Session;
use Balemy\LdapCommander\Service\WebControllerService;
use LdapRecord\LdapRecordException;
use LdapRecord\Models\Entry;
use LdapRecord\Models\ModelDoesNotExistException;
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

final class UserController
{
    public function __construct(public ViewRenderer          $viewRenderer,
                                public LdapService           $ldapService,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public SessionInterface      $session,
                                public ValidatorInterface    $validator,
                                public FormHydrator          $formHydrator,
                                public AssetManager          $assetManager,
                                public FlashInterface        $flash,
                                public SchemaService         $schemaService,
                                public ApplicationParameters $applicationParameters,
    )
    {
        $this->viewRenderer = $viewRenderer->withViewPath(__DIR__ . '/Views/');
    }

    public function list(ServerRequestInterface $request, WebControllerService $webService): ResponseInterface
    {
        $ous = $this->ldapService->getOrganizationalUnits();
        $ou = '';

        if (!empty($request->getQueryParams()['ou']) &&
            is_string($request->getQueryParams()['ou']) &&
            array_key_exists((string)$request->getQueryParams()['ou'], $ous)
        ) {
            $ou = (string)$request->getQueryParams()['ou'];
        }

        if ($ou === '') {
            $users = User::all();
        } else {
            $users = User::query()->in($ou)->paginate();
        }

        return $this->viewRenderer->render('list', [
            'urlGenerator' => $this->urlGenerator,
            'users' => $users,
            'columns' => Session::getCurrentSession()->userManager->listColumns ?? [],
            'organizationalUnits' => $ous,
            'organizationalUnit' => $ou
        ]);
    }

    /**
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function edit(ServerRequestInterface $request, WebControllerService $webService): ResponseInterface
    {
        $userModel = new UserForm(dn: $this->getDnByRequest($request), schemaService: $this->schemaService);
        if ($request->getMethod() === Method::POST &&
            $userModel->load($request->getParsedBody()) && $this->validator->validate($userModel)->isValid()) {
            $userModel->save();
            $this->flash->add('success', ['body' => 'User successfully saved!']);

            return $this->webService->getRedirectResponse('user-edit', [
                'dn' => $userModel->getDn(), 'saved' => 1
            ]);
        }

        return $this->viewRenderer->render('edit', [
            'urlGenerator' => $this->urlGenerator,
            'dn' => $userModel->getDn(),
            'parentDNs' => $this->ldapService->getOrganizationalUnits(),
            'userForm' => $userModel,
            'userFormSchema' => new UserFormSchema(),
            'groups' => $this->getGroupList()
        ]);
    }

    private function getGroupList(): array
    {
        $groups = [];
        foreach (Group::getAll() as $group) {
            $groups[$group->getDn()] = $group->getTitle();
        }
        return $groups;
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $dn = $this->getDnByRequest($request);
        if ($dn === null) {
            return $this->webService->getNotFoundResponse();
        }

        $entry = Entry::query()->find($dn);
        if ($entry == null || !($entry instanceof Entry)) {
            return $this->webService->getNotFoundResponse();
        }

        try {
            $entry->delete();
            $this->flash->add('success', ['body' => 'User successfully deleted!']);
        } catch (ModelDoesNotExistException $e) {
            $this->flash->add('danger', ['body' => 'User does not exist! Error: ' . $e->getMessage()]);
        } catch (LdapRecordException $e) {
            $this->flash->add('danger', ['body' => 'User not deleted! Error: ' . $e->getMessage()]);
        }

        return $this->webService->getRedirectResponse('user-list', ['deleted' => 1]);
    }

    private function getDnByRequest(ServerRequestInterface $request): string|null
    {
        if (!empty($request->getQueryParams()['dn']) && is_string($request->getQueryParams()['dn'])) {
            return (string)$request->getQueryParams()['dn'];
        }

        return null;
    }

}
