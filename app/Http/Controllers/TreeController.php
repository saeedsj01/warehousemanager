<?php
namespace App\Http\Controllers;
use App\Models\Warehouse;
class TreeController extends Controller {public function __invoke(){return view('tree.index',['warehouses'=>Warehouse::with('aisles.racks.rows.inventories.product')->orderBy('name')->get()]);}}
