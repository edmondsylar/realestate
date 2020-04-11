<?php

namespace RachidLaasri\LaravelInstaller\Controllers;

use Illuminate\Routing\Controller;
use RachidLaasri\LaravelInstaller\Helpers\DatabaseManager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DatabaseController extends Controller
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * Migrate and seed the database.
     *
     * @return \Illuminate\View\View
     */
    public function database()
    {
        $response = $this->databaseManager->migrateAndSeed();
        if($response['status'] == 'error'){
            return redirect()->route('LaravelInstaller::environmentClassic')
                ->with(['message' => 'Have an error when create env file. Please reset your DB or check your DB information again.']);
        }else {
            $command = 'storage:link';
            $result1 = Artisan::call($command);
            $result2 = Artisan::output();

            return redirect()->route('LaravelInstaller::final')
                ->with(['message' => $response]);
        }
    }
}
