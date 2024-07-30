<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMI</title>
    @vite(['resources/js/app.js'])
    @vite(['resources/css/app.css'])
</head>

<body>
<div id="app" class="app">
    <br>
</div>

<script>
    {{--window.items = @json($name);--}}
</script>


{{--<table border="1">--}}
{{--    <thead>--}}
{{--    <tr>--}}
{{--        <th colspan="3">Penetapan</th>--}}
{{--        <th rowspan="1" colspan="2">Pelaksanaan</th>--}}
{{--    </tr>--}}
{{--    <tr>--}}
{{--        <th>Standard</th>--}}
{{--        <th>Indicator</th>--}}
{{--        <th>Target</th>--}}
{{--        <th>Komentar</th>--}}
{{--        <th>Link Bukti</th>--}}
{{--    </tr>--}}
{{--    </thead>--}}
{{--<td colspan=6>input</td>--}}
{{--    <tbody>--}}
{{--    @foreach($name as $standar)--}}
{{--        @php--}}
{{--            $rowspan = count($standar['indicators']);--}}
{{--        @endphp--}}
{{--        <tr>--}}
{{--            <td rowspan="{{ $rowspan }}">{{ $standar['standar'] }}</td>--}}
{{--            <td>{{ $standar['indicators'][0]['indicator'] }}</td>--}}
{{--            <td>{{ $standar['indicators'][0]['target'] }}</td>--}}
{{--            <td><input type="text"></td>--}}
{{--            <td><input type="text"></td>--}}
{{--        </tr>--}}
{{--        @for($i = 1; $i < $rowspan; $i++)--}}
{{--            <tr>--}}
{{--                <td>{{ $standar['indicators'][$i]['indicator'] }}</td>--}}
{{--                <td>{{ $standar['indicators'][$i]['target'] }}</td>--}}
{{--                <td><input type="text"></td>--}}
{{--                <td><input type="text"></td>--}}
{{--            </tr>--}}
{{--        @endfor--}}
{{--    @endforeach--}}
{{--    </tbody>--}}
{{--</table>--}}


    <style>
        body {
            width: 100vw;
            overflow-x: hidden;
            padding: 3rem;
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
        width: 4rem;
    }

    .app{
        margin-bottom: 10rem;
    }
</style>
</body>

</html>
