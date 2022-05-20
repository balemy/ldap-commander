<?php

declare(strict_types=1);

namespace App\Controller;

use App\Ldap\EntityForm;
use App\Ldap\LdapService;
use App\Service\WebControllerService;
use LdapRecord\LdapRecordException;
use LdapRecord\Models\Entry;
use LdapRecord\Models\Model;
use LdapRecord\Models\ModelDoesNotExistException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Http\Method;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class EntityController
{
    public function __construct(public ViewRenderer          $viewRenderer,
                                public LdapService           $ldapService,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public AssetManager          $assetManager,
                                public FlashInterface        $flash
    )
    {
        $this->viewRenderer = $viewRenderer->withControllerName('entity');
    }

    public function open(ServerRequestInterface $request): ResponseInterface
    {
        $dn = $this->getDnByRequest($request) ?? $this->ldapService->baseDn;

        if ($this->ldapService->getChildrenCount($dn) > 0) {
            return $this->webService->getRedirectResponse('entity-list', ['dn' => $dn]);
        } else {
            return $this->webService->getRedirectResponse('entity-edit', ['dn' => $dn]);
        }
    }

    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $dn = $this->getDnByRequest($request) ?? $this->ldapService->baseDn;

        $query = $this->ldapService->connection->query();
        $query->select(['cn', 'dn'])->setDn($dn)->listing();

        $results = $query->paginate();

        return $this->viewRenderer->render('list', ['results' => $results,
            'dn' => $dn,
            'urlGenerator' => $this->urlGenerator,]);
    }

    public function edit(ServerRequestInterface $request): ResponseInterface
    {
        $dn = $this->getDnByRequest($request);
        if ($dn === null) {
            $this->flash->add('danger', 'DN not found!');
            return $this->webService->getRedirectResponse('entity-list');
        }

        if (isset($request->getQueryParams()['new']) && $request->getQueryParams()['new'] == 1) {
            $entity = new EntityForm(
                $this->ldapService->getSchema(),
                new Entry(),
                true,
                $dn
            );
        } else {
            $entry = Entry::query()->find($dn);
            if ($entry === null || !($entry instanceof Entry)) {
                $this->flash->add('danger', 'DN not found!');
                return $this->webService->getRedirectResponse('entity-list');
            }

            $entity = new EntityForm(
                $this->ldapService->getSchema(),
                $entry,
                false
            );
        }

        if ($request->getMethod() === Method::POST) {
            /** @var array<string, string|array> $body */
            $body = $request->getParsedBody();

            /*&& $validator->validate($form)->isValid()*/
            if ($entity->load($body)) {
                try {
                    $entity->save();
                    $this->flash->add('success', ['body' => 'Entity successfully saved!']);
                    return $this->webService->getRedirectResponse('entity-edit', ['dn' => $entity->getDn(), 'saved' => 1]);
                } catch (LdapRecordException $exception) {
                    $this->flash->add('danger', ['body' => $exception->getMessage()]);
                }
            }
        }

        return $this->viewRenderer->render('edit', [
            'dn' => $entity->entry->getDn() ?? $entity->parentDn,
            'urlGenerator' => $this->urlGenerator,
            'assetManager' => $this->assetManager,
            'schemaJsonInfo' => $this->ldapService->getSchema()->getJsonInfo(),
            'attributeTypes' => $this->ldapService->getSchema()->attributeTypes,
            'objectClassNames' => $this->ldapService->getSchema()->getObjectClassNames(),
            'entity' => $entity,
        ]);
    }

    public function delete(ServerRequestInterface $request): ResponseInterface
    {
        $dn = $this->getDnByRequest($request);
        if ($dn === null) {
            $this->flash->add('danger', 'No DN gieven!');
            return $this->webService->getRedirectResponse('entity-list');
        }

        $entry = Entry::query()->find($dn);
        if ($entry == null || !($entry instanceof Entry)) {
            $this->flash->add('danger', 'Could not load entry!');
            return $this->webService->getRedirectResponse('entity-list');
        }

        try {
            /** @var string|null $parentDn */
            $parentDn = $entry->getParentDn();

            $entry->delete();
            $this->flash->add('success', ['body' => 'Entity successfully deleted!']);
        } catch (ModelDoesNotExistException $e) {
            $this->flash->add('danger', ['body' => 'Entity does not exist! Error: ' . $e->getMessage()]);
        } catch (LdapRecordException $e) {
            $this->flash->add('danger', ['body' => 'Entity not deleted! Error: ' . $e->getMessage()]);
        }

        return $this->webService->getRedirectResponse('entity', [
                'dn' => $parentDn ?? $this->ldapService->baseDn,
                'deleted' => 1]
        );
    }

    private function getDnByRequest(ServerRequestInterface $request): string|null
    {
        if (isset($request->getQueryParams()['dn']) && is_string($request->getQueryParams()['dn'])) {
            return (string)$request->getQueryParams()['dn'];

        }

        return null;
    }


}
