<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.10
 * Time: 15.11
 */

namespace Tests\Factory;


use Payum\Core\Exception\LogicException;
use PHPUnit\Framework\TestCase;
use PlumTreeSystems\SecureTrading\Factory\SecureTradingApiFactory;

class SecureTradingApiFactoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldFailIfConstructorDoesNotContainFields()
    {
        $this->expectException(LogicException::class);

        new SecureTradingApiFactory([]);
    }

    /**
     * @test
     */
    public function shouldNotFailIfConstructorDoesContainFields()
    {
        $this->expectNotToPerformAssertions();

        new SecureTradingApiFactory(['username' => 'asd', 'password' => 'asd']);
    }

    public function shouldCreateApi()
    {
        $this->expectNotToPerformAssertions();

        $factory = new SecureTradingApiFactory(['username' => 'asd', 'password' => 'asd']);

        $factory->createApi();
    }
}