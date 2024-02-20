<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Service;

class Support extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'area_atuacao',
        'livre',   
        'user_id'    
    ];
    
    public function user()
    {
	return $this->belongsTo(User::class);
    }

    public function service()
    {
	return $this->hasMany(Service::class);
    }
}
