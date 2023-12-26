<?php

namespace Balemy\LdapCommander\ServerConfig\MemberOf;

use Yiisoft\FormModel\FormModel;
use Yiisoft\Validator\RulesProviderInterface;

class MemberOfForm extends FormModel implements RulesProviderInterface
{

    /*
    olcMemberOfRefInt              true
    olcMemberOfMemberOfAD
    olcMemberOfMemberAD            UniqueMember
    olcMemberOfGroupOC             groupOfUniqueNames
    olcMemberOfDN
    olcMemberOfDanglingError
    olcMemberOfDangling
    olcDisabled
    */

    private ?bool $enabled = null;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ?string $memberOfAD;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ?string $memberAD;
    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private ?string $groupOC;

    /**
     * @psalm-suppress PropertyNotSetInConstructor
     */
    private string $dn = '';
    private string $dangling = 'ignore';
    private string $danglingError = '';


    public function getAttributeLabels(): array
    {
        return [
            'enabled' => 'Enable module',
            'dn' => 'DN',
            'memberOfAD' => 'Attribute Name: MemberOf',
            'memberAd' => 'Attribute Name: Member',
            'groupOC' => 'Group ObjectClass Name'
        ];
    }

    public function getAttributeHints(): array
    {
        return [
            'dn' => 'DN to be used as modifiersName',
            'groupOC' => 'e.g. groupOfUniqueNames',
            'memberAD' => 'e.g. uniqueMember',
            'memberOfAD' => 'e.g. uniqueMember',
        ];
    }

    public function getRules(): array
    {
        return [];
    }
}
