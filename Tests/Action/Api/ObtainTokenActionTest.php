<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.7
 * Time: 16.08
 */

namespace Tests\Action\Api;


use Payum\Core\ApiAwareInterface;
use Payum\Core\Exception\LogicException;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Exception\UnsupportedApiException;
use Payum\Core\GatewayAwareInterface;
use Payum\Core\GatewayInterface;
use Payum\Core\Reply\HttpResponse;
use Payum\Core\Request\GetHttpRequest;
use Payum\Core\Request\RenderTemplate;
use Payum\Core\Security\SensitiveValue;
use PHPUnit\Framework\TestCase;
use PlumTreeSystems\SecureTrading\Action\Api\ObtainTokenAction;
use PlumTreeSystems\SecureTrading\Api;
use PlumTreeSystems\SecureTrading\Factory\SecureTradingApiFactory;
use PlumTreeSystems\SecureTrading\Request\Api\ObtainToken;

class ObtainTokenActionTest extends TestCase
{
    /**
     * @test
     */
    public function shouldImplementGatewayAwareInterface()
    {
        $rc = new \ReflectionClass(ObtainTokenAction::class);

        $this->assertTrue($rc->implementsInterface(GatewayAwareInterface::class));
    }

    /**
     * @test
     */
    public function shouldImplementsApiAwareInterface()
    {
        $rc = new \ReflectionClass(ObtainTokenAction::class);

        $this->assertTrue($rc->isSubclassOf(ApiAwareInterface::class));
    }

    /**
     * @test
     */
    public function couldBeConstructedWithTemplateAsFirstArgument()
    {
        $this->expectNotToPerformAssertions();

        new ObtainTokenAction('aTemplateName');
    }

    /**
     * @test
     */
    public function shouldAllowSetApiAsApi()
    {
        $this->expectNotToPerformAssertions();
        $action = new ObtainTokenAction('aTemplateName');

        $options = [
            'username' => 'test',
            'password' => 'test',
            'site_reference' => 'test_siteReference'
        ];

        $factory = new SecureTradingApiFactory($options);
        $innerApi = $factory->createApi();

        $api = new Api($options, $innerApi);

        $action->setApi($api);
    }

    /**
     * @test
     */
    public function throwIfNotApiInstanceSetAsApi()
    {
        $this->expectException(UnsupportedApiException::class);

        $action = new ObtainTokenAction('aTemplateName');

        $action->setApi('not keys instance');
    }

    /**
     * @test
     */
    public function shouldSupportObtainTokenRequestWithArrayAccessModel()
    {
        $action = new ObtainTokenAction('aTemplateName');

        $this->assertTrue($action->supports(new ObtainToken(array())));
    }

    /**
     * @test
     */
    public function shouldNotSupportObtainTokenRequestWithNotArrayAccessModel()
    {
        $action = new ObtainTokenAction('aTemplateName');

        $this->assertFalse($action->supports(new ObtainToken(new \stdClass())));
    }

    /**
     * @test
     */
    public function shouldNotSupportNotObtainTokenRequest()
    {
        $action = new ObtainTokenAction('aTemplateName');

        $this->assertFalse($action->supports(new \stdClass()));
    }

    /**
     * @test
     *
     */
    public function throwRequestNotSupportedIfNotSupportedGiven()
    {
        $this->expectException(RequestNotSupportedException::class);
        $action = new ObtainTokenAction('aTemplateName');

        $action->execute(new \stdClass());
    }

    /**
     * @test
     */
    public function throwIfModelAlreadyHaveTokenSet()
    {
        $this->expectException(LogicException::class);
        $action = new ObtainTokenAction('aTemplateName');

        $action->execute(new ObtainToken(array(
            'cachetoken' => 'aToken',
        )));
    }

    /**
     * @test
     */
    public function shouldRenderExpectedTemplate()
    {
        $model = new \ArrayObject();
        $templateName = 'theTemplateName';

        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->at(0))
            ->method('execute')
            ->with($this->isInstanceOf(GetHttpRequest::class))
            ->will($this->returnCallback(function (GetHttpRequest $request) {
                $request->method = 'GET';
            }))
        ;
        $gatewayMock
            ->expects($this->at(1))
            ->method('execute')
            ->with($this->isInstanceOf(RenderTemplate::class))
            ->will($this->returnCallback(function (RenderTemplate $request) use ($templateName, $model) {
                $this->assertEquals($templateName, $request->getTemplateName());

                $context = $request->getParameters();
                $this->assertArrayHasKey('model', $context);
                $this->assertArrayHasKey('site_reference', $context);
                $this->assertArrayHasKey('locale', $context);
                $this->assertArrayHasKey('actionUrl', $context);

                $request->setResult('theContent');
            }))
        ;

        $action = new ObtainTokenAction($templateName);
        $action->setGateway($gatewayMock);

        $options = [
            'username' => 'test',
            'password' => 'test',
            'site_reference' => 'test_siteReference'
        ];

        $factory = new SecureTradingApiFactory($options);
        $innerApi = $factory->createApi();

        $api = new Api($options, $innerApi);

        $action->setApi($api);

        try {
            $action->execute(new ObtainToken($model));
        } catch (HttpResponse $reply) {
            $this->assertEquals('theContent', $reply->getContent());

            return;
        }

        $this->fail('HttpResponse reply was expected to be thrown.');
    }

    /**
     * @test
     */
    public function shouldSetTokenIfHttpRequestContainsCacheToken()
    {
        $model = new \ArrayObject();
        $templateName = 'theTemplateName';

        $gatewayMock = $this->createGatewayMock();
        $gatewayMock
            ->expects($this->once())
            ->method('execute')
            ->with($this->isInstanceOf(GetHttpRequest::class))
            ->will($this->returnCallback(function (GetHttpRequest $request) {
                $request->method = 'GET';
                $request->query = ['cachetoken' => 'token'];
            }))
        ;

        $action = new ObtainTokenAction($templateName);
        $action->setGateway($gatewayMock);

        $options = [
            'username' => 'test',
            'password' => 'test',
            'site_reference' => 'test_siteReference'
        ];

        $factory = new SecureTradingApiFactory($options);
        $innerApi = $factory->createApi();

        $api = new Api($options, $innerApi);

        $action->setApi($api);

        $action->execute($obtainToken = new ObtainToken($model));

        $model = $obtainToken->getModel();
        $this->assertEquals(SensitiveValue::ensureSensitive('token'), $model['cachetoken']);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject|GatewayInterface
     */
    protected function createGatewayMock()
    {
        return $this->createMock(GatewayInterface::class);
    }
}