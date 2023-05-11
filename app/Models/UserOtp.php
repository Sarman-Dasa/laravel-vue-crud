<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    use HasFactory;
    protected $primaryKey = "phone";
    protected $fillable = ['phone', 'otp', 'expired_at'];
}
