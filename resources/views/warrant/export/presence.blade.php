<!DOCTYPE html>
<html>

<body>
    <table width="100%">
        <thead>
            <tr>
                <th colspan="10" style="text-align:center;">
                    <h3>Presence Report Of Warrant {{ $warrant['warrant']['name'] }}
                    </h3>
                </th>
            </tr>
        </thead>
        <thead>
            <tr>
                <th>
                    No
                </th>
                <th>
                    Date Time
                </th>
                <th>
                    Lat-Long
                </th>
                <th>
                    Address
                </th>
                <th>
                    Attachment
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($warrant['warrant']['presence'] as $index => $presence)
                <tr>
                    <td>
                        {{ $index + 1 }}
                    </td>
                    <td>
                        {{ date('d F Y H:i:s', strtotime($presence['created_at'])) }}
                    </td>
                    <td>
                        {{ $presence['latitude'] . ', ' . $presence['longitude'] }}
                    </td>
                    <td>
                        {{ $presence['address'] }}
                    </td>
                    <td>
                        <img width="45%" alt="upload" src="{{ $presence['attachment'] }}">
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
