<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title inertia>SPMI</title>
    @vite(['resources/js/app.js'])
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    @inertiaHead
</head>
<body class="font-sans antialiased w-screen bg-gray-100 overflow-x-hidden">
    @inertia

    <style>
        button {
            border-style: none;
            width: 3rem;
            height: 2rem;
        }
    </style>
</body>
</html>
