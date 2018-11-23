<?php
namespace PlumTreeSystems\SecureTrading;

use PlumTreeSystems\SecureTrading\Action\AuthorizeAction;
use PlumTreeSystems\SecureTrading\Action\CancelAction;
use PlumTreeSystems\SecureTrading\Action\ConvertPaymentAction;
use PlumTreeSystems\SecureTrading\Action\CaptureAction;
use PlumTreeSystems\SecureTrading\Action\NotifyAction;
use PlumTreeSystems\SecureTrading\Action\RefundAction;
use PlumTreeSystems\SecureTrading\Action\StatusAction;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\GatewayFactory;

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

            'payum.template.obtain_token' => '@PlumTreeSystems/Action/obtain_token.html.twig',

            'payum.action.capture' => new CaptureAction(),
            'payum.action.authorize' => new AuthorizeAction(),
            'payum.action.refund' => new RefundAction(),
            'payum.action.cancel' => new CancelAction(),
            'payum.action.notify' => new NotifyAction(),
            'payum.action.status' => new StatusAction(),
            'payum.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (false == $config['payum.api']) {
            $config['payum.default_options'] = array(
                'sitereference' => 'test_site12345',
                'locale' => 'en_gb'
            );
            $config->defaults($config['payum.default_options']);
            $config['payum.required_options'] = [];

//            $config['plumtreesystems.api'] = function (ArrayObject $config) {
//                $config->validateNotEmpty($config['plumtreesystems.required_options']);
//
//                //return new Api((array) $config, $config['plumtreesystems.http_client'], $config['httplug.message_factory']);
//            };
        }
    }
}
