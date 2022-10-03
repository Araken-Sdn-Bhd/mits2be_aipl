<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientRegistration;
use App\Models\GeneralSetting;
use App\Models\HospitalBranchManagement;
use App\Models\HospitalBranchTeamManagement;
use App\Models\PatientAppointmentDetails;
use App\Models\Postcode;
use App\Models\SharpRegistrationFinalStep;
use App\Models\StaffManagement;
use App\Models\State;
use Validator;
use Exception;
use Illuminate\Support\Facades\DB;
use mysqli;
use mysqli_result;
use PhpOffice\PhpSpreadsheet\Calculation\TextData\Replace;

class PatientDetailsController extends Controller
{
    public function demmographicDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $details = PatientRegistration::select('id', 'patient_mrn', 'sex', 'birth_date', 'mobile_no', 'citizenship', 'marital_id', 'drug_allergy', 'drug_allergy_description', 'traditional_medication', 'traditional_description', 'other_allergy', 'other_description')->where('id', $request->patient_id)->get();
        $result = [];
        $result['id'] = $details[0]->id;
        $result['patient_mrn'] = $details[0]->patient_mrn;
        $result['birth_date'] = date('d/m/Y', strtotime($details[0]->birth_date));
        $result['age'] = date_diff(date_create($details[0]->birth_date), date_create('today'))->y . " Years old";
        $result['gender'] = $this->getGeneralSettingValue($details[0]->sex);
        $result['marital_status'] = $this->getGeneralSettingValue($details[0]->marital_id);
        $result['contact_no'] = $details[0]->mobile_no;
        $result['Allergies'] = [
            'Drug Allergy' => ($details[0]->drug_allergy == '1') ? 'yes' : 'No',
            'Drug Allergy Desc' => ($details[0]->drug_allergy == '1') ? $details[0]->drug_allergy_description : '--',
            'Traditional Medication/Supplement Allergy' => ($details[0]->traditional_medication == '1') ? 'yes' : 'No',
            'Traditional Medication/Supplement Allergy Desc' => ($details[0]->traditional_medication == '1') ? $details[0]->traditional_description : '--',
            'Others Allergy' => ($details[0]->other_allergy == '1') ? 'yes' : 'No',
            'Others Allergy Desc' => ($details[0]->other_allergy == '1') ? $details[0]->other_description : '--',
        ];
        $result['nationality'] = ($details[0]->citizenship == 0) ? 'Malaysian' : (($details[0]->citizenship == 1) ? 'Permanent Resident' : 'Foreigner');

        return response()->json(["message" => "Patient Demographic Details", 'details' => $result, "code" => 200]);
    }

    public function getGeneralSettingValue($id)
    {
        $val = GeneralSetting::where('id', $id)->pluck('section_value');
        return $val[0];
    }

    public function serachPatient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'keyword' => 'required',
            'branch_id' => 'required|integer',
            'service_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $search = [];
        if ($request->branch_id != 0) {
            $search['branch_id'] = $request->branch_id;
        }
        if ($request->service_id != "0") {
            $search['services_type'] = $request->service_id;
        }

        $searchWord = $request->keyword;
        $resultSet = [];
        if ($searchWord) {
            $sql = PatientRegistration::select('id', 'patient_mrn', 'name_asin_nric', 'passport_no', 'nric_no', 'salutation_id','services_type')
            ->where('sharp', '=', '0');
            if (count($search) > 0) {
        
                $sql = $sql->where(function ($query) use ($searchWord) {

                    $query->where('patient_mrn', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('name_asin_nric', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('passport_no', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('nric_no', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('services_type', '=', $searchWord)
                        ->orWhereRaw("REPLACE(nric_no, '-', '') LIKE ?", ["%$searchWord%"]);
                        });
                        $sql->where($search);
            } else {
                $sql = $sql->where(function ($query) use ($searchWord) {

                    $query->where('patient_mrn', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('name_asin_nric', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('passport_no', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('nric_no', 'LIKE', '%' . $searchWord . '%')
                        ->orWhere('services_type', '=', $searchWord)
                        ->orWhereRaw("REPLACE(nric_no, '-', '') LIKE ?", ["%$searchWord%"]);
                });
            }
            $resultSet =
                $sql->with('salutation:section_value,id')->with('service:service_name,id')
                ->with('appointments', function ($query) {
                    $query->where('appointment_status', '=', '1');
                })
                ->get()->toArray();
        }
        // dd($resultSet);
        $result = [];
        if ($request->keyword == "no-keyword") {
            if(!$search){
            $list = PatientRegistration::where('status', '=', '1')->where('sharp', '=', '0')
                ->with('salutation:section_value,id')->with('service:service_name,id')
                ->with('appointments', function ($query) {
                    $query->where('appointment_status', '=', '1');
                })
                ->get()->toArray();
            }else{

                $list = PatientRegistration::where('status', '=', '1')->where('sharp', '=', '0')
                ->with('salutation:section_value,id')->with('service:service_name,id')
                ->with('appointments', function ($query) {
                    $query->where('appointment_status', '=', '1');
                })
                ->where($search)
                ->get()->toArray();   
            }
            // dd($list);
            // $result = [];
            foreach ($list as $key => $val) {
                $result[$key]['patient_mrn'] = $val['patient_mrn'];
                $result[$key]['name_asin_nric'] = $val['name_asin_nric'];
                $result[$key]['id'] = $val['id'];
                $result[$key]['age'] = date_diff(date_create($val['birth_date']), date_create('today'))->y;
                $result[$key]['nric_no'] = $val['nric_no'];
                $result[$key]['passport_no'] = $val['passport_no'];
                if ($val['salutation'] != null) {
                    $result[$key]['salutation'] = $val['salutation'][0]['section_value'];
                } else {
                    $result[$key]['salutation'] = 'NA';
                }
                if ($val['service'] != null) {
                    // dd($val['service']['id']);
                    $result[$key]['service'] = $val['service']['service_name'];
                } else {
                    $result[$key]['service'] = 'NA';
                }

                if ($val['appointments'] != null) {
                    //if ($val['service'] != null) {
                    //    $result[$key]['service'] = $val['service']['service_name'];
                    //} else {
                    //    $result[$key]['service'] = 'NA';
                    //}
                    $result[$key]['appointments'] = $val['appointments'][0]['booking_date'];
                    $team_id = $val['appointments'][0]['assign_team'];
                    $teamName = HospitalBranchTeamManagement::where('id', $team_id)->get();
                    $result[$key]['team_name'] = $teamName[0]['team_name'];
                } else {
                    // $result[$key]['service'] = 'NA';
                    $result[$key]['appointments'] = 'NA';
                    $result[$key]['team_name'] = 'NA';
                }
            }
        }
        if (count($resultSet) > 0) {
            foreach ($resultSet as $key => $val) {
                $result[$key]['patient_mrn'] = $val['patient_mrn'];
                $result[$key]['name_asin_nric'] = $val['name_asin_nric'];
                $result[$key]['id'] = $val['id'];
                $result[$key]['nric_no'] = $val['nric_no'];
                $result[$key]['passport_no'] = $val['passport_no'];

                if (!empty($val['salutation'][0])) {
                    $result[$key]['salutation'] = $val['salutation'][0]['section_value'];
                } else {
                    $result[$key]['salutation'] = 'NA';
                }
                if ($val['service'] != null) {
                    // dd($val['service']['id']);
                    $result[$key]['service'] = $val['service']['service_name'];
                } else {
                    $result[$key]['service'] = 'NA';
                }
                if ($val['appointments'] != null) {
                    // if ($val['service'] != null) {
                    //     $result[$key]['service'] = $val['service']['service_name'];
                    // } else {
                    //     $result[$key]['service'] = 'NA';
                    // }
                    $result[$key]['appointments'] = $val['appointments'][0]['booking_date'];
                    $team_id = $val['appointments'][0]['assign_team'];
                    $teamName = HospitalBranchTeamManagement::where('id', $team_id)->get();
                    $result[$key]['team_name'] = $teamName[0]['team_name'];
                } else {
                    // $result[$key]['service'] = 'NA';
                    $result[$key]['appointments'] = 'NA';
                    $result[$key]['team_name'] = 'NA';
                }
                //  dd($result);
            }
        }
        return response()->json(["message" => "Patients List", 'list' => $result, "code" => 200]);
    }

    public function patientDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $details = PatientRegistration::select('id', 'patient_mrn', 'sex', 'birth_date', 'mobile_no', 'nric_no', 'citizenship', 'name_asin_nric', 'marital_id')->where('id', $request->patient_id)->get();
        $result = [];
        $result['id'] = $details[0]->id;
        $result['patient_name'] = $details[0]->name_asin_nric;
        $result['patient_mrn'] = $details[0]->patient_mrn;
        $result['nric'] = $details[0]->nric_no;
        $result['birth_date'] = date('d/m/Y', strtotime($details[0]->birth_date));
        $result['age'] = date_diff(date_create($details[0]->birth_date), date_create('today'))->y;
        if($details[0]->sex){
        $result['gender'] = $this->getGeneralSettingValue($details[0]->sex);
        }else{
            $result['gender'] = 'NA';
        }
        $result['gender_id'] = $details[0]->sex;
        if($details[0]->marital_id){
        $result['marital_status'] = $this->getGeneralSettingValue($details[0]->marital_id);
        }else{
            $result['marital_status'] ='NA';
        }
        $result['contact_no'] = $details[0]->mobile_no;
        $result['nationality'] = ($details[0]->citizenship == 0) ? 'Malaysian' : (($details[0]->citizenship == 1) ? 'Permanent Resident' : 'Foreigner');

        return response()->json(["message" => "Patient Details", 'details' => $result, "code" => 200]);
    }

    public function getSharrpPatientList1(Request $request)
    {
        if ($request->fromDate == "dd-mm-yyyy" && $request->toDate == "dd-mm-yyyy") {
            $request->fromDate = "01-06-2020";
            $request->toDate = date("d-m-Y");
        } else {
        }

        $validator = Validator::make($request->all(), [
            'fromDate' => 'required',
            // 'toDate' => 'required',
            'keyword' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->keyword == 'no-keyword' && $request->fromDate == 'dd-mm-yyyy' && $request->toDate == 'dd-mm-yyyy') {
            $resultSet = SharpRegistrationFinalStep::select('patient_id', 'harm_date', 'harm_time')
                ->with('patient:id,name_asin_nric,age,passport_no,nric_no')

                // ->where('status', '1')
                ->get()->toArray();
        }
        $resultSet = [];
        $sql = SharpRegistrationFinalStep::select('patient_id', 'harm_date', 'harm_time', 'status', 'added_by')
            ->with('patient:id,name_asin_nric,age,passport_no,nric_no');

        if ($request->fromDate != 'dd-mm-yyyy' && $request->toDate != 'dd-mm-yyyy') {
            // $sql = $sql->where('booking_date', '=', $request->date);
            $sql = $sql->whereBetween('harm_date', [date('Y-m-d', strtotime($request->fromDate)), date('Y-m-d', strtotime($request->toDate))]);
        }
        if ($request->keyword != 'no-keyword') {
            $searchWord = $request->keyword;
            $ids =  PatientRegistration::select('id')->where(function ($query) use ($searchWord) {
                $query->where('name_asin_nric', 'LIKE', '%' . $searchWord . '%')
                    ->orWhere('nric_no', 'LIKE', '%' . $searchWord . '%');
            })->get();
            $resultSet = $sql->where(function ($query) use ($searchWord, $ids) {
                // $query->where('name_asin_nric', 'LIKE', '%' . $searchWord . '%');
                $query->orWhereIn('patient_id',  $ids);
            });
            // dd($ids);
        }
        $resultSet = $sql->get()->toArray();
        // dd($resultSet);
        $result = [];
        if (count($resultSet) > 0) {
            foreach ($resultSet as $key => $val) {
                // $patient =  PatientRegistration::select('id', 'patient_mrn', 'name_asin_nric','age', 'passport_no', 'nric_no','branch_id')->where('id', $val['patient_id'])
                //     ->with('sharpharm:harm_date,harm_time,id')
                //     ->get();

                $users = DB::table('sharp_registraion_final_step')
                    ->join('users', 'sharp_registraion_final_step.added_by', '=', 'users.id')
                    ->select('email')
                    ->where('sharp_registraion_final_step.added_by', '=', $val['added_by'])
                    ->get();

                // if($users){
                $tmp = json_decode(json_encode($users[0]), true)['email'];
                $branchid =  StaffManagement::select('branch_id')->where('email', '=', $tmp)
                    ->get();

                $pc = HospitalBranchTeamManagement::where(['id' => $branchid[0]['branch_id']])->get()->toArray();
                $result[$key]['hospital_branch_name'] = ($pc) ? $pc[0]['hospital_branch_name'] : 'NA';
                //     // dd($result[$key]['hospital_branch_name']);
                // }

                $result[$key]['harm_time'] = $val['harm_time'] ??  'NA';
                $result[$key]['harm_date'] = $val['harm_date'] ??  'NA';
                $result[$key]['patient_id'] = $val['patient'][0]['id'] ??  'NA';
                $result[$key]['patient_mrn'] = $val['patient'][0]['patient_mrn'] ??  'NA';
                $result[$key]['age'] = $val['patient'][0]['age'] ??  'NA';
                $result[$key]['name_asin_nric'] = $val['patient'][0]['name_asin_nric'] ??  'NA';
                $result[$key]['nric_no'] = $val['patient'][0]['nric_no'] ??  'NA';
                if ($val['status']) {
                    $result[$key]['status'] = "Completed" ??  'NA';
                } else {
                    $result[$key]['status'] = "Draft" ??  'NA';
                }
                // dd($result);

            }
        }

        return response()->json(["message" => "Patient List.", 'list' => $result, "code" => 200]);
    }

    public function getSharrpPatientList2(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'fromDate' => 'required',
            // 'toDate' => 'required',
            'keyword' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        if ($request->keyword == 'no-keyword' && $request->fromDate == 'dd-mm-yyyy' && $request->toDate == 'dd-mm-yyyy') {

            // $resultSet = DB::table('sharp_registraion_final_step')
            // ->leftjoin('patient_registration', 'patient_registration.id', '=', 'sharp_registraion_final_step.patient_id')
            // ->select('patient_registration.id', 'patient_registration.name_asin_nric','patient_registration.nric_no',
            // 'patient_registration.patient_mrn', 
            // 'sharp_registraion_final_step.patient_id', 'sharp_registraion_final_step.harm_date', 'sharp_registraion_final_step.harm_time','sharp_registraion_final_step.added_by')
            // ->where('patient_registration.status', '=', '1')
            // ->orderByDesc('patient_registration.patient_mrn')
            // ->get()->toArray();

            $resultSet = PatientRegistration::select('id', 'added_by', 'name_asin_nric', 'nric_no', 'age', 'patient_mrn')->where('status', '=', '1')
                ->with('salutation:section_value,id')->with('service:service_name,id')
                // ->with('appointments', function ($query) {
                //     $query->where('appointment_status', '=', '1');
                // })
                ->get()->toArray();
            // $resultSet = SharpRegistrationFinalStep::select('patient_id', 'harm_date', 'harm_time')
            //     ->with('patient:id,name_asin_nric,age,passport_no,nric_no')

            //     // ->where('status', '1')
            //     ->get()->toArray();
        }

        $resultSet = [];
        $sql = PatientRegistration::select('id', 'added_by', 'name_asin_nric', 'nric_no', 'age', 'patient_mrn')->where('status', '=', '1')
            ->with('salutation:section_value,id')->with('service:service_name,id');
        // ->with('appointments', function ($query) {

        //     $query->where('appointment_status', '=', '1');
        // });
        // ->get()->toArray();
        if ($request->fromDate != 'dd-mm-yyyy' && $request->toDate != 'dd-mm-yyyy') {
            // $sql = $sql->where('booking_date', '=', $request->date);
            // $sql = $sql->whereBetween('harm_date', [date('Y-m-d', strtotime($request->fromDate)), date('Y-m-d', strtotime($request->toDate))]);
        }
        if ($request->keyword != 'no-keyword') {
            $searchWord = $request->keyword;
            $ids =  PatientRegistration::select('id')->where(function ($query) use ($searchWord) {
                $query->where('name_asin_nric', 'LIKE', '%' . $searchWord . '%')
                    ->orWhere('nric_no', 'LIKE', '%' . $searchWord . '%');
            })->get();
            $resultSet = $sql->where(function ($query) use ($searchWord, $ids) {
                // $query->where('name_asin_nric', 'LIKE', '%' . $searchWord . '%');
                $query->orWhereIn('patient_id',  $ids);
            });
            // dd($ids);
        }
        $resultSet = $sql->get()->toArray();
        // dd($resultSet);
        $result = [];
        if (count($resultSet) > 0) {
            foreach ($resultSet as $key => $val) {
                $patient =  SharpRegistrationFinalStep::select('patient_id', 'harm_date', 'harm_time', 'status')->where('patient_id', $val['id'])
                    // ->with('sharpharm:harm_date,harm_time,id')
                    ->get();
                // dd($patient[0]['harm_time']);
                $users = DB::table('sharp_registraion_final_step')
                    ->join('users', 'sharp_registraion_final_step.added_by', '=', 'users.id')
                    ->select('email')
                    ->where('sharp_registraion_final_step.added_by', '=', $val['added_by'])
                    ->get();
                // dd($users[0]);
                // if($users){
                $tmp = json_decode(json_encode($users[0]), true)['email'];
                $branchid =  StaffManagement::select('branch_id')->where('email', '=', $tmp)
                    ->get();

                $pc = HospitalBranchTeamManagement::where(['id' => $branchid[0]['branch_id']])->get()->toArray();
                $result[$key]['hospital_branch_name'] = ($pc) ? $pc[0]['hospital_branch_name'] : 'NA';
                //     // }

                if ($patient) {
                    $result[$key]['harm_time'] = $patient[0]['harm_time'] ??  'NA';
                    $result[$key]['harm_date'] = $patient[0]['harm_date'] ??  'NA';
                } else {
                    $result[$key]['harm_time'] =   '-';
                    $result[$key]['harm_date'] = '-';
                }
                $result[$key]['patient_id'] = $val[0]['id'] ??  'NA';
                $result[$key]['patient_mrn'] = $val[0]['patient_mrn'] ??  'NA';
                $result[$key]['age'] = $val[0]['age'] ??  'NA';
                $result[$key]['name_asin_nric'] = $val[0]['name_asin_nric'] ??  'NA';
                $result[$key]['nric_no'] = $val[0]['nric_no'] ??  'NA';
                // if($patient[0]['status']){
                //     $result[$key]['status'] = "Completed" ??  'NA';
                // }else{
                //     $result[$key]['status'] = "Draft" ??  'NA';  
                // }
                // dd($result);

            }
        }

        return response()->json(["message" => "Patient List.", 'list' => $result, "code" => 200]);
    }

    public function getSharrpPatientList(Request $request)
    {
        DB::enableQueryLog();
        if ($request->keyword == 'no-keyword' && $request->fromDate == 'dd-mm-yyyy' && $request->toDate == 'dd-mm-yyyy') {
            
            $query = DB::select("SELECT pr.*, d.*" . "FROM patient_registration pr left join
        (select patient_id,harm_time,harm_date,status from sharp_registraion_final_step
        where id in (SELECT max(id) id FROM sharp_registraion_final_step group by patient_id))
         d on pr.id=d.patient_id order by patient_mrn;");
        } else {
            
            if ($request->fromDate != 'dd-mm-yyyy' && $request->toDate != 'dd-mm-yyyy') {
               
                $query = DB::select("SELECT pr.*, d.*" . "FROM patient_registration pr left join 
            (select patient_id,harm_time,harm_date,status,added_by from sharp_registraion_final_step 
            where id in (SELECT max(id) id FROM sharp_registraion_final_step
            where harm_date between $request->fromDate and $request->toDate
            group by patient_id))
             d on pr.id=d.patient_id order by patient_mrn;");
            } else if ($request->keyword != 'no-keyword' && $request->fromDate != 'dd-mm-yyyy' && $request->toDate != 'dd-mm-yyyy') {
                $query = DB::select("SELECT pr.*, d.*" . "FROM patient_registration pr left join
                (select id,patient_id,harm_time,harm_date,status,added_by from sharp_registraion_final_step
                where id in (SELECT max(id) id FROM sharp_registraion_final_step
                where harm_date between $request->fromDate and $request->toDate
                group by patient_id))
                d on pr.id=d.patient_id order by patient_mrn
                where pr.name_asin_nric like '%$request->keyword%' or pr.name_asin_nric like '%$request->keyword%';");
            } else {
                $query = DB::select("SELECT pr.*, d.*" . "FROM patient_registration pr left join 
                (select id,patient_id,harm_time,harm_date,status,added_by from sharp_registraion_final_step 
                where id in (SELECT max(id) id FROM sharp_registraion_final_step group by patient_id))
                 d on pr.id=d.patient_id
                 order by patient_mrn;");
            }
        }
        //  $data_get = mysqli_query($query);
        // $dataset =$query->get();
        //  dd($query);
        // $ab=collect($query)->toArray();
        // $row = mysqli_free_result($query);

        $result = [];
        foreach ($query as $key => $val) {
            // dd($val->added_by);
            if ($request->keyword != 'no-keyword') {
                // dd('if');   $query->where('name_asin_nric', 'LIKE', '%' . $searchWord . '%')  $val->name_asin_nric==$request->keyword
                if (stripos($val->name_asin_nric, $request->keyword) !== false || stripos($val->nric_no, $request->keyword) !== false) {
                    // dd('if');
                    $result[$key]['harm_time'] = $val->harm_time ??  '-';
                    $result[$key]['harm_date'] = $val->harm_date ??  '-';
                    $result[$key]['patient_id'] = $val->id ??  'NA';
                    $result[$key]['patient_mrn'] = $val->patient_mrn ??  'NA';
                    $result[$key]['age'] = $val->age ??  'NA';
                    $result[$key]['name_asin_nric'] = $val->name_asin_nric ??  'NA';
                    $result[$key]['nric_no'] = $val->nric_no ??  'NA';
                    if ($val->status) {
                        $result[$key]['status'] = "Completed" ??  '-';
                    } else {
                        $result[$key]['status'] = "Draft" ??  '-';
                    }
                }
            } else {
                $result[$key]['harm_time'] = $val->harm_time ??  '-';
                $result[$key]['harm_date'] = $val->harm_date ??  '-';
                $result[$key]['patient_id'] = $val->id ??  'NA';
                $result[$key]['patient_mrn'] = $val->patient_mrn ??  'NA';
                $result[$key]['age'] = $val->age ??  'NA';
                $result[$key]['name_asin_nric'] = $val->name_asin_nric ??  'NA';
                $result[$key]['nric_no'] = $val->nric_no ??  'NA';
                if ($val->status) {
                    $result[$key]['status'] = "Completed" ??  '-';
                } else {
                    $result[$key]['status'] = "Draft" ??  '-';
                }
                if ($val->added_by) {
                    $users = DB::table('patient_registration')
                        ->join('users', 'patient_registration.added_by', '=', 'users.id')
                        ->select('users.email')
                        ->where('patient_registration.added_by', '=', $val->added_by)
                        ->get();
                    // dd($users[0]);
                    if ($users) {
                        $tmp = json_decode(json_encode($users[0]), true)['email'];
                        $branchid =  StaffManagement::select('branch_id')->where('email', '=', $tmp)
                            ->get();
                        if (!empty($branchid[0]['branch_id'])) {
                            $pc = HospitalBranchManagement::where(['id' => $branchid[0]['branch_id']])->get()->toArray();
                            $result[$key]['hospital_branch_name'] = ($pc) ? $pc[0]['hospital_branch_name'] : 'NA';
                        } else {
                            $result[$key]['hospital_branch_name'] = 'NA';
                        }
                        // $pc = HospitalBranchManagement::where(['id' => $branchid[0]['branch_id']])->get()->toArray();
                        // $result[$key]['hospital_branch_name'] = ($pc) ? $pc[0]['hospital_branch_name'] : 'NA';
                    }
                } else {
                    $result[$key]['hospital_branch_name'] = 'NA';
                }
            }
        }
        return response()->json(["message" => "Patient List.", 'list' => $result, "code" => 200]);
    }

    public function staffDesignatioDetail(Request $request)
    {
        $users = DB::table('patient_registration')
            ->join('users', 'patient_registration.added_by', '=', 'users.id')
            ->select('users.email')
            ->where('patient_registration.added_by', '=', $request->added_by)
            ->get();
        // dd($users[0]);
        $result = [];
        if ($users) {
            $tmp = json_decode(json_encode($users[0]), true)['email'];
            $designation_id =  StaffManagement::select('designation_id')->where('email', '=', $tmp)
                ->get();
            if (!empty($designation_id[0]['designation_id'])) {
                $pc = GeneralSetting::where(['id' => $designation_id[0]['designation_id']])->get()->toArray();
                $result[0]['section_value'] = ($pc) ? $pc[0]['section_value'] : 'NA';
            } else {
                $result[0]['hospital_branch_name'] = 'NA';
            }
            // $pc = HospitalBranchManagement::where(['id' => $branchid[0]['branch_id']])->get()->toArray();
            // $result[$key]['hospital_branch_name'] = ($pc) ? $pc[0]['hospital_branch_name'] : 'NA';
        }
        return response()->json(["message" => "Patient Details", 'details' => $result, "code" => 200]);
    }

    public function staffInchargeDetail(Request $request)
    {
        $users = DB::table('patient_registration')
            ->join('users', 'patient_registration.added_by', '=', 'users.id')
            ->select('users.email')
            ->where('patient_registration.added_by', '=', $request->added_by)
            ->get();
        // dd($users[0]);
        $result = [];
        if ($users) {
            $tmp = json_decode(json_encode($users[0]), true)['email'];
            $is_incharge =  StaffManagement::select('is_incharge')->where('email', '=', $tmp)
                ->get();
                $branch_id =  StaffManagement::select('branch_id')->where('email', '=', $tmp)
                ->get();
                if(!empty($branch_id[0])){
                    $branch_id =  StaffManagement::select('branch_id')->where('email', '=', $tmp)
                ->get();
                }else{
                    $branch_id =  "0";
                }
            if (!empty($is_incharge[0]['is_incharge'])) {
                $result[0]['is_incharge'] = $is_incharge[0]['is_incharge'] ?? 'NA';
                $pc = HospitalBranchManagement::where(['id' => $branch_id[0]['branch_id']])->get()->toArray();
                $result[0]['address'] = ($pc) ? $pc[0]['branch_adrress_1'] : 'NA';
            } else {
                $result[0]['is_incharge'] = 'NA';
            }
        }
        return response()->json(["message" => "Hospital Inchagre Details", 'details' => $result, 'branch'=>$branch_id, "code" => 200]);
    }
}
