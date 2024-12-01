<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        return response()->json($leaveTypes, 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'type_name' => 'required|string|max:255',
            'max_leave_per_month' => 'required|integer|min:1',
            'total_leave' => 'required|integer|min:1',
            'status' => 'boolean',
        ]);

        $leaveType = LeaveType::create([
            'company_id' => $request->company_id,
            'type_name' => $request->type_name,
            'max_leave_per_month' => $request->max_leave_per_month,
            'total_leave' => $request->total_leave,
            'status' => $request->status ?? 1,
        ]);

        return response()->json($leaveType, 200);
    }

    public function show($id)
    {
        $leaveType = LeaveType::find($id);

        if (!$leaveType) {
            return response()->json(['message' => 'Leave type not found'], 404);
        }

        return response()->json($leaveType, 200);
    }

    public function update(Request $request, $id)
    {
        $leaveType = LeaveType::find($id);

        if (!$leaveType) {
            return response()->json(['message' => 'Leave type not found'], 404);
        }

        $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'type_name' => 'nullable|string|max:255',
            'max_leave_per_month' => 'nullable|integer|min:1',
            'total_leave' => 'nullable|integer|min:1',
            'status' => 'boolean',
        ]);

        $leaveType->update($request->all());

        return response()->json($leaveType, 200);
    }

    public function destroy($id)
    {
        $leaveType = LeaveType::find($id);

        if (!$leaveType) {
            return response()->json(['message' => 'Leave type not found'], 404);
        }

        $leaveType->delete();

        return response()->json(['message' => 'Leave type deleted successfully'], 200);
    }
}
