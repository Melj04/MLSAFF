<?php

namespace App\Livewire;

use App\Models\Command;
use App\Models\IoT_devices;
use Livewire\Component;

class ParamWeight2 extends Component
{
    public $numWeight = 0;
    public $hour = 0;
    public $minute = 0;
    public $device;
    public $rtc;
    public $ActiveStat;
    public $message;
    public $message2;

    public function mount()
    {
        // Retrieve the IoT device associated with the weight command
        $commandW = Command::find(10);
        $this->device = IoT_devices::find($commandW->actuators_id);

        // Retrieve the IoT device associated with the status command
        $commandStat = Command::find(6);
        $this->ActiveStat = IoT_devices::find($commandStat->actuators_id);

        // Retrieve the IoT device associated with the RTC command
        $rtc = Command::find(3);
        $this->rtc = IoT_devices::find($rtc->actuators_id);

        $this->decryptData();
    }

    protected function encrypt($data, $device)
    {
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

    public function aWeight()
    {
        return $this->encrypt((string)$this->numWeight, $this->device);
    }

    public function statEncrypt()
    {
        return $this->encrypt("1", $this->ActiveStat); // Status value is "1" for active
    }

    public function h()
    {
        return $this->encrypt((string)$this->hour, $this->rtc);
    }

    public function m()
    {
        return $this->encrypt((string)$this->minute, $this->rtc);
    }

    public function decryptData()
    {
        // Decrypt weight data
        $commandW = Command::find(10);
        if ($commandW && $this->device) {
            $ciphertextHex = $commandW->command;
            $tagHex = $commandW->tag;
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

            $this->numWeight = $decryptedWeight !== false ? (int)$decryptedWeight : 0;
        } else {
            $this->numWeight = 0;
        }

        // Decrypt hour data
        $commandH = Command::find(3);
        if ($commandH && $this->rtc) {
            $ciphertextHex = $commandH->command;
            $tagHex = $commandH->tag;
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
            $this->hour = $decryptedHour !== false ? (int)$decryptedHour : 6;
        } else {
            $this->hour = 0;
        }

        // Decrypt minute data
        $commandM = Command::find(4);
        if ($commandM && $this->rtc) {
            $ciphertextHex = $commandM->command;
            $tagHex = $commandM->tag;
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

            $this->minute = $decryptedMinute !== false ? (int)$decryptedMinute : 0;
        } else {
            $this->minute = 0;
        }
    }

    public function saveWeightAfternoon()
    {
        // Validate hour and minute before encryption and saving
        $this->validate([
            'hour' => 'integer|min:13|max:18',
            'minute' => 'integer|min:0|max:59',
        ]);

        // Encrypt the data and get ciphertexts and tags
        $weightData = $this->aWeight();
        $statusData = $this->statEncrypt();
        $hourData = $this->h();
        $minuteData = $this->m();

        // Save the encrypted data and tags to the 'command' and 'tag' columns in the 'commands' table
        $commandW = Command::find(10);
        if ($commandW) {
            $commandW->command = $weightData['ciphertext'];
            $commandW->tag = $weightData['tag'];
            $commandW->save();
        } else {
            $this->message2 = 'Weight command not found';
            return;
        }

        $commandStat = Command::find(6);
        if ($commandStat) {
            $commandStat->command = $statusData['ciphertext'];
            $commandStat->tag = $statusData['tag'];
            $commandStat->save();
        } else {
            $this->message2 = 'Status command not found';
            return;
        }

        $commandH = Command::find(3);
        if ($commandH) {
            $commandH->command = $hourData['ciphertext'];
            $commandH->tag = $hourData['tag'];
            $commandH->save();
        } else {
            $this->message2 = 'Hour command not found';
            return;
        }

        $commandM = Command::find(4);
        if ($commandM) {
            $commandM->command = $minuteData['ciphertext'];
            $commandM->tag = $minuteData['tag'];
            $commandM->save();
        } else {
            $this->message2 = 'Minute command not found';
            return;
        }

        $this->message2 = "Update successfully";
    }

    public function up()
    {
        if ($this->numWeight < 200) {
            $this->numWeight = $this->numWeight + 10;
        }
    }

    public function down()
    {
        if ($this->numWeight > 10) {
            $this->numWeight = $this->numWeight - 10;
        }
    }

    public function render()
    {
        return view('livewire.param-weight2');
    }
}
