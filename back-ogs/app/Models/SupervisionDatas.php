<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupervisionDatas extends Model
{
    protected $table = 'supervision_datas';
    public $timestamps = true;
    protected $fillable = [
        'website_id',
        'wp_ext_datas',
        'cms',
        'is_ssl_valid',
        'is_robotxt',
        'get_header_response',
        'is_sitemap',
        'ga_datas',
        'google_webmaster'
    ];


    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function errors()
    {
        return $this->hasOne(Error::class);
    }
}
