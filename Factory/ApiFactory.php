<?php
/**
 * Created by PhpStorm.
 * User: marius
 * Date: 18.12.7
 * Time: 13.35
 */

namespace PlumTreeSystems\SecureTrading\Factory;


use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\LogicException;

abstract class ApiFactory
{
    /**
     * @var array
     */
    protected $options = [
        'username' => null,
        'password' => null
    ];

    public function __construct($options)
    {
        $options = ArrayObject::ensureArrayObject($options);
        $options->defaults($this->options);
        $options->validateNotEmpty([
            'username',
            'password',
        ]);

        $this->options = $options;
    }

    abstract public function createApi();
}