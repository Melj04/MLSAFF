<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\command;
use App\Models\IoT_devices;
use Carbon\Carbon;

class NextFed extends Component
{
    protected $command;
    protected $device;

    public $amFeedH ;
    public $amFeedM ;
    public $pmFeedH ;
    public $pmFeedM ;
    public $nextFeedingDate;
    public $nextFeedingTime;

    public function mount()
    {
        // Retrieve the IoT device associated with the weight command
        $command = Command::find(1);
        $this->device = IoT_devices::find($command->actuators_id);

        $this->amFeedH=$this->decryptData(1);
        $this->amFeedM=$this->decryptData(2);
        $this->pmFeedH=$this->decryptData(3);
        $this->pmFeedM=$this->decryptData(4);
    }

    public function decryptData($id)
    {
        // Retrieve the command record to decrypt
        $command = Command::find($id);
        $ciphertextHex = $command->command;
        $tagHex = $command->tag; // Assuming tag is saved in the `tag` column
        $ciphertextBinary = hex2bin($ciphertextHex);
        $tagBinary = hex2bin($tagHex);
        $key = hex2bin($this->device->key);
        $nonce = hex2bin($this->device->nonce);

        // Decrypt the data using ChaCha20-Poly1305
        $decryptedText = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
            $ciphertextBinary . $tagBinary,
            '', // Optional additional authenticated data
            $nonce,
            $key
        );

        return $decryptedText; // Return decrypted value
    }
     // Function to calculate the next feeding time
     public function getNextFeedingTime()
    {
        // Get the current time
        $now = Carbon::now();

        // Create Carbon instances for AM and PM feeding times using today’s date
        $amFeedTime = Carbon::createFromTime($this->amFeedH, $this->amFeedM, 0);
        $pmFeedTime = Carbon::createFromTime($this->pmFeedH, $this->pmFeedM, 0);

        // If the current time is before today's AM feeding time, return today's AM feed time
        if ($now->lt($amFeedTime)) {
            return $amFeedTime;
        }

        // If the current time is before today's PM feeding time, return today's PM feed time
        if ($now->lt($pmFeedTime)) {
            return $pmFeedTime;
        }

        // If both feeding times have passed, return tomorrow's AM feeding time
        return $amFeedTime->addDay();
    }

    public function render()
    {
        // Get the next feeding time as a Carbon object
        $nextFeed = $this->getNextFeedingTime();

        // Format the next feeding time and date separately
        $this->nextFeedingDate = $nextFeed->format('F j, Y'); // Full month name, day, and year
        $this->nextFeedingTime = $nextFeed->format('g:i A'); // 12-hour time format with AM/PM

        return view('livewire.next-fed',[
            'nextFeedingDate' => $this->nextFeedingDate,
            'nextFeedingTime' => $this->nextFeedingTime,
        ]);
    }
}
