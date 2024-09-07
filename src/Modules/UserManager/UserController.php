<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\UserManager;

use Balemy\LdapCommander\ApplicationParameters;
use Balemy\LdapCommander\LDAP\Services\LdapService;
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
use Yiisoft\Yii\View\Renderer\ViewRenderer;

final class UserController
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

    public function list(ServerRequestInterface $request, WebControllerService $webService): ResponseInterface
    {
        $ous = array_merge(['' => 'All'], $this->getParentDns());
        $ou = $webService->getParamAsString('ou', $request);

        if ($ou !== '' && !in_array($ou, $ous)) {
            throw new \Exception('Invalid OU given');
        }
        return $this->viewRenderer->render('list', [
            'urlGenerator' => $this->urlGenerator,
            'users' => ($ou === '') ? User::all() : User::query()->in($ou)->paginate(),
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
        $userModel = new UserForm(
            dn: $this->getDnByRequest($request),
            schemaService: Session::getCurrentSession()->getSchemaService()
        );

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
            'parentDNs' => $this->getParentDns(),
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

    /**
     * @psalm-suppress MixedArgument, MixedAssignment
     * @return array<string, string>
     */
    private function getParentDns(): array
    {
        $requiredObjectClass = UserForm::$requiredObjectClasses[0] ?? 'inetOrgPerson';

        $pdns = [];
        foreach ((new LdapService())->getParentDns($requiredObjectClass) as $dn) {
            $pdns[$dn] = $dn;
        }
        return $pdns;
    }

    private function getDnByRequest(ServerRequestInterface $request): string|null
    {
        if (!empty($request->getQueryParams()['dn']) && is_string($request->getQueryParams()['dn'])) {
            return (string)$request->getQueryParams()['dn'];
        }

        return null;
    }

}
