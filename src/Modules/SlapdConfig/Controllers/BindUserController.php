<?php

declare(strict_types=1);

namespace Balemy\LdapCommander\Modules\SlapdConfig\Controllers;

use Balemy\LdapCommander\LDAP\Services\LdapService;
use Balemy\LdapCommander\Modules\Session\Session;
use Balemy\LdapCommander\Modules\SlapdConfig\Models\BindUser;
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

final class BindUserController
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
        $this->viewRenderer = $viewRenderer->withViewPath(dirname(__DIR__) . '/Views/bind-user/');
    }

    public function list(WebControllerService $webService): ResponseInterface
    {
        return $this->viewRenderer->render('list', [
            'urlGenerator' => $this->urlGenerator,
            'bindUsers' => BindUser::getAll()
        ]);
    }

    /**
     * @psalm-suppress PossiblyInvalidArgument
     */
    public function edit(ServerRequestInterface $request, WebControllerService $webService): ResponseInterface
    {
        $bindUser = new BindUser(
            dn: $this->getDnByRequest($request),
            schemaService: Session::getCurrentSession()->getSchemaService()
        );
        if ($request->getMethod() === Method::POST &&
            /** @psalm-suppress PossiblyInvalidArgument */
            $bindUser->load($request->getParsedBody()) && $this->validator->validate($bindUser)->isValid()) {
            $bindUser->save();
            $this->flash->add('success', ['body' => 'Bind User successfully saved!']);

            return $this->webService->getRedirectResponse('bind-user-edit', ['dn' => $bindUser->getDn(), 'saved' => 1]);
        }

        return $this->viewRenderer->render('edit', [
            'urlGenerator' => $this->urlGenerator,
            'dn' => $bindUser->getDn(),
            'parentDNs' => $this->getParentDns(),
            'bindUser' => $bindUser,
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
        $requiredObjectClass = BindUser::$requiredObjectClasses[0] ?? 'groupOfUniqueNames';

        $pdns = [];
        foreach ($this->ldapService->getParentDns($requiredObjectClass) as $dn) {
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
