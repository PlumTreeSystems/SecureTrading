<?php
namespace PlumTreeSystems\SecureTrading;

use Action\Api\AccountCheckAction;
use PlumTreeSystems\SecureTrading\Action\Api\ObtainTokenAction;
use PlumTreeSystems\SecureTrading\Action\AuthorizeAction;
use PlumTreeSystems\SecureTrading\Action\CancelAction;
use PlumTreeSystems\SecureTrading\Action\ConvertPaymentAction;
use PlumTreeSystems\SecureTrading\Action\CaptureAction;
use PlumTreeSystems\SecureTrading\Action\NotifyAction;
use PlumTreeSystems\SecureTrading\Action\RefundAction;
use PlumTreeSystems\SecureTrading\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;
use PlumTreeSystems\SecureTrading\Factory\SecureTradingApiFactory;

class SecureTradingGatewayFactory extends GatewayFactory
{
    /**
     * {@inheritDoc}
     */
    protected function populateConfig(ArrayObject $config)
    {
        $config->defaults([
            'payum.factory_name' => 'secure_trading',
            'payum.factory_title' => 'secure_trading',

            'payum.template.obtain_token' => '@PlumTreeSystemsSecureTrading/Action/obtain_token.html.twig',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
            'payum.action.account_check' => new AccountCheckAction(),

            'payum.action.obtain_token' => function (ArrayObject $config) {
                return new ObtainTokenAction($config['payum.template.obtain_token']);
            }
        ]);

        $rootPath = __DIR__ . DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR . '..' .
            DIRECTORY_SEPARATOR;

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = [
                'site_reference' => '',
                'locale' => 'en_gb',
                'username' => '',
                'password' => '',
                'log_filepath' =>
                    $rootPath . 'var' .
                    DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR,
                'log_archive_filepath' =>
                    $rootPath . 'var' .
                    DIRECTORY_SEPARATOR . 'archive' . DIRECTORY_SEPARATOR
            ];
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [
                'site_reference', 'locale', 'username', 'password'
            ];

            $config['payum.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['payum.required_options']);
                $innerApi = $this->createApi($config);

                return new Api(
                    (array) $config,
                    $innerApi
                );
            };
        }

        $config['payum.paths'] = array_replace([
            'PlumTreeSystemsSecureTrading' => __DIR__.'/Resources/views',
        ], $config['payum.paths'] ?: []);
    }

    protected function createApi($config) {
        $factory = new SecureTradingApiFactory((array) $config);
        $innerApi = $factory->createApi();
        return $innerApi;
    }
}
