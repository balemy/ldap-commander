<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\User;

use Balemy\LdapCommander\Group\Group;
use Balemy\LdapCommander\Ldap\LdapService;
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
            'parentDNs' => $this->ldapService->getOrganizationalUnits(),
            'user' => $user,
        ]);
    }

    public function members(ServerRequestInterface $request): ResponseInterface
    {
        $user = new User();

        $dn = $this->getDnByRequest($request);
        if ($dn !== null && !$user->loadByEntryByDn($dn)) {
            return $this->webService->getNotFoundResponse();
        }

        if ($request->getMethod() === 'POST') {
            $body = $request->getParsedBody();

            if (isset($body['addDn']) && is_string($body['addDn'])) {
                $group = Group::getOne($body['addDn']);
                if ($group !== null) {
                    $group->addMember($user->getDn());
                }
            }
            if (isset($body['delDn']) && is_string($body['delDn'])) {
                $group = Group::getOne($body['delDn']);
                if ($group !== null) {
                    $group->removeMember($user->getDn());
                }
            }
            // Reload user
            $user->loadByEntryByDn($user->getDn());
        }


        $groups = $user->getGroups();

        $notAssignedGroups = [];
        $assignedGroups = [];
        foreach (Group::getAll() as $group) {
            if (in_array($group->getDn(), $groups)) {
                $assignedGroups[] = $group;
            } else {
                $notAssignedGroups[] = $group;
            }
        }

        return $this->viewRenderer->render('groups', [
            'urlGenerator' => $this->urlGenerator,
            'assignedGroups' => $assignedGroups,
            'notAssignedGroups' => $notAssignedGroups,
            'dn' => $user->getDn(),
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
