<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.6
 * Time: 20.39
 */

namespace Tests;


use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;
use PHPUnit\Framework\TestCase;
use PlumTreeSystems\SecureTrading\Api;
use PlumTreeSystems\SecureTrading\Factory\SecureTradingApiFactory;
use Securetrading\Data\Data;

class ApiTest extends TestCase
{
    public function testConstructorFailsWithoutSomeParameters()
    {
        $this->expectException(LogicException::class);

        $options = [
            'username' => ''
        ];

        $factory = new SecureTradingApiFactory($options);
        $innerApi = $factory->createApi();

        new Api($options, $innerApi);
    }

    public function testCorrectConstructor()
    {
        $this->expectNotToPerformAssertions();

        $options = [
            'username' => 'test',
            'password' => 'test',
            'site_reference' => 'test'
        ];

        $factory = new SecureTradingApiFactory($options);
        $innerApi = $factory->createApi();

        new Api($options, $innerApi);
    }

    public function testApiReturnsSiteReference()
    {
        $options = [
            'username' => 'test',
            'password' => 'test',
            'site_reference' => 'test_siteReference'
        ];

        $factory = new SecureTradingApiFactory($options);
        $innerApi = $factory->createApi();

        $api = new Api($options, $innerApi);

        $siteRef = $api->getSiteReference();

        $this->assertSame($options['site_reference'], $siteRef);
    }

    public function testApiSimpleRequestCallsApiFunction()
    {
        $testValue = '123';
        $mock = $this->createMock(\Securetrading\Stpp\JsonInterface\Api::class);
        $mock
            ->expects($this->once())
            ->method('process')
            ->with([
                'accounttypedescription' => 'ECOM',
                'requesttypedescriptions' => ['AUTH'],
                'sitereference' => 'test_boop',
                'testField' => $testValue
            ])
            ->willReturn(new Data());

        $api = new Api(['site_reference' => 'test_boop'], $mock);
        $api->simpleChargeRequest(['testField' => $testValue]);
    }
}