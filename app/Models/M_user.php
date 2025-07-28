<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class M_user extends Model
{
    use HasFactory, HasApiTokens, Notifiable, HasRoles;

    protected $table = 'users'; // Specify the table name if it's not the plural of the model name
    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];
}
