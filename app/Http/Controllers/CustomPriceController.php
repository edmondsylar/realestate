<?php

namespace App\Http\Controllers;

use App\HomeAvailability;
use App\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\CustomPrice;

class CustomPriceController extends Controller
{
    public function _changeStatusCustomPriceItem(Request $request)
    {
        $id = Input::get('priceID');
        $encrypt = Input::get('priceEncrypt');
        $status = Input::get('val');

        if (!hh_compare_encrypt($id, $encrypt)) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This range is not available')
            ], true);
        }
        $price_model = new CustomPrice();
        $has = $price_model->getByID($id);

        if (!$has) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('This range is not available')
            ], true);
        }
        $avai = new HomeAvailability();

        $hasAvai = $avai->getItem($has->home_id, $has->start_time, $has->end_time);
        if ($hasAvai && is_object($hasAvai)) {
            if ($status == 'on') {
                $avai->deleteAvailability($has->home_id, $has->start_time, $has->end_time);
            }
        } else {
            if ($status != 'on') {
                $avai->createAvailability([
                    'home_id' => $has->home_id,
                    'start_time' => $has->start_time,
                    'start_date' => $has->start_time,
                    'end_time' => $has->end_time,
                    'end_date' => $has->end_time,
                    'total_minutes' => 1440
                ]);
            }
        }
        $data = [
            'available' => $status ? 'on' : 'off'
        ];
        $updated = $price_model->updatePrice($data, $id);

        if (!is_null($updated)) {
            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('Updated successfully')
            ], true);
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Can not update this range')
            ], true);
        }
    }

    public function _deleteCustomPriceItem(Request $request)
    {
        $priceID = Input::get('priceID');
        $postID = Input::get('postID');
        if (!$priceID || !$postID) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Can not save data. This service is invalid')
            ], true);
        }
        $priceObject = new CustomPrice();
        $has = $priceObject->getByID($priceID);
        if ($has && is_object($has)) {
            $avai = new HomeAvailability();
            $avai->deleteAvailability($has->home_id, $has->start_time, $has->end_time);
        }
        $delete = $priceObject->deletePrice($priceID);
        if ($delete) {
            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('Successfully'),
                'html' => view('dashboard.components.custom_price', ['custom_price' => $priceObject->getAllPrices($postID)])->render(),
            ], true);
        }
        $this->sendJson([
            'status' => 0,
            'title' => __('System Alert'),
            'message' => __('Have error when delete custom price')
        ], true);
    }

    public function _addNewCustomPrice(Request $request)
    {
        $type = Input::get('type_of_bulk', '');
        $day_of_week = Input::get('days_of_week_bulk');
        $day_of_month = Input::get('days_of_month_bulk');
        $month = Input::get('month_bulk');
        $year = Input::get('year_bulk');
        $price = Input::get('price_bulk');
        $available = Input::get('available_bulk', 'on');
        $postID = Input::get('post_id_bulk');

        if (!$postID) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Can not save data. This service is invalid')
            ], true);
        }

        if (!is_numeric($price) || (float)$price < 0) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('The price is incorrect')
            ], true);
        }
        if (!empty($month) && !empty($year)) {
            $price = (float)$price;

            $priceModel = new CustomPrice();
            $availability_model = new HomeAvailability();

            $data_week = [
                'monday' => 1,
                'tuesday' => 2,
                'wednesday' => 3,
                'thursday' => 4,
                'friday' => 5,
                'saturday' => 6,
                'sunday' => 7
            ];
            $group = $alone = [];
            $convert_data_week = [];
            if (!empty($day_of_week) || !empty($day_of_month)) {
                if ($type == 'days_of_week') {
                    $day_of_week = (array)$day_of_week;
                    foreach ($day_of_week as $key => $day) {
                        $convert_data_week[] = $data_week[$day];
                    }
                } elseif ($type == 'days_of_month') {
                    $day_of_month = (array)$day_of_month;
                    $convert_data_week = $day_of_month;
                }
                $full_stack = false;
                for ($i = 0; $i < count($convert_data_week) - 1; $i++) {
                    $group_tmp = [$convert_data_week[$i]];
                    for ($j = $i + 1; $j < count($convert_data_week); $j++) {
                        if ($convert_data_week[$j] == $group_tmp[count($group_tmp) - 1] + 1) {
                            $group_tmp[] = $convert_data_week[$j];
                            if (count($group_tmp) == count($convert_data_week) - $i) {
                                $full_stack = true;
                            }
                        } else {
                            $i = $j - 1;
                            break;
                        }
                    }
                    if (count($group_tmp) >= 2) {
                        $group[] = $group_tmp;
                    }
                    if ($full_stack) {
                        break;
                    }
                }
                $alone = $convert_data_week;
                foreach ($convert_data_week as $key => $day) {
                    foreach ($group as $item) {
                        if (in_array($day, $item)) {
                            unset($alone[$key]);
                        }
                    }
                }
            }
            if (!empty($group) || !empty($alone)) {
                $data_week = array_flip($data_week);
                if (!empty($group)) {
                    foreach ($year as $_year) {
                        foreach ($month as $_month) {
                            if ($type == 'days_of_week') {
                                foreach ($group as $group_item) {
                                    $start = strtotime('first ' . $data_week[$group_item[0]] . ' of ' . $_year . '-' . sprintf('%02d', $_month));
                                    $_start = strtotime('last ' . $data_week[$group_item[0]] . ' of ' . $_year . '-' . sprintf('%02d', $_month));
                                    if ($start) {
                                        for ($i = $start; $i <= $_start; $i = strtotime('+1 week', $i)) {
                                            $start_item = $i;
                                            $end_item = strtotime('first ' . $data_week[$group_item[count($group_item) - 1]], $i);
                                            if (date('Ym', $start_item) != date('Ym', $end_item)) {
                                                $last_date = strtotime(date('Y-m-t', $i));
                                                if ($last_date < $end_item) {
                                                    $end_item = $last_date;
                                                }
                                            }
                                            $priceModel->_savePrice($postID, $start_item, $end_item, $price, $available);
                                            $availability_model->_saveAvailability($postID, $start_item, $end_item, $available);

                                        }
                                    }
                                }
                            } elseif ($type == 'days_of_month') {
                                foreach ($group as $group_item) {
                                    $start = strtotime($_year . '-' . sprintf('%02d', $_month) . '-' . sprintf('%02d', $group_item[0]));
                                    $end = strtotime($_year . '-' . sprintf('%02d', $_month) . '-' . sprintf('%02d', $group_item[count($group_item) - 1]));
                                    $last_date = strtotime(date('Y-m-t', $start));
                                    if ($end > $last_date) {
                                        $end = $last_date;
                                    }
                                    if ($start && $end) {
                                        $priceModel->_savePrice($postID, $start, $end, $price, $available);
                                        $availability_model->_saveAvailability($postID, $start, $end, $available);
                                    }
                                }

                            }
                        }
                    }
                }
                if (!empty($alone)) {
                    foreach ($year as $_year) {
                        foreach ($month as $_month) {
                            if ($type == 'days_of_week') {
                                foreach ($alone as $day) {
                                    $start = strtotime('first ' . $data_week[$day] . ' of ' . $_year . '-' . sprintf('%02d', $_month));
                                    $_start = strtotime('last ' . $data_week[$day] . ' of ' . $_year . '-' . sprintf('%02d', $_month));
                                    if ($start) {
                                        for ($i = $start; $i <= $_start; $i = strtotime('+1 week', $i)) {
                                            $priceModel->_savePrice($postID, $i, $i, $price, $available);
                                            $availability_model->_saveAvailability($postID, $i, $i, $available);
                                        }
                                    }
                                }
                            } elseif ($type == 'days_of_month') {
                                foreach ($alone as $day) {
                                    $start = strtotime($_year . '-' . sprintf('%02d', $_month) . '-' . sprintf('%02d', $day));
                                    if ($start) {
                                        $priceModel->_savePrice($postID, $start, $start, $price, $available);
                                        $availability_model->_saveAvailability($postID, $start, $start, $available);
                                    }
                                }
                            }

                        }
                    }
                }
            } else {
                foreach ($year as $_year) {
                    foreach ($month as $_month) {
                        $start = strtotime($_year . '-' . sprintf('%02d', $_month) . '-01');
                        $end = strtotime(date($_year . '-' . sprintf('%02d', $_month) . '-t'));
                        $priceModel->_savePrice($postID, $start, $end, $price, $available);
                        $availability_model->_saveAvailability($postID, $start, $end, $available);
                    }
                }
            }
            $this->sendJson([
                'status' => 1,
                'title' => __('System Alert'),
                'message' => __('Created successfully'),
                'html' => view('dashboard.components.custom_price', ['custom_price' => $priceModel->getAllPrices($postID)])->render(),
            ], true);
        } else {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('The data is incorrect')
            ], true);
        }


        $oldItems = $priceModel->getPriceItems($postID, $start, $end);
        if ($oldItems['total'] > 0) {
            $this->sendJson([
                'status' => 0,
                'title' => __('System Alert'),
                'message' => __('Can not set custom price in this range')
            ], true);
        } else {
            $res = $priceModel->createPrice([
                'home_id' => $postID,
                'start_time' => $start,
                'end_time' => $end,
                'price' => $price,
                'available' => $available
            ]);
            if ($available != 'on') {
                $avai = new HomeAvailability();
                $avai->createAvailability([
                    'home_id' => $postID,
                    'start_time' => $start,
                    'start_date' => $start,
                    'end_time' => $end,
                    'end_date' => $end,
                    'total_minutes' => 1440
                ]);
            }
            if ($res) {
                $this->sendJson([
                    'status' => 1,
                    'title' => __('System Alert'),
                    'message' => __('Created successfully'),
                    'html' => view('dashboard.components.custom_price', ['custom_price' => $priceModel->getAllPrices($postID)])->render(),
                ], true);
            } else {
                $this->sendJson([
                    'status' => 0,
                    'title' => __('System Alert'),
                    'message' => __('Have error when add new custom price')
                ], true);
            }
        }
    }

    public static function getAllPrices($home_id)
    {
        $price = new CustomPrice();

        return $price->getAllPrices($home_id);
    }
}
