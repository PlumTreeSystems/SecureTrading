<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.19
 * Time: 15.56
 */

namespace PlumTreeSystems\SecureTrading\Model;


interface ApiConnectorInterface
{
    public function process(array $fields): array;

    public function getScriptImportUrl(): string;

    public function getScript($siteRef, $locale): string;
}