<?php

declare(strict_types=1);

namespace App\Controller;

use App\Ldap\Group;
use App\Ldap\GroupForm;
use App\Ldap\LdapService;
use App\Ldap\User;
use App\Service\WebControllerService;
use LdapRecord\LdapRecordException;
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
            'formModel' => $formModel
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
        foreach (User::getAll() as $user) {
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

    private function getGroup(ServerRequestInterface $request): ?Group
    {
        $dn = $this->getDnByRequest($request);
        if ($dn !== null) {
            /** @var Entry|null $entry */
            $entry = Entry::query()->find($dn);
            if ($entry !== null) {
                return new Group($entry);
            }
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