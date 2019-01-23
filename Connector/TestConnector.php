<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.20
 * Time: 15.38
 */

namespace PlumTreeSystems\SecureTrading\Connector;


use PlumTreeSystems\SecureTrading\Model\ApiConnectorInterface;

class TestConnector implements ApiConnectorInterface
{

    public function process(array $fields): array
    {
        if (isset($fields['requesttypedescriptions'])) {
            switch ($fields['requesttypedescriptions'][0]) {
                case 'ACCOUNTCHECK':
                    if ($fields['subscriptionnumber'] === "1" &&
                        $fields['subscriptiontype'] === "RECURRING" &&
                        $fields['credentialsonfile'] === "1"
                    ) {
                        return $this->preparePositiveResponse('ACCOUNTCHECK', [
                            'transactionreference' => 'boop1'
                        ]);
                    } else {
                        return $this->prepareNegativeResponse('ACCOUNTCHECK');
                    }
                case 'AUTH':
                    if (isset($fields['currencyiso3a']) &&
                        isset($fields['baseamount']) &&
                        $fields['baseamount'] !== "70000"
                    ) {
                        // initial Recurring
                        if (
                            $fields['subscriptionnumber'] === "1" &&
                            $fields['subscriptiontype'] === "RECURRING" &&
                            $fields['credentialsonfile'] === "1" &&
                            $fields['cachetoken'] === "booptoken"
                        ) {
                            return $this->preparePositiveResponse('AUTH', [
                                'transactionreference' => 'boop1',
                                'subscriptionnumber' => '1',
                                'credentialsonfile' => '1'
                            ]);
                        // consecutive Recurring
                        } else if (
                            (int)$fields['subscriptionnumber'] >= 1 &&
                            $fields['subscriptiontype'] === "RECURRING" &&
                            $fields['credentialsonfile'] === "2" &&
                            isset($fields['parenttransactionreference'])
                        ) {
                            return $this->preparePositiveResponse('AUTH', [
                                'transactionreference' => 'boop2',
                                'subscriptionnumber' => $fields['subscriptionnumber'],
                                'credentialsonfile' => '2'
                            ]);
                        // non recurring
                        } else {
                            return $this->preparePositiveResponse('AUTH');
                        }
                    }
            }
        }
        return $this->prepareNegativeResponse('UNKNOWN');
    }

    public function getScriptImportUrl(): string
    {
        return 'https://code.jquery.com/jquery-3.3.1.min.js';
    }

    public function getScript($siteRef, $locale): string
    {
        return "
            window.location.href = window.location.href + '?cachetoken=test'
        ";
    }

    private function preparePositiveResponse($requestType, $bonusStuff = []) {
        return [
            'responses' => [
                array_merge([
                    'errorcode' => '0',
                    'requesttypedescription' => $requestType
                ], $bonusStuff)
            ]
        ];
    }

    private function prepareNegativeResponse($requestType, $bonusStuff = []) {
        return [
            'responses' => [
                array_merge([
                    'errorcode' => '1',
                    'requesttypedescription' => $requestType
                ], $bonusStuff)
            ]
        ];
    }
}