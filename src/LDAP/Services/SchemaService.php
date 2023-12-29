<?php

namespace Balemy\LdapCommander\LDAP\Services;

use Balemy\LdapCommander\LDAP\LdapService;
use Balemy\LdapCommander\LDAP\Schema\ObjectClass;
use Balemy\LdapCommander\LDAP\Schema\Schema;

class SchemaService
{
    private Schema $schema;

    public function __construct(private LdapService $ldapService)
    {
        $this->schema = $this->ldapService->getSchema();
    }


    /**
     * @param ObjectClass[] $objectClassNames
     * @param $lookupSupClasses
     * @return array
     * @throws \Exception
     */
    public function getObjectClassesByNames(array $objectClassNames, $lookupSupClasses = true): array
    {
        $objectClasses = [];
        foreach ($objectClassNames as $objectClassName) {
            $objectClass = $this->schema->getObjectClass($objectClassName);
            if ($objectClass === null) {
                throw new \Exception('Invalid Object Class: ' . $objectClassName);
            }
            $objectClasses[] = $objectClass;
        }

        if (!$lookupSupClasses) {
            return $objectClasses;
        }

    }

    public function hasAttribute($attributeName, array $objectClasses = []): bool
    {
        $attributeName = strtolower($attributeName);

        #       $attrs = [];
        foreach ($objectClasses as $objectClassName) {
            $objectClass = $this->schema->getObjectClass($objectClassName);
            if ($objectClass === null) {
                throw new \Exception('Invalid Object Class: ' . $objectClassName);
            }

            #          $attrs = array_merge($objectClass->getAttributeIds(true), $attrs);
            if (in_array($attributeName, $objectClass->getAttributeIds(true))) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $objectClassNames
     * @return string[]
     * @throws \Exception
     */
    public function getMustAttributes(array $objectClassNames = []): array
    {
        $mustAttributesNames = [];
        foreach ($objectClassNames as $objectClassName) {
            $objectClass = $this->schema->getObjectClass($objectClassName);

            $mustAttributes = $objectClass->getMustAttributes();
            foreach ($objectClass->getSuperClassesRecursive() as $superOc) {
                $mustAttributes = array_merge($mustAttributes, $superOc->getMustAttributes());
            }

            foreach ($mustAttributes as $mustAttribute) {
                $mustAttributesNames[] = $mustAttribute->getPrimaryName();
            }
        }

        return array_unique($mustAttributesNames);
    }
}
