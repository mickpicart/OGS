<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Error extends Model
{

    protected $table = 'errors';
    public $timestamps = true;
    protected $primaryKey = 'id';
    protected $fillable = [
        'supervision_datas_id',
        'message',
        'criticity',
    ];

    public function supervision_datas()
    {
        return $this->belongsTo(SupervisionDatas::class);
    }
}
