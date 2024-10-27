<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;
use Illuminate\Support\Facades\DB;


class CustomerProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profilelist = CustomerProfile::orderBy('updated_at', 'asc')->get();

        if ($profilelist->isNotEmpty()) {
            $data = $profilelist->map(function ($profile) {
                return adminProfileData($profile);
            });
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No data found',
            ], 404);
        }
    }

    public function annonindex(Request $request)
    {
        $profilelist = CustomerProfile::where('is_verified', '1')
            ->where('status', '1')
            ->orderBy('updated_at', 'asc')
            ->paginate(10);

        if ($profilelist->isNotEmpty()) {
            $data = $profilelist->map(function ($profile) {
                return extractProfileData($profile);
            });


            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $data,
                'pagination' => [
                    'current_page' => $profilelist->currentPage(),
                    'last_page' => $profilelist->lastPage(),
                    'total_items' => $profilelist->total(),
                    'items_per_page' => $profilelist->perPage(),
                    'current_page_url' => $request->url() . '?page=' . $profilelist->currentPage(), // Construct the current page URL
                    'next_page_url' => $profilelist->nextPageUrl(),
                    'previous_page_url' => $profilelist->previousPageUrl(),
                ],
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No data found',
            ], 404);
        }
    }

    public function annonfilterindex(Request $request)
    {
        // Start building the query
        $query = CustomerProfile::query();

        // Define possible filters
        $gender = $request->gender;
        $religion = $request->religion;
        $isVerified = '1';
        $status = '1';

        // Apply filters dynamically
        if (!empty($gender)) {
            // Use CAST to treat personal_infos as JSON for gender filtering
            $query->whereRaw("CAST(personal_infos AS json)->>'gender' = ?", [$gender]);
        }

        if (!empty($religion)) {
            // Use CAST to treat religious_infos as JSON for religion filtering
            $query->whereRaw("CAST(religious_infos AS json)->>'religion' = ?", [$religion]);
        }

        // Apply static filters
        $query->where('is_verified', $isVerified);
        $query->where('status', $status);

        // Handle age filtering with min and max range
        $ageMin = $request->min_age;
        $ageMax = $request->max_age;
        if (!empty($ageMin) || !empty($ageMax)) {
            if ($ageMin && $ageMax) {
                $query->whereRaw("CAST(personal_infos AS json)->>'age' BETWEEN ? AND ?", [$ageMin, $ageMax]);
            } elseif ($ageMin) {
                $query->whereRaw("CAST(personal_infos AS json)->>'age' >= ?", [$ageMin]);
            } elseif ($ageMax) {
                $query->whereRaw("CAST(personal_infos AS json)->>'age' <= ?", [$ageMax]);
            }
        }

        // Paginate the filtered results
        $profilelist = $query->paginate(10);

        // Check if there are no results
        if ($profilelist->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'No Matched Profile Found',
                'data' => [],
            ], 200);
        }

        // Transform the data using a custom function
        $data = $profilelist->map(function ($profile) {
            return extractProfileData($profile); // Ensure extractProfileData function is defined
        });

        // Return response with pagination info
        return response()->json([
            'success' => true,
            'message' => 'Data retrieved successfully',
            'data' => $data,
            'pagination' => [
                'current_page' => $profilelist->currentPage(),
                'last_page' => $profilelist->lastPage(),
                'total_items' => $profilelist->total(),
                'items_per_page' => $profilelist->perPage(),
                'current_page_url' => $request->url() . '?page=' . $profilelist->currentPage(),
                'next_page_url' => $profilelist->nextPageUrl(),
                'previous_page_url' => $profilelist->previousPageUrl(),
            ],
        ], 200);
    }





    public function shortindex()
    {
        //
        $profilelist = CustomerProfile::where('is_verified', '1')->where('status', '1')->orderBy('updated_at', 'asc')->get();
        if ($profilelist->isNotEmpty()) {
            $data = $profilelist->map(function ($profile) {
                return extractProfileData($profile);  // Use the global function here
            });

            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $data,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No data found',
            ], 404);
        }
    }
    public function prefferedindex()
    {
        //
        $authuser = Auth::user();



        $request1 = CustomerProfile::select('preferance_infos', 'personal_infos')->where('user_id', '=', $authuser->id)->get();
        $request = json_decode($request1[0]['preferance_infos']);
        $gender = json_decode($request1[0]['personal_infos']);
        $minAge = $request->age_range->min_age;
        $maxAge = $request->age_range->max_age;
        if ($gender->gender === 'male') {
            $gender = 'female';
        } else {
            $gender = 'male';
        }

        ////////////////////

        $filtered = CustomerProfile::where('personal_infos->gender', $gender)
            ->whereBetween('personal_infos->age', ['min' => $minAge, 'max' => $maxAge])
            ->where('educational_infos->education_level', '>=', $request->minimum_education_level)
            ->where('employment_infos->salary', '>=', $request->minimum_salary)
            ->where('marital_infos->marital_status', $request->marital_status,)
            ->where('personal_infos->district', $request->home_district)
            ->where('personal_infos->lives_in', $request->lives_in)
            ->where('religious_infos->religion', $request->religion)
            ->where('religious_infos->sect', $request->sect)
            ->where('religious_infos->pray_5_times', $request->pray_5_times)
            ->where('religious_infos->wear_burka', $request->wear_burka)
            ->where('religious_infos->recit_quran', $request->recit_quran)
            ->where('religious_infos->read_quaran_daily',  $request->read_quaran_daily)
            ->where('religious_infos->follow_sharia_rule',  $request->follow_sharia_rule)
            ->where('is_verified', '0')
            ->where('status', '0')
            ->get();


        ///////////////////

        // $query = CustomerProfile::query();

        // return response()->json([
        //     'success' => true,
        //     'message' => 'Data retrieved successfully',
        //     'data' => $filtered,
        // ], 200);

        // $filters = [
        //     'personal_infos->gender' => $gender,
        //     'personal_infos->age' => ['min' => $minAge, 'max' => $maxAge],
        //     'educational_infos->education_level' => $request->minimum_education_level,
        //     'employment_infos->salary'  => $request->minimum_salary,
        //     'marital_infos->marital_status'  => $request->marital_status,
        //     'personal_infos->district' => $request->home_district,
        //     'personal_infos->lives_in' => $request->lives_in,
        //     'religious_infos->religion' => $request->religion,
        //     'religious_infos->sect' => $request->sect,
        //     'religious_infos->pray_5_times' => $request->pray_5_times,
        //     'religious_infos->wear_burka' => $request->wear_burka,
        //     'religious_infos->recit_quran' => $request->recit_quran,
        //     'religious_infos->read_quaran_daily' => $request->read_quaran_daily,
        //     'religious_infos->follow_sharia_rule' => $request->follow_sharia_rule,
        // ];
        // // dd($filters);
        // // Apply filters dynamically
        // foreach ($filters as $field => $value) {
        //     // For direct value filters like gender, height, education_level, etc.
        //     if (is_string($value) && !empty($value)) {
        //         $query->where($field, $value);

        //     }

        //     // For range filters like age
        //     if (is_array($value)) {
        //         if (!empty($value['min']) && !empty($value['max'])) {
        //             $query->whereBetween($field, [$value['min'], $value['max']]);
        //         } elseif (!empty($value['min'])) {
        //             $query->where($field, '>=', $value['min']);
        //         } elseif (!empty($value['max'])) {
        //             $query->where($field, '<=', $value['max']);
        //         }

        //         if (!empty($value['salary'])) {
        //             $query->where($field, '>=', $value['salary']);
        //         }
        //     }
        // }

        // // Get the filtered results
        // $profilelist = $query->get();

        $data = $filtered->map(function ($profile) {
            return extractProfileData($profile);
        });

        if (is_null($filtered) || $filtered->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => 'No Matched Profile Found',
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $data,
            ], 200);
        }
    }
    public function customerfilteedindex(Request $request)
    {
        //
        $query = CustomerProfile::query();
        // Define possible filters
        $filters = [
            'personal_infos->gender' => $request->gender,
            'personal_infos->age' => ['min' => $request->min_age, 'max' => $request->max_age],
            'educational_infos->education_level' => $request->education_level,
            'employment_infos->salary'  => $request->salary,
            'marital_infos->marital_status'  => $request->marital_status,
            'personal_infos->district' => $request->home_district,
            'personal_infos->lives_in' => $request->lives_in,
            'religious_infos->religion' => $request->religion,
            'religious_infos->sect' => $request->sect,
            'religious_infos->pray_5_times' => $request->pray_5_times,
            'religious_infos->wear_burka' => $request->wear_burka,
            'religious_infos->recit_quran' => $request->recit_quran,
            'religious_infos->read_quaran_daily' => $request->read_quaran_daily,
            'religious_infos->follow_sharia_rule' => $request->follow_sharia_rule,
            'is_verified' => '1',
            'status' => '1'
        ];

        // Apply filters dynamically
        foreach ($filters as $field => $value) {
            // For direct value filters like gender, height, education_level, etc.
            if (is_string($value) && !empty($value)) {
                $query->where($field, $value);
            }

            // For range filters like age
            if (is_array($value)) {
                if (!empty($value['min']) && !empty($value['max'])) {
                    $query->whereBetween($field, [$value['min'], $value['max']]);
                } elseif (!empty($value['min'])) {
                    $query->where($field, '>=', $value['min']);
                } elseif (!empty($value['max'])) {
                    $query->where($field, '<=', $value['max']);
                }

                if (!empty($value['salary'])) {
                    $query->where($field, '>=', $value['salary']);
                }
            }
        }

        // Get the filtered results
        $profilelist = $query->get();

        $data = $profilelist->map(function ($profile) {
            return extractProfileData($profile);
        });

        if (is_null($profilelist) || $profilelist->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => 'No Matched Profile Found',
            ], 404);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $data,
            ], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function customerstore(Request $request)
    // {
    //     //
    //     try {

    //         $validate = Validator::make(
    //             $request->all(),
    //             [

    //             ]
    //         );
    //         if ($validate->fails()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Validation error',
    //                 'errors' => $validate->errors()
    //             ], 422);
    //         }

    //         if (Auth::check()) {
    //             if (CustomerProfile::where('user_id', Auth::user()->id)->exists()) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'User already has a customer profile',
    //                 ], 409); // 409 Conflict if the profile already exists
    //             }
    //             else{
    //             $uuid = Str::uuid()->toString();
    //             $Crprofile = CustomerProfile::create([
    //                 'uuid' => $uuid,
    //                 'user_id' => Auth::user()->id,
    //                 'personal_infos' => json_encode($request->personal_infos),
    //                 'demographic_infos' => json_encode($request->demographic_infos),
    //                 'educational_infos' => json_encode($request->educational_infos),
    //                 'employment_infos' => json_encode($request->employment_infos),
    //                 'marital_infos' => json_encode($request->marital_infos),
    //                 'referees_infos' => json_encode($request->referees_infos),
    //                 'religious_infos' => json_encode($request->religious_info),
    //                 'is_verified' => 0,
    //                 'created_by' => 'SELF',
    //                 'status' => 0,
    //             ]);

    //             if ($Crprofile) {
    //                 return response()->json([
    //                     'success' => true,
    //                     'message' => 'Profile created Successfully',
    //                     'data' => $Crprofile
    //                 ], 202);
    //             } else {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Some thing went worng',
    //                 ], 500);
    //             }
    //             }
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $th->getMessage()
    //         ], 500);
    //     }
    // }

    // public function adminstore(Request $request)
    // {
    //     //
    //     try {

    //         $validate = Validator::make(
    //             $request->all(),
    //             [
    //                 'user_id' => 'integer|unique:customer_profiles',
    //             ]
    //         );
    //         if ($validate->fails()) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Validation error',
    //                 'errors' => $validate->errors()
    //             ], 422);
    //         }

    //         if (Auth::check()) {
    //             $uuid = Str::uuid()->toString();
    //             $Crprofile = CustomerProfile::create([
    //                 'uuid' => $uuid,
    //                 'user_id' => $request->user_id,
    //                 'personal_infos' => json_encode($request->personal_infos),
    //                 'demographic_infos' => json_encode($request->demographic_infos),
    //                 'educational_infos' => json_encode($request->educational_infos),
    //                 'employment_infos' => json_encode($request->employment_infos),
    //                 'marital_infos' => json_encode($request->marital_infos),
    //                 'referees_infos' => json_encode($request->referees_infos),
    //                 'religious_infos' => json_encode($request->religious_info),
    //                 'is_verified' => 1,
    //                 'created_by' => 'ADMIN',
    //                 'status' => 1,
    //             ]);

    //             if ($Crprofile) {
    //                 return response()->json([
    //                     'success' => true,
    //                     'message' => 'Profile created Successfully',
    //                     'data' => $Crprofile
    //                 ], 202);
    //             } else {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Some thing went worng',
    //                 ], 500);
    //             }
    //         }
    //     } catch (\Throwable $th) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $th->getMessage()
    //         ], 500);
    //     }
    // }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    public function customerselfshow()
    {
        //
        $customerselfprofile = CustomerProfile::where('user_id', '=', Auth::user()->id)->get();
        if ($customerselfprofile) {
            return response()->json([
                'success' => true,
                'message' => 'Has Profile',
                'data' => $customerselfprofile
            ], 202);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No Profile',
            ], 500);
        }
    }

    public function adminsingleshow(string $id)
    {
        //
        $customerselfprofile = CustomerProfile::where('uuid', '=', $id)->get();
        if ($customerselfprofile) {
            return response()->json([
                'success' => true,
                'message' => 'Has Profile',
                'data' => $customerselfprofile
            ], 202);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No Profile',
            ], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function adminupdate(Request $request, string $id)
    {

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $profile = CustomerProfile::where('uuid', $id)->firstOrFail();
            $profile->update($request->all());

            return response()->json(['success' => true, 'message' => 'Profile updated successfully', 'data' => $profile], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function customerupdate(Request $request)
    {

        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            $user_id  = Auth::user()->id;
            $profile = CustomerProfile::where('user_id', $user_id)->firstOrFail();
            $profile->update($request->all());

            return response()->json(['success' => true, 'message' => 'Profile updated successfully', 'data' => $profile], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function admininactive(Request $request, string $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            // Find the profile by UUID
            $profile = CustomerProfile::where('uuid', $id)->firstOrFail();

            // Update only the status field
            $profile->update(['status' => $request->status]);

            return response()->json(['success' => true, 'message' => 'Status updated successfully', 'data' => $profile->status], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function adminverified(Request $request, string $id)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        try {
            // Find the profile by UUID
            $profile = CustomerProfile::where('uuid', $id)->firstOrFail();

            // Update only the status field
            $profile->update(['is_verified' => $request->is_verified]);

            return response()->json(['success' => true, 'message' => 'Verification Status updated successfully', 'data' => $profile->status], 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
