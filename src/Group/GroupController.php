<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Group;

use Balemy\LdapCommander\Ldap\LdapService;
use Balemy\LdapCommander\Service\WebControllerService;
use Balemy\LdapCommander\User\User;
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
        $this->viewRenderer = $viewRenderer
            ->withControllerName('group')
            ->withLayout('@views/layout/main');
    }

    public function list(WebControllerService $webService): ResponseInterface
    {
        return $this->viewRenderer->render('list', [
            'urlGenerator' => $this->urlGenerator,
            'groups' => Group::getAll()
        ]);
    }

    public function add(ServerRequestInterface $request): ResponseInterface
    {
        $formModel = new GroupAddForm();

        if ($request->getMethod() === Method::POST) {
            /** @var array<string, array> $body */
            $body = $request->getParsedBody();

            if (!is_array($body['GroupAddForm']['initialMembers'])) {
                $body['GroupAddForm']['initialMembers'] = [];
            }

            if ($formModel->load($body) && $this->validator->validate($formModel)->isValid()) {
                $entry = new Entry();
                $entry->inside($formModel->getParentDn());
                $entry->setAttribute('objectclass', 'groupofuniquenames');
                $entry->setAttribute('cn', $formModel->getTitle());
                $entry->setAttribute('description', $formModel->getDescription());
                foreach ($formModel->getInitialMembers() as $memberDn) {
                    $entry->addAttributeValue('uniqueMember', $memberDn);
                }
                $entry->save();

                $this->flash->add('success', ['body' => 'Group successfully saved!']);
                return $this->webService->getRedirectResponse('group-list', ['saved' => 1]);
            }
        }

        $users = [];
        /** @var User $user */
        foreach (User::all() as $user) {
            $users[$user->getDn() ?? ''] = $user->getDisplayName();
        }


        return $this->viewRenderer->render('add', [
            'urlGenerator' => $this->urlGenerator,
            'formModel' => $formModel,
            'users' => $users,
            'parentDns' => $this->ldapService->getOrganizationalUnits()
        ]);
    }

    public function edit(ServerRequestInterface $request, WebControllerService $webService): ResponseInterface
    {
        $group = $this->getGroup($request);
        if ($group === null) {
            return $this->webService->getNotFoundResponse();
        }

        $formModel = new GroupForm();
        $formModel->loadGroup($group);

        if ($request->getMethod() === Method::POST) {
            /** @var array<string, array> $body */
            $body = $request->getParsedBody();
            if ($formModel->load($body) && $this->validator->validate($formModel)->isValid()) {
                $group->update($formModel);
                $this->flash->add('success', ['body' => 'Group successfully saved!']);
                return $this->webService->getRedirectResponse('group-edit', ['dn' => $group->getDn(), 'saved' => 1]);
            }
        }

        return $this->viewRenderer->render('edit', [
            'urlGenerator' => $this->urlGenerator,
            'dn' => $group->getDn(),
            'formModel' => $formModel,
            'parentDns' => $this->ldapService->getOrganizationalUnits()
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
        if ($entry == null || !($entry instanceof Entry)) {
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
        if (isset($request->getQueryParams()['dn']) && is_string($request->getQueryParams()['dn'])) {
            return (string)$request->getQueryParams()['dn'];
        }

        return null;
    }


}
