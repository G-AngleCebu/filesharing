<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Download extends Eloquent
{
    protected $fillable = [
		'upload_file_id',
		'upload_file_id',
		'download_date',
		'ip_address',
		'host',
		'user_agent'
    ];
    
    public function upload(){
        return $this->hasMany('App\UploadFile');
    }
}
