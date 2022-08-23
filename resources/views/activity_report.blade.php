<table>
<thead>
        
        </thead>
        <tbody>
            <tr>
            <td style="font-weight: bold;">REPORT OF CULSULTATION CLINIC</td>
            </tr>
    
            <tr>
            <td style="font-weight: bold;">PERIOD OF SERVICES</td>
                <td>{{ $fromDate }} To {{$toDate}}</td>
            </tr>
           
            <tr>
                <td style="font-weight: bold;">TOTAL DAYS</td>
                <td>{{ $totalRecord }}</td>
            </tr>

            <tr>
                <td style="font-weight: bold;">TOTAL PATIENT</td>
                <td>{{ $totalRecord }}</td>
            </tr>
        </tbody>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">DATE</th>
            <th style="font-weight: bold;">Time</th>
            <th style="font-weight: bold;">NAME</th> 
            <th style="font-weight: bold;">APPOINTMENT TYPE</th>
            <th style="font-weight: bold;">REFERRAL_TYPE</th>
            <th style="font-weight: bold;">NRIC_NO_PASSPORT_NO</th>
            <th style="font-weight: bold;">Gender</th>
            <th style="font-weight: bold;">AGE</th>
            <th style="font-weight: bold;">DIAGONISIS</th>
            <th style="font-weight: bold;">PROCEDURE</th>
            <th style="font-weight: bold;">NEXT VISIT</th>
            <th style="font-weight: bold;">TIME REGISTERED</th>
            <th style="font-weight: bold;">TIME SEEN</th>
            <th style="font-weight: bold;">ATTENDENCE STATUS</th>
            <th style="font-weight: bold;">ATTENDENCE STAFF</th>
             
        </tr>
    </thead>
    <tbody>
        @foreach($shharpRecords as $invoice)
        <tr>
            <td>{{ $invoice['DATE'] }}</td>
            <td>{{ $invoice['Time'] }}</td>
            <td>{{ $invoice['Name'] }}</td>
            <td>{{ $invoice['APPOINTMENT_TYPE'] }}</td>
            <td>{{ $invoice['REFERRAL_TYPE'] }}</td>
            <td>{{ $invoice['NRIC_NO_PASSPORT_NO'] }}</td>
            <td>{{ $invoice['Gender'] }}</td>
            <td>{{ $invoice['Age'] }}</td>
            <td>{{ $invoice['Diagnosis'] }}</td>
            <td>{{ $invoice['PROCEDURE'] }}</td>
            <td>{{ $invoice['NEXT_VISIT'] }}</td>
            <td>{{ $invoice['TIME_REGISTERED'] }}</td>
            <td>{{ $invoice['TIME_SEEN'] }}</td>
            <td>{{ $invoice['ATTENDANCE_STATUS'] }}</td> 
            <td>{{ $invoice['ATTENDING_STAFF'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>