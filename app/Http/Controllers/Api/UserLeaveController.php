<?php

namespace App\Http\Controllers\Api;

use App\Models\UserLeave;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UserLeaveController extends Controller
{
    public function index()
    {
        $userLeaves = UserLeave::where('user_id', Auth::id())->get();
        return response()->json([
            'success' => true,
            'message' => 'Leave applications retrieved successfully',
            'data' => $userLeaves
        ], 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'type_id' => 'required|exists:leave_types,id',
            'other_type' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'number_of_days_off' => 'required|integer|min:1',
            'reason' => 'nullable|string',
            'mark' => 'nullable|string|max:255',
            'leave_status' => 'nullable|in:pending,approved,rejected',
            'checked_by' => 'nullable|string',
            'remark' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $leaveData = array_merge($request->all(), [
            'user_id' => Auth::id(),
        ]);

        $userLeave = UserLeave::create($leaveData);

        return response()->json($userLeave, 201);
    }

    public function show($id)
    {
        $userLeave = UserLeave::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$userLeave) {
            return response()->json(['message' => 'User leave not found'], 404);
        }

        return response()->json($userLeave, 200);
    }

    public function update(Request $request, $id)
    {
        $userLeave = UserLeave::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$userLeave) {
            return response()->json(['message' => 'User leave not found'], 404);
        }

        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'type_id' => 'nullable|exists:leave_types,id',
            'other_type' => 'nullable|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'number_of_days_off' => 'nullable|integer|min:1',
            'reason' => 'nullable|string',
            'mark' => 'nullable|string|max:255',
            'leave_status' => 'nullable|in:pending,approved,rejected',
            'checked_by' => 'nullable|string',
            'remark' => 'nullable|string',
            'status' => 'boolean',
        ]);

        $userLeave->update($request->all());

        return response()->json($userLeave, 200);
    }

    public function destroy($id)
    {
        $userLeave = UserLeave::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$userLeave) {
            return response()->json(['message' => 'User leave not found'], 404);
        }

        $userLeave->delete();

        return response()->json(['message' => 'User leave deleted successfully'], 200);
    }
}
