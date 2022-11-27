<?php

namespace Balemy\LdapCommander\User;

use Balemy\LdapCommander\Group\Group;
use LdapRecord\Models\Entry;
use LdapRecord\Models\OpenLDAP\User as LrUser;
use Yiisoft\Form\FormModel;
use Yiisoft\Validator\Rule\Required;

class User extends FormModel
{
    private Entry $entry;

    private ?int $id = null;

    private string $parentDn = '';


    private string $username = '';
    private string $commonName = '';

    private string $title = '';
    private string $firstName = '';
    private string $lastName = '';
    private string $mail = '';

    private string $dn = '';
    private string $mobile = '';
    private string $homeNumber = '';
    private string $initials = '';
    private string $telephoneNumber = '';

    public function __construct(?Entry $entry = null)
    {
        parent::__construct();

        if ($entry !== null) {
            $this->entry = $entry;
        } else {
            $this->entry = new LrUser();
        }
        $this->loadByEntry();
    }

    /**
     * @return User[]
     */
    public static function getAll(): array
    {
        $users = [];
        /** @var Entry $entry */
        foreach (LrUser::all() as $entry) {
            $users[] = new User($entry);
        }
        return $users;
    }

    public function loadByEntryByDn(string $dn): bool
    {
        $entry = LrUser::query()->addSelect(['*', '+'])->find($dn, ['*']);

        if ($entry instanceof Entry) {
            $this->entry = $entry;
            $this->loadByEntry();

            return true;
        }
        return false;
    }

    public function getEntry(): Entry
    {
        return $this->entry;
    }

    public function getParentDn(): string
    {
        return $this->parentDn;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommonName(): string
    {
        return $this->commonName;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getInitials(): string
    {
        return $this->initials;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMail(): string
    {
        return $this->mail;
    }


    public function getMobile(): string
    {
        return $this->mobile;
    }

    public function getTelephoneNumber(): string
    {
        return $this->telephoneNumber;
    }

    public function getHomeNumber(): string
    {
        return $this->homeNumber;
    }


    public function getDn(): string
    {
        return $this->dn;
    }

    /**
     * @return Group[]
     */
    public function getGroups(): array
    {
        $groups = [];
        if (!empty($this->entry->getAttributeValue('memberof')) && is_array($this->entry->getAttributeValue('memberof'))) {
            /** @var string[] $memberOf */
            $memberOf = $this->entry->getAttributeValue('memberof');
            foreach ($memberOf as $groupDn) {
                $group = Group::getOne($groupDn);
                if ($group !== null) {
                    $groups[] = $group;
                }
            }
        }
        return $groups;
    }

    public function getDisplayName(): string
    {
        $displayName = '';
        $firstName = $this->getFirstName();
        $lastName = $this->getFirstName();

        if (!empty($firstName)) {
            $displayName = $firstName;
        }
        if (empty($lastName)) {
            if ($displayName !== '') {
                $displayName .= ' ';
            }
            $displayName .= $lastName;
        }
        return $displayName;
    }

    private function getEntryValue(string $name): string
    {
        /**
         * @psalm-suppress MixedAssignment
         */
        $res = $this->entry->getAttributeValue($name);
        if (is_array($res)) {
            if (isset($res[0]) && is_string($res[0])) {
                return $res[0];
            }
        }
        return '';
    }

    public function getAttributeLabels(): array
    {
        return [
            'parentDn' => 'Organizational Unit'
        ];
    }

    public function getRules(): array
    {
        return [
            'username' => [new Required()],
            'lastName' => [new Required()],
            'commonName' => [new Required()],
            'parentDn' => [new Required()],
        ];
    }

    public function updateEntry(): bool
    {
        $isNewRecord = $this->isNewRecord();

        $this->setEntryAttribute('cn', $this->getCommonName(), $isNewRecord);
        $this->setEntryAttribute('uid', $this->getUsername(), $isNewRecord);

        //$this->setEntryAttribute('uidNumber', (string)$this->getId(), $isNewRecord);
        $this->setEntryAttribute('givenName', $this->getFirstName(), $isNewRecord);
        $this->setEntryAttribute('sn', $this->getLastName());
        $this->setEntryAttribute('initials', $this->getInitials(), $isNewRecord);
        $this->setEntryAttribute('telephoneNumber', $this->getTelephoneNumber(), $isNewRecord);
        $this->setEntryAttribute('mobile', $this->getMobile(), $isNewRecord);
        $this->setEntryAttribute('homeNumber', $this->getHomeNumber(), $isNewRecord);
        $this->setEntryAttribute('mail', $this->getMail(), $isNewRecord);

        $moveToDn = null;

        if ($this->isNewRecord()) {
            $this->entry->inside($this->getParentDn());
        } else {
            if ($this->entry->getParentDn() !== $this->getParentDn()) {
                $moveToDn = $this->getParentDn();
            }

            $head = $this->entry->getHead();
            if ($head !== null && $this->entry->isDirty($head)) {
                $this->entry->rename((string)$this->entry->getFirstAttribute($head));
                $this->entry->refresh();
                $this->dn = $this->entry->getDn() ?? '';
            }
        }

        $this->entry->save();

        if ($moveToDn !== null) {
            $this->entry->move($moveToDn);
            $this->entry->refresh();
            $this->dn = $this->entry->getDn();
        }

        return true;
    }


    private function setEntryAttribute(string $name, string $value, bool $skipWhenEmpty = true): void
    {
        if (!empty($value) || !$skipWhenEmpty) {
            $this->entry->setFirstAttribute($name, $value);
        }
    }

    private function loadByEntry(): void
    {
        $id = $this->getEntryValue('uidNumber');
        if (empty($id)) {
            $this->id = intval($id);
        }

        $this->commonName = $this->getEntryValue('cn');
        $this->username = $this->getEntryValue('uid');
        $this->firstName = $this->getEntryValue('givenName');
        $this->lastName = $this->getEntryValue('sn');
        $this->initials = $this->getEntryValue('initials');
        $this->telephoneNumber = $this->getEntryValue('telephoneNumber');
        $this->mobile = $this->getEntryValue('mobile');
        $this->homeNumber = $this->getEntryValue('homeNumber');
        $this->mail = $this->getEntryValue('mail');
        $this->parentDn = $this->entry->getParentDn() ?? '';
        $this->dn = $this->entry->getDn() ?? '';
    }

    public function isNewRecord(): bool
    {
        return ($this->getDn() === '');
    }

}
