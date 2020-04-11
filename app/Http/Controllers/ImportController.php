<?php
/**
 * Created by PhpStorm.
 * User: HanhDo
 * Date: 1/7/2020
 * Time: 4:00 PM
 */
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Post;
use App\Option;
use Illuminate\Http\Request;
use Sentinel;

class ImportController{
    private $old_url = 'https://data.awebooking.org';
    public function __construct()
    {

    }

    public function index(){
        $folder = $this->getFolder();
        return view("dashboard.screens.{$folder}.import", ['bodyClass' => 'hh-dashboard']);
    }

    private function resetDataBeforeImport(){
        DB::statement("DELETE FROM post");
        DB::statement("DELETE FROM page");
        DB::statement("DELETE FROM home");
        DB::statement("DELETE FROM menu");
        DB::statement("DELETE FROM menu_structure");
        DB::statement("DELETE FROM media");
        DB::statement("DELETE FROM comments");
        DB::statement("DELETE FROM coupon");
        DB::statement("DELETE FROM earning");
        DB::statement("DELETE FROM home_availability");
        DB::statement("DELETE FROM home_booking");
        DB::statement("DELETE FROM home_price");
        DB::statement("DELETE FROM options");
        DB::statement("DELETE FROM term");
        DB::statement("DELETE FROM term_relation");
    }

    public function _adminImportData(){
        $step = Input::post('step');

        $next_step = $step + 1;
        if ($step == 1) {
            $this->resetDataBeforeImport();
            $file_name = ['post'];
            $import_label = 'Post';
        } elseif ($step == 2) {
            $file_name = ['page'];
            $import_label = 'Page';
        } elseif ($step == 3) {
            $file_name = ['home'];
            $import_label = 'Home';
        } elseif ($step == 4) {
            $file_name = ['menu', 'menu_structure'];
            $import_label = 'Menu';
        } else {
            $file_name = ['media', 'comments', 'coupon', 'earning', 'home_availability', 'home_booking', 'home_price', 'options', 'term', 'term_relation'];
            $import_label = 'Settings';
        }

        if (!empty($file_name)) {
            foreach ($file_name as $file) {
                $sql = file_get_contents(public_path("installer/files/" . $file . ".sql"));
                if ($sql) {
                    if($file == 'menu_structure'){
                        $sql = str_replace($this->old_url, url('/'), $sql);
                    }
                    $statements = array_filter(array_map('trim', explode('INSERT INTO', $sql)));
                    foreach ($statements as $stmt) {
                        if (!empty($stmt)) {
                            DB::insert("INSERT INTO " . $stmt);
                        }
                    }
                }
            }
        }

        if ($step == 5) {
            $next_step = 'final';
            $installedLogFile = storage_path('imported');
            $dateStamp = date("Y/m/d h:i:sa");
            if (!file_exists($installedLogFile))
            {
                $message = sprintf(__('Your site has been imported at %s' ), $dateStamp) . "\n";
                file_put_contents($installedLogFile, $message);
            } else {
                $message = sprintf(__('Your site has been imported at %s'), $dateStamp) . "\n";
                file_put_contents($installedLogFile, $message.PHP_EOL , FILE_APPEND | LOCK_EX);
            }
        }

        echo json_encode([
            'status' => true,
            'next_step' => $next_step,
            'label' => $import_label
        ]); die;
    }

    public function _runImport(){
        if (!file_exists(storage_path('imported'))) {
            $step = Input::post('step');

            $next_step = $step + 1;
            if ($step == 1) {
                $file_name = ['post'];
                $import_label = 'Post';
            } elseif ($step == 2) {
                $file_name = ['page'];
                $import_label = 'Page';
            } elseif ($step == 3) {
                $file_name = ['home'];
                $import_label = 'Home';
            } elseif ($step == 4) {
                $file_name = ['menu', 'menu_structure'];
                $import_label = 'Menu';
            } else {
                $file_name = ['media', 'comments', 'coupon', 'earning', 'home_availability', 'home_booking', 'home_price', 'options', 'term', 'term_relation'];
                $import_label = 'Settings';
            }

            if (!empty($file_name)) {
                foreach ($file_name as $file) {
                    $sql = file_get_contents(public_path("installer/files/" . $file . ".sql"));
                    if ($sql) {
                        if($file == 'menu_structure'){
                            $sql = str_replace($this->old_url, url('/'), $sql);
                        }
                        $statements = array_filter(array_map('trim', explode('INSERT INTO', $sql)));
                        foreach ($statements as $stmt) {
                            if (!empty($stmt)) {
                                DB::insert("INSERT INTO " . $stmt);
                            }
                        }
                    }
                }
            }

            if ($step == 5) {
                if (!empty(env('APP_NAME'))) {
                    $option = new Option();
                    $hasOption = $option->getOption(\ThemeOptions::getOptionID());
                    $optionValue = (!is_null($hasOption)) ? unserialize($hasOption->option_value) : [];

                    $optionValue['site_name'] = env('APP_NAME');

                    $optionValue = serialize($optionValue);
                    $option->updateOption(\ThemeOptions::getOptionID(), $optionValue);
                }
                $next_step = 'final';

                $installedLogFile = storage_path('imported');
                $dateStamp = date("Y/m/d h:i:sa");
                if (!file_exists($installedLogFile))
                {
                    $message = 'Your site has been imported at ' . $dateStamp . "\n";
                    file_put_contents($installedLogFile, $message);
                } else {
                    $message = 'Your site has been imported at ' . $dateStamp . "\n";
                    file_put_contents($installedLogFile, $message.PHP_EOL , FILE_APPEND | LOCK_EX);
                }
            }

            echo json_encode([
                'status' => true,
                'next_step' => $next_step,
                'label' => $import_label
            ]);
        }else{
            echo json_encode([
                'status' => true,
                'next_step' => 'imported',
                'label' => __('Your site has been imported')
            ]);
        }
    }

    private function getFolder()
    {
        $folder = 'customer';
        if (Sentinel::inRole('administrator')) {
            $folder = 'administrator';
        } elseif (Sentinel::inRole('partner')) {
            $folder = 'partner';
        }

        return $folder;
    }
}