<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    protected $table = 'websites';
    public $timestamps = false;
    protected $primaryKey = 'id';
    protected $fillable = [
        'url',
        'supervised'
    ];

    public function supervision_datas()
    {
        return $this->hasMany(SupervisionDatas::class);
    }
}
