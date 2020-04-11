<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Sentinel;

class TaxonomyController extends Controller
{

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
