<?php

namespace App\Http\Controllers;
use App\Models\Notifications;
use App\Models\ScreenAccessRoles;
use App\Models\StaffManagement;
use DateTime;
use Illuminate\Http\Request;


class NotificationController extends Controller
{
    public function getNotification(Request $request)
    {
        $staff_id=$request->user_id; //staff id is user id 
        $screen_access= ScreenAccessRoles::select('screen_id')->where('staff_id',$staff_id)->where('branch_id',$request->branch_id)->get()->toArray();
        $ab=[];
        $count=0;

        foreach ($screen_access as $key => $v){

            $result = Notifications::select('*')
            ->where('branch_id', '=', $request->branch_id)
            ->where(function ($query) use ($v,$staff_id){
                $query->where('staff_id', '=', $staff_id)
                ->orWhere('screen_id', '=', $v['screen_id']);
                        })
            ->orderBy('id', 'DESC')
            ->first();
            
            
            if ($result){
  

            $count++;
            $datetime1 = new DateTime();
            $datetime12 = new DateTime($result['created_at']);

            if (DATE_FORMAT($datetime12, 'Y-m-d') == date('Y-m-d')) {
                $ab[$key]['time']  = $datetime1->diff(new DateTime($result['created_at']))->format('%h hours %i minutes');
                $ab[$key]['time_order']=$datetime1->diff(new DateTime($result['created_at']));
            } else {
                $ab[$key]['time_order']=$datetime1->diff(new DateTime($result['created_at']));
                $ab[$key]['time']  = $datetime1->diff(new DateTime($result['created_at']))->format('%a days %h hours %i minutes');
            }
            
            $ab[$key]['id']  = $result['id'];
            $ab[$key]['message']  = $result['message'];
            $ab[$key]['patient_mrn']  = $result['patient_mrn'];
            $ab[$key]['url_route']  = $result['url_route'];
        
}
        }
 

            return response()->json(["message" => "Notifications List", 'list' => $ab, 'notification_count' => $count, "code" => 200]);

          
        
    }


    public function deleteNotification(Request $request)
    {
        $list = Notifications::where('id',$request->notifi_id)->delete();

        return response()->json(["message" => "Notifications Deleted", "code" => 200]);
    }
}
