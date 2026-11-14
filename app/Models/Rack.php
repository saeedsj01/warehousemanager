<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Rack extends Model {protected $fillable=['aisle_id','name','code','description','is_active']; protected $casts=['is_active'=>'boolean']; public function aisle(){return $this->belongsTo(Aisle::class);} public function rows(){return $this->hasMany(StorageRow::class);} }
