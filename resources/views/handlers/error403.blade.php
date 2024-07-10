<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 Error</title>
    <link rel="stylesheet" href="../../css/error403.css"> 
</head>
<body>
    <div class="error-403-container">
        <img src="{{ asset('images/403.svg') }}" alt="403 Image">
        <button class="return-button">Return to Admin</button>
    </div>

    {{-- Optional: Load scripts if needed --}}
    {{-- <script src="{{ asset('js/custom.js') }}"></script> --}}
</body>
</html>
