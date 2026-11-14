<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StorageRow extends Model {protected $table='storage_rows'; protected $fillable=['rack_id','name','code','public_id','description','is_active']; protected $casts=['is_active'=>'boolean']; public function rack(){return $this->belongsTo(Rack::class);} public function inventories(){return $this->hasMany(RowInventory::class);} }
