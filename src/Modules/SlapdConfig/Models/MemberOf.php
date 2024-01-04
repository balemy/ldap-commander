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
        /** @var Entry $lrEntry */
        $lrEntry = Entry::query()
            ->setConnection($configService->lrConnection)
            ->where('objectclass', '=', 'olcMemberOfConfig')
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
            'olcMemberOfRefInt' => 'Ref. Integrity'
        ];
    }

    public function getPropertyHints(): array
    {
        return [
            'olcMemberOfGroupOC' => 'e.g. groupOfUniqueNames',
            'olcMemberOfMemberAD' => 'e.g. UniqueMember',
            'olcMemberOfRefInt' => 'e.g. TRUE'
        ];
    }

    public function getFormName(): string
    {
        return 'MemberOfForm';
    }
}
