<?php

namespace Balemy\LdapCommander\Schema;

class Syntax
{

    public function __construct(
        public string $oid,
        public string $description,
        public bool   $isBinaryTransferRequired = false,
        public bool   $isNotHumanReadable = false
    )
    {

    }

    public static function createByString(string $string): ?Syntax
    {
        if (strlen($string) < 5) {
            return null;
        }

        $r = '@^\(\s(.*?)\sDESC\s\'(.*?)\'.*\)$@';
        preg_match($r, $string, $matches);

        if (empty($matches[1])) {
            return null;
        }

        return new Syntax(
            $matches[1],
            $matches[2],
            (str_contains($string, "X-NOT-HUMAN-READABLE 'TRUE'")),
            (str_contains($string, "X-BINARY-TRANSFER-REQUIRED 'TRUE'"))
        );
    }
}

/*
 *         (
        [count] => 32
        [0] => ( 1.3.6.1.4.1.1466.115.121.1.4 DESC 'Audio' X-NOT-HUMAN-READABLE 'TRUE' )
        [1] => ( 1.3.6.1.4.1.1466.115.121.1.5 DESC 'Binary' X-NOT-HUMAN-READABLE 'TRUE' )
        [2] => ( 1.3.6.1.4.1.1466.115.121.1.6 DESC 'Bit String' )
        [3] => ( 1.3.6.1.4.1.1466.115.121.1.7 DESC 'Boolean' )
        [4] => ( 1.3.6.1.4.1.1466.115.121.1.8 DESC 'Certificate' X-BINARY-TRANSFER-REQUIRED 'TRUE' X-NOT-HUMAN-READABLE 'TRUE' )
        [5] => ( 1.3.6.1.4.1.1466.115.121.1.9 DESC 'Certificate List' X-BINARY-TRANSFER-REQUIRED 'TRUE' X-NOT-HUMAN-READABLE 'TRUE' )
        [6] => ( 1.3.6.1.4.1.1466.115.121.1.10 DESC 'Certificate Pair' X-BINARY-TRANSFER-REQUIRED 'TRUE' X-NOT-HUMAN-READABLE 'TRUE' )
        [7] => ( 1.3.6.1.4.1.4203.666.11.10.2.1 DESC 'X.509 AttributeCertificate' X-BINARY-TRANSFER-REQUIRED 'TRUE' X-NOT-HUMAN-READABLE 'TRUE' )
        [8] => ( 1.3.6.1.4.1.1466.115.121.1.12 DESC 'Distinguished Name' )
        [9] => ( 1.2.36.79672281.1.5.0 DESC 'RDN' )
        [10] => ( 1.3.6.1.4.1.1466.115.121.1.14 DESC 'Delivery Method' )
        [11] => ( 1.3.6.1.4.1.1466.115.121.1.15 DESC 'Directory String' )
        [12] => ( 1.3.6.1.4.1.1466.115.121.1.22 DESC 'Facsimile Telephone Number' )
        [13] => ( 1.3.6.1.4.1.1466.115.121.1.24 DESC 'Generalized Time' )
        [14] => ( 1.3.6.1.4.1.1466.115.121.1.26 DESC 'IA5 String' )
        [15] => ( 1.3.6.1.4.1.1466.115.121.1.27 DESC 'Integer' )
        [16] => ( 1.3.6.1.4.1.1466.115.121.1.28 DESC 'JPEG' X-NOT-HUMAN-READABLE 'TRUE' )
        [17] => ( 1.3.6.1.4.1.1466.115.121.1.34 DESC 'Name And Optional UID' )
        [18] => ( 1.3.6.1.4.1.1466.115.121.1.36 DESC 'Numeric String' )
        [19] => ( 1.3.6.1.4.1.1466.115.121.1.38 DESC 'OID' )
        [20] => ( 1.3.6.1.4.1.1466.115.121.1.39 DESC 'Other Mailbox' )
        [21] => ( 1.3.6.1.4.1.1466.115.121.1.40 DESC 'Octet String' )
        [22] => ( 1.3.6.1.4.1.1466.115.121.1.41 DESC 'Postal Address' )
        [23] => ( 1.3.6.1.4.1.1466.115.121.1.44 DESC 'Printable String' )
        [24] => ( 1.3.6.1.4.1.1466.115.121.1.11 DESC 'Country String' )
        [25] => ( 1.3.6.1.4.1.1466.115.121.1.45 DESC 'SubtreeSpecification' )
        [26] => ( 1.3.6.1.4.1.1466.115.121.1.49 DESC 'Supported Algorithm' X-BINARY-TRANSFER-REQUIRED 'TRUE' X-NOT-HUMAN-READABLE 'TRUE' )
        [27] => ( 1.3.6.1.4.1.1466.115.121.1.50 DESC 'Telephone Number' )
        [28] => ( 1.3.6.1.4.1.1466.115.121.1.52 DESC 'Telex Number' )
        [29] => ( 1.3.6.1.1.1.0.0 DESC 'RFC2307 NIS Netgroup Triple' )
        [30] => ( 1.3.6.1.1.1.0.1 DESC 'RFC2307 Boot Parameter' )
        [31] => ( 1.3.6.1.1.16.1 DESC 'UUID' )
    )
 */
