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
        $sensor = IoT_devices::find(1);

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
            Command::updateOrCreate(
                ['rfid_tag_id' => $rfidTag->id],
                ['access' => true]
            );

            return response()->json(['access' => 'granted', 'user_id' => $rfidTag->user_id]);
        } else {
            // Store the RFID tag in the database to wait until it is bound
            RfidBInd::updateOrCreate(
                ['tag' => $rfidTagValue],
                ['user_id' => null]
            );

            return response()->json(['access' => 'denied'], 403);
        }
    }

    public function showUnboundTags()
    {
        // Get RFID tags that are not bound to any user
        $unboundTags = RfidBInd::whereNull('user_id')->get();
        $users = User::all(); // Fetch users for the binding form
        return view('rfid.unbind', compact('unboundTags', 'users'));
    }
}
