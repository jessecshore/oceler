<?php

namespace oceler;

use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'queues';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id'];

    public function users() {
      return $this->belongsTo('\oceler\User', 'user_id', 'id');
    }
}
