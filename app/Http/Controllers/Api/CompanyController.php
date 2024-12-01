<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CompanyController extends Controller
{
    public function index()
    {
        $companies = Company::all();
        return response()->json($companies);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:companies,email',
            'address' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'logo' => 'nullable|string|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $logoPath = null;
        if ($request->hasFile('logo')) {
            // If logo is an uploaded file, validate and store it
            $validator = Validator::make($request->all(), [
                'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5048',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 422);
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
        } elseif ($request->logo) {
            // If logo is a URL, directly assign it
            $logoPath = $request->logo;
        }

        $company = Company::create([
            'user_id' => $user->id,
            'country_id' => $request->country_id,
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'code' => $request->code,
            'phone_number' => $request->phone_number,
            'logo' => $logoPath,
        ]);

        return response()->json([
            'message' => 'Company created successfully',
            'data' => $company
        ], 200);
    }

    public function show($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        return response()->json($company);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:companies,id',
            'country_id' => 'nullable|exists:countries,id',
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:companies,email,' . $request->id,
            'address' => 'nullable|string|max:255',
            'code' => 'nullable|string|max:255',
            'phone_number' => 'nullable|string|max:255',
            'logo' => 'nullable|string|url',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $company = Company::find($request->id);

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // if ($company->user_id !== Auth::id()) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        $logoPath = $company->logo;

        if ($request->hasFile('logo')) {
            // If logo is an uploaded file, validate and store it
            $fileValidator = Validator::make($request->all(), [
                'logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5048',
            ]);

            if ($fileValidator->fails()) {
                return response()->json(['error' => $fileValidator->errors()], 422);
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
        } elseif ($request->logo) {
            // If logo is a URL, directly assign it
            $logoPath = $request->logo;
        }

        // Update company details
        $company->update([
            'country_id' => $request->country_id,
            'name' => $request->name,
            'email' => $request->email,
            'address' => $request->address,
            'code' => $request->code,
            'phone_number' => $request->phone_number,
            'logo' => $logoPath,
        ]);

        return response()->json([
            'message' => 'Company updated successfully',
            'data' => $company,
        ], 200);
    }

    public function destroy($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $company->delete();

        return response()->json(['message' => 'Company deleted successfully']);
    }

    public function getLeavesCount($id)
    {
        $company = Company::find($id);
        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $totalLeaves = $company->userLeaves()->count();

        return response()->json([
            'total_leaves' => $totalLeaves
        ]);
    }

    public function getAttendanceCount($id)
    {
        $company = Company::find($id);
        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        $totalAttendance = $company->attendances()->count();

        return response()->json([
            'total_attendance' => $totalAttendance
        ]);
    }

    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors(),
                'success' => false,
                'status' => 200,
            ]);
        }

        try {
            $limit = $request->input('limit', 10);
            $keyword = strtoupper($request->input('keyword'));

            // Get the authenticated user's ID
            $userId = auth()->id();

            // Search query, filtering by authenticated user's ID
            $query = Company::query();

            $query->where('user_id', $userId)
                ->where(function ($subQuery) use ($keyword) {
                    $subQuery->where(DB::raw('upper(name)'), 'like', "%{$keyword}%")
                        ->orWhere(DB::raw('upper(email)'), 'like', "%{$keyword}%")
                        ->orWhere(DB::raw('upper(address)'), 'like', "%{$keyword}%");
                });

            $companies = $query->orderBy('id', 'desc')->paginate($limit);

            // Transform data
            $transformedData = [
                'current_page' => $companies->currentPage(),
                'data' => $companies->items(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Companies retrieved successfully.',
                'data' => $transformedData,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while searching for companies.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getUsersByCompany($id)
    {
        $company = Company::find($id);

        if (!$company) {
            return response()->json(['message' => 'Company not found'], 404);
        }

        // Fetch users under the company
        $users = $company->users()->get();

        return response()->json([
            'success' => true,
            'data' => $users,
        ], 200);
    }
}
