<?php

namespace App\Http\Controllers;

use App\Models\CustomerMatching;
use App\Models\CustomerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;

class CustomerMatchingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Retrieve all matching records
        $matchingList = CustomerMatching::all();
    
        // Initialize an array to hold the results
        $data = [];
    
        // Loop through each matching record
        foreach ($matchingList as $matchingl) {
            // Retrieve the sender profile (user_id)
            $senderProfile = CustomerProfile::where('user_id', $matchingl->user_id)->first();
    
            // Retrieve the receiver profile (matchingUser)
            $receiverProfile = CustomerProfile::where('uuid', $matchingl->matchingUser)->first();
    
            // Check if both profiles exist
            if ($senderProfile && $receiverProfile) {
                // Extract profile data for sender and receiver
                $data1 = extractProfileData($senderProfile);  // Use the global function here
                $data2 = extractProfileData($receiverProfile); // Use the global function here
    
                // Add the matching record and associated profiles to the result set
                $data[] = [
                    'matching_details' => $matchingl,
                    'sender' => $data1,
                    'receiver' => $data2
                ];
            }
        }
    
        // Check if any data was retrieved
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No matching profiles found',
            ], 404);
        }
    
        // Return the data as a JSON response
        return response()->json([
            'success' => true,
            'message' => 'Matching profiles retrieved successfully',
            'data' => $data,
        ], 200);
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
                    'matchingUser' => 'required',
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


                $matchinglist = CustomerMatching::create([
                    'uuid' => $uuid,
                    'user_id' => Auth::user()->id,
                    'matchingUser' => $request->matchingUser,
                    'acceptBysender' => 1,
                    'acceptByreceiver' => 0,
                    'approveByAdmin' => 0,
                    'status' => 1,


                ]);

                if ($matchinglist) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Added as Shortlisted',
                        'data' => $matchinglist
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
