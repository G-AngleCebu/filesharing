<?php

namespace App;

use Illuminate\Database\Eloquent\Model as Eloquent;

class UploadFile extends Eloquent
{
    protected $fillable = [
		'upload_group_id',
		'upload_group_id',
		'file_name',
		'file_directory',
		'file_size',
		'validity'
    ];
    
    public function uploadGroup(){
        return $this->hasMany('App\UploadGroup');
    }
}
