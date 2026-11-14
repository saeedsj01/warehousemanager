<?php
namespace App\Http\Controllers;

use App\Models\Warehouse;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    public function index()
    {
        return view('layouts.index', ['warehouses'=>Warehouse::orderBy('name')->get()]);
    }

    public function show(Warehouse $warehouse)
    {
        $layout = $warehouse->layout()->firstOrCreate([], ['canvas_width'=>1200,'canvas_height'=>700,'layout_data'=>[]]);
        return view('layouts.editor', compact('warehouse','layout'));
    }

    public function save(Request $request, Warehouse $warehouse)
    {
        $data = $request->validate([
            'layout_data'=>'present|array',
            'layout_data.*.id'=>'required|string|max:80',
            'layout_data.*.label'=>'required|string|max:100',
            'layout_data.*.x'=>'required|numeric|min:0|max:1200',
            'layout_data.*.y'=>'required|numeric|min:0|max:700',
            'layout_data.*.width'=>'required|numeric|min:35|max:600',
            'layout_data.*.height'=>'required|numeric|min:25|max:500',
            'layout_data.*.rotation'=>'nullable|numeric|min:-360|max:360',
            'layout_data.*.kind'=>'required|in:aisle,rack,row',
        ]);
        $warehouse->layout()->updateOrCreate([], [
            'canvas_width'=>1200,
            'canvas_height'=>700,
            'layout_data'=>$data['layout_data'],
        ]);
        return response()->json(['ok'=>true,'message'=>'نقشه ذخیره شد.']);
    }
}
