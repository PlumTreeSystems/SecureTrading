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
            'plumtreesystems.factory_name' => 'secure_trading',
            'plumtreesystems.factory_title' => 'secure_trading',

            'plumtreesystems.template.obtain_token' => '@PlumTreeSystems/Action/obtain_token.html.twig',

            'plumtreesystems.action.capture' => new CaptureAction(),
            'plumtreesystems.action.authorize' => new AuthorizeAction(),
            'plumtreesystems.action.refund' => new RefundAction(),
            'plumtreesystems.action.cancel' => new CancelAction(),
            'plumtreesystems.action.notify' => new NotifyAction(),
            'plumtreesystems.action.status' => new StatusAction(),
            'plumtreesystems.action.convert_payment' => new ConvertPaymentAction(),
        ]);

        if (false == $config['plumtreesystems.api']) {
            $config['plumtreesystems.default_options'] = array(
                'sandbox' => true,
            );
            $config->defaults($config['plumtreesystems.default_options']);
            $config['plumtreesystems.required_options'] = [];

            $config['plumtreesystems.api'] = function (ArrayObject $config) {
                $config->validateNotEmpty($config['plumtreesystems.required_options']);

                return new Api((array) $config, $config['plumtreesystems.http_client'], $config['httplug.message_factory']);
            };
        }
    }
}
