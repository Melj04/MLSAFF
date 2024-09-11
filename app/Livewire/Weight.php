<?php
namespace App\Livewire;

use App\Models\Command;
use App\Models\IoT_devices;
use Livewire\Component;
use RealRashid\SweetAlert\Facades\Alert;

class Weight extends Component
{
    public $numWeight = 0;
    protected $device;
    protected $ActiveStat;
    public $message;
    public $amH;
    public $amM;
    public $pmH;
    public $pmM;

    // Runs when the component is mounted
    public function mount()
    {
        $commandW = Command::find(8);
        $this->device = IoT_devices::find($commandW->actuators_id);
        $commandStat = Command::find(7);
        $this->ActiveStat = IoT_devices::find($commandStat->actuators_id);
    }

    // Public getter to expose the hide button logic in the view
    public function getShouldHideButtonProperty()
    {
        return $this->shouldHideButton();
    }

    // Logic to determine whether the button should be hidden
    protected function shouldHideButton()
    {
        $currentTime = now();
        $this->amH = $this->decryptime(1);
        $this->amM = $this->decryptime(2);
        $this->pmH = $this->decryptime(3);
        $this->pmM = $this->decryptime(4);

        $amHour = (int) $this->amH;
        $amMinute = (int) $this->amM;
        $pmHour = (int) $this->pmH;
        $pmMinute = (int) $this->pmM;

        if ($currentTime->hour == $amHour && $currentTime->minute == $amMinute) {
            $this->message = "Morning Feeding in progress.";
            return true;
        }

        if ($currentTime->hour == $pmHour && $currentTime->minute == $pmMinute) {
            $this->message = "Afternoon Feeding in progress.";
            return true;
        }

        return false;
    }

    // Logic to decrypt the time from the command
    protected function decryptime($id)
    {
        $command = Command::find($id);
        $iot = IoT_devices::find($command->actuators_id);
        if ($command && $iot) {
            $ciphertextHex = $command->command;
            $tagHex = $command->tag;
            $ciphertextBinary = hex2bin($ciphertextHex);
            $tagBinary = hex2bin($tagHex);
            $key = hex2bin($iot->key);
            $nonce = hex2bin($iot->nonce);

            return sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
                $ciphertextBinary . $tagBinary,
                '',
                $nonce,
                $key
            );
        }
        return null;
    }

    // Logic to decrypt the weight data
    protected function decryptData()
    {
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

    // Logic to encrypt the data
    protected function encrypt($data, $iot)
    {
        $iot = Command::find($iot);
        $iot = IoT_devices::find($iot->actuators_id);
        $key = hex2bin($iot->key);
        $nonce = hex2bin($iot->nonce);

        $ciphertext = sodium_crypto_aead_chacha20poly1305_ietf_encrypt(
            $data,
            '',
            $nonce,
            $key
        );

        $tagLength = SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_ABYTES;
        $tag = substr($ciphertext, -$tagLength);
        $ciphertext = substr($ciphertext, 0, -$tagLength);

        return [
            'ciphertext' => bin2hex($ciphertext),
            'tag' => bin2hex($tag)
        ];
    }

    // Public method to encrypt weight data
    public function encryptData()
    {
        return $this->encrypt((string)$this->numWeight, 8);
    }

    // Public method to encrypt status data
    public function statEncrypt()
    {
        return $this->encrypt("1",7);
    }

    public $modalVisible = false; // To control modal visibility

    public function showModal()
    {
        $this->modalVisible = true; // Show the modal
    }
    // Method to save the weight and show an alert
    public function saveWeight()
    {
        $weightData = $this->encryptData();
        $statusData = $this->statEncrypt();

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
        $this->modalVisible = false;
    }

    // Increment weight
    public function up()
    {
        if ($this->numWeight < 200) {
            $this->numWeight += 10;
        }
    }

    // Decrement weight
    public function down()
    {
        if ($this->numWeight > 10) {
            $this->numWeight -= 10;
        }
    }

    // Render the component view
    public function render()
    {
        return view('livewire.weight', [
            'shouldHideButton' => $this->getShouldHideButtonProperty(),
        ]);
    }
}
