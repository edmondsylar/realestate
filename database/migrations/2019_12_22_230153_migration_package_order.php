<?php

/**
 * Part of the Sentinel package.
 *
 * NOTICE OF LICENSE
 *
 * Licensed under the 3-clause BSD License.
 *
 * This source file is subject to the 3-clause BSD License that is
 * bundled with this package in the LICENSE file.
 *
 * @package    Sentinel
 * @version    2.0.18
 * @author     Cartalyst LLC
 * @license    BSD License (3-clause)
 * @copyright  (c) 2011-2019, Cartalyst LLC
 * @link       http://cartalyst.com
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MigrationPackageOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_order', function (Blueprint $table) {
            $table->bigIncrements('pkorder_id');
            $table->string('partner_name');
            $table->string('partner_id');
            $table->string('partner_info');
	        $table->string('package_id');
            $table->string('package_name');
            $table->string('package_price')->nullable();
            $table->string('package_time')->nullable();
            $table->string('package_commission')->nullable();
            $table->string('package_description')->nullable();
            $table->string('package_price_payment')->nullable();
            $table->string('gateway');
            $table->string('gateway_name');
            $table->string('token')->nullable();
            $table->string('status');
            $table->string('balance')->nullable();
            $table->string('created_at', 20);
            $table->engine = 'InnoDB';
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('package_order');
    }
}
