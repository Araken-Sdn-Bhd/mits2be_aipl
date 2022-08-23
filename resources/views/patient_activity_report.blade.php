<table>
    <tbody>
        <tr>
            <td style="font-weight: bold;">PERIOD OF SERVICES</td>
            <td>{{$fromDate}}</td>
            <td>To</td>
            <td>{{$toDate}}</td>
        </tr>
        <tr></tr>
        <tr>
            <td style="font-weight: bold;">TOTAL DAYS</td>
            <td>{{ $totalDays }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">TOTAL PATIENT</td>
            <td>{{ $totalPatients }}</td>
        </tr>
    </tbody>
</table>
<table>
    <thead>
        <tr>
            <th style="font-weight: bold;">No</th>
            <th style="font-weight: bold;">Name</th>
            <th style="font-weight: bold;">Appointment Type</th>
            <th style="font-weight: bold;">Type Of Visit</th>
            <th style="font-weight: bold;">Type Of Refferal</th>
            <th style="font-weight: bold;">IC No</th>
            <th style="font-weight: bold;">Gender</th>
            <th style="font-weight: bold;">Age</th>
            <th style="font-weight: bold;">Diagnosis</th>
            <th style="font-weight: bold;">Medications</th>
            <th style="font-weight: bold;">Appointment No</th>
            <th style="font-weight: bold;">Procedure</th>
            <th style="font-weight: bold;">Next Visit</th>
            <th style="font-weight: bold;">Time Regisered</th>
            <th style="font-weight: bold;">Time Seen</th>
            <th style="font-weight: bold;">Attendance Status</th>
            <th style="font-weight: bold;">Attendee Staff</th>
        </tr>
    </thead>
    <tbody>
        @foreach($shharpRecords as $i => $invoice)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $invoice['Name'] }}</td>
            <td>{{ $invoice['APPOINTMENT_TYPE'] }}</td>
            <td>{{ $invoice['TYPE_OF_Visit'] }}</td>
            <td>{{ $invoice['TYPE_OF_Refferal'] }}</td>
            <td>{{ $invoice['IC_NO'] }}</td>
            <td>{{ $invoice['GENDER'] }}</td>
            <td>{{ $invoice['AGE'] }}</td>
            <td>{{ $invoice['DIAGNOSIS'] }}</td>
            <td>{{ $invoice['MEDICATIONS'] }}</td>
            <td>{{ $invoice['app_no'] }}</td>
            <td>{{ $invoice['Procedure'] }}</td>
            <td>{{ $invoice['Next_visit'] }}</td>
            <td>{{ $invoice['time_registered'] }}</td>
            <td>{{ $invoice['time_seen'] }}</td>
            <td>{{ $invoice['Attendance_status'] }}</td>
            <td>{{ $invoice['Attending_staff'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>