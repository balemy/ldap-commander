<?php

namespace Balemy\LdapCommander\Ldap\Schema;

use Balemy\LdapCommander\Ldap\LdapService;
use Balemy\LdapCommander\Timer;
use LdapRecord\Connection;
use Yiisoft\Json\Json;

class Schema
{
    /**
     * @var AttributeType[]
     */
    public $attributeTypes = [];

    /**
     * @var ObjectClass[]
     */
    public $objectClasses = [];

    /**
     * @var Syntax[]
     */
    public $syntaxes = [];

    /**
     * @var AttributeType[]|null
     */
    private $_attributeTypesBinary = null;

    public function __construct(public LdapService $ldapService, private Timer $timer)
    {

    }


    public function getObjectClass(string $name): ?ObjectClass
    {
        return $this->objectClasses[strtolower($name)] ?? null;
    }

    /**
     * @param string $oid
     * @return ObjectClass|null
     */
    public function getObjectClassByOid(string $oid): ?ObjectClass
    {
        foreach ($this->objectClasses as $objectClass) {
            if ($objectClass->oid === $oid) {
                return $objectClass;
            }
        }
        return null;
    }

    /**
     * @param string $oid
     * @return AttributeType|null
     */
    public function getAttributeTypeByOid(string $oid): ?AttributeType
    {
        foreach ($this->attributeTypes as $attributeType) {
            if ($attributeType->oid === $oid) {
                return $attributeType;
            }
        }
        return null;
    }

    /**
     * @return string[]
     *
     * @psalm-return array<string, string>
     */
    public function getObjectClassNames(): array
    {
        $list = [];
        foreach ($this->objectClasses as $objectClass) {
            $list[strtolower($objectClass->name)] = $objectClass->name;
        }
        return $list;
    }

    /**
     * @param AttributeType $attribute
     * @return ObjectClass[]
     */
    public function getObjectClassesByAttributeType(AttributeType $attribute): array
    {
        $objectClasses = [];
        foreach ($this->objectClasses as $objectClass) {
            foreach (array_merge($objectClass->getMustAttributes(), $objectClass->getMayAttributes()) as $attr) {
                if ($attr->oid === $attribute->oid) {
                    $objectClasses[$objectClass->oid] = $objectClass;
                }
            }
        }

        return $objectClasses;
    }


    public function getAttribute(string $id): ?AttributeType
    {
        if (!isset($this->attributeTypes[strtolower($id)])) {
            # NOsubtreeSpecificationNOdITStructureRulesNOnameFormsNOdITContentRules
            # print "NO" . $name;
            return null;
        }

        return $this->attributeTypes[strtolower($id)];
    }

    public function getBinaryAttributes(): array
    {
        if ($this->_attributeTypesBinary !== null) {
            return $this->_attributeTypesBinary;
        }

        $this->_attributeTypesBinary = [];
        foreach ($this->attributeTypes as $attributeId => $attributeType) {
            if (!empty($attributeType->syntax)) {
                $syntax = $this->getSyntax($attributeType->syntax);
                if ($syntax !== null) {
                    if ($syntax->isNotHumanReadable || $syntax->isBinaryTransferRequired) {
                        $this->_attributeTypesBinary[$attributeId] = $attributeType;
                    }
                }
            }
        }

        return $this->_attributeTypesBinary;
    }


    public function getSyntax(string $oid): ?Syntax
    {
        if (!isset($this->syntaxes[strtolower($oid)])) {
            return null;
        }

        return $this->syntaxes[strtolower($oid)];
    }

    public function resolveAttributeIdByName(string $name): string
    {
        $name = strtolower($name);

        if (isset($this->attributeTypes[$name])) {
            return $name;
        }

        foreach ($this->attributeTypes as $attributeType) {
            if (in_array($name, array_map('strtolower', $attributeType->names))) {
                return $attributeType->names[0];
            }
        }

        if (!in_array($name, ['subtreespecification', 'ditstructurerules', 'nameforms', 'ditcontentrules'])) {
            throw new \Exception('Could not resolve if of name: ' . $name);
        }

        return $name;
    }


    /**
     * @return void
     */
    public function populate(Connection $connection): void
    {
        $this->timer->start('schema');

        $query = $this->ldapService->connection->query();

        $query->read()
            ->setBaseDn('cn=Subschema')
            ->addSelect(['objectclasses', 'attributetypes', 'ldapsyntaxes', 'matchingrules', 'matchingruleuse'])
            ->rawFilter('(objectClass=*)');

        $res = (array)$query->get()[0];

        /** @var string $line */
        foreach ($res['ldapsyntaxes'] as $line) {
            $syntax = Syntax::createByString($line);
            if ($syntax !== null) {
                $this->syntaxes[$syntax->oid] = $syntax;
            }
        }

        /** @var string $line */
        foreach ($res['attributetypes'] as $line) {
            $attributeType = AttributeType::createByString($line);
            if ($attributeType !== null && isset($attributeType->names[0])) {
                $id = strtolower(string: $attributeType->names[0]);
                $this->attributeTypes[$id] = $attributeType;
            }
        }

        /** @var string $line */
        foreach ($res['objectclasses'] as $line) {
            $objectClass = ObjectClass::createByString($this, $line);
            if ($objectClass !== null) {
                $this->objectClasses[strtolower($objectClass->name)] = $objectClass;
                /** @var string $attrName */
                foreach ($objectClass->getAttributeIds() as $attrName) {
                    $attribute = $this->getAttribute($attrName);
                    if ($attribute !== null) {
                        $attribute->objectClasses[$objectClass->oid] = $objectClass;
                    }
                }
                /** @var string $attrName */
                foreach ($objectClass->mustAttributes as $attrName) {
                    $attribute = $this->getAttribute($attrName);
                    if ($attribute !== null) {
                        $attribute->objectClassesMust[$objectClass->oid] = $objectClass;
                    }
                }
            }
        }

        $this->timer->stop('schema');

    }


    public function getJsonInfo(): string
    {
        $json = [];

        $json['objectClasses'] = [];
        foreach ($this->objectClasses as $objectClass) {
            $json['objectClasses'][strtolower($objectClass->name)] = [
                'name' => $objectClass->name,
                'sups' => $objectClass->sups,
                'must' => $objectClass->mustAttributes, // should be lower case
                'may' => $objectClass->mayAttributes // should be lower case
            ];
        }

        return Json::encode($json);
    }
}
