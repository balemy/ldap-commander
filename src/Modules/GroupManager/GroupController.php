<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\GroupManager;

use Balemy\LdapCommander\LDAP\Services\LdapService;
use Balemy\LdapCommander\LDAP\Services\SchemaService;
use Balemy\LdapCommander\Modules\Session\Session;
use Balemy\LdapCommander\Modules\UserManager\User;
use Balemy\LdapCommander\Service\WebControllerService;
use LdapRecord\LdapRecordException;
use LdapRecord\Models\Entry;
use LdapRecord\Models\ModelDoesNotExistException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Session\SessionInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class GroupController
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
        $this->viewRenderer = $viewRenderer->withViewPath(__DIR__ . '/Views/');
    }

    public function list(WebControllerService $webService): ResponseInterface
    {
        return $this->viewRenderer->render('list', [
            'urlGenerator' => $this->urlGenerator,
            'groups' => Group::getAll()
        ]);
    }

    /**
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function edit(ServerRequestInterface $request, WebControllerService $webService): ResponseInterface
    {
        $groupModel = new GroupForm(
            dn: $this->getDnByRequest($request),
            schemaService: Session::getCurrentSession()->getSchemaService()
        );
        if ($request->getMethod() === Method::POST &&
            /** @psalm-suppress PossiblyInvalidArgument */
            $groupModel->load($request->getParsedBody()) && $this->validator->validate($groupModel)->isValid()) {
            $groupModel->save();
            $this->flash->add('success', ['body' => 'Group successfully saved!']);

            return $this->webService->getRedirectResponse('group-edit', ['dn' => $groupModel->getDn(), 'saved' => 1]);
        }

        $users = [];
        /** @var User $user */
        foreach (User::all() as $user) {
            $users[$user->getDn() ?? ''] = $user->getDisplayName();
        }

        return $this->viewRenderer->render('edit', [
            'urlGenerator' => $this->urlGenerator,
            'dn' => $groupModel->getDn(),
            'parentDNs' => $this->getParentDns(),
            'groupModel' => $groupModel,
            'users' => $users,
        ]);
    }

    public function members(ServerRequestInterface $request, WebControllerService $webService): ResponseInterface
    {
        $group = $this->getGroup($request);
        if ($group === null) {
            return $this->webService->getNotFoundResponse();
        }

        if ($request->getMethod() === 'POST') {
            $body = $request->getParsedBody();
            if (isset($body['addDn']) && is_string($body['addDn'])) {
                $group->addMember($body['addDn']);
            }
            if (isset($body['delDn']) && is_string($body['delDn'])) {
                $group->removeMember($body['delDn']);
            }
        }

        $noMembers = [];
        $members = [];
        /** @var User $user */
        foreach (User::all() as $user) {
            if (in_array($user->getDn(), $group->getUserDns())) {
                $members[] = $user;
            } else {
                $noMembers[] = $user;
            }
        }

        return $this->viewRenderer->render('members', [
            'urlGenerator' => $this->urlGenerator,
            'members' => $members,
            'noMembers' => $noMembers,
            'dn' => $group->getDn()
        ]);
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $dn = $this->getDnByRequest($request);
        if ($dn === null) {
            return $this->webService->getNotFoundResponse();
        }

        $entry = Entry::query()->find($dn);
        if (!($entry instanceof Entry)) {
            return $this->webService->getNotFoundResponse();
        }

        try {
            $entry->delete();
            $this->flash->add('success', ['body' => 'Group successfully deleted!']);
        } catch (ModelDoesNotExistException $e) {
            $this->flash->add('danger', ['body' => 'Group does not exist! Error: ' . $e->getMessage()]);
        } catch (LdapRecordException $e) {
            $this->flash->add('danger', ['body' => 'Group not deleted! Error: ' . $e->getMessage()]);
        }

        return $this->webService->getRedirectResponse('group-list', ['deleted' => 1]);
    }

    /**
     * @psalm-suppress MixedArgument, MixedAssignment
     * @return array<string, string>
     */
    private function getParentDns(): array
    {
        $userModel = new GroupForm(
            dn: null,
            schemaService: Session::getCurrentSession()->getSchemaService()
        );
        $requiredObjectClass = $userModel->requiredObjectClasses[0] ?? 'groupOfUniqueNames';

        $pdns = [];
        foreach ($this->ldapService->getParentDns($requiredObjectClass) as $dn) {
            $pdns[$dn] = $dn;
        }
        return $pdns;
    }

    private function getGroup(ServerRequestInterface $request): ?Group
    {
        $dn = $this->getDnByRequest($request);
        if ($dn !== null) {
            return Group::getOne($dn);
        }

        return null;
    }

    private function getDnByRequest(ServerRequestInterface $request): string|null
    {
        if (!empty($request->getQueryParams()['dn']) && is_string($request->getQueryParams()['dn'])) {
            return (string)$request->getQueryParams()['dn'];
        }

        return null;
    }


}
