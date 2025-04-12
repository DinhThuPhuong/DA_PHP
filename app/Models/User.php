<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Store; // <-- THÊM DÒNG NÀY

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = "users";
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'avatar',
        'phoneNumber',
        'role_id',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    public function store()
    {
        // Giả định khóa ngoại trong bảng 'store' là 'ownId' liên kết với 'id' của bảng 'users'
        return $this->hasOne(Store::class, 'ownId', 'id');
    }
    protected $hidden = [
        'password'
       
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    // protected function casts(): array
    // {
    //     return [
           
    //         'password' => 'hashed'
    //     ];
    // }
}