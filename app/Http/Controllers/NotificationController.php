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
//    public function getNotification(Request $request)
//    {
//        $staff_id=$request->user_id; //staff id is user id
//        $screen_access= ScreenAccessRoles::select('screen_id')->where('staff_id',$staff_id)->where('branch_id',$request->branch_id)->get()->toArray();
//        $ab=[];
//        $count=0;
//$index=0;
//
//        foreach ($screen_access as $key => $v){
//
//            $result1 = Notifications::select('*')
//            ->where('branch_id', '=', $request->branch_id)
//            ->where('staff_id', '=', $staff_id)
//            ->orderBy('id', 'DESC')
//            ->get()->toArray();
//
//
//            if ($result1){
//
//foreach($result1 as $k=>$r){
//
//
//            $datetime1 = new DateTime();
//            $datetime12 = new DateTime($r['created_at']);
//
//            if (DATE_FORMAT($datetime12, 'Y-m-d') == date('Y-m-d')) {
//                $ab[$count]['time']  = $datetime1->diff(new DateTime($r['created_at']))->format('%h hours %i minutes');
//                $ab[$count]['time_order']=$datetime1->diff(new DateTime($r['created_at']));
//            } else {
//                $ab[$count]['time_order']=$datetime1->diff(new DateTime($r['created_at']));
//                $ab[$count]['time']  = $datetime1->diff(new DateTime($r['created_at']))->format('%a days %h hours %i minutes');
//            }
//
//            $ab[$count]['id']  = $r['id'] ??= $r;
//            $ab[$count]['message']  = $r['message'];
//            $ab[$count]['patient_mrn']  = $r['patient_mrn'];
//            $ab[$count]['url_route']  = $r['url_route'];
//            $count++;
//
//
//}
//            }
//            $result2 = Notifications::select('*')
//            ->where('branch_id', '=', $request->branch_id)
//            ->where('staff_id', '=', 0)
//            ->Where('screen_id', '=', $v['screen_id'])
//            ->orderBy('id', 'DESC')
//            ->get()->toArray();
//
//
//            if ($result2){
//
//foreach($result2 as $k=>$r){
//
//
//            $datetime1 = new DateTime();
//            $datetime12 = new DateTime($r['created_at']);
//
//            if (DATE_FORMAT($datetime12, 'Y-m-d') == date('Y-m-d')) {
//                $ab[$count]['time']  = $datetime1->diff(new DateTime($r['created_at']))->format('%h hours %i minutes');
//                $ab[$count]['time_order']=$datetime1->diff(new DateTime($r['created_at']));
//            } else {
//                $ab[$count]['time_order']=$datetime1->diff(new DateTime($r['created_at']));
//                $ab[$count]['time']  = $datetime1->diff(new DateTime($r['created_at']))->format('%a days %h hours %i minutes');
//            }
//
//            $ab[$count]['id']  = $r['id'] ??= $r;
//            $ab[$count]['message']  = $r['message'];
//            $ab[$count]['patient_mrn']  = $r['patient_mrn'];
//            $ab[$count]['url_route']  = $r['url_route'];
//            $count++;
//
//
//}
//            }
//        }
//
//        $result = array_reverse(array_values(array_column(
//            array_reverse($ab),
//            null,
//            'id'
//        )));
//        $index=count($result);
//
//            return response()->json(["message" => "Notifications List", 'list' => $result, 'notification_count' => $index, "code" => 200]);
//
//
//
//    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getNotification(Request $request)
    {
        $staff_id = $request->user_id; // Staff id is user id
        $screen_access = ScreenAccessRoles::where('staff_id', $staff_id)
            ->where('branch_id', $request->branch_id)
            ->pluck('screen_id')
            ->toArray();

        $notifications = Notifications::where(function ($query) use ($staff_id, $request) {
            $query->where('staff_id', $staff_id)
                ->where('branch_id', $request->branch_id);
        })
            ->orWhere(function ($query) use ($screen_access, $request) {
                $query->where('staff_id', 0)
                    ->whereIn('screen_id', $screen_access)
                    ->where('branch_id', $request->branch_id);
            })
            ->orderBy('id', 'DESC')
            ->take(30)
            ->get();

        $result = $notifications->map(function ($notification) {
            $timeDiff = $notification->created_at->diffForHumans();
            $timeOrder = $notification->created_at->diff(new DateTime());
            return [
                'id' => $notification->id,
                'message' => $notification->message,
                'patient_mrn' => $notification->patient_mrn,
                'url_route' => $notification->url_route,
                'time' => $timeDiff,
                'time_order' => $timeOrder,
            ];
        })->toArray();

        $notificationCount = count($result);

        return response()->json([
            "message" => "Notifications List",
            'list' => $result,
            'notification_count' => $notificationCount,
            "code" => 200
        ]);
    }


    public function deleteNotification(Request $request)
    {
        $list = Notifications::where('id',$request->notifi_id)->delete();

        return response()->json(["message" => "Notifications Deleted", "code" => 200]);
    }
}
