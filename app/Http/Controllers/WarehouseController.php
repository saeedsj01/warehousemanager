<?php
namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehouseController extends Controller
{
    public function index()
    {
        return view('warehouses.index', ['items'=>Warehouse::withCount('aisles')->latest()->paginate(15)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|max:150','code'=>'required|max:50|unique:warehouses,code','description'=>'nullable|max:2000']);
        Warehouse::create([...$data,'is_active'=>true]);
        return back()->with('success','انبار با موفقیت ایجاد شد.');
    }

    public function update(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate(['name'=>'required|max:150','code'=>'required|max:50|unique:warehouses,code,'.$warehouse->id,'description'=>'nullable|max:2000']);
        $warehouse->update($data);
        return back()->with('success','انبار ویرایش شد.');
    }

    public function toggle(Warehouse $warehouse)
    {
        $warehouse->is_active = ! $warehouse->is_active;
        $warehouse->save();
        return back()->with('success','وضعیت انبار تغییر کرد.');
    }

    public function destroy(Warehouse $warehouse)
    {
        abort_if($warehouse->aisles()->exists(), 422, 'این انبار دارای راهرو است و امکان حذف آن وجود ندارد؛ حتی مدیر کل نیز نمی‌تواند آن را حذف کند.');
        $warehouse->delete();
        return back()->with('success','انبار حذف شد.');
    }
}
