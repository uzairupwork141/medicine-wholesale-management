<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index() { $users=User::orderBy('name')->get(); return view('users.index',compact('users')); }
    public function create() { return view('users.create'); }
    public function edit($id) { return view('users.create',['user'=>User::findOrFail($id)]); }

    public function store(Request $request)
    {
        $data=$request->validate(['name'=>['required','string','max:100'],'email'=>['required','email','max:255','unique:users,email'],'password'=>['required','string','min:8'],'role'=>['required',Rule::in(['admin','seller'])],'is_active'=>['required','boolean']]);
        $data['password']=Hash::make($data['password']);
        User::create($data);
        return redirect()->route('users.index')->with('success','User created successfully.');
    }

    public function update(Request $request,$id)
    {
        $user=User::findOrFail($id);
        $data=$request->validate(['name'=>['required','string','max:100'],'email'=>['required','email','max:255','unique:users,email,'.$user->id],'role'=>['required',Rule::in(['admin','seller'])],'is_active'=>['required','boolean'],'password'=>['nullable','string','min:8']]);
        if($user->id===$request->user()->id && ($data['role']!=='admin' || !$data['is_active'])) return back()->withErrors(['role'=>'You cannot remove your own admin access or deactivate your own account.'])->withInput();
        if($user->role==='admin' && ($data['role']!=='admin' || !$data['is_active']) && User::where('role','admin')->where('is_active',true)->where('id','!=',$user->id)->count()===0) return back()->withErrors(['role'=>'At least one active administrator must remain.'])->withInput();
        if(!empty($data['password'])) $data['password']=Hash::make($data['password']); else unset($data['password']);
        $user->update($data);
        return redirect()->route('users.index')->with('success','User updated successfully.');
    }

    public function destroy(Request $request, $id)
    {
        $user=User::findOrFail($id);
        if($user->id===$request->user()->id) return back()->with('error','You cannot delete your own account.');
        if($user->role==='admin' && User::where('role','admin')->where('is_active',true)->where('id','!=',$user->id)->count()===0) return back()->with('error','The last active administrator cannot be deleted.');
        $user->delete();
        return redirect()->route('users.index')->with('success','User deleted.');
    }
}
