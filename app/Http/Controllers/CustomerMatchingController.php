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
        foreach ($matchingList as $matchingl) {
            $senderProfile = CustomerProfile::where('user_id', $matchingl->user_id)->first();
            $receiverProfile = CustomerProfile::where('user_id', $matchingl->matchingUser)->first();
    
            if ($senderProfile && $receiverProfile) {
                $data1 = extractProfileData($senderProfile);  
                $data2 = extractProfileData($receiverProfile); 
                $data[] = [
                    'matching_details' => $matchingl,
                    'sender' => $data1,
                    'receiver' => $data2
                ];
            }
        }
    
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No matching profiles found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Matching profiles retrieved successfully',
            'data' => $data,
        ], 200);
    }

    public function customerSelfindex()
    {
        // Retrieve all matching records
        $matchingList = CustomerMatching::where('user_id', Auth::user()->id)->get();
    
        // Initialize an array to hold the results
        $data = [];
        foreach ($matchingList as $matchingl) {
            // $senderProfile = CustomerProfile::where('user_id', $matchingl->user_id)->first();
            $receiverProfile = CustomerProfile::where('user_id', $matchingl->matchingUser)->first();
    
            if ($receiverProfile) {
                // $data1 = extractProfileData($senderProfile); 
                if($matchingl->approveByAdmin == '1')
                {
                    $data2 = extractProfilecustomerfullData($receiverProfile); 
                }
                else
                {
                    $data2 = extractProfileData($receiverProfile); 
                }
                
                $data[] = [
                    'matching_details' => $matchingl,
                    // 'sender' => $data1,
                    'receiver' => $data2
                ];
            }
        }
    
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No matching profiles found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Matching profiles retrieved successfully',
            'data' => $data,
        ], 200);
    }

    public function customerSelfRequestindex()
    {
        // Retrieve all matching records
        $matchingList = CustomerMatching::where('matchingUser', Auth::user()->id)->get();
    
        // Initialize an array to hold the results
        $data = [];
        foreach ($matchingList as $matchingl) {
            // $senderProfile = CustomerProfile::where('user_id', $matchingl->user_id)->first();
            $receiverProfile = CustomerProfile::where('user_id', $matchingl->user_id)->first();
    
            if ($receiverProfile) {
                // $data1 = extractProfileData($senderProfile); 
                if($matchingl->approveByAdmin == '1')
                {
                    $data2 = extractProfilecustomerfullData($receiverProfile); 
                }
                else
                {
                    $data2 = extractProfileData($receiverProfile); 
                }
                
                $data[] = [
                    'matching_details' => $matchingl,
                    // 'sender' => $data1,
                    'receiver' => $data2
                ];
            }
        }
    
        if (empty($data)) {
            return response()->json([
                'success' => false,
                'message' => 'No matching profiles found',
            ], 404);
        }

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
                    'matchingUser' => $request->user_id,
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
    public function adminAppreoval(Request $request, string $id)
    {
        //
        try {
            $validate = Validator::make(
                $request->all(),
                [
                    'approveByAdmin' => 'required',
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
                // Check if the record exists
                $matchinglist = CustomerMatching::where('uuid', $id)->first();
        
                if ($matchinglist) {
                    // Update the record
                    $matchinglist->update([
                        'approveByAdmin' => $request->input('approveByAdmin', $matchinglist->approveByAdmin),
                    ]);
        
                    return response()->json([
                        'success' => true,
                        'message' => 'Record updated successfully',
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record not found',
                    ], 404);
                }
            }
        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function receiverAppreoval(Request $request, string $id)
    {
        //
        try {
            $validate = Validator::make(
                $request->all(),
                [
                    'acceptByreceiver' => 'required',
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
                // Check if the record exists
                $matchinglist = CustomerMatching::where('uuid', $id)
                    ->first();
        
                if ($matchinglist) {
                    // Update the record
                    $matchinglist->update([
                        'acceptByreceiver' => $request->input('acceptByreceiver', $matchinglist->acceptByreceiver),
                    ]);
        
                    return response()->json([
                        'success' => true,
                        'message' => 'Record updated successfully',
                    ], 200);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => 'Record not found',
                    ], 404);
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
