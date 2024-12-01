<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        return response()->json($countries, 200);
    }

    public function show($id)
    {
        $country = Country::find($id);

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        return response()->json($country, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_name' => 'required|string',
            'time_format' => 'nullable|string',
            'currency_format' => 'nullable|string',
            'currency_code' => 'nullable|string',
            'mobile_code' => 'nullable|string',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $country = Country::create($request->all());

        return response()->json($country, 200);
    }

    // public function store(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'countries' => 'required|array',
    //         'countries.*.country_name' => 'required|string',
    //         'countries.*.time_format' => 'nullable|string',
    //         'countries.*.currency_format' => 'nullable|string',
    //         'countries.*.currency_code' => 'nullable|string',
    //         'countries.*.mobile_code' => 'nullable|string',
    //         'countries.*.status' => 'boolean',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json($validator->errors(), 400);
    //     }

    //     $countries = Country::insert($request->input('countries'));

    //     return response()->json(['message' => 'Countries added successfully'], 201);
    // }


    public function update(Request $request, $id)
    {
        $country = Country::find($id);

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'country_name' => 'nullable|string',
            'time_format' => 'nullable|string',
            'currency_format' => 'nullable|string',
            'currency_code' => 'nullable|string',
            'mobile_code' => 'nullable|string',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $country->update($request->all());

        return response()->json($country, 200);
    }

    public function destroy($id)
    {
        $country = Country::find($id);

        if (!$country) {
            return response()->json(['message' => 'Country not found'], 404);
        }

        $country->delete();

        return response()->json(['message' => 'Country deleted successfully'], 200);
    }
}
