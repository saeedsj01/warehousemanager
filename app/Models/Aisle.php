<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Aisle extends Model {protected $fillable=['warehouse_id','name','code','description','is_active']; protected $casts=['is_active'=>'boolean']; public function warehouse(){return $this->belongsTo(Warehouse::class);} public function racks(){return $this->hasMany(Rack::class);} }
