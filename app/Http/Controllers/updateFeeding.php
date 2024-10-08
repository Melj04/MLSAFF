<?php

namespace App\Http\Controllers;
use App\Models\command;
use App\Models\logs;
use Illuminate\Http\Request;
use App\Models\sensor_reading;
class updateFeeding extends Controller
{
    public function activeFed(Request $request){
        // Validate the incoming request
    $validatedData = $request->validate([
        'tag' => 'required',
        'ciphertext' => 'required',
    ]);

    // Fetch the command by ID (example: 11)
    $command = Command::find(7);

    // If command exists, update the 'command' and 'tag' fields
    if ($command) {
        $command->command = $validatedData['ciphertext']; // Use the correct field from the request
        $command->tag = $validatedData['tag']; // Ensure the 'tag' field is updated correctly
        $command->save();
    }

    // Return a JSON response with a 201 status
    return response()->json(['active feeding' => 'stop'], 201);
    }

    public function AmFed(Request $request){
        // Validate the incoming request
    $validatedData = $request->validate([
        'tag' => 'required',
        'ciphertext' => 'required',
    ]);

    // Fetch the command by ID (example: 11)
    $command = Command::find(5);

    // If command exists, update the 'command' and 'tag' fields
    if ($command) {
        $command->command = $validatedData['ciphertext']; // Use the correct field from the request
        $command->tag = $validatedData['tag']; // Ensure the 'tag' field is updated correctly
        $command->save();
    }

    // Return a JSON response with a 201 status
    return response()->json(['active feeding' => 'stop'], 201);
    }


    public function PmFed(Request $request){
        // Validate the incoming request
    $validatedData = $request->validate([
        'tag' => 'required',
        'ciphertext' => 'required',
    ]);

    // Fetch the command by ID (example: 11)
    $command = Command::find(6);

    // If command exists, update the 'command' and 'tag' fields
    if ($command) {
        $command->command = $validatedData['ciphertext']; // Use the correct field from the request
        $command->tag = $validatedData['tag']; // Ensure the 'tag' field is updated correctly
        $command->save();
    }

    // Return a JSON response with a 201 status
    return response()->json(['active feeding' => 'stop'], 201);
    }

    public function upW(Request $request){
        // Validate the incoming request
    $validatedData = $request->validate([
        'tag' => 'required',
        'ciphertext' => 'required',
    ]);

    // Fetch the command by ID (example: 11)
    $command = Command::find(8);

    // If command exists, update the 'command' and 'tag' fields
    if ($command) {
        $command->command = $validatedData['ciphertext']; // Use the correct field from the request
        $command->tag = $validatedData['tag']; // Ensure the 'tag' field is updated correctly
        $command->save();
    }

    // Return a JSON response with a 201 status
    return response()->json(['update' => 'weight'], 201);
    }

    public function upWeight(Request $request){
        // Validate the incoming request
    $validatedData = $request->validate([
        'tag' => 'required',
        'ciphertext' => 'required',
    ]);

    // Fetch the command by ID (example: 11)
    $command = Command::find(12);

    // If command exists, update the 'command' and 'tag' fields
    if ($command) {
        $command->command = $validatedData['ciphertext']; // Use the correct field from the request
        $command->tag = $validatedData['tag']; // Ensure the 'tag' field is updated correctly
        $command->save();
    }

    // Return a JSON response with a 201 status
    return response()->json(['update' => 'weight'], 201);
    }

    public function logs(Request $request){
        // Validate the incoming request
        $validatedData = $request->validate([
            'log' => 'required',

        ]);

        // Create a new command entry with the validated data
        $command = logs::create([
            'errors' => $validatedData['log'], // Insert the 'ciphertext' field

        ]);

        // Return a JSON response with a 201 status
        return response()->json(['message' => 'logs complete'], 201);
    }

    public function sensor(Request $request){
        // Validate the incoming request
        $validatedData = $request->validate([
            'tag' => 'required',
            'ciphertext' => 'required',
        ]);

        // Create a new command entry with the validated data
        $command = sensor_reading::create([
            'sensor_id' => 6, // Insert the 'ciphertext' field
            'encrypted_reading' => $validatedData['ciphertext'],
            'tag' => $validatedData['tag'],
        ]);

        // Return a JSON response with a 201 status
        return response()->json(['message' => 'weight update complete'], 201);
    }
}
