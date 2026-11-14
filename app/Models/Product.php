<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {protected $fillable=['sku','name','barcode','description','unit','is_active']; protected $casts=['is_active'=>'boolean']; public function inventories(){return $this->hasMany(RowInventory::class);} }
