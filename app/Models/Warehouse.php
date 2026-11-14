<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Warehouse extends Model {protected $fillable=['name','code','description','is_active']; protected $casts=['is_active'=>'boolean']; public function aisles(){return $this->hasMany(Aisle::class);} public function layout(){return $this->hasOne(WarehouseLayout::class);} }
