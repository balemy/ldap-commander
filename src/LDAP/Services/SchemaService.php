<?php

namespace Balemy\LdapCommander\LDAP\Services;

use Balemy\LdapCommander\LDAP\Schema\Schema;

class SchemaService
{
    private Schema $schema;

    public function __construct(private LdapService $ldapService)
    {
        $this->schema = $this->ldapService->getSchema();
    }


    /**
     * @param string $attributeName
     * @param string[] $objectClasses
     * @return bool
     */
    public function hasAttribute(string $attributeName, array $objectClasses = []): bool
    {
        $attributeName = strtolower($attributeName);

        foreach ($objectClasses as $objectClassName) {
            $objectClass = $this->schema->getObjectClass($objectClassName);

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
