<?php

namespace Balemy\LdapCommander\Modules\SlapdConfig\Models;

use Balemy\LdapCommander\LDAP\LdapFormModel;
use Balemy\LdapCommander\Modules\GroupManager\Group;
use Balemy\LdapCommander\Modules\Session\Session;
use Balemy\LdapCommander\Modules\SlapdConfig\Services\SlapdConfigService;
use LdapRecord\Models\Entry;

class MemberOf extends LdapFormModel
{
    public static array $requiredObjectClasses = ['olcMemberOfConfig', 'olcOverlayConfig'];

    public static function getModel(SlapdConfigService $configService): self
    {
        // Support for older OpenLDAP Version, which renamed olcmemberof Overlay
        if (isset($configService->getSession()->schema->objectClasses['olcmemberof'])) {
            self::$requiredObjectClasses = ['olcMemberOf', 'olcOverlayConfig'];
        }

        /** @var Entry|null $lrEntry */
        $lrEntry = Entry::query()
            ->setConnection($configService->lrConnection)
            ->where('objectclass', '=', self::$requiredObjectClasses[0])
            ->in($configService->getDatabaseConfigDn())
            ->first();

        if ($lrEntry === null) {
            $lrEntry = new Entry;
            $lrEntry->setAttribute('objectclass', static::$requiredObjectClasses);
            $lrEntry->inside($configService->getDatabaseConfigDn());
            $lrEntry->setDn('olcOverlay={0}memberof,' . $configService->getDatabaseConfigDn());
            $lrEntry->save();
        }

        $model = new MemberOf(dn: null,
            lrEntry: $lrEntry,
            schemaService: Session::getCurrentSession()->getSchemaService()
        );

        return $model;
    }


    public function getPropertyLabels(): array
    {
        return [
            'olcMemberOfGroupOC' => 'Group ObjectClass',
            'olcMemberOfMemberAD' => 'Member Attribute',
            'olcMemberOfMemberOfAD' => 'MemberOf Attribute',
            'olcMemberOfRefInt' => 'Ref. Integrity',
            'olcMemberOfDangling' => 'Dangling'
        ];
    }

    public function getPropertyHints(): array
    {
        return [
            'olcMemberOfGroupOC' => 'e.g. groupOfUniqueNames',
            'olcMemberOfMemberAD' => 'e.g. UniqueMember',
            'olcMemberOfMemberOfAD' => 'e.g. memberOf',
            'olcMemberOfRefInt' => 'e.g. TRUE',
            'olcMemberOfDangling' => 'Possible values: ignore, drop, error'
        ];
    }

    public function getFormName(): string
    {
        return 'MemberOfForm';
    }
}
