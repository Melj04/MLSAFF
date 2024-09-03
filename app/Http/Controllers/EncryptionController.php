<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Sensor;
use App\Models\DryRun;
use Illuminate\Http\Request;

class EncryptionController extends Controller
{
    public function encryptDecrypt($sensorId)
    {
        // Retrieve sensor details from the database
        $sensor = Sensor::find($sensorId);

        // Convert hex to binary for encryption/decryption
        $key = hex2bin($sensor->key);
        $nonce = hex2bin($sensor->nonce);

        // Example plaintext
        $plaintext = "Hello, ESP8266!";

        // Encrypt the data
        $ciphertext = sodium_crypto_aead_chacha20poly1305_ietf_encrypt(
            $plaintext,
            '', // Additional authenticated data (optional)
            $nonce,
            $key
        );

        // Decrypt the data
        $decrypted = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
            $ciphertext,
            '',
            $nonce,
            $key
        );

        // Prepare data for the view
        $data = [
            'plaintext' => $plaintext,
            'ciphertext' => bin2hex($ciphertext), // Convert to hex for display
            'decrypted' => $decrypted,
            'key' => $sensor->key,
            'nonce' => $sensor->nonce,
            'sensor' => $sensor->name,
        ];

        return view('encryption', $data);
    }

    public function receiveData(Request $request)
    {
        // Retrieve the ciphertext and tag from the request
        $ciphertext = hex2bin($request->input('ciphertext'));
        $tag = hex2bin($request->input('tag'));

        // Retrieve sensor details from the database
        $sensor = Sensor::find(1);

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
            return response()->json(['error' => 'Decryption failed'], 400);
        }

        return response()->json(['plaintext' => $plaintext]);
    }


    //dryrun
    public function storePlainText(Request $request)
    {
        $plaintext = $request->input('plaintext');
        $time_encrypt = $request->input('encryption_time');

        $data = new DryRun();
        $data->plaintext = $plaintext;
        $data->time_encrypt=$time_encrypt;
        $data->save();

        return response()->json(['message' => 'Plain text stored successfully.']);
    }

    public function storeCipher(Request $request)
    {
        $ciphertext = $request->input('ciphertext');
        $tag = $request->input('tag');

        $data = new DryRun();
        $data->cypher = $ciphertext;
        $data->tag = $tag;
        $data->save();

        return response()->json(['message' => 'Ciphertext and tag stored successfully.']);
    }
}

