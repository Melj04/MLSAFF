<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>ChaCha20 and Poly1305 Encryption/Decryption</h1>

                    <p><strong>Sensor:</strong> {{ $sensor }}</p>
                    <p><strong>Plaintext:</strong> {{ $plaintext }}</p>
                    <p><strong>Key:</strong> {{ $key }}</p>
                    <p><strong>Nonce:</strong> {{ $nonce }}</p>
                    <p><strong>Ciphertext (Hex):</strong> {{ $ciphertext }}</p>
                    <p><strong>Decrypted Text:</strong> {{ $decrypted }}</p>

                    @if($plaintext === $decrypted)
                        <p><strong>Success:</strong> The decrypted text matches the original plaintext.</p>
                    @else
                        <p><strong>Error:</strong> The decrypted text does not match the original plaintext.</p>
                    @endif

</body>
</html>
