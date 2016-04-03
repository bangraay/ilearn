<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Announcement;
use App\Models\Classroom;
use App\Models\Major;
use App\Models\Subject;
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'DESC')->paginate(5);
        $majors = Major::orderBy('created_at', 'DESC')->paginate(5);
        $subjects = Subject::orderBy('created_at', 'DESC')->paginate(5);
        $classrooms = Classroom::orderBy('created_at', 'DESC')->paginate(5);
        $announcements = Announcement::orderBy('created_at', 'DESC')->paginate(5);
        return view('admin.home.home', compact('users', 'majors', 'subjects', 'classrooms', 'announcements'));

        return view('auth.login');
    }

    public function profile()
    {
        $user = User::findOrFail(Auth::user()->id);
        $page_title = 'Profile';

        return view('admin.home.profile', compact('user', 'page_title'));
    }

    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::user()->id);

        $this->validate($request, [
            'username' => 'required|unique:users,username,' . $user->id,
            'firstname' => 'required',
            'lastname' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ], [
            'required' => 'Kolom :attribute diperlukan!',
            'unique' => 'Kolom :attribute sudah dipakai!',
            'email' => 'Kolom :attribute harus berupa email!'
        ]);

        $user->update($request->all());

        $user->usermeta()->update([
            'tempatlahir' => $request->tempatlahir,
            'tanggallahir' => $request->tanggallahir,
            'alamat' => $request->alamat,
            'telp' => $request->telp
        ]);

        \Flash::success('Profil berhasil diperbaharui.');
        return redirect()->back();
    }

    public function passwordupdate(Request $request, $id)
    {
        $this->validate($request, [
            'password' => 'required|confirmed|min:6',
            'password_confirmation' => 'required'
        ], [
            'required' => 'Kolom :attribute diperlukan!',
            'confirmed' => 'Kolom :attribute tidak cocok!'
        ]);

        $user = User::findOrFail($id);
        if( $request->has('password') ) {
            $user->update(['password' => bcrypt($request->password)]);
        }

        \Flash::success('Password berhasil diperbaharui.');
        return redirect()->back();
    }

    public function changeimage(Request $request)
    {
        $this->validate($request,[
            'field' => 'required',
            'image' => 'required|mimes:jpeg,bmp,png'
        ], [
            'required' => 'Kolom :attribute diperlukan!',
            'mimes' => 'Format file harus *.jpg, *.png *.bmp'
        ]);

        $data = [];

        if($request->hasFile('image')){
            $data[$request->field] = $this->upload($request->image);
            $user->usermeta()->update($data);
        }
        
    }

    public function upload(UploadedFile $image)
    {
        $timestamp = str_replace([' ', ':'], '-', Carbon::now()->toDateTimeString());
        $fileName = $timestamp . '-' . $file->getClientOriginalName();
        $destination = public_path() . DIRECTORY_SEPARATOR . 'img';
        $image->move($destination, $fileName);

        return $fileName;
    }
}
