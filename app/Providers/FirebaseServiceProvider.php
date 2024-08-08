<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class FirebaseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        
        $this->app->singleton('firebase', function ($app) {
            // Directly specify the path to the service account file
            $credentialsPath = base_path('config/mixxer-2024a-firebase-adminsdk-y6w1i-11a45265df.json');
            
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Service account file does not exist at path: " . $credentialsPath);
            }

            // Initialize the Firebase Factory with the service account
            $factory = (new Factory)->withServiceAccount($credentialsPath);

            return $factory;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
