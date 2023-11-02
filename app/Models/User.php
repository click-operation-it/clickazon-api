<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use jeremykenedy\LaravelRoles\Traits\HasRoleAndPermission;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoleAndPermission;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
    ];

    protected $with = [
        'roles',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

     /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function usershipping()
    {
        if(class_exists("\SbscPackage\Ecommerce\Models\EcommerceUserShipping")){
            return $this->hasOne(\SbscPackage\Ecommerce\Models\EcommerceUserShipping::class);
        }
        return null;  
    }

    public function userbilling()
    {
        if(class_exists("\SbscPackage\Ecommerce\Models\EcommerceUserBilling")){
            return $this->hasOne(\SbscPackage\Ecommerce\Models\EcommerceUserBilling::class);
        }
        return null;  
    }

    public function userecommercewishlist()
    {
        if(class_exists("\SbscPackage\Ecommerce\Models\EcommerceWishlist")){
            return $this->hasMany(\SbscPackage\Ecommerce\Models\EcommerceWishlist::class);
        }
        return null;  
    }

    public function userecommercecarts()
    {
        if(class_exists("\SbscPackage\Ecommerce\Models\EcommerceCart")){
            return $this->hasMany(\SbscPackage\Ecommerce\Models\EcommerceCart::class);
        }
        return null;  
    }
}
