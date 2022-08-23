<table>
    <thead>
        
    </thead>
    <tbody>
        <tr>
        <td style="font-weight: bold;">REQUEST APPOINTMENT</td>
        </tr>

        <tr>
        <td style="font-weight: bold;"> SERVICES</td>
            <td>{{ $fromDate }} To {{$toDate}}</td>
        </tr>
       
        <tr>
            <td style="font-weight: bold;">Total Request</td>
            <td>{{ $totalRecord }}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">No</th>
            <th style="font-weight: bold;">Name</th>
            <th style="font-weight: bold;">NRIC_NO_PASSPORT_NO</th>
            <th style="font-weight: bold;">ADDRESS</th>
            <th style="font-weight: bold;">CITY</th>
            <th style="font-weight: bold;">STATE</th>
            <th style="font-weight: bold;">POSTCODE</th>
            <th style="font-weight: bold;">CONTACT NUMBER</th>
            <th style="font-weight: bold;">EMAIL</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shharpRecords as $k=> $invoice)
        <tr>
            <td>{{ $k+1 }}</td>
            <td>{{ $invoice['Name'] }}</td>
            <td>{{ $invoice['NRIC_NO_PASSPORT_NO'] }}</td>
            <td>{{ $invoice['ADDRESS'] }}</td>
            <td>{{ $invoice['CITY'] }}</td>
            <td>{{ $invoice['STATE'] }}</td>
            <td>{{ $invoice['POSTCODE'] }}</td>
            <td>{{ $invoice['PHONE_NUMBER'] }}</td>
            <td>{{ $invoice['EMAIL'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>