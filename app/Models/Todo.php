<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Todo extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'priority',
        'status',
        'due_date',
        'user_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    //Todo-User Relationship
    public function user() {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
