<?php

namespace App\Http\Controllers;
use App\Models\IoT_devices;
use Carbon\Carbon;
use App\Models\sensor_reading;
use Illuminate\Http\Request;

class ChartControl extends Controller
{
    public function chart(){
        $data=sensor_reading::all();//fetch data from db
        $chartData = $data->map(function ($item) {
            return [
                'time' => Carbon::parse($item->created_at)->format('Y-m-d H:i'), // show time labels
                'value' => $item->value // Adjust according to your data structure
            ];
        });

        return view('dashboard', compact('chartData'));
    }

    public function getChartData()
    {
        $device = IoT_devices::find(6); // Replace with appropriate device ID

        if (!$device) {
            return response()->json(['error' => 'Device not found'], 404);
        }$data = sensor_reading::orderBy('created_at', 'desc')
        ->take(15) // Limit to 15 records
        ->get()
        ->map(function ($item) use ($device) {
            // Decrypt the ciphertext and tag
            $ciphertextBinary = hex2bin($item->ciphertext); // Convert ciphertext to binary
            $tagBinary = hex2bin($item->tag); // Convert tag to binary
            $key = hex2bin($device->key); // Use the correct device key
            $nonce = hex2bin($device->nonce); // Use the correct device nonce

            // Perform decryption using sodium_crypto_aead_chacha20poly1305_ietf_decrypt
            $decryptedValue = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
                $ciphertextBinary . $tagBinary, // Concatenate ciphertext and tag
                '',
                $nonce,
                $key
            );

            // Return time and decrypted value
            return [
                'time' => $item->created_at->format('Y-m-d H:i:s'), // Format time
                'value' => $decryptedValue !== false ? (int) $decryptedValue : 0 // Decrypt value, default to 0 if failed
            ];
        });

    // Return the data as JSON response
    return response()->json($data);
}
}
