<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Service;

class Contact extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'nome_pessoa',
        'nome_cliente',
        'area_atendimento',            
    ];
   
    public function service()
    {
	return $this->hasMany(Service::class);
    }
}
