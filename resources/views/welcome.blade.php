<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMI</title>
    @vite(['resources/js/app.js'])
    @vite(['resources/css/app.css'])
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
    />
</head>

<body>
<div id="app" class="app">
    <br>
</div>

<div id="modal" class="Modal">

</div>

    <style>
        body {
            width: 100vw;
            overflow-x: hidden;
            /*padding: 3rem;*/
        }

        table,
        th,
        td {
            border: 1px solid black;
            text-align: center;
            padding: 5px;
            width: 2rem;
        }

    button{
        border-style: none;
        width: 3rem;
        height: 2rem;
    }
</style>
</body>

</html>
