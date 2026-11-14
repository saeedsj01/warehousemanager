<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        return view('users.index', [
            'items'=>User::with('roles')->latest()->paginate(20),
            'roles'=>Role::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name'=>'required|max:150','username'=>'required|max:80|unique:users,username','password'=>'required|min:8|confirmed','role'=>'required|exists:roles,name']);
        $user = User::create(['name'=>$data['name'],'username'=>$data['username'],'password'=>Hash::make($data['password']),'is_active'=>true]);
        $user->assignRole($data['role']);
        return back()->with('success','کاربر ایجاد شد.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate(['name'=>'required|max:150','username'=>'required|max:80|unique:users,username,'.$user->id,'password'=>'nullable|min:8|confirmed','role'=>'required|exists:roles,name','is_active'=>'required|boolean']);
        $user->update(['name'=>$data['name'],'username'=>$data['username'],'is_active'=>$data['is_active'],...(!empty($data['password'])?['password'=>Hash::make($data['password'])]:[])]);
        $user->syncRoles([$data['role']]);
        return back()->with('success','اطلاعات کاربر ویرایش شد.');
    }

    public function toggle(User $user)
    {
        abort_if($user->id === auth()->id(), 422, 'امکان غیرفعال کردن حساب کاربری خودتان وجود ندارد.');
        $user->is_active = ! $user->is_active;
        $user->save();
        return back()->with('success','وضعیت کاربر تغییر کرد.');
    }

    public function permissions(User $user)
    {
        return view('users.permissions', [
            'user'=>$user->load('roles','permissions'),
            'roles'=>Role::orderBy('name')->get(),
            'permissions'=>Permission::orderBy('name')->get(),
        ]);
    }

    public function savePermissions(Request $request, User $user)
    {
        $data = $request->validate([
            'role'=>'required|exists:roles,name',
            'permissions'=>'array',
            'permissions.*'=>'exists:permissions,name',
        ]);
        $user->syncRoles([$data['role']]);
        $user->syncPermissions($data['permissions'] ?? []);
        return redirect()->route('users.index')->with('success','نقش و مجوزهای اختصاصی کاربر ذخیره شد.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 422, 'امکان حذف حساب خودتان وجود ندارد.');
        $user->delete();
        return back()->with('success','کاربر حذف شد.');
    }
}
