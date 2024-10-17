<?php

namespace App\Http\Controllers;

use App\Models\CustomerShortList;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;

class CustomerShortListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $shortlist = CustomerShortList::pluck('shortlistedUsers'); 

        $profilelist = CustomerProfile::whereIn('uuid', $shortlist)->get();

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
    public function store(Request $request)
    {
        //
        try {

            $validate = Validator::make(
                $request->all(),
                [
                    'shortlistedUsers' => 'required',
                ],

                []


            );

            if ($validate->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors' => $validate->errors()
                ], 422);
            }

            if (Auth::check()) {

                $uuid = Str::uuid()->toString();


                $shortlist = CustomerShortList::create([
                    'uuid' => $uuid,
                    'user_id' => Auth::user()->id,
                    'shortlistedUsers' => $request->shortlistedUsers,
                    'status' => 1,


                ]);

                if ($shortlist) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Added as Shortlisted',
                        'data' => $shortlist
                    ], 202);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Some thing went worng',
                    ], 500);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
