<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Service;
use Illuminate\Database\Eloquent\SoftDeletes;

class Support extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'area_atuacao',
        'livre',   
        'user_id'    
    ];
    
    public function user()
    {
	return $this->belongsTo(User::class, 'user_id');
    }

    public function services()
    {
	return $this->hasMany(Service::class, 'support_id');
    }
}
