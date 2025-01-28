<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ApiToken;

class GenerateApiToken extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:apitoken {description} {--expires_in_days= : Number of days until the token expires}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new API token and store it in the database';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Retrieve the description argument
        $description = $this->argument('description');

        // Retrieve the optional expiration days option
        $expiresInDays = $this->option('expires_in_days');
        
        // Call the static method from the ApiToken model to generate the token
        $result = ApiToken::generateToken($description, $expiresInDays);

        // Display the token to the user
        $this->info("API Token generated successfully!");
        $this->info("Token: {$result['token']}");
        
        if ($result['expires_at']) {
            $this->info("Expires At: {$result['expires_at']}");
        } else {
            $this->info("Expires At: Never");
        }
    }
}
