<?php

namespace App\Http\Controllers;

use App\Earning;
use App\HomeBooking;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    public function updateEarning($booking_id)
    {
        $bookingObject = get_booking($booking_id);

        $earning_model = new Earning();
        $booking_model = new HomeBooking();

        $ownerID = $bookingObject->owner;
        $earningItem = $earning_model->getEarning($ownerID);

        $amount = $booking_model->getTotalAmountByStatus(['completed', 'incomplete'], $ownerID);
        $net = $booking_model->getNetAmount($ownerID);
        $payout = isset($earningItem->payout) ? $earningItem->payout : 0;
        $commission = (float)get_option('partner_commission', 0);
        $net -= ($net * $commission / 100);
        $balance = $net - $payout;
        if ($earningItem) {
            $earning_model->updateEarning($earningItem->user_id, [
                'amount' => $amount,
                'net_amount' => $net,
                'payout' => $payout,
                'balance' => $balance
            ]);
        } else {
            $earning_model->insertEarning([
                'user_id' => $ownerID,
                'amount' => $amount,
                'payout' => $payout,
                'balance' => $balance,
            ]);
        }
    }

    public function getEarning($user_id)
    {
        $earning_model = new Earning();

        $default = [
            'amount' => 0,
            'net_amount' => 0,
            'balance' => 0,
            'payout' => 0
        ];
        $data = $earning_model->getEarning($user_id);
        return is_null($data) ? $default : (array)$data;
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
