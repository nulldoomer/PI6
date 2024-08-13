<?php

namespace App\Http\Controllers\Patient;

use App\User;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\DataTables\PatientDatatable;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\PatientRequest;
use Mews\Purifier\Facades\Purifier;

class PatientController extends Controller
{
    public function index(PatientDatatable $dataTable)
    {
        $user = User::where('role', 'patient')->get();
        return $dataTable->render('admin.patient.index', compact('user'));
    }

    public function create()
    {
        return view('admin.patient.create');
    }

    public function store(PatientRequest $request)
    {

        $user = User::create([
            'fname' => Purifier::clean($request->first_name),
            'lname' => Purifier::clean($request->last_name),
            'name' => Purifier::clean($request->first_name) . ' ' . Purifier::clean($request->last_name),
            'email' => Purifier::clean($request->email),
            'gender' => Purifier::clean($request->gender),
            'password' => Hash::make($request->password),
            'role' => 'patient',
        ]);

        session()->flash('success', __('Successfully Created'));

        Toastr::success('success', __('Successfully Created'), ["positionClass" => "toast-top-right"]);

        return redirect()->route('patient.index');
    }

    public function show(User $user)
    {
        return view('admin.patient.edit', compact('user'));
    }

    public function update(User $user, Request $request)
    {
        if ($request->password) {
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => [
                    'required',
                    Rule::unique('users')->ignore($user->id),
                ],
                'password' => [
                    'string',
                    'min:6',
                    'confirmed',
                ],
            ]);
        } else {
            $request->validate([
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => [
                    'required',
                    Rule::unique('users')->ignore($user->id),
                ],
            ]);
        }
        $user->update([
            'fname' => Purifier::clean($request->first_name),
            'lname' => Purifier::clean($request->last_name),
            'name' => Purifier::clean($request->first_name) . ' ' . Purifier::clean($request->last_name),
            'email' => Purifier::clean($request->email),
            'gender' => Purifier::clean($request->gender),
        ]);
        if ($request->password) {
            $user->update(['password' => Hash::make($request->password),]);
        }
        session()->flash('success', __('Successfully Updated'));

        Toastr::success('success', __('Successfully Updated'), ["positionClass" => "toast-top-right"]);

        return redirect()->route('patient.index');
    }
}
