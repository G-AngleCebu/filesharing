<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UploadGroup extends Eloquent
{
    protected $timestamp = false;
    
    protected $fillable = [
        'download_uid',
        'upload_date',
        'expiration_date',
        'password',
        'validity'
    ];
    
    public function uploadFiles(){
        return $this->hasMany('App\UploadFile');
    }
}
