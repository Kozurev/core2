<?php
/**
 * Created by PhpStorm.
 * User: Kozurev Egor
 * Date: 21.05.2018
 * Time: 10:01
 */

$aoCertificates = Core::factory("Certificate")
    ->orderBy("sell_date", "DESC")
    ->findAll();

foreach ($aoCertificates as $cert)
{
    $cert->sellDate(refactorDateFormat($cert->sellDate()));
    $cert->activeTo(refactorDateFormat($cert->activeTo()));
}

Core::factory("Core_Entity")
    ->addEntities($aoCertificates)
    ->xsl("musadm/certificates/certificates.xsl")
    ->show();