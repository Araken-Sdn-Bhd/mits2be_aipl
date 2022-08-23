<table>
    <tbody>
        <tr>
            <td colspan="4" style="font-weight: bold;">TOTAL PATIENT AND TYPE OF REFFERAL</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">TOTAL DAYS</td>
            <td colspan="3">{{ $totalDays }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">TOTAL PATIENT</td>
            <td colspan="3">{{ $totalPatients }}</td>
        </tr>
        <tr>
            <td>1</td>
            <td style="font-weight: bold;">CATEGORY OF PATIENT</td>
            @foreach($patientCategories as $k => $v)
            <td>{{ $k }} <br> {{ $v }}</td>
            @endforeach
        </tr>
        <tr>
            <td>2</td>
            <td style="font-weight: bold;">TYPE OF VISIT</td>
            @foreach($visitTypes as $k => $v)
            <td>{{ $k }} <br> {{ $v }}</td>
            @endforeach
        </tr>
        <tr>
            <td>3</td>
            <td style="font-weight: bold;">TYPE OF REFFERAL</td>
            @foreach($refferals as $k => $v)
            <td>{{ $k }} <br> {{ $v }}</td>
            @endforeach
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">No</th>
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
            <th style="font-weight: bold;">CATEGORY_OF_PATIENTS</th>
            <th style="font-weight: bold;">TYPE_OF_Visit</th>
            <th style="font-weight: bold;">TYPE_OF_Refferal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shharpRecords as $i => $invoice)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $invoice['DATE'] }}</td>
            <td>{{ $invoice['TIME'] }}</td>
            <td>{{ $invoice['NRIC_NO_PASSPORT_NO'] }}</td>
            <td>{{ $invoice['Name'] }}</td>
            <td>{{ $invoice['ADDRESS'] }}</td>
            <td>{{ $invoice['CITY'] }}</td>
            <td>{{ $invoice['STATE'] }}</td>
            <td>{{ $invoice['POSTCODE'] }}</td>
            <td>{{ $invoice['PHONE_NUMBER'] }}</td>
            <td>{{ $invoice['DATE_OF_BIRTH'] }}</td>
            <td>{{ $invoice['CATEGORY_OF_PATIENTS'] }}</td>
            <td>{{ $invoice['TYPE_OF_Visit'] }}</td>
            <td>{{ $invoice['TYPE_OF_Refferal'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>