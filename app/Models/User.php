<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
class User extends Authenticatable implements MustVerifyEmail
{
    use  HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'password',
        'phone',
        'address',
        'city_id',
        'pcode',
        'source',
        'status',
        'role',
        'bio',           // new
        'country',       // new
        'postal_code',   // new
        'tax_id',        // new
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->pcode)) {

                $counter = PcodeCounter::firstOrCreate(
                    ['city_id' => $user->city_id],
                    ['last_number' => 0]
                );

                $counter->increment('last_number');

                $cityCode = $user->city->city_code;

                // Add zero only for numbers below 100
                $number = $counter->last_number < 100
                    ? str_pad($counter->last_number, 2, '0', STR_PAD_LEFT)
                    : $counter->last_number;

                $user->pcode = $cityCode . $number;
            }
        });
    }
    // ========== RELATIONSHIPS ==========

    /**
     * Get the city that the user belongs to.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    /**
     * Get all shipments for the user.
     */
    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    /**
     * Get all revenue records for the user.
     */
    public function revenues()
    {
        return $this->hasMany(Revenue::class);
    }

    /**
     * Get all debtor records for the user.
     */
    public function debtors()
    {
        return $this->hasMany(Debtor::class);
    }

    // ========== HELPER METHODS (optional) ==========

    /**
     * Check if user is admin.
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is approved.
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }
}

