<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class UpdateController extends Controller
{
    private $versions = [
        'version_1_1'
    ];
    private $messages = [];

    public function _update(Request $request)
    {
        foreach ($this->versions as $version) {
            $updated = get_opt('awebooking_' . $version, false);
            if (!$updated) {
                $this->$version();
                update_opt('awebooking_' . $version, 'updated');
            } else {
                $this->messages[] = sprintf(__('Has updated version %s'), $version);
            }
        }

        return view('common.update', ['messages' => $this->messages]);
    }

    public function version_1_1()
    {
        Artisan::call('migrate');
        $output = Artisan::output();
        DB::table('home')->update(['booking_form' => 'instant']);

        $this->messages[] = $output;
    }
}
