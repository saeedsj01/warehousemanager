<?php
namespace App\Http\Controllers;
use App\Models\{Warehouse,Product,StorageRow,RowInventory,InventoryMovement};
class DashboardController extends Controller {public function __invoke(){return view('dashboard',['warehouses'=>Warehouse::count(),'products'=>Product::count(),'rows'=>StorageRow::count(),'inventory'=>RowInventory::count(),'movements'=>InventoryMovement::latest()->with(['product','user'])->take(8)->get()]);}}
