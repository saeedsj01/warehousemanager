<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class WarehouseLayout extends Model {protected $fillable=['warehouse_id','canvas_width','canvas_height','layout_data']; protected $casts=['layout_data'=>'array']; public function warehouse(){return $this->belongsTo(Warehouse::class);} }
