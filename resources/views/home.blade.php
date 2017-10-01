<!doctype html>

<html lang="{{ app()->getLocale() }}">

    <head>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>EVE Stock Manager</title>

    </head>

    <body>

        <p>
            <img src="{{ $user->avatar }}" width="40" height="40" alt="{{ $user->name }}" style="border-radius: 20px;">
            Welcome back, {{ $user->name }}! 
            <a href="/logout">Logout</a>
        </p>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Location</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($assets as $row)
                    <tr>
                        <td>{{ $row->typeName }}</td>
                        <td>{{ $row->location_name }}</td>
                        <td>{{ $row->quantity }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </body>

</html>
