<?php

namespace App\Http\Controllers;
use App\Models\Notifications;
use App\Models\ScreenAccessRoles;
use App\Models\StaffManagement;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class NotificationController extends Controller
{
    public function getNotification(Request $request)
    {
        $staff_id=$request->user_id; //staff id is user id 
        $screen_access= ScreenAccessRoles::select('screen_id')->where('staff_id',$staff_id)->where('branch_id',$request->branch_id)->get()->toArray();
        $ab=[];
        $count=0;
$index=0;

        foreach ($screen_access as $key => $v){

            $result1 = Notifications::select('*')
            ->where('branch_id', '=', $request->branch_id)
            ->where('staff_id', '=', $staff_id)
            ->orderBy('id', 'DESC')
            ->get()->toArray();
                    
            
            if ($result1){

foreach($result1 as $k=>$r){

    
            $datetime1 = new DateTime();
            $datetime12 = new DateTime($r['created_at']);

            if (DATE_FORMAT($datetime12, 'Y-m-d') == date('Y-m-d')) {
                $ab[$count]['time']  = $datetime1->diff(new DateTime($r['created_at']))->format('%h hours %i minutes');
                $ab[$count]['time_order']=$datetime1->diff(new DateTime($r['created_at']));
            } else {
                $ab[$count]['time_order']=$datetime1->diff(new DateTime($r['created_at']));
                $ab[$count]['time']  = $datetime1->diff(new DateTime($r['created_at']))->format('%a days %h hours %i minutes');
            }
            
            $ab[$count]['id']  = $r['id'] ??= $r;
            $ab[$count]['message']  = $r['message'];
            $ab[$count]['patient_mrn']  = $r['patient_mrn'];
            $ab[$count]['url_route']  = $r['url_route'];
            $count++;
    
           
}
            }
            $result2 = Notifications::select('*')
            ->where('branch_id', '=', $request->branch_id)
            ->where('staff_id', '=', 0)
            ->Where('screen_id', '=', $v['screen_id'])
            ->orderBy('id', 'DESC')
            ->get()->toArray();
                    
            
            if ($result2){

foreach($result2 as $k=>$r){

    
            $datetime1 = new DateTime();
            $datetime12 = new DateTime($r['created_at']);

            if (DATE_FORMAT($datetime12, 'Y-m-d') == date('Y-m-d')) {
                $ab[$count]['time']  = $datetime1->diff(new DateTime($r['created_at']))->format('%h hours %i minutes');
                $ab[$count]['time_order']=$datetime1->diff(new DateTime($r['created_at']));
            } else {
                $ab[$count]['time_order']=$datetime1->diff(new DateTime($r['created_at']));
                $ab[$count]['time']  = $datetime1->diff(new DateTime($r['created_at']))->format('%a days %h hours %i minutes');
            }
            
            $ab[$count]['id']  = $r['id'] ??= $r;
            $ab[$count]['message']  = $r['message'];
            $ab[$count]['patient_mrn']  = $r['patient_mrn'];
            $ab[$count]['url_route']  = $r['url_route'];
            $count++;
    
           
}
            }
        }

        $result = array_reverse(array_values(array_column(
            array_reverse($ab),
            null,
            'id'
        )));
        $index=count($result);
  
            return response()->json(["message" => "Notifications List", 'list' => $result, 'notification_count' => $index, "code" => 200]);

          
        
    }


    public function deleteNotification(Request $request)
    {
        $list = Notifications::where('id',$request->notifi_id)->delete();

        return response()->json(["message" => "Notifications Deleted", "code" => 200]);
    }
}
