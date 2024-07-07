<?php

namespace App\Http\Controllers\Face;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class FaceController extends Controller
{
    public function processImage(Request $request)
    {
        $image = $request->input('image');
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = uniqid().'.jpeg';
        Storage::put($imageName, base64_decode($image));

        $output = shell_exec("python3 face_recognition_script.py encode storage/app/{$imageName}");
        $encoding = json_decode($output, true);
        
        return response()->json(['encoding' => $encoding]);
    }

    public function registerFace(Request $request)
    {
        $user = User::find($request->user_id);
        $image = $request->input('image');
        $image = str_replace('data:image/jpeg;base64,', '', $image);
        $image = str_replace(' ', '+', $image);
        $imageName = uniqid().'.jpeg';
        Storage::put($imageName, base64_decode($image));

        $output = shell_exec("python3 face_recognition_script.py encode storage/app/{$imageName}");
        $encoding = json_decode($output, true);

        if ($encoding) {
            $user->face_encoding = json_encode($encoding);
            $user->save();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'No face found']);
        }
    }
}
