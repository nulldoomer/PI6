<?php

namespace App\Http\Controllers\Doctor;

use App\User;
use App\Models\Doctor;
use App\Models\DoctorSlot;
use Illuminate\Http\Request;
use ParagonIE\Sodium\Compat;
use App\Models\DoctorCategory;
use Mews\Purifier\Facades\Purifier;
use App\DataTables\DoctorsDatatable;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\CreateDoctorRequest;
use App\Http\Requests\DoctorScheduleRequest;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class DoctorsController extends Controller
{
    public function index(DoctorsDatatable $dataTable)
    {
        $user = User::where('role', 'doctor')->get();
        return $dataTable->render('admin.doctor.index', compact('user'));
    }
    
    public function create()
    {
        $category = DoctorCategory::all();
        return view('admin.doctor.create', compact('category'));
    }

    public function show(User $user)
    {
        $category = DoctorCategory::all();
        return view('admin.doctor.edit', compact('user', 'category'));
    }

    public function store(CreateDoctorRequest $request)
    {
        try {
            if ($request->off_day != null) {
                $offday = implode(',', Purifier::clean($request->off_day));
            }

            if (!empty($request->profile_image)) {
                $profile_image = fileUpload($request['profile_image'], path_user_image()); // upload file
            } else {
                $profile_image = null;
            }
            if (!empty($request->thumb_image)) {
                $thumb_image = fileUpload($request['thumb_image'], path_user_image()); // upload file
            } else {
                $thumb_image = null;
            }

            $user = User::create([
                'name' => Purifier::clean($request->name),
                'email' => Purifier::clean($request->email),
                'gender' => Purifier::clean($request->gender),
                'image' => $thumb_image,
                'password' => Hash::make($request->password),
                'role' => 'doctor',
            ]);

            $doctor = Doctor::create([
                'user_id' => $user->id,
                'gender' => Purifier::clean($request->gender),
                'category_id' => $request->docat,
                'fees' => $request->fees,
                'profile_image' => $profile_image,
                'thumb_image' => $thumb_image,
                'offday' => isset($offday) ? $offday : '',
            ]);

            // $slots = DoctorSlot::get();

            // foreach ($slots as $slot) {
            //     $doctor->slot()->syncWithoutDetaching($slot->id);
            // }

            session()->flash('success', __('Successfully Created'));
            Toastr::success('success', __('Successfully Created'), ["positionClass" => "toast-top-right"]);
            return redirect()->route('doctor.index');
        } catch (Exception $e) {
            session()->flash('error', __('Something went wrong'));
            return redirect()->route('doctor.index');
        }
    }

    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed'
        ]);

        if ($request->off_day != null) {
            $offday = implode(',', Purifier::clean($request->off_day));
        }

        $user = User::find($user->id);
        $doctor = Doctor::where('user_id', $user->id)->first();
        if (!empty($request->profile_image)) {
            $profile_image = fileUpload($request['profile_image'], path_user_image()); // upload file
            $doctor->update([
                'profile_image' => $profile_image,
            ]);
        }

        if (!empty($request->thumb_image)) {
            $thumb_image = fileUpload($request['thumb_image'], path_user_image()); // upload file
            $doctor->update([
                'thumb_image' => $thumb_image,
            ]);
            $user->update([
                'image' => $thumb_image,
            ]);
        }

        if ($request->password) {
            $user->update([
                'password' => Hash::make($request->password),
            ]);
        }

        $user->update([
            'name' => Purifier::clean($request->name),
            'email' => Purifier::clean($request->email),
            'gender' => Purifier::clean($request->gender),
            'role' => 'doctor',
        ]);

        $doctor->update([
            'user_id' => $user->id,
            'gender' => Purifier::clean($request->gender),
            'category_id' => $request->docat,
            'fees' => $request->fees,
            'offday' => isset($offday) ? $offday : '',
        ]);

        session()->flash('success', __('Successfully Updated'));
        Toastr::success('success', __('Successfully Updated'), ["positionClass" => "toast-top-right"]);

        return redirect()->route('doctor.index');
    }
}
