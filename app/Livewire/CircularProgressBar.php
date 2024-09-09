<?php

namespace App\Livewire;

use App\Models\Command;
use Livewire\Component;
use App\Models\IoT_devices;

class CircularProgressBar extends Component
{
    public int $reference_weight = 1000; // 1kg in grams
    public int $current_weight = 1;      // Default to 1g
    public $percentage = 0;
    public $kg;
    protected $device;

    public function mount()
    {
        // Fetch the IoT device linked to this weight sensor
        $commandW = Command::find(12);
        $this->device = IoT_devices::find($commandW->actuators_id);
        // Decrypt data after component mounts
        $this->decryptData();

        // Calculate percentage based on decrypted weight
        $this->calculatePercentage();
    }

    protected function decryptData()
    {
        // Retrieve and decrypt weight data
        $commandW = Command::find(12); // Weight command ID
        if ($commandW && $this->device) {
            $ciphertextHex = $commandW->command;
            $tagHex = $commandW->tag; // Assuming tag is saved in the `tag` column
            $ciphertextBinary = hex2bin($ciphertextHex);
            $tagBinary = hex2bin($tagHex);
            $key = hex2bin($this->device->key);
            $nonce = hex2bin($this->device->nonce);

            $decryptedWeight = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
                $ciphertextBinary . $tagBinary,
                '',
                $nonce,
                $key
            );

            $this->current_weight = $decryptedWeight !== false ? (int)$decryptedWeight : 0; // Default to 0 on failure
        } else {
            $this->current_weight = 0; // Default to 0 if command or device not found
        }
    }

    protected function calculatePercentage()
    {
        // Ensure reference_weight is not zero to avoid division by zero
        if ($this->reference_weight > 0) {
            $this->percentage = ($this->current_weight / $this->reference_weight) * 100;
            $this->kg=$this->current_weight/1000;
            if($this->kg<=0){
                $this->kg=0;
            }
        } else {
            $this->percentage = 0; // Default to 0 if reference_weight is invalid
        }
    }

    public function render()
    {
        return view('livewire.circular-progress-bar', [
            'percentage' => $this->percentage,
            'percentage' => $this->kg,
        ]);
    }
}
