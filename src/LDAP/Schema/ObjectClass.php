<?php

namespace Balemy\LdapCommander\LDAP\Schema;

use Yiisoft\Arrays\ArrayHelper;

final class ObjectClass
{
    /**
     * @param Schema $schema
     * @param string $definition
     * @param string $oid
     * @param string $name
     * @param string $description
     * @param string[] $sups
     * @param array $mayAttributes
     * @param array $mustAttributes
     * @param bool $isAuxiliary
     * @param bool $isStructural
     */
    public function __construct(
        private Schema $schema,
        public string $definition,
        public string $oid,
        public string $name,
        public string $description,
        public array $sups,
        public array $mayAttributes,
        public array $mustAttributes,
        public bool $isAuxiliary,
        public bool $isStructural
    ) {
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getAttributeIds(bool $includeSup = false): array
    {
        $supAttributes = [];
        if ($includeSup) {
            foreach ($this->getSuperClasses() as $superClass) {
                $supAttributes = ArrayHelper::merge($supAttributes, $superClass->getAttributeIds(true));
            }
        }

        return ArrayHelper::merge($this->mustAttributes, $this->mayAttributes, $supAttributes);
    }

    /**
     * @return AttributeType[]
     */
    public function getMayAttributes()
    {
        $arr = [];
        /** @var string $id */
        foreach ($this->mayAttributes as $id) {
            $attribute = $this->schema->getAttribute($id);
            if ($attribute !== null) {
                $arr[] = $attribute;
            }
        }
        return $arr;
    }

    /**
     * @return AttributeType[]
     */
    public
    function getMustAttributes()
    {
        $arr = [];
        /** @var string $id */
        foreach ($this->mustAttributes as $id) {
            $attribute = $this->schema->getAttribute($id);
            if ($attribute !== null) {
                $arr[] = $attribute;
            }
        }
        return $arr;
    }


    /**
     * @return ObjectClass[]
     */
    public function getSuperClasses(): array
    {
        $supers = [];
        foreach ($this->sups as $sup) {
            $supers[] = $this->schema->getObjectClass($sup);
        }

        return $supers;
    }

    /**
     * @param ObjectClass[] $supers
     * @return ObjectClass[]
     */
    public function getSuperClassesRecursive(array $supers = []): array
    {
        $newSupers = [];
        foreach ($this->getSuperClasses() as $superClass) {
            $newSupers[] = $superClass;
            $newSupers = array_merge($newSupers, $superClass->getSuperClassesRecursive($supers));
        }


        return array_unique(array_merge($newSupers, $supers));
    }


    public static function createByString(Schema $schema, string $string): ?ObjectClass
    {
        if (strlen($string) < 5) {
            return null;
        }

        $r = '@^\(\s(.*?)\sNAME\s\'(.*?)\'.*\)$@';
        preg_match($r, $string, $matches);

        if (empty($matches[1])) {
            return null;
        }

        preg_match('@DESC\s\'(.*?)\'\s@', $string, $desc);

        $sups = [];
        preg_match('@SUP\s(.*?)\s@', $string, $sup);
        if (isset($sup[1])) {
            if ($sup[1] === '(') {
                preg_match('@SUP\s\(\s(.*?)\s\)\s@', $string, $sup);
                $sups = explode(" $ ", $sup[1]);
            } else {
                $sups[] = $sup[1];
            }
        }

        preg_match('@MAY\s(.*?)\s@', $string, $may);

        $mustAttributes = [];
        preg_match('@MUST\s(.*?)\s@', $string, $must);
        if (isset($must[1])) {
            if ($must[1] === '(') {
                preg_match('@MUST\s\(\s(.*?)\s\)\s@', $string, $must);
                $mustAttributes = explode(" $ ", $must[1]);
                #$mustAttributes = array_map('strtolower', $mustAttributes);
            } else {
                #$mustAttributes[] = strtolower($must[1]);
                $mustAttributes[] = $must[1];
            }
        }

        $mayAttributes = [];
        preg_match('@MAY\s(.*?)\s@', $string, $may);
        if (isset($may[1])) {
            if ($may[1] === '(') {
                preg_match('@MAY\s\(\s(.*?)\s\)\s@', $string, $may);
                $mayAttributes = explode(" $ ", $may[1]);
                #$mayAttributes = array_map('strtolower', $mayAttributes);
            } else {
                #$mayAttributes[] = strtolower($may[1]);
                $mayAttributes[] = $may[1];
            }
        }

        $mustAttributes = array_unique(array_map(function ($v) use ($schema): string {
            return $schema->resolveAttributeIdByName($v);
        }, $mustAttributes));

        $mayAttributes = array_unique(array_map(function ($v) use ($schema): string {
            return $schema->resolveAttributeIdByName($v);
        }, $mayAttributes));

        return new ObjectClass(
            $schema,
            $string,
            $matches[1],
            $matches[2],
            $desc[1] ?? '',
            $sups,
            $mayAttributes,
            $mustAttributes,
            (str_contains($string, 'AUXILARY')),
            (str_contains($string, 'STRUCTURAL')),
        );
    }

}
/*
        L[0] => ( 2.5.6.0 NAME 'top' DESC 'top of the superclass chain' ABSTRACT MUST objectClass )
        [1] => ( 1.3.6.1.4.1.1466.101.120.111 NAME 'extensibleObject' DESC 'RFC4512: extensible object' SUP top AUXILIARY )
        [2] => ( 2.5.6.1 NAME 'alias' DESC 'RFC4512: an alias' SUP top STRUCTURAL MUST aliasedObjectName )
        [3] => ( 2.16.840.1.113730.3.2.6 NAME 'referral' DESC 'namedref: named subordinate referral' SUP top STRUCTURAL MUST ref )
        [4] => ( 1.3.6.1.4.1.4203.1.4.1 NAME ( 'OpenLDAProotDSE' 'LDAProotDSE' ) DESC 'OpenLDAP Root DSE object' SUP top STRUCTURAL MAY cn )
        [5] => ( 2.5.17.0 NAME 'subentry' DESC 'RFC3672: subentry' SUP top STRUCTURAL MUST ( cn $ subtreeSpecification ) )
        [6] => ( 2.5.20.1 NAME 'subschema' DESC 'RFC4512: controlling subschema (sub)entry' AUXILIARY MAY ( dITStructureRules $ nameForms $ dITContentRules $ objectClasses $ attributeTypes $ matchingRules $ matchingRuleUse ) )
        [7] => ( 1.3.6.1.4.1.1466.101.119.2 NAME 'dynamicObject' DESC 'RFC2589: Dynamic Object' SUP top AUXILIARY )
        [8] => ( 1.3.6.1.4.1.4203.1.12.2.4.0.0 NAME 'olcConfig' DESC 'OpenLDAP configuration object' SUP top ABSTRACT )
        [9] => ( 1.3.6.1.4.1.4203.1.12.2.4.0.1 NAME 'olcGlobal' DESC 'OpenLDAP Global configuration options' SUP olcConfig STRUCTURAL MAY ( cn $ olcConfigFile $ olcConfigDir $ olcAllows $ olcArgsFile $ olcAttributeOptions $ olcAuthIDRewrite $ olcAuthzPolicy $ olcAuthzRegexp $ olcConcurrency $ olcConnMaxPending $ olcConnMaxPendingAuth $ olcDisallows $ olcGentleHUP $ olcIdleTimeout $ olcIndexSubstrIfMaxLen $ olcIndexSubstrIfMinLen $ olcIndexSubstrAnyLen $ olcIndexSubstrAnyStep $ olcIndexIntLen $ olcListenerThreads $ olcLocalSSF $ olcLogFile $ olcLogLevel $ olcPasswordCryptSaltFormat $ olcPasswordHash $ olcPidFile $ olcPluginLogFile $ olcReadOnly $ olcReferral $ olcReplogFile $ olcRequires $ olcRestrict $ olcReverseLookup $ olcRootDSE $ olcSaslAuxprops $ olcSaslHost $ olcSaslRealm $ olcSaslSecProps $ olcSecurity $ olcServerID $ olcSizeLimit $ olcSockbufMaxIncoming $ olcSockbufMaxIncomingAuth $ olcTCPBuffer $ olcThreads $ olcTimeLimit $ olcTLSCACertificateFile $ olcTLSCACertificatePath $ olcTLSCertificateFile $ olcTLSCertificateKeyFile $ olcTLSCipherSuite $ olcTLSCRLCheck $ olcTLSRandFile $ olcTLSVerifyClient $ olcTLSDHParamFile $ olcTLSCRLFile $ olcTLSProtocolMin $ olcToolThreads $ olcWriteTimeout $ olcObjectIdentifier $ olcAttributeTypes $ olcObjectClasses $ olcDitContentRules $ olcLdapSyntaxes ) )
        [10] => ( 1.3.6.1.4.1.4203.1.12.2.4.0.2 NAME 'olcSchemaConfig' DESC 'OpenLDAP schema object' SUP olcConfig STRUCTURAL MAY ( cn $ olcObjectIdentifier $ olcLdapSyntaxes $ olcAttributeTypes $ olcObjectClasses $ olcDitContentRules ) )
        [11] => ( 1.3.6.1.4.1.4203.1.12.2.4.0.3 NAME 'olcBackendConfig' DESC 'OpenLDAP Backend-specific options' SUP olcConfig STRUCTURAL MUST olcBackend )
        [12] => ( 1.3.6.1.4.1.4203.1.12.2.4.0.4 NAME 'olcDatabaseConfig' DESC 'OpenLDAP Database-specific options' SUP olcConfig STRUCTURAL MUST olcDatabase MAY ( olcHidden $ olcSuffix $ olcSubordinate $ olcAccess $ olcAddContentAcl $ olcLastMod $ olcLimits $ olcMaxDerefDepth $ olcPlugin $ olcReadOnly $ olcReplica $ olcReplicaArgsFile $ olcReplicaPidFile $ olcReplicationInterval $ olcReplogFile $ olcRequires $ olcRestrict $ olcRootDN $ olcRootPW $ olcSchemaDN $ olcSecurity $ olcSizeLimit $ olcSyncUseSubentry $ olcSyncrepl $ olcTimeLimit $ olcUpdateDN $ olcUpdateRef $ olcMirrorMode $ olcMonitoring $ olcExtraAttrs ) )
        [13] => ( 1.3.6.1.4.1.4203.1.12.2.4.0.5 NAME 'olcOverlayConfig' DESC 'OpenLDAP Overlay-specific options' SUP olcConfig STRUCTURAL MUST olcOverlay )
        [14] => ( 1.3.6.1.4.1.4203.1.12.2.4.0.6 NAME 'olcIncludeFile' DESC 'OpenLDAP configuration include file' SUP olcConfig STRUCTURAL MUST olcInclude MAY ( cn $ olcRootDSE ) )
        [15] => ( 1.3.6.1.4.1.4203.1.12.2.4.0.7 NAME 'olcFrontendConfig' DESC 'OpenLDAP frontend configuration' AUXILIARY MAY ( olcDefaultSearchBase $ olcPasswordHash $ olcSortVals ) )
        [16] => ( 1.3.6.1.4.1.4203.1.12.2.4.0.8 NAME 'olcModuleList' DESC 'OpenLDAP dynamic module info' SUP olcConfig STRUCTURAL MAY ( cn $ olcModulePath $ olcModuleLoad ) )
        [17] => ( 1.3.6.1.4.1.4203.1.12.2.4.2.2.1 NAME 'olcLdifConfig' DESC 'LDIF backend configuration' SUP olcDatabaseConfig STRUCTURAL MUST olcDbDirectory )
        [18] => ( 1.3.6.1.4.1.4203.1.12.2.4.2.12.1 NAME 'olcMdbConfig' DESC 'MDB backend configuration' SUP olcDatabaseConfig STRUCTURAL MUST olcDbDirectory MAY ( olcDbCheckpoint $ olcDbEnvFlags $ olcDbNoSync $ olcDbIndex $ olcDbMaxReaders $ olcDbMaxSize $ olcDbMode $ olcDbSearchStack $ olcDbRtxnSize ) )
        [19] => ( 2.5.6.2 NAME 'country' DESC 'RFC2256: a country' SUP top STRUCTURAL MUST c MAY ( searchGuide $ description ) )
        [20] => ( 2.5.6.3 NAME 'locality' DESC 'RFC2256: a locality' SUP top STRUCTURAL MAY ( street $ seeAlso $ searchGuide $ st $ l $ description ) )
        [21] => ( 2.5.6.4 NAME 'organization' DESC 'RFC2256: an organization' SUP top STRUCTURAL MUST o MAY ( userPassword $ searchGuide $ seeAlso $ businessCategory $ x121Address $ registeredAddress $ destinationIndicator $ preferredDeliveryMethod $ telexNumber $ teletexTerminalIdentifier $ telephoneNumber $ internationaliSDNNumber $ facsimileTelephoneNumber $ street $ postOfficeBox $ postalCode $ postalAddress $ physicalDeliveryOfficeName $ st $ l $ description ) )
        [22] => ( 2.5.6.5 NAME 'organizationalUnit' DESC 'RFC2256: an organizational unit' SUP top STRUCTURAL MUST ou MAY ( userPassword $ searchGuide $ seeAlso $ businessCategory $ x121Address $ registeredAddress $ destinationIndicator $ preferredDeliveryMethod $ telexNumber $ teletexTerminalIdentifier $ telephoneNumber $ internationaliSDNNumber $ facsimileTelephoneNumber $ street $ postOfficeBox $ postalCode $ postalAddress $ physicalDeliveryOfficeName $ st $ l $ description ) )
        [23] => ( 2.5.6.6 NAME 'person' DESC 'RFC2256: a person' SUP top STRUCTURAL MUST ( sn $ cn ) MAY ( userPassword $ telephoneNumber $ seeAlso $ description ) )
        [24] => ( 2.5.6.7 NAME 'organizationalPerson' DESC 'RFC2256: an organizational person' SUP person STRUCTURAL MAY ( title $ x121Address $ registeredAddress $ destinationIndicator $ preferredDeliveryMethod $ telexNumber $ teletexTerminalIdentifier $ telephoneNumber $ internationaliSDNNumber $ facsimileTelephoneNumber $ street $ postOfficeBox $ postalCode $ postalAddress $ physicalDeliveryOfficeName $ ou $ st $ l ) )
        [25] => ( 2.5.6.8 NAME 'organizationalRole' DESC 'RFC2256: an organizational role' SUP top STRUCTURAL MUST cn MAY ( x121Address $ registeredAddress $ destinationIndicator $ preferredDeliveryMethod $ telexNumber $ teletexTerminalIdentifier $ telephoneNumber $ internationaliSDNNumber $ facsimileTelephoneNumber $ seeAlso $ roleOccupant $ preferredDeliveryMethod $ street $ postOfficeBox $ postalCode $ postalAddress $ physicalDeliveryOfficeName $ ou $ st $ l $ description ) )
        [26] => ( 2.5.6.9 NAME 'groupOfNames' DESC 'RFC2256: a group of names (DNs)' SUP top STRUCTURAL MUST ( member $ cn ) MAY ( businessCategory $ seeAlso $ owner $ ou $ o $ description ) )
        [27] => ( 2.5.6.10 NAME 'residentialPerson' DESC 'RFC2256: an residential person' SUP person STRUCTURAL MUST l MAY ( businessCategory $ x121Address $ registeredAddress $ destinationIndicator $ preferredDeliveryMethod $ telexNumber $ teletexTerminalIdentifier $ telephoneNumber $ internationaliSDNNumber $ facsimileTelephoneNumber $ preferredDeliveryMethod $ street $ postOfficeBox $ postalCode $ postalAddress $ physicalDeliveryOfficeName $ st $ l ) )
        [28] => ( 2.5.6.11 NAME 'applicationProcess' DESC 'RFC2256: an application process' SUP top STRUCTURAL MUST cn MAY ( seeAlso $ ou $ l $ description ) )
        [29] => ( 2.5.6.12 NAME 'applicationEntity' DESC 'RFC2256: an application entity' SUP top STRUCTURAL MUST ( presentationAddress $ cn ) MAY ( supportedApplicationContext $ seeAlso $ ou $ o $ l $ description ) )
        [30] => ( 2.5.6.13 NAME 'dSA' DESC 'RFC2256: a directory system agent (a server)' SUP applicationEntity STRUCTURAL MAY knowledgeInformation )
        [31] => ( 2.5.6.14 NAME 'device' DESC 'RFC2256: a device' SUP top STRUCTURAL MUST cn MAY ( serialNumber $ seeAlso $ owner $ ou $ o $ l $ description ) )
        [32] => ( 2.5.6.15 NAME 'strongAuthenticationUser' DESC 'RFC2256: a strong authentication user' SUP top AUXILIARY MUST userCertificate )
        [33] => ( 2.5.6.16 NAME 'certificationAuthority' DESC 'RFC2256: a certificate authority' SUP top AUXILIARY MUST ( authorityRevocationList $ certificateRevocationList $ cACertificate ) MAY crossCertificatePair )
        [34] => ( 2.5.6.17 NAME 'groupOfUniqueNames' DESC 'RFC2256: a group of unique names (DN and Unique Identifier)' SUP top STRUCTURAL MUST ( uniqueMember $ cn ) MAY ( businessCategory $ seeAlso $ owner $ ou $ o $ description ) )
        [35] => ( 2.5.6.18 NAME 'userSecurityInformation' DESC 'RFC2256: a user security information' SUP top AUXILIARY MAY supportedAlgorithms )
        [36] => ( 2.5.6.16.2 NAME 'certificationAuthority-V2' SUP certificationAuthority AUXILIARY MAY deltaRevocationList )
        [37] => ( 2.5.6.19 NAME 'cRLDistributionPoint' SUP top STRUCTURAL MUST cn MAY ( certificateRevocationList $ authorityRevocationList $ deltaRevocationList ) )
        [38] => ( 2.5.6.20 NAME 'dmd' SUP top STRUCTURAL MUST dmdName MAY ( userPassword $ searchGuide $ seeAlso $ businessCategory $ x121Address $ registeredAddress $ destinationIndicator $ preferredDeliveryMethod $ telexNumber $ teletexTerminalIdentifier $ telephoneNumber $ internationaliSDNNumber $ facsimileTelephoneNumber $ street $ postOfficeBox $ postalCode $ postalAddress $ physicalDeliveryOfficeName $ st $ l $ description ) )
        [39] => ( 2.5.6.21 NAME 'pkiUser' DESC 'RFC2587: a PKI user' SUP top AUXILIARY MAY userCertificate )
        [40] => ( 2.5.6.22 NAME 'pkiCA' DESC 'RFC2587: PKI certificate authority' SUP top AUXILIARY MAY ( authorityRevocationList $ certificateRevocationList $ cACertificate $ crossCertificatePair ) )
        [41] => ( 2.5.6.23 NAME 'deltaCRL' DESC 'RFC2587: PKI user' SUP top AUXILIARY MAY deltaRevocationList )
        [42] => ( 1.3.6.1.4.1.250.3.15 NAME 'labeledURIObject' DESC 'RFC2079: object that contains the URI attribute type' SUP top AUXILIARY MAY labeledURI )
        [43] => ( 0.9.2342.19200300.100.4.19 NAME 'simpleSecurityObject' DESC 'RFC1274: simple security object' SUP top AUXILIARY MUST userPassword )
        [44] => ( 1.3.6.1.4.1.1466.344 NAME 'dcObject' DESC 'RFC2247: domain component object' SUP top AUXILIARY MUST dc )
        [45] => ( 1.3.6.1.1.3.1 NAME 'uidObject' DESC 'RFC2377: uid object' SUP top AUXILIARY MUST uid )
        [46] => ( 0.9.2342.19200300.100.4.4 NAME ( 'pilotPerson' 'newPilotPerson' ) SUP person STRUCTURAL MAY ( userid $ textEncodedORAddress $ rfc822Mailbox $ favouriteDrink $ roomNumber $ userClass $ homeTelephoneNumber $ homePostalAddress $ secretary $ personalTitle $ preferredDeliveryMethod $ businessCategory $ janetMailbox $ otherMailbox $ mobileTelephoneNumber $ pagerTelephoneNumber $ organizationalStatus $ mailPreferenceOption $ personalSignature ) )
        [47] => ( 0.9.2342.19200300.100.4.5 NAME 'account' SUP top STRUCTURAL MUST userid MAY ( description $ seeAlso $ localityName $ organizationName $ organizationalUnitName $ host ) )
        [48] => ( 0.9.2342.19200300.100.4.6 NAME 'document' SUP top STRUCTURAL MUST documentIdentifier MAY ( commonName $ description $ seeAlso $ localityName $ organizationName $ organizationalUnitName $ documentTitle $ documentVersion $ documentAuthor $ documentLocation $ documentPublisher ) )
        [49] => ( 0.9.2342.19200300.100.4.7 NAME 'room' SUP top STRUCTURAL MUST commonName MAY ( roomNumber $ description $ seeAlso $ telephoneNumber ) )
        [50] => ( 0.9.2342.19200300.100.4.9 NAME 'documentSeries' SUP top STRUCTURAL MUST commonName MAY ( description $ seeAlso $ telephonenumber $ localityName $ organizationName $ organizationalUnitName ) )
        [51] => ( 0.9.2342.19200300.100.4.13 NAME 'domain' SUP top STRUCTURAL MUST domainComponent MAY ( associatedName $ organizationName $ description $ businessCategory $ seeAlso $ searchGuide $ userPassword $ localityName $ stateOrProvinceName $ streetAddress $ physicalDeliveryOfficeName $ postalAddress $ postalCode $ postOfficeBox $ streetAddress $ facsimileTelephoneNumber $ internationalISDNNumber $ telephoneNumber $ teletexTerminalIdentifier $ telexNumber $ preferredDeliveryMethod $ destinationIndicator $ registeredAddress $ x121Address ) )
        [52] => ( 0.9.2342.19200300.100.4.14 NAME 'RFC822localPart' SUP domain STRUCTURAL MAY ( commonName $ surname $ description $ seeAlso $ telephoneNumber $ physicalDeliveryOfficeName $ postalAddress $ postalCode $ postOfficeBox $ streetAddress $ facsimileTelephoneNumber $ internationalISDNNumber $ telephoneNumber $ teletexTerminalIdentifier $ telexNumber $ preferredDeliveryMethod $ destinationIndicator $ registeredAddress $ x121Address ) )
        [53] => ( 0.9.2342.19200300.100.4.15 NAME 'dNSDomain' SUP domain STRUCTURAL MAY ( ARecord $ MDRecord $ MXRecord $ NSRecord $ SOARecord $ CNAMERecord ) )
        [54] => ( 0.9.2342.19200300.100.4.17 NAME 'domainRelatedObject' DESC 'RFC1274: an object related to an domain' SUP top AUXILIARY MUST associatedDomain )
        [55] => ( 0.9.2342.19200300.100.4.18 NAME 'friendlyCountry' SUP country STRUCTURAL MUST friendlyCountryName )
        [56] => ( 0.9.2342.19200300.100.4.20 NAME 'pilotOrganization' SUP ( organization $ organizationalUnit ) STRUCTURAL MAY buildingName )
        [57] => ( 0.9.2342.19200300.100.4.21 NAME 'pilotDSA' SUP dsa STRUCTURAL MAY dSAQuality )
        [58] => ( 0.9.2342.19200300.100.4.22 NAME 'qualityLabelledData' SUP top AUXILIARY MUST dsaQuality MAY ( subtreeMinimumQuality $ subtreeMaximumQuality ) )
        [59] => ( 1.3.6.1.1.1.2.0 NAME 'posixAccount' DESC 'Abstraction of an account with POSIX attributes' SUP top AUXILIARY MUST ( cn $ uid $ uidNumber $ gidNumber $ homeDirectory ) MAY ( userPassword $ loginShell $ gecos $ description ) )
        [60] => ( 1.3.6.1.1.1.2.1 NAME 'shadowAccount' DESC 'Additional attributes for shadow passwords' SUP top AUXILIARY MUST uid MAY ( userPassword $ shadowLastChange $ shadowMin $ shadowMax $ shadowWarning $ shadowInactive $ shadowExpire $ shadowFlag $ description ) )
        [61] => ( 1.3.6.1.1.1.2.2 NAME 'posixGroup' DESC 'Abstraction of a group of accounts' SUP top STRUCTURAL MUST ( cn $ gidNumber ) MAY ( userPassword $ memberUid $ description ) )
        [62] => ( 1.3.6.1.1.1.2.3 NAME 'ipService' DESC 'Abstraction an Internet Protocol service' SUP top STRUCTURAL MUST ( cn $ ipServicePort $ ipServiceProtocol ) MAY description )
        [63] => ( 1.3.6.1.1.1.2.4 NAME 'ipProtocol' DESC 'Abstraction of an IP protocol' SUP top STRUCTURAL MUST ( cn $ ipProtocolNumber $ description ) MAY description )
        [64] => ( 1.3.6.1.1.1.2.5 NAME 'oncRpc' DESC 'Abstraction of an ONC/RPC binding' SUP top STRUCTURAL MUST ( cn $ oncRpcNumber $ description ) MAY description )
        [65] => ( 1.3.6.1.1.1.2.6 NAME 'ipHost' DESC 'Abstraction of a host, an IP device' SUP top AUXILIARY MUST ( cn $ ipHostNumber ) MAY ( l $ description $ manager ) )
        [66] => ( 1.3.6.1.1.1.2.7 NAME 'ipNetwork' DESC 'Abstraction of an IP network' SUP top STRUCTURAL MUST ( cn $ ipNetworkNumber ) MAY ( ipNetmaskNumber $ l $ description $ manager ) )
        [67] => ( 1.3.6.1.1.1.2.8 NAME 'nisNetgroup' DESC 'Abstraction of a netgroup' SUP top STRUCTURAL MUST cn MAY ( nisNetgroupTriple $ memberNisNetgroup $ description ) )
        [68] => ( 1.3.6.1.1.1.2.9 NAME 'nisMap' DESC 'A generic abstraction of a NIS map' SUP top STRUCTURAL MUST nisMapName MAY description )
        [69] => ( 1.3.6.1.1.1.2.10 NAME 'nisObject' DESC 'An entry in a NIS map' SUP top STRUCTURAL MUST ( cn $ nisMapEntry $ nisMapName ) MAY description )
        [70] => ( 1.3.6.1.1.1.2.11 NAME 'ieee802Device' DESC 'A device with a MAC address' SUP top AUXILIARY MAY macAddress )
        [71] => ( 1.3.6.1.1.1.2.12 NAME 'bootableDevice' DESC 'A device with boot parameters' SUP top AUXILIARY MAY ( bootFile $ bootParameter ) )
        [72] => ( 2.16.840.1.113730.3.2.2 NAME 'inetOrgPerson' DESC 'RFC2798: Internet Organizational Person' SUP organizationalPerson STRUCTURAL MAY ( audio $ businessCategory $ carLicense $ departmentNumber $ displayName $ employeeNumber $ employeeType $ givenName $ homePhone $ homePostalAddress $ initials $ jpegPhoto $ labeledURI $ mail $ manager $ mobile $ o $ pager $ photo $ roomNumber $ secretary $ uid $ userCertificate $ x500uniqueIdentifier $ preferredLanguage $ userSMIMECertificate $ userPKCS12 ) )
        [73] => ( 1.3.6.1.4.1.47732.1.1.0.0 NAME 'kopano-user' DESC 'KOPANO: an user of Kopano' SUP top AUXILIARY MUST cn MAY ( kopanoQuotaOverride $ kopanoQuotaWarn $ kopanoQuotaSoft $ kopanoSendAsPrivilege $ kopanoQuotaHard $ kopanoAdmin $ kopanoSharedStoreOnly $ kopanoResourceType $ kopanoResourceCapacity $ kopanoAccount $ kopanoHidden $ kopanoAliases $ kopanoUserServer $ kopanoEnabledFeatures $ kopanoDisabledFeatures $ kopanoUserArchiveServers $ kopanoUserArchiveCouplings $ uidNumber ) )
        [74] => ( 1.3.6.1.4.1.47732.1.6.0.0 NAME 'kopano-contact' DESC 'KOPANO: a contact of Kopano' SUP top AUXILIARY MUST ( cn $ uidNumber ) MAY ( kopanoSendAsPrivilege $ kopanoHidden $ kopanoAliases $ kopanoAccount ) )
        [75] => ( 1.3.6.1.4.1.47732.1.2.0.0 NAME 'kopano-group' DESC 'KOPANO: a group of Kopano' SUP top AUXILIARY MUST cn MAY ( kopanoAccount $ kopanoHidden $ mail $ kopanoAliases $ kopanoSecurityGroup $ kopanoSendAsPrivilege $ gidNumber ) )
        [76] => ( 1.3.6.1.4.1.47732.1.3.0.0 NAME 'kopano-company' DESC 'KOPANO: a company of Kopano' SUP top AUXILIARY MUST ou MAY ( kopanoAccount $ kopanoHidden $ kopanoViewPrivilege $ kopanoAdminPrivilege $ kopanoSystemAdmin $ kopanoQuotaOverride $ kopanoQuotaWarn $ kopanoUserDefaultQuotaOverride $ kopanoUserDefaultQuotaWarn $ kopanoUserDefaultQuotaSoft $ kopanoUserDefaultQuotaHard $ kopanoQuotaUserWarningRecipients $ kopanoQuotaCompanyWarningRecipients $ kopanoCompanyServer ) )
        [77] => ( 1.3.6.1.4.1.47732.1.4.0.0 NAME 'kopano-server' DESC 'KOPANO: a Kopano server' SUP top AUXILIARY MUST cn MAY ( kopanoAccount $ kopanoHidden $ kopanoHttpPort $ kopanoSslPort $ kopanoFilePath $ kopanoContainsPublic $ kopanoProxyURL ) )
        [78] => ( 1.3.6.1.4.1.47732.1.5.0.0 NAME 'kopano-addresslist' DESC 'KOPANO: a Kopano Addresslist' SUP top STRUCTURAL MUST cn MAY ( kopanoAccount $ kopanoHidden $ kopanoFilter $ kopanoBase ) )
        [79] => ( 1.3.6.1.4.1.47732.1.7.0.0 NAME 'kopano-dynamicgroup' DESC 'KOPANO: a Kopano dynamic group' SUP top STRUCTURAL MUST cn MAY ( kopanoAccount $ kopanoHidden $ mail $ kopanoAliases $ kopanoFilter $ kopanoBase ) )
*/
