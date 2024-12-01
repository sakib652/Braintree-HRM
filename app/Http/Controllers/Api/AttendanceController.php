<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index()
    {
        $attendances = Attendance::where('user_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->get();

        return response()->json($attendances, 200);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'branch_id' => 'nullable|exists:branches,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'photo' => 'nullable|string',
            'location' => 'nullable|string',
            'reason_of_outside' => 'nullable|string',
        ]);

        $validatedData['photo'] = $request->photo ?? null;

        $attendanceData = array_merge($validatedData, [
            'user_id' => Auth::id(),
        ]);

        $attendance = Attendance::create($attendanceData);

        return response()->json([
            'message' => 'Attendance created successfully',
            'attendance' => $attendance
        ], 200);
    }

    public function uploadPhoto(Request $request)
    {
        $validatedData = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $filePath = $request->file('photo')->store('photos', 'public');

        return response()->json([
            'message' => 'Photo uploaded successfully',
            'photo' => $filePath
        ], 200);
    }


    // public function show($id)
    // {
    //     $attendance = Attendance::where('id', $id)
    //         ->where('user_id', Auth::id())
    //         ->first();

    //     if (!$attendance) {
    //         return response()->json([
    //             'message' => 'Attendance not found or unauthorized access'
    //         ], 404);
    //     }

    //     return response()->json($attendance, 200);
    // }

    // public function update(Request $request, $id)
    // {
    //     $attendance = Attendance::where('id', $id)
    //         ->where('user_id', Auth::id())
    //         ->first();

    //     if (!$attendance) {
    //         return response()->json([
    //             'message' => 'Attendance not found or unauthorized access'
    //         ], 404);
    //     }

    //     $validatedData = $request->validate([
    //         'company_id' => 'required|exists:companies,id',
    //         'branch_id' => 'nullable|exists:branches,id',
    //         'latitude' => 'nullable|numeric|between:-90,90',
    //         'longitude' => 'nullable|numeric|between:-180,180',
    //         'photo' => 'nullable|string',
    //         'face_recognition' => 'required|in:1,2,3',
    //         'location' => 'required|in:1,2',
    //         'reason_of_outside' => 'nullable|string',
    //         'status' => 'required|in:1,2,3',
    //     ]);

    //     $attendance->update($validatedData);

    //     return response()->json([
    //         'message' => 'Attendance updated successfully',
    //         'attendance' => $attendance
    //     ], 200);
    // }

    // public function destroy($id)
    // {
    //     $attendance = Attendance::where('id', $id)
    //         ->where('user_id', Auth::id())
    //         ->first();

    //     if (!$attendance) {
    //         return response()->json([
    //             'message' => 'Attendance not found or unauthorized access'
    //         ], 404);
    //     }

    //     $attendance->delete();

    //     return response()->json([
    //         'message' => 'Attendance deleted successfully'
    //     ], 200);
    // }
}
