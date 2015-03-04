<?php namespace Panlogic\DONATE\Exceptions;
/**
* DONATE helper package by Panlogic Ltd.
*
* NOTICE OF LICENSE
*
* Licensed under the terms from Panlogic Ltd.
*
* @package DONATE
* @version 1.0.0
* @author Panlogic Ltd
* @license MIT
* @copyright (c) 2015, Panlogic Ltd
* @link http://www.panlogic.co.uk
*/

use Exception;

class DonateException extends Exception {
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'DONATE configuration must be published. Use: "php artisan vendor:publish".';
}