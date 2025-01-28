<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiToken extends Model
{
    use HasFactory;

    protected $fillable = ['token', 'description', 'expires_at'];
    
    public static function generateToken($description, $expiresInDays = null)
    {
        // Generate a unique token
        $plainToken = Str::random(60);
        $hashedToken = hash('sha256', $plainToken);

        // Calculate expiration date
        $expiresAt = $expiresInDays ? Carbon::now()->addDays($expiresInDays) : null;

        // Store token in the database
        $apiToken = self::create([
            'token' => $hashedToken,
            'description' => $description,
            'expires_at' => $expiresAt,
        ]);

        // Return the plain token for the user (hash stored in the DB)
        return [
            'id' => $apiToken->id,
            'description' => $description,
            'token' => $plainToken,
            'expires_at' => $expiresAt,
        ];
    }
    
    // Check if token is expired (optional)
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }
}
