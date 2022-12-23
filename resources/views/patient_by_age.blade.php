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
    <tr class="tr-box">
      <td rowspan="3">Ethnic Group</td>
      <td colspan="11">TOTAL ATTENDANCE</td>
    </tr>
    <tr class="tr-box">
        <td colspan="2">&lt; 10 years</td>
        <td colspan="2">10-19 years</td>
        <td colspan="2">20-59 years</td>
        <td colspan="2">&gt;=60 years</td>
        <td colspan="2">Total by Gender</td>
        <td rowspan="2">Total by Race</td>
      </tr>
    <tr class="tr-box">
        <td>Male</td>
        <td>Female</td>
        <td>Male</td>
        <td>Female</td>
        <td>Male</td>
        <td>Female</td>
        <td>Male</td>
        <td>Female</td>
        <td>Male</td>
        <td>Female</td>
    </tr>

    @foreach($shharpRecords as $i => $year)
        @foreach($year as $month => $year1)
        @foreach($year1 as $ethnic_group => $year2)
        <tr class="tr-box">
        <td>{{$ethnic_group}}</td>
        {{-- <!-- @foreach($year2 as $data1 => $year3) --> --}}

        <td>{{$year2['below_10']['male']}}</td>
        <td>{{$year2['below_10']['female']}}</td>
        <td>{{$year2['10-19']['male']}}</td>
        <td>{{$year2['10-19']['female']}}</td>
        <td>{{$year2['20-59']['male']}}</td>
        <td>{{$year2['20-59']['female']}}</td>
        <td>{{$year2['greater_60']['male']}}</td>
        <td>{{$year2['greater_60']['female']}}</td>
        <td>{{$year2['total']['male']}}</td>
        <td>{{$year2['total']['female']}}</td>
        <td>{{$year2['jumlah_besar']}}</td>

        {{-- <!-- @endforeach --> --}}
        </tr>
        @endforeach
        <tr class="tr-box">
          <td colspan="11">Total</td>

          <td> {{ $totalReports }}</td>
        </tr>
        @endforeach
       
        @endforeach


  </tbody>
</table>