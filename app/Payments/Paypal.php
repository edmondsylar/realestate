<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/18/2019
 * Time: 3:30 PM
 */

use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use PayPal\Api\PaymentExecution;
use App\Abstracts\Gateways;

class Paypal extends Gateways
{
    protected $apiContext;
    static $paymentId = 'paypal';

    public function __construct()
    {
        parent::__construct();
    }

    public static function getID()
    {
        return self::$paymentId;
    }

    public static function getName()
    {

        return 'Paypal';
    }

    public static function getHtml()
    {
        return '';
    }

    public static function getLogo()
    {
        $img = get_option(self::$paymentId . '_logo');
        return get_attachment_url($img, 'full');
    }

    public static function getDescription()
    {
        $desc = get_option(self::$paymentId . '_description');
        return ($desc);
    }

    public static function getOptions()
    {
        // TODO: Implement setOptions() method.

        return [

            'title' => [
                'id' => 'sub_tab_' . self::$paymentId,
                'label' => self::getName()
            ],
            'content' => [
                [
                    'id' => 'enable_' . self::$paymentId,
                    'label' => __('Enable'),
                    'type' => 'on_off',
                    'std' => 'off',
                    'section' => 'sub_tab_' . self::$paymentId
                ],
                [
                    'id' => self::$paymentId . '_logo',
                    'label' => __('Logo'),
                    'type' => 'upload',
                    'trans' => 'none',
                    'section' => 'sub_tab_' . self::$paymentId
                ],
                [
                    'id' => self::$paymentId . '_test_mode',
                    'label' => __('Test Mode'),
                    'type' => 'on_off',
                    'std' => 'on',
                    'section' => 'sub_tab_' . self::$paymentId
                ],
                [
                    'id' => self::$paymentId . '_client_id',
                    'label' => __('Client ID'),
                    'type' => 'text',
                    'trans' => 'none',
                    'layout' => 'col-12 col-sm-6',
                    'section' => 'sub_tab_' . self::$paymentId
                ],
                [
                    'id' => self::$paymentId . '_client_secret',
                    'label' => __('Client Secret'),
                    'type' => 'text',
                    'trans' => 'none',
                    'layout' => 'col-12 col-sm-6',
                    'break' => true,
                    'section' => 'sub_tab_' . self::$paymentId
                ],
                [
                    'id' => self::$paymentId . '_description',
                    'label' => __('Description'),
                    'type' => 'textarea',
                    'trans' => 'yes',
                    'section' => 'sub_tab_' . self::$paymentId
                ],
            ]
        ];
    }

    public function setDefaultParams()
    {
        // TODO: Implement setDefaultParams() method.

        date_default_timezone_set(@date_default_timezone_get());

        $clientId = get_option(self::$paymentId . '_client_id');
        $clientSecret = get_option(self::$paymentId . '_client_secret');

        $apiContext = new ApiContext(
            new OAuthTokenCredential(
                $clientId,
                $clientSecret
            )
        );
        $testMode = get_option(self::$paymentId . '_test_mode', 'off');
        $apiContext->setConfig(
            array(
                'mode' => ($testMode == 'on') ? 'sandbox' : 'live',
                'log.LogEnabled' => false
            )
        );

        $this->apiContext = $apiContext;
    }

    public function setParams($params = [])
    {
        // TODO: Implement setParams() method.

        $default = [
            'items' => [
                'name' => $this->orderObject->booking_description,
                'currency' => \Currencies::get_inst()->currentCurrency('unit'),
                'quantity' => 1,
                'itemNumber' => $this->orderObject->booking_id,
                'price' => $this->convertPrice($this->orderObject->total, $this->orderObject->currency)
            ],
            'currency' => $this->getCurrency($this->orderObject->currency),
            'total' => $this->convertPrice($this->orderObject->total, $this->orderObject->currency),
            'description' => sprintf('Booking ID: %s', $this->orderObject->booking_id),
            'returnUrl' => $this->successUrl(),
            'cancelUrl' => $this->cancelUrl(),
            'invoice' => uniqid()
        ];

        $params = wp_parse_args($params, $default);
        $this->params = $params;
    }

    public function validation()
    {
        // TODO: Implement validation() method.

        return true;
    }

    public function purchase($orderID = false, $params = [])
    {
        // TODO: Implement purchase() method.
        $this->setOrderObject($orderID);
        $this->setDefaultParams();
        $this->setParams($params);
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

        $item = new Item();
        $item->setName($this->params['items']['name'])
            ->setCurrency($this->params['items']['currency'])
            ->setQuantity($this->params['items']['quantity'])
            ->setSku($this->params['items']['itemNumber'])
            ->setPrice($this->params['items']['price']);
        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $amount = new Amount();
        $amount->setCurrency($this->params['currency'])->setTotal($this->params['total']);

        $transaction = new Transaction();
        $transaction->setAmount($amount)->setItemList($itemList)->setDescription($this->params['description'])->setInvoiceNumber($this->params['invoice']);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl($this->params['returnUrl'])->setCancelUrl($this->params['cancelUrl']);

        $payment = new Payment();
        $payment->setIntent("sale")->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array($transaction));

        do_action('hh_purchase_' . self::$paymentId, $this->params);

        try {
            $payment->create($this->apiContext);
            $approvalUrl = $payment->getApprovalLink();
            return [
                'status' => 'incomplete',
                'message' => 'The system will be redirect to the Paypal',
                'redirectUrl' => $approvalUrl
            ];
        } catch (Exception $ex) {
            return [
                'status' => 'pending',
                'message' => sprintf('Have error when processing: Code %s - Message %s', $ex->getCode(), $ex->getMessage())
            ];
        }
    }

    public function completePurchase($orderID = false, $params = [])
    {
        // TODO: Implement completePurchase() method.

        do_action('hh_complete_purchase_' . self::$paymentId, $this->params);

        if (isset($_GET['payment']) && $_GET['payment'] == 'paypal') {
            $this->setOrderObject($orderID);
            $this->setDefaultParams();
            $default = [
                'currency' => $this->getCurrency($this->orderObject->currency),
                'total' => $this->convertPrice($this->orderObject->total, $this->orderObject->currency),
            ];
            $this->params = wp_parse_args($params, $default);
            $paymentId = $_GET['paymentId'];

            $payment = Payment::get($paymentId, $this->apiContext);

            $execution = new PaymentExecution();
            $execution->setPayerId($_GET['PayerID']);

            $transaction = new Transaction();
            $amount = new Amount();

            $amount->setCurrency($this->params['currency']);
            $amount->setTotal($this->params['total']);
            $transaction->setAmount($amount);

            $execution->addTransaction($transaction);
            try {
                $payment->execute($execution, $this->apiContext);
                try {
                    $payment = Payment::get($paymentId, $this->apiContext);
                    return [
                        'status' => 'completed',
                        'message' => sprintf('Executed Payment. The Payment is: %s', $payment->getId())
                    ];
                } catch (Exception $ex) {
                    return [
                        'status' => 'incomplete',
                        'message' => sprintf('Get the error: Code %s - Message %s', $ex->getCode(), $ex->getMessage())
                    ];
                }
            } catch (Exception $ex) {
                return [
                    'status' => 'incomplete',
                    'message' => sprintf('Get the error: Code %s - Message %s', $ex->getCode(), $ex->getMessage())
                ];
            }
        } else {
            return [
                'status' => 'canceled',
                'message' => 'Cancelled the Approval'
            ];
        }
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
