<?php

namespace oceler;

use Illuminate\Database\Eloquent\Model;

class Nameset extends Model
{
    public function names()
    {
      return $this->hasMany('\oceler\Name');
    }

    public static function addNameset($config)
    {

      // Save the new Nameset
      $nameset = new Nameset();
      $nameset->name = $config['name'];
      $nameset->save();

      // Then store each name in the set, along with the Nameset ID
      foreach ($config['names'] as $name) {
        $n = new \oceler\Name();
        $n->nameset_id = $nameset->id;
        $n->name = $name;
        $n->save();
      }
    }
}
