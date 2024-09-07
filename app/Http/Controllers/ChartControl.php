<?php

namespace App\Http\Controllers;
use App\Models\IoT_devices;
use Carbon\Carbon;
use App\Models\sensor_reading;
use Illuminate\Http\Request;
class ChartControl extends Controller
{// Function to decrypt the data
    protected function decryptSensorData($ciphertextHex, $tagHex, $device)
    {
        $ciphertextBinary = hex2bin($ciphertextHex);
        $tagBinary = hex2bin($tagHex);
        $key = hex2bin($device->key);
        $nonce = hex2bin($device->nonce);

        // Decrypt the data using ChaCha20-Poly1305
        $decryptedData = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
            $ciphertextBinary . $tagBinary,
            '', // Additional authenticated data (AAD)
            $nonce,
            $key
        );
        return $decryptedData !== false ? (int)$decryptedData : 0; // Return null on failure
    }

    public function chart()
    {
        $device = IoT_devices::find(6); // Assuming you have a device with ID 1

        // Fetch the latest sensor readings from the database
        $data = sensor_reading::orderBy('created_at', 'desc')->take(15)->get();

        // Map through the data and decrypt it before displaying in the chart
        $chartData = $data->map(function ($item) use ($device) {
            $decryptedValue = $this->decryptSensorData($item->encrypted_reading, $item->tag, $device);

            return [
                'time' => Carbon::parse($item->created_at)->format('Y-m-d H:i'),
                'value' => $decryptedValue
            ];
        });

        return view('dashboard', compact('chartData'));
    }

    public function getChartData()
    {
        $device = IoT_devices::find(6); // Retrieve device for decryption

        // Fetch the latest 15 records from the database
        $data = sensor_reading::orderBy('created_at', 'desc')
            ->take(15)
            ->get()
            ->map(function ($item) use ($device) {
                $decryptedValue = $this->decryptSensorData($item->encrypted_reading, $item->tag, $device);

                return [
                    'time' => $item->created_at->format('Y-m-d H:i:s'),
                    'value' => $decryptedValue
                ];
            });
        return response()->json($data);
    }
}
