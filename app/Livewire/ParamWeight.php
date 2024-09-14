<?php

namespace App\Livewire;

use App\Models\command;
use App\Models\IoT_devices;
use Livewire\Component;

class ParamWeight extends Component
{
    public $numWeight = 0;
    public $hour = 0;
    public $minute = 0;
    protected $device;
    protected $rtc;
    protected $ActiveStat;
    public $message;

    public function mount()
    {
        // Retrieve the IoT device associated with the weight command
        $commandW = Command::find(9);
        $this->device = IoT_devices::find($commandW->actuators_id);

        // Retrieve the IoT device associated with the status command
        $commandStat = Command::find(7);
        $this->ActiveStat = IoT_devices::find($commandStat->actuators_id);

        // Retrieve the IoT device associated with the RTC command
        $rtc = Command::find(1);
        $this->rtc = IoT_devices::find($rtc->actuators_id);

        // Decrypt the data on mount
        $this->decryptData();
    }

    protected function encrypt($data, $device)
    {

        $device = Command::find($device);
        $device = IoT_devices::find($device->actuators_id);
        $key = hex2bin($device->key);
        $nonce = hex2bin($device->nonce);

        // Encrypt the data and generate the authentication tag
        $ciphertext = sodium_crypto_aead_chacha20poly1305_ietf_encrypt(
            $data,
            '',
            $nonce,
            $key
        );

        // Extract the tag from the ciphertext
        $tagLength = SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_ABYTES;
        $tag = substr($ciphertext, -$tagLength);
        $ciphertext = substr($ciphertext, 0, -$tagLength);

        return [
            'ciphertext' => bin2hex($ciphertext),
            'tag' => bin2hex($tag)
        ];
    }

    public function mWeight()
    {
        return $this->encrypt((string)$this->numWeight, 9);
    }

    public function statEncrypt()
    {
        return $this->encrypt("1", 7); // Status value is "1" for active
    }

    public function h()
    {
        return $this->encrypt((string)$this->hour, 1);
    }

    public function m()
    {
        return $this->encrypt((string)$this->minute, 1);
    }

    public function decryptData()
    {
        // Retrieve and decrypt weight data
        $commandW = Command::find(9); // Weight command ID
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

            $this->numWeight = $decryptedWeight !== false ? (int)$decryptedWeight : 0; // Default to 0 on failure
        } else {
            $this->numWeight = 0; // Default to 0 if command or device not found
        }

        // Decrypt hour data
        $commandH = Command::find(1); // Hour command ID
        if ($commandH && $this->rtc) {
            $ciphertextHex = $commandH->command;
            $tagHex = $commandH->tag; // Assuming tag is saved in the `tag` column
            $ciphertextBinary = hex2bin($ciphertextHex);
            $tagBinary = hex2bin($tagHex);
            $key = hex2bin($this->rtc->key);
            $nonce = hex2bin($this->rtc->nonce);

            $decryptedHour = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
                $ciphertextBinary . $tagBinary,
                '',
                $nonce,
                $key
            );
            $this->hour = $decryptedHour !== false ? (int)$decryptedHour : 7; // Handle decryption failure
        } else {
            $this->hour = 0; // Default to 0 if command or device not found
        }

        // Decrypt minute data
        $commandM = Command::find(2); // Minute command ID
        if ($commandM && $this->rtc) {
            $ciphertextHex = $commandM->command;
            $tagHex = $commandM->tag; // Assuming tag is saved in the `tag` column
            $ciphertextBinary = hex2bin($ciphertextHex);
            $tagBinary = hex2bin($tagHex);
            $key = hex2bin($this->rtc->key);
            $nonce = hex2bin($this->rtc->nonce);

            $decryptedMinute = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
                $ciphertextBinary . $tagBinary,
                '',
                $nonce,
                $key
            );

            $this->minute = $decryptedMinute !== false ? (int)$decryptedMinute : 0; // Handle decryption failure
        } else {
            $this->minute = 0; // Default to 0 if command or device not found
        }
    }

    public $modalVisible = false; // To control modal visibility

    public function showModal()
    {
        $this->modalVisible = true; // Show the modal
    }

    public function saveWeightMorning()
    {
        $this->validate([
            'hour' => 'integer|min:6|max:12',
            'minute' => 'integer|min:0|max:59',
        ]);

        // Encrypt the data and get ciphertexts and tags
        $weightData = $this->mWeight();
        $statusData = $this->statEncrypt();
        $hourData = $this->h(); // Encrypt hour
        $minuteData = $this->m(); // Encrypt minute

        // Save the encrypted data and tags to the 'command' and 'tag' columns in the 'commands' table
        $commandW = Command::find(9);
        if ($commandW) {
            $commandW->command = $weightData['ciphertext'];
            $commandW->tag = $weightData['tag'];
            $commandW->save();
        } else {
            $this->message = 'Weight command not found';
            return;
        }

        $commandStat = Command::find(5);
        if ($commandStat) {
            $commandStat->command = $statusData['ciphertext'];
            $commandStat->tag = $statusData['tag'];
            $commandStat->save();
        } else {
            $this->message = 'Status command not found';
            return;
        }

        $commandH = Command::find(1); // Hour command ID
        if ($commandH) {
            $commandH->command = $hourData['ciphertext'];
            $commandH->tag = $hourData['tag'];
            $commandH->save();
        } else {
            $this->message = 'Hour command not found';
            return;
        }

        $commandM = Command::find(2); // Minute command ID
        if ($commandM) {
            $commandM->command = $minuteData['ciphertext'];
            $commandM->tag = $minuteData['tag'];
            $commandM->save();
        } else {
            $this->message = 'Minute command not found';
            return;
        }

        $this->message = "Update successfully";
        $this->modalVisible = false;

    }

    public function up()
    {
        if ($this->numWeight < 200) {
            $this->numWeight=$this->numWeight+10;
        }
    }

    public function down()
    {
        if ($this->numWeight > 10) {
            $this->numWeight=$this->numWeight-10;
        }
    }

    public function render()
    {
        return view('livewire.param-weight');
    }
}
