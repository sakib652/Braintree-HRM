<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Company;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function sendApprovalRequest(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'company_id' => 'required|exists:companies,id',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $company = Company::find($request->company_id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.',
            ], 404);
        }

        $notification = Notification::create([
            'user_id' => $user->id,
            'company_id' => $request->company_id,
            'type' => 'ApproveCompany',
            'message' => 'Please approve the company request for company ID: ' . $company->id,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Approval request sent successfully.',
            'data' => $notification,
        ], 200);
    }

    public function approveRequest(Request $request)
    {
        $request->validate([
            'notification_id' => 'required|exists:notifications,id',
        ]);

        // Step 1: Retrieve the notification by its ID
        $notification = Notification::find($request->notification_id);

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found.',
            ], 404);
        }

        if ($notification->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Notification has already been approved.',
            ], 400);
        }

        // Step 4: Fetch the company associated with the notification
        $company = Company::find($notification->company_id);

        if (!$company) {
            return response()->json([
                'success' => false,
                'message' => 'Company not found.',
            ], 404);
        }

        // Step 5: Create an approval record in the approvals table
        \App\Models\Approval::create([
            'user_id' => $notification->user_id,
            'company_id' => $company->id,
        ]);

        // Step 6: Mark the notification as approved
        $notification->update(['is_approved' => true]);

        // Step 7: Return a success response
        return response()->json([
            'success' => true,
            'message' => 'Request approved successfully.',
        ], 200);
    }

    public function getUserCompanyDetails($user_id)
    {
        try {
            // Validate if the user exists
            $user = User::find($user_id);

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.',
                ], 404);
            }

            // Fetch companies and notification IDs related to the user
            $companies = Company::join('notifications', 'companies.id', '=', 'notifications.company_id')
                ->where('notifications.user_id', $user_id)
                ->select('companies.*', 'notifications.id as notification_id')
                ->get();

            if ($companies->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No company details found for the given user.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Company details retrieved successfully.',
                'data' => $companies,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
