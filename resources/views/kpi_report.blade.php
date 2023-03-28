<style type="text/css">

   </style>
   
   
  
   
   <table>
    
    <tbody>
            
<tr class="tr-box">
            <td rowspan="3">BIL</td>
            <td rowspan="3" colspan="3">MENTARI</td>
            <td colspan="12" rowspan="1"> YEAR {{ $kpiyear }} </td>       
</tr>



<tr>
    @foreach($kpirecord as $i => $record)
@foreach($record as $r => $branch)
@endforeach
@foreach($branch as $m => $month)


    {{-- foreach untuk month akan mula dekat sini --}}
    @if($m ==1)
    <td colspan="4">January</td>
    @endif
    @if($m ==2)
    <td colspan="4">February</td>
    @endif
    @if($m ==3)
    <td colspan="4">March</td>
    @endif
    @if($m ==4)
    <td colspan="4">April</td>
    @endif
    @if($m ==5)
    <td colspan="4">May</td>
    @endif
    @if($m ==6)
    <td colspan="4">June</td>
    @endif
    @if($m ==7)
    <td colspan="4">July</td>
    @endif
    @if($m ==8)
    <td colspan="4">August</td>
    @endif
    @if($m ==9)
    <td colspan="4">September</td>
    @endif
    @if($m ==10)
    <td colspan="4">October</td>
    @endif
    @if($m ==11)
    <td colspan="4">November</td>
    @endif
    @if($m ==12)
    <td colspan="4">December</td>
    @endif

@endforeach
@endforeach
</tr>
<tr>
    @foreach($kpiaverage as $a => $average)
    @foreach($average as $av => $ave)


    {{-- foreach untuk month akan mula dekat sini --}}
    @if($av==1)
    <td>Newly Job Placed</td> {{-- Jan --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==2)
    <td>Newly Job Placed</td> {{-- Feb --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==3)
    <td>Newly Job Placed</td> {{-- Mar --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==4)
    <td>Newly Job Placed</td> {{-- Apr --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==5)
    <td>Newly Job Placed</td> {{-- May --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==6)
    <td>Newly Job Placed</td> {{-- Jun --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==7)
    <td>Newly Job Placed</td> {{-- Jul --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==8)
    <td>Newly Job Placed</td> {{-- Aug --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==9)
    <td>Newly Job Placed</td> {{-- Sep --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==10)
    <td>Newly Job Placed</td> {{-- Oct --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==11)
    <td>Newly Job Placed</td> {{-- Nov --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

    @if($av==12)
    <td>Newly Job Placed</td> {{-- Dec --}}
    <td>Ongoing Job Placement</td>
    <td>Total Caseload</td>
    <td>KPI (%)</td>
    @endif

@endforeach
@endforeach

</tr>

    @foreach($kpirecord as $i => $record)
   @foreach($record as $r => $branch)
  
   <tr>
    <td>{{$loop->iteration}}</td>
   <td colspan="3">{{ $r }} </td>
   @foreach($branch as $m => $month)
   @foreach($month as $v => $value)
   <td>{{ $value }} </td>
   @endforeach
   @endforeach
   @endforeach
   @endforeach

</tr>
<tr>
    <td colspan="4"></td>

    
    @foreach($kpiaverage as $a => $average)
    @foreach($average as $av => $ave)

        @if($av==1)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==2)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==3)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==4)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==5)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==6)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==7)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==8)
        <td colspan="4">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==9)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==10)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==11)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif

        @if($av==12)
        <td colspan="3">Average</td>
        <td>{{ $ave }}</td>
        @endif
        
        

    @endforeach
    @endforeach

</tr>
     </tbody>
   </table>

   {{-- @foreach($shharpRecords as $i => $year)
   @foreach($year as $month => $year1)
   @foreach($year1 as $ethnic_group => $year2)
   <tr class="tr-box">
   <td>{{$ethnic_group}}</td> --}}
   {{-- <!-- @foreach($year2 as $data1 => $year3) --> --}}

   {{-- <td>{{$year2['below_10']['male']}}</td>
   <td>{{$year2['below_10']['female']}}</td>
   <td>{{$year2['10-19']['male']}}</td>
   <td>{{$year2['10-19']['female']}}</td>
   <td>{{$year2['20-59']['male']}}</td>
   <td>{{$year2['20-59']['female']}}</td>
   <td>{{$year2['greater_60']['male']}}</td>
   <td>{{$year2['greater_60']['female']}}</td>
   <td>{{$year2['total']['male']}}</td>
   <td>{{$year2['total']['female']}}</td>
   <td>{{$year2['jumlah_besar']}}</td> --}}

   {{-- <!-- @endforeach --> --}}
   {{-- </tr>
   @endforeach --}}