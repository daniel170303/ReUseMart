<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     * Ini digunakan untuk menentukan kolom mana saja yang bisa diisi secara massal
     * untuk mencegah mass assignment vulnerability.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Ini untuk menyembunyikan atribut yang tidak boleh dikirim ke client.
     * Misalnya password dan remember_token untuk keamanan.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     * Digunakan untuk mengonversi tipe data secara otomatis saat diambil dari database.
     * Misalnya email_verified_at akan dikonversi ke datetime object.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Mutator untuk password hashing.
     * Fungsi ini memastikan password selalu di-hash sebelum disimpan ke database.
     * Dengan ini, developer tidak perlu khawatir lupa meng-hash password secara manual.
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}