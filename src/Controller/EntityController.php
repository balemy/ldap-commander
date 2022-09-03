<?php

declare(strict_types=1);

namespace App\Controller;

use App\Ldap\EntityForm;
use App\Ldap\LdapService;
use App\Service\WebControllerService;
use HttpSoft\Message\StreamFactory;
use HttpSoft\Message\UploadedFile;
use LdapRecord\LdapRecordException;
use LdapRecord\Models\Entry;
use LdapRecord\Models\ModelDoesNotExistException;
use LdapRecord\Support\Arr;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Yiisoft\Assets\AssetManager;
use Yiisoft\Http\Header;
use Yiisoft\Http\Method;
use Yiisoft\Http\Status;
use Yiisoft\Router\UrlGeneratorInterface;
use Yiisoft\Session\Flash\FlashInterface;
use Yiisoft\Validator\ValidatorInterface;
use Yiisoft\Yii\View\ViewRenderer;

final class EntityController
{
    public function __construct(public ViewRenderer          $viewRenderer,
                                public LdapService           $ldapService,
                                public WebControllerService  $webService,
                                public UrlGeneratorInterface $urlGenerator,
                                public AssetManager          $assetManager,
                                public FlashInterface        $flash,
                                public ValidatorInterface    $validator
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
            return $this->webService->getNotFoundResponse();
        }

        if (isset($request->getQueryParams()['new']) && $request->getQueryParams()['new'] == 1) {
            /** @var Entry $parentEntry */
            $parentEntry = Entry::query()->find($dn);

            if (isset($request->getQueryParams()['duplicate']) && $request->getQueryParams()['duplicate'] == 1) {
                $parentDn = $parentEntry->getParentDn();
                if ($parentDn !== null) {
                    $parentEntry = Entry::query()->find($parentDn);
                }
            }

            /** @var string $pdn */
            $pdn = ($parentEntry !== null) ? $parentEntry->getDn() : '';

            $entity = new EntityForm(
                $this->ldapService->getSchema(),
                new Entry(),
                true,
                $pdn
            );

            if (isset($request->getQueryParams()['duplicate']) && $request->getQueryParams()['duplicate'] == 1) {
                /** @var Entry $e */
                $e =  Entry::query()->find($dn);
                $entity->preloadAttributesFromEntry($e);
            }
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
            /** @var array<string, array> $body */
            $body = $request->getParsedBody();

            if (isset($request->getUploadedFiles()['EntityForm']) && is_array($request->getUploadedFiles()['EntityForm'])) {
                /** @var array $files */
                foreach ($request->getUploadedFiles()['EntityForm'] as $attribute => $files) {
                    assert(is_string($attribute));
                    $body['EntityForm'][$attribute] = [];

                    /** @var UploadedFile $uploadedFile */
                    foreach ($files as $index => $uploadedFile) {
                        if ($uploadedFile->getError() === 0) {
                            /** @var int $index */
                            $body['EntityForm'][$attribute][$index] = $uploadedFile->getStream()->getContents();
                        } else {
                            $body['EntityForm'][$attribute][$index] = '';
                        }
                    }
                }
            }

            if ($entity->load($body) && $this->validator->validate($entity)->isValid()) {
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


    public function rename(ServerRequestInterface $request): ResponseInterface
    {
        $dn = $this->getDnByRequest($request);
        if ($dn === null) {
            return $this->webService->getNotFoundResponse();
        }

        $entry = Entry::query()->find($dn);
        if ($entry === null || !($entry instanceof Entry)) {
            return $this->webService->getNotFoundResponse();
        }

        return $this->viewRenderer->render('rename', [
            'dn' => $entry->getDn(),
            'urlGenerator' => $this->urlGenerator,
            'assetManager' => $this->assetManager,
            'schemaJsonInfo' => $this->ldapService->getSchema()->getJsonInfo(),
        ]);
    }


    public function move(ServerRequestInterface $request): ResponseInterface
    {
        $dn = $this->getDnByRequest($request);
        if ($dn === null) {
            return $this->webService->getNotFoundResponse();
        }

        $entry = Entry::query()->find($dn);
        if ($entry === null || !($entry instanceof Entry)) {
            return $this->webService->getNotFoundResponse();
        }

        return $this->viewRenderer->render('move', [
            'dn' => $entry->getDn(),
            'urlGenerator' => $this->urlGenerator,
            'assetManager' => $this->assetManager,
            'schemaJsonInfo' => $this->ldapService->getSchema()->getJsonInfo(),
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

    public function downloadBinaryAttribute(ServerRequestInterface $request, ResponseFactoryInterface $responseFactory): ResponseInterface
    {
        $dn = $this->getDnByRequest($request);

        $attribute = null;
        if (isset($request->getQueryParams()['attribute']) && is_string($request->getQueryParams()['attribute'])) {
            $attribute = (string)$request->getQueryParams()['attribute'];
        }

        $index = null;
        if (isset($request->getQueryParams()['i']) && is_string($request->getQueryParams()['i'])) {
            $index = (int)$request->getQueryParams()['i'];
        }


        if ($index !== null && $attribute !== null && $dn !== null) {
            $entry = Entry::query()->find($dn);

            if ($entry !== null) {
                $attr = Arr::wrap($entry->getAttributeValue($attribute));

                if (isset($attr[$index])) {
                    return $responseFactory
                        ->createResponse(Status::OK)
                        ->withHeader(Header::CONTENT_TYPE, 'application/octet-stream')
                        ->withBody((new StreamFactory())->createStream((string)$attr[$index]));
                }
            }
        }

        return $responseFactory->createResponse(Status::NOT_FOUND);
    }

    private function getDnByRequest(ServerRequestInterface $request): string|null
    {
        if (isset($request->getQueryParams()['dn']) && is_string($request->getQueryParams()['dn'])) {
            return (string)$request->getQueryParams()['dn'];
        }

        return null;
    }
}
