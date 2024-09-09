<?php

namespace App\Livewire;

use App\Models\Command;
use App\Models\IoT_devices;
use Livewire\Component;
use Stringable;

class Weight extends Component
{
    public $numWeight = 0;
    public $device;
    public $message;
    public $ActiveStat;
    public $amH;
    public $amM;
    public $pmH;
    public $pmM;

    public function mount()
    {
        // Retrieve the IoT device associated with the weight command
        $commandW = Command::find(8);
        $this->device = IoT_devices::find($commandW->actuators_id);
        $commandStat = Command::find(7);
        $this->ActiveStat = IoT_devices::find($commandStat->actuators_id);


    }

    protected function shouldHideButton()
    {
        $currentTime = now(); // Get the current time
        $this->amH=$this->decryptime(1);
        $this->amM=$this->decryptime(2);
        $this->pmH=$this->decryptime(3);
        $this->pmM=$this->decryptime(4);

            $amHour = (int) $this->amH;
            $amMinute = (int) $this->amM;

            if ($currentTime->hour == $amHour && $currentTime->minute == $amMinute) {
                return true; // Hide the button
                $this->message= "Morning Feeding in progress. ";
            }


        // Check if current time matches the decrypted PM feed time
            $pmHour = (int) $this->pmH;
            $pmMinute = (int) $this->pmM;

            if ($currentTime->hour == $pmHour && $currentTime->minute == $pmMinute) {
                return true; // Hide the button
                $this->message= "Afternoon Feeding in progress. ";

            }

        return false; // Do not hide the button

    }


protected function decryptime($id)
{
    // Decrypt time data
    $command = Command::find($id);
    $iot=IoT_devices::find($command->actuators_id);
    if ($command && $iot) {
        $ciphertextHex = $command->command;
        $tagHex = $command->tag;
        $ciphertextBinary = hex2bin($ciphertextHex);
        $tagBinary = hex2bin($tagHex);
        $key = hex2bin($iot->key);
        $nonce = hex2bin($iot->nonce);

        $time = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
            $ciphertextBinary . $tagBinary,
            '',
            $nonce,
            $key
        );

        return $time;
    }
}
    protected function decryptData()
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
    }
    protected function encrypt($data, $iot)
    {
        $key = hex2bin($iot->key);
        $nonce = hex2bin($iot->nonce);

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

    public function encryptData()
    {
        return $this->encrypt((string)$this->numWeight, $this->device);
    }
    public function statEncrypt()
    {
        return $this->encrypt("1", $this->ActiveStat); // Status value is "1" for active
    }

    public function saveWeight()
    {
        // Encrypt the data and get ciphertexts and tags
        $weightData = $this->encryptData();
        $statusData = $this->statEncrypt();
        // Save the encrypted data and tags to the 'command' column in the 'commands' table
        $commandW = Command::find(8);
        if ($commandW) {
            $commandW->command = $weightData['ciphertext'];
            $commandW->tag = $weightData['tag'];
            $commandW->save();
            $this->message = "Update successfully";
        } else {
            $this->message = "Command not found";
        }
        $commandStat = Command::find(7);
        if ($commandStat) {
            $commandStat->command = $statusData['ciphertext'];
            $commandStat->tag = $statusData['tag'];
            $commandStat->save();
        } else {
            $this->message = 'Status command not found';
            return;
        }
    }

    public function up()
    {
        if ($this->numWeight < 200) {
            $this->numWeight += 10;
        }
    }

    public function down()
    {
        if ($this->numWeight > 10) {
            $this->numWeight -= 10;
        }
    }

    public function render()
    {
        return view('livewire.weight', [
            'shouldHideButton' => $this->shouldHideButton(), // Pass the result to the view
        ]);
    }
}
