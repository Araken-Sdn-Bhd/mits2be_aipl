<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">Month</th>
            <th style="font-weight: bold;">No of Shharp Report Generated</th>
        </tr>
    </thead>
    <tbody>
        @php
        $total = 0;
        @endphp
        @foreach($totalRecord as $invoice)
        @php
        $total += $invoice['total'];
        @endphp
        <tr>
            <td>{{ $invoice['month'] }}</td>
            <td>{{ $invoice['total'] }}</td>
        </tr>
        @endforeach
        <tr>
            <td style="font-weight: bold;">Total</td>
            <td>{{ $total }}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">DATE</th>
            <th style="font-weight: bold;">Time</th>
            <th style="font-weight: bold;">NRIC_NO_PASSPORT_NO</th>
            <th style="font-weight: bold;">Name</th>
            <th style="font-weight: bold;">ADDRESS</th>
            <th style="font-weight: bold;">CITY</th>
            <th style="font-weight: bold;">STATE</th>
            <th style="font-weight: bold;">POSTCODE</th>
            <th style="font-weight: bold;">PHONE_NUMBER</th>
            <th style="font-weight: bold;">DATE_OF_BIRTH</th>
            <th style="font-weight: bold;">RISK_FACTOR</th>
            <th style="font-weight: bold;">PROTECTIVE_FACTOR</th>
            <th style="font-weight: bold;">METHOD_OF_SELF_HARM</th>
            <th style="font-weight: bold;">SUCIDAL_INTENT</th>
            <th style="font-weight: bold;">IDEA_OF_METHOD</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shharpRecords as $invoice)
        <tr>
            <td>{{ $invoice['DATE'] }}</td>
            <td>{{ $invoice['Time'] }}</td>
            <td>{{ $invoice['NRIC_NO_PASSPORT_NO'] }}</td>
            <td>{{ $invoice['Name'] }}</td>
            <td>{{ $invoice['ADDRESS'] }}</td>
            <td>{{ $invoice['CITY'] }}</td>
            <td>{{ $invoice['STATE'] }}</td>
            <td>{{ $invoice['POSTCODE'] }}</td>
            <td>{{ $invoice['PHONE_NUMBER'] }}</td>
            <td>{{ $invoice['DATE_OF_BIRTH'] }}</td>
            <td>{{ $invoice['RISK_FACTOR'] }}</td>
            <td>{{ $invoice['PROTECTIVE_FACTOR'] }}</td>
            <td>{{ $invoice['METHOD_OF_SELF_HARM'] }}</td>
            <td>{{ $invoice['SUCIDAL_INTENT'] }}</td>
            <td>{{ $invoice['IDEA_OF_METHOD'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>