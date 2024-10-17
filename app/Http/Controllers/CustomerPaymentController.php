<?php

namespace App\Http\Controllers;

use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Auth;

class CustomerPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $payment = CustomerPayment::get();

        if (is_null($payment) || $payment->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => 'No Matched Profile Found',
            ], 404);
        }
        else {
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $payment
            ], 200);
        }
    }
    public function customerindex()
    {
        //
        $payment = CustomerPayment::where('user_id', Auth::user()->id)->get();

        if (is_null($payment) || $payment->isEmpty()) {
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => 'No Matched Profile Found',
            ], 404);
        }
        else {
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $payment
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
    public function store(Request $request)
    {
        //
        try {

            $validate = Validator::make(
                $request->all(),
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


                $payment = CustomerPayment::create([
                    'uuid' => $uuid,
                    'user_id' => $request->user_id,
                    'payment_type' => $request->payment_type,
                    'payment_for' => $request->payment_for,
                    'payment_amount' => $request->payment_amount,
                    'payment_reference' => $request->payment_reference,
                    'payment_date' => $request->payment_date,
                    'status' => 1,


                ]);

                if ($payment) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment created Successfully',
                        'data' => $payment
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
        $payment = CustomerPayment::where('uuid', $id)->first();
        if ($payment) {
            return response()->json([
                'success' => true,
                'message' => 'Data retrieved successfully',
                'data' => $payment
            ], 200);
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
        try {

            $validate = Validator::make(
                $request->all(),
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

                $payment = CustomerPayment::where('uuid', $id)->first();
                if (!$payment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment not found'
                    ], 404);
                }

                $paymentupdate = $request->only([
                    'payment_type',
                    'payment_for',
                    'payment_amount',
                    'payment_reference',
                    'payment_date',
                    'status',

                ]);
                $payment->update($paymentupdate);

                if ($payment) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment Updated Successfully',
                        'data' => $payment
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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $payment = CustomerPayment::where('uuid',$id)->first();
        $payment->delete();
            return response()->json([
                'success' => true,
                'message' => 'Payment Deleted Successfully',
            ], 202);
    }
}
