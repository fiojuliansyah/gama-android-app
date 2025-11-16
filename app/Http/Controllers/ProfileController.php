<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    public function faceRegister()
    {
        $user = Auth::user();
        $title = 'Face Recognition';
        return view('profiles.face-register', compact('user', 'title'));
    }

    public function account()
    {
        $user = Auth::user();
        $title = 'Akun';
        return view('profiles.account', compact('user', 'title'));
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();
        $input = $request->all();

        if (isset($input['password'])) {
            $input['password'] = Hash::make($input['password']);
        } else {
            unset($input['password']);
        }

        $user->update($input);

        return redirect()->route('setting')
            ->with('success', 'Profil ' . $user->name . ' berhasil diperbarui');
    }

    public function profile()
    {
        $user = Auth::user();
        $title = 'Profilku';
        return view('profiles.profile', compact('user', 'title'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile()->first();
        $data = [];

        try {
            if ($request->hasFile('avatar')) {
                $imageFile = $request->file('avatar');
                $image = Image::make($imageFile)->encode('jpg', 75);
                $tempFile = tempnam(sys_get_temp_dir(), 'avatar_');
                file_put_contents($tempFile, $image->__toString());

                $storageOption = $request->input('storage_option', 'local');

                if ($storageOption === 's3') {
                    $path = Storage::disk('s3')->putFile('avatars', $tempFile);
                    $url = Storage::disk('s3')->url($path);
                } else {
                    $path = Storage::disk('public')->putFile('avatars', $tempFile);
                    $url = asset("storage/{$path}");
                }

                unlink($tempFile);
                $data['avatar_url'] = $url;
            } elseif ($request->filled('face_image_data')) {
                $base64_str = $request->face_image_data;

                if (strpos($base64_str, ';base64,') !== false) {
                    $base64_str = explode(';base64,', $base64_str)[1];
                } elseif (strpos($base64_str, ',') !== false) {
                    $base64_str = explode(',', $base64_str)[1];
                }

                if (base64_encode(base64_decode($base64_str, true)) === $base64_str) {
                    $imageData = base64_decode($base64_str);
                    $tempFile = tempnam(sys_get_temp_dir(), 'face_') . '.jpg';
                    file_put_contents($tempFile, $imageData);

                    $storageOption = $request->input('storage_option', 'local');

                    if ($storageOption === 's3') {
                        $path = Storage::disk('s3')->putFile('avatars', $tempFile);
                        $url = Storage::disk('s3')->url($path);
                    } else {
                        $path = Storage::disk('public')->putFile('avatars', $tempFile);
                        $url = asset("storage/{$path}");
                    }

                    unlink($tempFile);
                    $data['avatar_url'] = $url;
                }
            }
        } catch (\Exception $e) {
            Log::error('Error uploading image', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'trace' => $e->getTraceAsString()
            ]);
        }

        if ($request->filled('face_id')) {
            $data['face_id'] = (string) Str::uuid();
            $data['face_descriptor'] = $request->face_id;
        }

        $data += [
            'address' => $request->address,
            'gender' => $request->gender,
            'birth_place' => $request->birth_place,
            'birth_date' => $request->birth_date,
            'mother_name' => $request->mother_name,
            'npwp_number' => $request->npwp_number,
            'marriage_status' => $request->marriage_status,
        ];

        if ($profile) {
            if (isset($data['face_id']) && !empty($profile->face_id)) {
                $profile->update(['face_id' => null]);
            }
            $profile->update($data);
        } else {
            $user->profile()->create($data);
        }

        return redirect()->route('setting')
            ->with('success', 'Profil ' . $user->name . ' berhasil diperbarui');
    }

    public function bank()
    {
        $user = Auth::user();
        $title = 'Bank';
        return view('profiles.bank', compact('user', 'title'));
    }

    public function updateBank(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile()->first();
        $data = [
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
        ];

        if ($profile) {
            $profile->update($data);
        } else {
            $user->profile()->create($data);
        }

        return redirect()->route('setting')
            ->with('success', 'Bank ' . $user->name . ' berhasil diperbarui');
    }

    public function esign()
    {
        $user = Auth::user();
        $title = 'E-Sign';
        return view('profiles.esign', compact('user', 'title'));
    }

    public function updateEsign(Request $request)
    {
        $user = Auth::user();
        $profile = $user->profile()->first();

        if ($request->esign) {
            $imageData = $request->input('esign');
            list($type, $imageData) = explode(';', $imageData);
            list(, $imageData) = explode(',', $imageData);
            $imageData = base64_decode($imageData);
            $tempFile = tempnam(sys_get_temp_dir(), 'esign_') . '.png';
            file_put_contents($tempFile, $imageData);

            $storageOption = $request->input('storage_option', 'local');

            if ($storageOption === 's3') {
                $path = Storage::disk('s3')->putFile('esign_images', $tempFile);
                $esignUrl = Storage::disk('s3')->url($path);
            } else {
                $path = Storage::disk('public')->putFile('esign_images', $tempFile);
                $esignUrl = asset("storage/{$path}");
            }

            unlink($tempFile);

            if ($profile) {
                $profile->esign_url = $esignUrl;
                $profile->save();
            } else {
                $user->profile()->create([
                    'esign_url' => $esignUrl
                ]);
            }
        }

        return redirect()->route('setting')
            ->with('success', 'Tanda Tangan ' . $user->name . ' berhasil diperbarui');
    }
}
