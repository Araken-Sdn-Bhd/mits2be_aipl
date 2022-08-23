<!-- <table>
    <tr>
        <td colspan="2" style="border-bottom:none"></td>
        @foreach($yearArray as $i => $year)
        <td colspan="{{ count($year)}}" style="text-align:center">{{ $i }}</td>
        @endforeach
    </tr>
    <tr>
        <td colspan="2" style="border-top:none">dddd</td>
        @foreach($yearArray as $i => $year)
        @foreach($year as $month)
        <td>{{ $month }}</td>
        @endforeach
        @endforeach
    </tr>
    <tr>
        <td>Bill</td>
        <td>Mentari</td>
        <td>
            <table>
                <tr>
                    <td style="writing-mode: tb-rl;">1st dsd dsdsd</td>
                    <td>1st</td>
                    <td>1st</td>
                    <td>1st</td>
                    <td>1st</td>
                </tr>
            </table>
        </td>
        <td>
            <table>
                <tr>
                    <td>1st</td>
                    <td>1st</td>
                    <td>1st</td>
                    <td>1st</td>
                    <td>1st</td>
                </tr>
            </table>
        </td>
    </tr>

</table> -->

<style type="text/css">
table td {
    border: 1px solid #000;
    padding: 5px;
    opacity: 0.8;
    font-weight: 500;
}

table {
    width: auto;
    border-spacing: 0;
    border: 1px solid #000;
    text-align: center;
}

.tr-box td:first-child {
    text-align: left;
}

.last-tr td {
    font-weight: 600;
    opacity: 1
}

.vertical td {
    transform: rotate(-180deg);
    writing-mode: vertical-lr;
    text-orientation: mixed;
}
.bg{
        background: #b8cce4;
}
</style>
<table>
    <tbody>
        <tr class="bg">
            <td rowspan="3">Bil</td>
            <td rowspan="3">Mentari</td>
            @foreach($yearArray as $i => $year)
                <td colspan="{{ count($year)*5 }}" style="text-align:center">{{ $i }}</td>
            @endforeach
        </tr>
        <tr  class="bg">
        <!-- <td colspan="2" style="border-top:none"></td> -->
        @foreach($yearArray as $i => $year)
            @foreach($year as $month)
            <td colspan="5" style="text-align:center">{{  $months[$month-1] }}</td>
            @endforeach
        @endforeach
        </tr>
       
        <tr class="vertical bg">
        @foreach($yearArray as $i => $year)
        @foreach($year as $month)
            <td class="first-td" style=" transform: rotate(-180deg);writing-mode: vertical-lr;text-orientation: mixed;">Newly Job Place(a)</td>
            <td>ongoing Job Placement(b)</td>
            <td>Total Caseload(c)</td>
            <td>Total Dismissed(d)</td>
            <td class="fifth-td">KPI(%)</td>
            @endforeach
        @endforeach
        </tr>
        @php ($index = 1)
        @foreach($shharpRecords as $mentari => $yearArr)
            @foreach($yearArr as $year => $MonthArr)
            <tr class="tr-box">
            <td>{{$index++}}</td>
                <td>{{$mentari}}</td>
            @foreach($MonthArr as $month => $rec)
            
                
                <td>{{(string)$rec['new_job']}}</td>
                <td>{{$rec['ongoing_job']}}</td>
                <td>{{$rec['total_caseload']}}</td>
                <td>{{$rec['total_dismissed']}}</td>
                <td>{{$rec['kpi']}}</td>
               
               
                @endforeach
                </tr>
            @endforeach
        @endforeach
       
       
        <!-- <tr class="tr-box last-tr">
            <td></td>
            <td></td>
            <td colspan="4">AVERAGE</td>
            <td>65.3</td>
            <td colspan="4">AVERAGE</td>
            <td>65.3</td>
            <td colspan="4">AVERAGE</td>
            <td>65.3</td>
        </tr> -->
    </tbody>
</table>