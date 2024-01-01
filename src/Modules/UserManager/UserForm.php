<?php

namespace Balemy\LdapCommander\Modules\UserManager;

use Balemy\LdapCommander\LDAP\LdapFormModel;
use Balemy\LdapCommander\Modules\GroupManager\Group;


class UserForm extends LdapFormModel
{
    public static array $requiredObjectClasses = ['inetorgperson'];


    protected string $headAttribute = 'uid';
    protected array $customProperties = ['groups'];

    protected function init(): void
    {
        if (!$this->isNewRecord) {
            $this->loadedProperties['groups'] = (array)$this->lrEntry->getAttributeValue('memberof');
        }
    }


    /**
     * @psalm-suppress MixedArrayAccess
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedArrayAssignment
     * @psalm-suppress MixedOperand
     */
    public function load(array $data): bool
    {
        if (is_array($data[$this->getFormName()])) {
            // Auto set CN based on givenName and SN
            $givenName = $data[$this->getFormName()]['givenName'] ?? '';
            $sn = $data[$this->getFormName()]['sn'] ?? '';
            $data[$this->getFormName()]['cn'] = $givenName . ' ' . $sn;
        }
        return parent::load($data);
    }


    public function save(): bool
    {
        // Auto Hash Password
        $password = $this->loadedProperties['userPassword'];
        if (!empty($password) && is_string($password) && !preg_match("/^\{\w{1,10}\}/", $password)) {
            $salt = mt_rand(0, mt_getrandmax());
            $this->loadedProperties['userPassword'] =
                '{SSHA}' . base64_encode(sha1($password . $salt, TRUE) . $salt);
        }

        // Group Memberships
        /** @var string[] $currentGroups */
        $currentGroups = (array)$this->lrEntry->getAttributeValue('memberof');
        /** @var string[] $newGroups */
        $newGroups = (is_array($this->loadedProperties['groups'])) ? $this->loadedProperties['groups'] : [];

        foreach (array_diff($newGroups, $currentGroups) as $groupDn) {
            (Group::getOne($groupDn))?->addMember($this->getDn());
        }
        foreach (array_diff($currentGroups, $newGroups) as $groupDn) {
            (Group::getOne($groupDn))?->removeMember($this->getDn());
        }

        return parent::save();
    }

    public function getFormName(): string
    {
        return 'UserForm';
    }
}
