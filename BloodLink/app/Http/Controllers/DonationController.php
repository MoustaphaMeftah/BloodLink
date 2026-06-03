<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    public function store(Request $request)
    {
        $donation = Donation::create($request->all());

        return response()->json([
            'message' => 'Donation recorded',
            'data' => $donation
        ]);
    }

    public function history($donor_id)
    {
        return Donation::where('donor_id', $donor_id)->get();
    }
}
