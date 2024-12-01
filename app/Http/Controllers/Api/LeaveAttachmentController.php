<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\LeaveAttachment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class LeaveAttachmentController extends Controller
{
    public function uploadAttachment(Request $request)
    {
        $validatedData = $request->validate([
            'attachment_file' => 'required|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
        ]);

        // Store the uploaded file
        $filePath = $request->file('attachment_file')->store('leave_attachments', 'public');

        return response()->json([
            'message' => 'Attachment uploaded successfully',
            'file_path' => $filePath
        ], 201);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_leaves_id' => 'required|exists:user_leaves,id',
            'attachment_title' => 'required|string|max:255',
            'attachment_file_name' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $leaveAttachmentData = array_merge($validatedData, [
            'status' => $request->status ?? 1,
        ]);

        $leaveAttachment = LeaveAttachment::create($leaveAttachmentData);

        return response()->json([
            'message' => 'Leave attachment created successfully',
            'leaveAttachment' => $leaveAttachment,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $leaveAttachment = LeaveAttachment::find($id);

        if (!$leaveAttachment) {
            return response()->json(['message' => 'Leave attachment not found'], 404);
        }

        $validatedData = $request->validate([
            'user_leaves_id' => 'nullable|exists:user_leaves,id',
            'attachment_title' => 'nullable|string|max:255',
            'attachment_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,doc,docx|max:2048',
            'status' => 'nullable|boolean',
        ]);

        // Update the attachment title if provided
        if ($request->has('attachment_title')) {
            $leaveAttachment->attachment_title = $request->attachment_title;
        }

        // Handle file upload if a new file is provided
        if ($request->hasFile('attachment_file')) {
            // Delete the old file if it exists
            if (Storage::disk('public')->exists($leaveAttachment->attachment_file_name)) {
                Storage::disk('public')->delete($leaveAttachment->attachment_file_name);
            }

            // Store the new file
            $filePath = $request->file('attachment_file')->store('leave_attachments', 'public');
            $leaveAttachment->attachment_file_name = $filePath;
        }

        // Update status if provided
        if ($request->has('status')) {
            $leaveAttachment->status = $request->status;
        }

        $leaveAttachment->save();

        return response()->json([
            'message' => 'Leave attachment updated successfully',
            'leaveAttachment' => $leaveAttachment,
        ], 200);
    }
}
