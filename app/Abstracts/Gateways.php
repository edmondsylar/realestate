<?php

namespace App\Abstracts;

abstract class Gateways
{
    protected $paymentObject;
    protected $params;
    protected $orderObject;

    public function __construct()
    {

    }

    abstract public function setParams($params);

    abstract public function setDefaultParams();

    abstract public function validation();

    abstract public function purchase();

    abstract public function completePurchase();

    public function setOrderObject($booking_id)
    {
        if (!$this->orderObject || ($this->orderObject && $this->orderObject->ID != $booking_id)) {
            $this->orderObject = get_booking($booking_id);
        }
    }

    protected function returnUrl()
    {
        return thankyou_url();
    }

    public function successUrl()
    {
        $args = [
            'payment' => $this->orderObject->payment_type,
            'orderID' => $this->orderObject->ID,
            'orderEncrypt' => hh_encrypt($this->orderObject->ID),
            'token_code' => $this->orderObject->token_code,
            'status' => 1
        ];
        $args['hash'] = $this->createHash($args);

        return add_query_arg($args, $this->returnUrl());
    }

    public function cancelUrl()
    {
        $args = [
            'payment' => $this->orderObject->payment_type,
            'orderID' => $this->orderObject->ID,
            'orderEncrypt' => hh_encrypt($this->orderObject->ID),
            'token_code' => $this->orderObject->token_code,
            'status' => 0,
        ];
        $args['hash'] = $this->createHash($args);

        return add_query_arg($args, $this->returnUrl());
    }

    public function createHash($data)
    {
        return md5($data['payment'] . '|' . $data['orderID'] . '|' . $data['status'] . '|' . $data['token_code']);
    }

    protected function convertPrice($price, $currency)
    {
        $currency = maybe_unserialize($currency);
        $price = (float)$price * (float)$currency['exchange'];
        $price = number_format($price, $currency['currency_decimal'], '.', '');
        return $price;
    }

    protected function getCurrency($currency)
    {
        $currency = maybe_unserialize($currency);
        return $currency['unit'];
    }
}
