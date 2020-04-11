<?php

use Illuminate\Support\Facades\Session;

class CartHome
{
    protected $cartID;

    public function __construct()
    {
        $this->setCartID('hh_cart_service');
    }

    public function totalCalculation($rules = [], $taxRule = [])
    {
        $subTotal = $this->getAmount(0, $rules);
        $amount = $this->getAmount($subTotal, $taxRule);

        return [
            'subTotal' => $subTotal,
            'amount' => $amount
        ];
    }

    public function getAmount($total = 0, $rules = [])
    {
        foreach ($rules as $key => $item) {
            if ($item['unit'] === '+') {
                $total += $item['price'];
            } elseif ($item['unit'] === '-') {
                $total -= $item['price'];
            } elseif ($item['unit'] === 'tax') {
                $total += ($total * $item['price'] / 100);
            }
        }

        return apply_filters('hh_amount_before_add_to_cart', $total, $rules);
    }

    public function getTax()
    {
        $included = get_option('included_tax', 'off');
        $tax = (float)get_option('tax', 0);
        return [
            'included' => $included,
            'tax' => $tax
        ];
    }

    public function setCartID($name = '')
    {
        $this->cartID = $name;
    }

    public function getCartID()
    {
        return $this->cartID;
    }

    public function setCart($params = [])
    {
        Session::put($this->getCartID(), $params);
    }

    public function updateCartItem($key = '', $value = '')
    {
        if ($this->issetCart()) {
            $cart = $this->getCart();
            $cart[$key] = $value;

            $this->setCart($cart);
        }
    }

    public function getCart()
    {
        return ($this->issetCart()) ? Session::get($this->getCartID()) : false;
    }

    public function issetCart()
    {
        return Session::exists($this->getCartID()) ? true : false;
    }

    public function deleteCart()
    {
        Session::remove($this->getCartID());
    }

    public static function get_inst()
    {
        static $instance;
        if (is_null($instance)) {
            $instance = new self();
        }
        return $instance;
    }
}
