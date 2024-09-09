<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\sensor_reading;
use App\Models\IoT_devices;
use Carbon\Carbon;
use App\Exports\WeightDataExport;
use Illuminate\Support\Facades\Response;

class MonthlyReport extends Component
{
    public $totalWeight;
    public $averageFeedingTime;
    public $data;
    public $a;


    public function mount()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        $device = IoT_devices::find(6);
        $now = Carbon::now();
        $startDate = $now->startOfMonth()->toDateTimeString(); // Format: 'Y-m-d H:i:s'
        $endDate = $now->endOfMonth()->toDateTimeString(); // Format: 'Y-m-d H:i:s'

        // $this->data = sensor_reading::whereBetween('created_at', [$startDate, $endDate])->get();
        // foreach ($this->data as $item) {
        //     $item->encrypted_reading = $this->decryptSensorData($item->encrypted_reading,$item->tag,$device); // Example decryption
        // }

        $this->data = sensor_reading::whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($item) use ($device) {
                $decryptedValue = $this->decryptSensorData($item->encrypted_reading, $item->tag, $device);

                return [
                    'time' => $item->created_at->format('Y-m-d H:i:s'),
                    'timestamp' => $item->created_at->timestamp,
                    'value' => $decryptedValue
                ];
            });

        $this->totalWeight = $this->data->sum('value');
        $averageTimestamp = $this->data->avg('timestamp');
        $this->averageFeedingTime = \Carbon\Carbon::createFromTimestamp($averageTimestamp)->format('Y-m-d H:i:s');
    }

    protected function decryptSensorData($ciphertextHex, $tagHex,$device)
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

    public function export()
    {
        $export = new WeightDataExport($this->data,$this->totalWeight,$this->averageFeedingTime);
        $filePath = $export->export();

        return Response::download($filePath)->deleteFileAfterSend(true);
    }

    public function render()
    {
        return view('livewire.monthly-report');
    }
}
