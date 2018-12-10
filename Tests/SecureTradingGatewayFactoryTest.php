<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.10
 * Time: 15.22
 */

namespace Tests;


use Payum\Core\GatewayFactoryInterface;
use PHPUnit\Framework\TestCase;
use PlumTreeSystems\SecureTrading\SecureTradingGatewayFactory;

class SecureTradingGatewayFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldImplementGatewayFactoryInterface()
    {
        $rc = new \ReflectionClass(SecureTradingGatewayFactory::class);

        $this->assertTrue($rc->implementsInterface(GatewayFactoryInterface::class));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithoutAnyArguments()
    {
        $this->expectNotToPerformAssertions();

        new SecureTradingGatewayFactory();
    }
}