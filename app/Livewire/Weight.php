<?php

namespace App\Livewire;

use App\Models\Command;
use App\Models\IoT_devices;
use Livewire\Component;

class Weight extends Component
{
    public $numWeight = 0;
    public $device;
    public $message;

    public function mount()
    {
        // Retrieve the IoT device associated with the weight command
        $commandW = Command::find(8);
        $this->device = IoT_devices::find($commandW->actuators_id);
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

    public function encryptData()
    {
        return $this->encrypt((string)$this->numWeight, $this->device);
    }

    public function saveWeight()
    {
        // Encrypt the data and get ciphertexts and tags
        $weightData = $this->encryptData();

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
    }

    public function up()
    {
        if ($this->numWeight < 200) {
            $this->numWeight += 10;
        }
    }

    public function down()
    {
        if ($this->numWeight > 0) {
            $this->numWeight -= 10;
        }
    }

    public function render()
    {
        return view('livewire.weight');
    }
}
