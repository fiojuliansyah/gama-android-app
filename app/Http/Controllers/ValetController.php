<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Valet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ValetController extends Controller
{
    public function index()
    {
        $valets = Valet::all();
        return view ('mobiles.valets.index',compact('valets'));
    }

    public function create()
    {
        return view ('mobiles.valets.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'transaction_id' => 'nullable|string|max:255',
            'name' => 'nullable|string',
            'image_url' => 'nullable|string',
            'image_public_id' => 'nullable|string',
            'plat_number' => 'nullable|string|max:255',
            'amount' => 'nullable|string|max:255',
            'q_code' => 'nullable|string',
            'status' => 'nullable|in:success,pending,canceled',
        ]);

        $validated['user_id'] = Auth::id();
        // Generate transaction ID
        $validated['transaction_id'] = date('Ymd') . '-' . str_pad(rand(0, 99999), 5, '0', STR_PAD_LEFT);

        // Initialize Midtrans Configuration
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');

        // Create transaction payload
        $transaction = [
            'transaction_details' => [
                'order_id' => $validated['transaction_id'],
                'gross_amount' => $validated['amount'], // Payment amount
            ],
            'item_details' => [
                [
                    'id' => 'item-1',
                    'price' => $validated['amount'],
                    'quantity' => 1,
                    'name' => 'Valet Payment'
                ]
            ],
            'customer_details' => [
                'first_name' => 'Customer', // Replace with customer name
                'email' => 'customer@example.com', // Replace with customer email
                'phone' => '08123456789'
            ]
        ];

        // Create Snap Transaction
        $snapToken = Snap::getSnapToken($transaction);

        // Store the SnapToken in the request data to generate QR code
        $validated['q_code'] = $snapToken;

        // Generate the QR code in SVG format using SimpleQRCode
        $qrCodeSvg = QrCode::size(250)->generate($snapToken);  // Generate QR Code as SVG

        // Save the SVG string to the database (into the q_code column)
        $validated['q_code'] = $qrCodeSvg;

        // Create the valet transaction in the database
        $valet = Valet::create($validated);

        // Redirect to the show route with the newly created valet ID
        return redirect()->route('valet.show', ['id' => $valet->id])
                        ->with('success', 'Valet transaction created successfully.');
    }

    public function show($id)
    {
        // Ambil data valet berdasarkan ID
        $valet = Valet::findOrFail($id);

        // Kirim data valet ke view
        return view('valets.show', compact('valet'));
    }


}
