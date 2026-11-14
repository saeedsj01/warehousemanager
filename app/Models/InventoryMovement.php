<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class InventoryMovement extends Model {protected $fillable=['product_id','from_row_id','to_row_id','quantity','user_id','type','note']; public function product(){return $this->belongsTo(Product::class);} public function fromRow(){return $this->belongsTo(StorageRow::class,'from_row_id');} public function toRow(){return $this->belongsTo(StorageRow::class,'to_row_id');} public function user(){return $this->belongsTo(User::class);} }
