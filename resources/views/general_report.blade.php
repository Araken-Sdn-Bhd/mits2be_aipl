<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">Month</th>
            <th style="font-weight: bold;">No of General Report Generated</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight: bold;">GENERAL REPORT</td>
        </tr>

        <tr>
            <td style="font-weight: bold;">PERIOD OF SERVICE</td>
            <td>{{ $fromDate }} To {{$toDate}}</td>
        </tr>

        <tr>
            <td style="font-weight: bold;">TOTAL </td>
            <td>{{ $totalRecord }}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">No</th>
            <th style="font-weight: bold;">REGISTRATION DATE</th>
            <th style="font-weight: bold;">REGISTRATION Time</th>
            <th style="font-weight: bold;">NRIC No</th>
            <th style="font-weight: bold;">CATEGORY OF PATIENT</th>
            <th style="font-weight: bold;">Referral_Type</th>
            <th style="font-weight: bold;">CITIZENSHIP</th>
            <th style="font-weight: bold;">APPOINTMENT TYPE</th>
            <th style="font-weight: bold;">TYPE OF VISIT</th>
            <th style="font-weight: bold;">PATIENT CATEGORY</th>
            <th style="font-weight: bold;">DIAGNOSIS</th>
            <th style="font-weight: bold;">GENDER</th>
            <th style="font-weight: bold;">AGE</th>
            <th style="font-weight: bold;">RACE </th>
            <th style="font-weight: bold;">RELEGION</th>
            <th style="font-weight: bold;">METERIAL STATUS</th>
            <th style="font-weight: bold;">ACCOMODATION</th>
            <th style="font-weight: bold;">EDUCATION LEVEL</th>
            <th style="font-weight: bold;">OCCUPATION STATUS</th>
            <th style="font-weight: bold;">FEE EXEMPTION</th>
            <th style="font-weight: bold;">OCCUPATION SECTOR</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shharpRecords as $invoice)
        <tr>
            <td>{{ $invoice['Registration_date'] }}</td>
            <td>{{ $invoice['Registration_Time'] }}</td>
            <td>{{ $invoice['nric_no'] }}</td>
            <td>{{ $invoice['Name'] }}</td>
            <td>{{ $invoice['TYPE_OF_Refferal'] }}</td>
            <td>{{ $invoice['citizenship'] }}</td>
            <td>{{ $invoice['APPOINTMENT_TYPE'] }}</td>
            <td>{{ $invoice['TYPE_OF_Visit'] }}</td>
            <td>{{ $invoice['CATEGORY_OF_PATIENTS'] }}</td>
            <td>{{ $invoice['DIAGNOSIS'] }}</td>
            <td>{{ $invoice['GENDER'] }}</td>
            <td>{{ $invoice['AGE'] }}</td>
            <td>{{ $invoice['race'] }}</td>
            <td>{{ $invoice['religion'] }}</td>
            <td>{{ $invoice['marital'] }}</td>
            <td>{{ $invoice['accomodation'] }}</td>
            <td>{{ $invoice['education_level'] }}</td>
            <td>{{ $invoice['occupation_status'] }}</td>
            <td>{{ $invoice['fee_exemption_status'] }}</td>
            <td>{{ $invoice['occupation_sector'] }}</td>

        </tr>
        @endforeach
    </tbody>
</table>