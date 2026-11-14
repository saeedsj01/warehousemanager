<?php
namespace App\Http\Controllers;

use App\Models\Aisle;
use App\Models\Rack;
use App\Models\StorageRow;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StructureController extends Controller
{
    private function getType(): string
    {
        $name = request()->route()->getName() ?? '';
        return match (true) {
            str_starts_with($name, 'aisles.') => 'aisles',
            str_starts_with($name, 'racks.') => 'racks',
            str_starts_with($name, 'rows.') => 'rows',
            default => abort(404),
        };
    }

    private function model(string $type): string
    {
        return match ($type) {
            'aisles' => Aisle::class,
            'racks' => Rack::class,
            'rows' => StorageRow::class,
            default => abort(404),
        };
    }

    public function index()
    {
        $type = $this->getType();
        $items = match ($type) {
            'aisles' => Aisle::with('warehouse')->latest()->paginate(20),
            'racks' => Rack::with('aisle.warehouse')->latest()->paginate(20),
            'rows' => StorageRow::with('rack.aisle.warehouse')->latest()->paginate(20),
        };
        $parents = $this->parents($type);
        return view('structure.index', compact('type', 'items', 'parents'));
    }

    public function store(Request $request)
    {
        $type = $this->getType();
        $rules = match ($type) {
            'aisles' => ['parent_id'=>'required|exists:warehouses,id','name'=>'required|max:150','code'=>'required|max:50','description'=>'nullable|max:2000'],
            'racks' => ['parent_id'=>'required|exists:aisles,id','name'=>'required|max:150','code'=>'required|max:50','description'=>'nullable|max:2000'],
            'rows' => ['parent_id'=>'required|exists:racks,id','name'=>'required|max:150','code'=>'required|max:70|unique:storage_rows,code','description'=>'nullable|max:2000'],
        };
        $data = $request->validate($rules);
        if ($type === 'aisles') {
            Aisle::create(['warehouse_id'=>$data['parent_id'],'name'=>$data['name'],'code'=>$data['code'],'description'=>$data['description'] ?? null,'is_active'=>true]);
        } elseif ($type === 'racks') {
            Rack::create(['aisle_id'=>$data['parent_id'],'name'=>$data['name'],'code'=>$data['code'],'description'=>$data['description'] ?? null,'is_active'=>true]);
        } else {
            StorageRow::create(['rack_id'=>$data['parent_id'],'name'=>$data['name'],'code'=>$data['code'],'public_id'=>(string) Str::uuid(),'description'=>$data['description'] ?? null,'is_active'=>true]);
        }
        return back()->with('success','اطلاعات با موفقیت ثبت شد.');
    }

    public function update(Request $request, int $id)
    {
        $type = $this->getType();
        $model = $this->model($type);
        $item = $model::findOrFail($id);
        $rules = match ($type) {
            'aisles' => ['parent_id'=>'required|exists:warehouses,id','name'=>'required|max:150','code'=>'required|max:50','description'=>'nullable|max:2000'],
            'racks' => ['parent_id'=>'required|exists:aisles,id','name'=>'required|max:150','code'=>'required|max:50','description'=>'nullable|max:2000'],
            'rows' => ['parent_id'=>'required|exists:racks,id','name'=>'required|max:150','code'=>'required|max:70|unique:storage_rows,code,'.$id,'description'=>'nullable|max:2000'],
        };
        $data = $request->validate($rules);
        $parentField = ['aisles'=>'warehouse_id','racks'=>'aisle_id','rows'=>'rack_id'][$type];
        $item->update([$parentField=>$data['parent_id'],'name'=>$data['name'],'code'=>$data['code'],'description'=>$data['description'] ?? null]);
        return back()->with('success','اطلاعات با موفقیت ویرایش شد.');
    }

    public function toggle(int $id)
    {
        $model = $this->model($this->getType());
        $item = $model::findOrFail($id);
        $item->is_active = ! $item->is_active;
        $item->save();
        return back()->with('success','وضعیت مورد با موفقیت تغییر کرد.');
    }

    public function destroy(int $id)
    {
        $type = $this->getType();
        $model = $this->model($type);
        $item = $model::findOrFail($id);
        $hasChildren = match ($type) {
            'aisles' => $item->racks()->exists(),
            'racks' => $item->rows()->exists(),
            'rows' => $item->inventories()->exists(),
        };
        abort_if($hasChildren, 422, 'این مورد دارای زیرمجموعه یا موجودی است و امکان حذف آن وجود ندارد. ابتدا زیرمجموعه‌ها را تعیین تکلیف کنید.');
        $item->delete();
        return back()->with('success','حذف انجام شد.');
    }

    public function parents(string $type)
    {
        return match ($type) {
            'aisles' => Warehouse::where('is_active',true)->orderBy('name')->get(),
            'racks' => Aisle::with('warehouse')->where('is_active',true)->whereHas('warehouse',fn($q)=>$q->where('is_active',true))->orderBy('name')->get(),
            'rows' => Rack::with('aisle.warehouse')->where('is_active',true)->whereHas('aisle',fn($q)=>$q->where('is_active',true)->whereHas('warehouse',fn($w)=>$w->where('is_active',true)))->orderBy('name')->get(),
            default => abort(404),
        };
    }
}
