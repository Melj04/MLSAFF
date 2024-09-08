<?php

namespace App\Http\Controllers;

use App\Models\RfidBInd;
use App\Models\IoT_devices;
use App\Models\Command;
use App\Models\User;
use Illuminate\Http\Request;

class RfidBinding_controller extends Controller
{
    public function showBindForm()
    {
        $users = User::all();
        return view('rfid.binds', compact('users'));
    }

    // Function to bind or update an RFID tag to a user
    public function bindTag(Request $request)
    {
        $request->validate([
            'tag' => 'required|exists:rfid_b_inds,tag',
            'user_id' => 'required|exists:users,id',
        ]);

        // Find the existing RFID tag
        $rfidTag = RfidBInd::where('tag', $request->input('tag'))->first();

        if ($rfidTag) {
            // Update the user associated with the existing RFID tag
            $rfidTag->user_id = $request->input('user_id');
            $rfidTag->save();
            return redirect()->route('rfid.unbound')->with('status', 'RFID tag updated with new user successfully.');
        } else {
            return back()->withErrors(['error' => 'RFID tag invalid.']);
        }
    }

    // Function to verify an RFID tag
    public function verifyTag(Request $request)
    {
        $request->validate([
            'ciphertext' => 'required',
            'tag' => 'required',
        ]);

        // Retrieve the ciphertext and tag from the request
        $ciphertext = hex2bin($request->input('ciphertext'));
        $tag = hex2bin($request->input('tag'));

        // Retrieve sensor details from the database
        $sensor = IoT_devices::find(4);

        // Convert hex to binary for encryption/decryption
        $key = hex2bin($sensor->key);
        $nonce = hex2bin($sensor->nonce);

        // Append the tag to the ciphertext as expected by the decryption function
        $ciphertext_with_tag = $ciphertext . $tag;

        // Decrypt the data using sodium_crypto_aead_chacha20poly1305_ietf_decrypt
        $plaintext = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
            $ciphertext_with_tag,
            '', // Additional authenticated data (AAD)
            $nonce,
            $key
        );

        if ($plaintext === false) {
            return response()->json(['error' => 'Decryption failed.'], 400);
        }

        // Convert the decrypted plaintext to a readable RFID tag format (if necessary)
        $rfidTagValue = $plaintext;

        // Search for the RFID tag in the database
        $rfidTag = RfidBInd::where('tag', $rfidTagValue)->first();

        if ($rfidTag && $rfidTag->user_id) {
        // If the RFID tag is found and bound, update the command table (e.g., access=true)
        $cmd="1";
        $lock = $this->lockEncrypt($cmd);
        // Save the encrypted data and tags to the 'command' and 'tag' columns in the 'commands' table
        $command = Command::find(11);
        if ($command) {
            $command->command = $lock['ciphertext'];
            $command->tag = $lock['tag'];
            $command->save();
        }
        return response()->json(['access' => 'grant'], 201);

        } else {
            // Store the RFID tag in the database to wait until it is bound
            RfidBInd::updateOrCreate(
                ['tag' => $rfidTagValue],
                ['user_id' => null]
            );
        $cmd="0";
        $lock = $this->lockEncrypt($cmd);
        // Save the encrypted data and tags to the 'command' and 'tag' columns in the 'commands' table
        $command = Command::find(11);
        if ($command) {
            $command->command = $lock['ciphertext'];
            $command->tag = $lock['tag'];
            $command->save();
        }
            return response()->json(['access' => 'denied'], 403);
        }
    }

    public function lockEncrypt($cmd)
    {
        return $this->encrypt($cmd); // Status value is "1" for active
    }
    protected function encrypt($data)
    {
        $commandStat = Command::find(11);
        $ActiveStat = IoT_devices::find($commandStat->actuators_id);
        $key = hex2bin($ActiveStat->key);
        $nonce = hex2bin($ActiveStat->nonce);

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

    public function showUnboundTags()
    {
        // Get RFID tags that are not bound to any user
        $unboundTags = RfidBInd::whereNull('user_id')->get();
        $users = User::all(); // Fetch users for the binding form
        return view('rfid.unbind', compact('unboundTags', 'users'));
    }

    public function cmd(Request $request){
         // Validate the incoming request
    $validatedData = $request->validate([
        'tag' => 'required',
        'ciphertext' => 'required',
    ]);

    // Fetch the command by ID (example: 11)
    $command = Command::find(11);

    // If command exists, update the 'command' and 'tag' fields
    if ($command) {
        $command->command = $validatedData['ciphertext']; // Use the correct field from the request
        $command->tag = $validatedData['tag']; // Ensure the 'tag' field is updated correctly
        $command->save();
    }

    // Return a JSON response with a 201 status
    return response()->json(['storage' => 'closed'], 201);
    }


}

