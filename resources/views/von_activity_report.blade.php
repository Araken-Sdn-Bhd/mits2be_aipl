<table>
    <tbody>
        <tr>
            <td colspan="4" style="font-weight: bold;">REPORT OF VOLUNTEER,OUTREACH AND NETWORKING</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">PERIOD OF SERVICES</td>
            <td>{{$fromDate}}</td>
            <td>To</td>
            <td>{{$toDate}}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">TOTAL DAYS</td>
            <td>{{ $totalDays }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">TOTAL PATIENT</td>
            <td>{{ $totalPatients }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;"></td>
            @foreach($toiArr as $k => $v)
            <td>{{ $k }} <br> {{ $v }}</td>
            @endforeach
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">No</th>
            <th style="font-weight: bold;">NAME/EVENT/PROGRAM</th>
            <th style="font-weight: bold;">TYPE OF COLLABORATION</th>
            <th style="font-weight: bold;">AREA OF INVOLVEMNT</th>
            <th style="font-weight: bold;">COST</th>
            <th style="font-weight: bold;">LOCATION</th>
            <th style="font-weight: bold;">MENTARI CENTER</th>
            <th style="font-weight: bold;">OTHERS</th>
            <th style="font-weight: bold;">SCREENING MODE</th>
            <th style="font-weight: bold;">NO OF PARTICIPANTS</th>
            <th style="font-weight: bold;">CONTACT NUMBER</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shharpRecords as $i => $invoice)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $invoice['Name'] }}</td>
            <td>{{ $invoice['Type_of_Collaboration'] }}</td>
            <td>{{ $invoice['Type_of_Involvement'] }}</td>
            <td>{{ $invoice['Cost'] }}</td>
            <td>{{ $invoice['Location'] }}</td>
            <td>{{ $invoice['Mentari'] }}</td>
            <td>{{ $invoice['Others'] }}</td>
            <td>{{ $invoice['Screening_Done'] }}</td>
            <td>{{ $invoice['No_of_Participants'] }}</td>
            <td>{{ $invoice['Contact_Number'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>