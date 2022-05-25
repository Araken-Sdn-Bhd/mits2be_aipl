<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttemptTest;
use App\Models\TestResult;
use Validator;
use Exception;
use Illuminate\Support\Facades\DB;

class AttemptTestController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'patient_id' => 'required|integer',
            'test_name' => 'required|string',
            'test_section_name' => 'required|string',
            'result' => 'required|json',
            'user_ip_address' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }

        $result = json_decode($request->result, true);
        // dd($result);
        $addTestResult = [];
        $level = [];
        if (count($result) > 0) {
            $i = 0;
            $whoDasTotal = 0;
            foreach ($result as $key => $val) {
                foreach ($val as $kk => $vv) {
                    //  $TestResult[$request->test_name][$kk] =  $this->prepareResult($vv, $request->test_name);
                    if (
                        $request->test_name == 'cbi'
                        || $request->test_name == 'phq9'
                        || $request->test_name == 'whodas'
                        || $request->test_name == 'bdi'
                        || $request->test_name == 'bai'
                        || $request->test_name == 'psp'
                        || $request->test_name == 'atq'
                        || $request->test_name == 'si'
                    ) {
                        $testResult[$i] =
                            [
                                'added_by' =>  $request->added_by,
                                'patient_id' =>  $request->patient_id,
                                'test_name' =>  $request->test_name,
                                'ip_address' => $request->user_ip_address,
                                'created_at' =>  date('Y-m-d H:i:s'),
                                'updated_at' =>  date('Y-m-d H:i:s'),
                                'test_section_name' => $kk,
                                'result' => $this->prepareCBIResult($vv)
                            ];
                        if ($request->test_name != 'bdi' && $request->test_name != 'bai' && $request->test_name != 'atq' && $request->test_name != 'psp' && $request->test_name != 'si') {
                            $level[$kk] = ($request->test_name == 'cbi' || $request->test_name == 'whodas') ? $this->prepareCBIResult($vv) : $this->preparePHQ9Result($vv);
                        }
                        if ($request->test_name == 'phq9') {
                            $level['PHQ9Score'] = $this->getPHQ9ResultValue($vv);
                        }
                        if ($request->test_name == 'whodas') {
                            $whoDasTotal += $this->prepareCBIResult($vv);
                            $level['UserTotal'] =  $whoDasTotal;
                        }
                        if ($request->test_name == 'bdi' || $request->test_name == 'bai' || $request->test_name == 'atq' || $request->test_name == 'psp' || $request->test_name == 'si') {
                            $res = $this->getBDINBAIResultValue($this->prepareCBIResult($vv), $request->test_name);
                            $level[$kk] = $res[0];
                            $level[$request->test_name . 'Score'] = $res[1];
                        }
                    } else if ($request->test_name == 'dass') {
                        $testResult = $this->prepareDASSResult($vv, $request);
                        $level = $this->getDassLevel($testResult);
                    }

                    foreach ($vv as $k => $v) {
                        $addTestResult[$i] = [
                            'added_by' =>  $request->added_by,
                            'patient_mrn_id' =>  $request->patient_id,
                            'test_name' =>  $request->test_name,
                            'test_section_name' => $kk,
                            'question_id' =>  $k,
                            'answer_id' => $v,
                            'user_ip_address' => $request->user_ip_address,
                            'created_at' =>  date('Y-m-d H:i:s'),
                            'updated_at' =>  date('Y-m-d H:i:s')
                        ];
                        $i++;
                    }
                }
            }
            //  dd($testResult);
            try {
                AttemptTest::insert($addTestResult);
                TestResult::insert($testResult);
                return response()->json(["message" => "Answer submitted", "result" => $level, "code" => 200]);
            } catch (Exception $e) {
                return response()->json(["message" => $e->getMessage(), 'Exception' => $addTestResult, "code" => 200]);
            }
        }
    }

    public function prepareCBIResult($resultSet)
    {
        $result = 0;
        foreach ($resultSet as $k => $v) {
            $result += $v;
        }
        return $result;
    }
    public function preparePHQ9Result($resultSet)
    {
        $value = $this->prepareCBIResult($resultSet);
        if ($value >= 0 && $value <= 4) {
            return 'Minimal Depression';
        } else if ($value >= 5 && $value <= 9) {
            return 'Mild Depression';
        } else if ($value >= 10 && $value <= 14) {
            return 'Moderate Depression';
        } else if ($value >= 15 && $value <= 19) {
            return 'Moderately severe depression';
        } else {
            return 'Severe Depression';
        }
    }
    public function getPHQ9ResultValue($resultSet)
    {
        return  $this->prepareCBIResult($resultSet);
    }
    public function prepareDASSResult($resultSet, $request)
    {
        $stress = [1, 6, 8, 11, 12, 14, 18];
        $anxiety = [2, 4, 7, 9, 15, 19, 20];
        $depression = [3, 5, 10, 13, 16, 17, 21];
        $result['stress'] = 0;
        $result['anxiety'] = 0;
        $result['depression'] = 0;
        foreach ($resultSet as $k => $v) {
            if (in_array($k, $stress)) {
                $result['stress'] += $v;
            } else if (in_array($k, $anxiety)) {
                $result['anxiety'] += $v;
            } else if (in_array($k, $depression)) {
                $result['depression'] += $v;
            }
        }

        $testResult[0] =
            [
                'added_by' =>  $request->added_by,
                'patient_id' =>  $request->patient_id,
                'test_name' =>  $request->test_name,
                'ip_address' => $request->user_ip_address,
                'created_at' =>  date('Y-m-d H:i:s'),
                'updated_at' =>  date('Y-m-d H:i:s'),
                'test_section_name' => 'Stress',
                'result' => $result['stress']
            ];
        $testResult[1] =
            [
                'added_by' =>  $request->added_by,
                'patient_id' =>  $request->patient_id,
                'test_name' =>  $request->test_name,
                'ip_address' => $request->user_ip_address,
                'created_at' =>  date('Y-m-d H:i:s'),
                'updated_at' =>  date('Y-m-d H:i:s'),
                'test_section_name' => 'Anxiety',
                'result' => $result['anxiety']
            ];
        $testResult[2] =
            [
                'added_by' =>  $request->added_by,
                'patient_id' =>  $request->patient_id,
                'test_name' =>  $request->test_name,
                'ip_address' => $request->user_ip_address,
                'created_at' =>  date('Y-m-d H:i:s'),
                'updated_at' =>  date('Y-m-d H:i:s'),
                'test_section_name' => 'Depression',
                'result' => $result['depression']
            ];
        return $testResult;
    }

    public function getDassLevel($testResult)
    {
        $result = [];
        foreach ($testResult as $k => $v) {
            $level = strtolower($v['test_section_name']);
            $value = $v['result'];
            if ($level == 'stress') {
                if ($value >= 0 && $value <= 7) {
                    $result['Stress'] = 'Normal';
                } else if ($value >= 8 && $value <= 9) {
                    $result['Stress'] = 'Mild';
                } else if ($value >= 10 && $value <= 13) {
                    $result['Stress'] = 'Moderate';
                } else if ($value >= 14 && $value <= 17) {
                    $result['Stress'] = 'Severe';
                } else {
                    $result['Stress'] = 'Extreme';
                }
                $result['Stress_Value'] = $value;
            }
            if ($level == 'depression') {
                if ($value >= 0 && $value <= 5) {
                    $result['Depression'] = 'Normal';
                } else if ($value >= 6 && $value <= 7) {
                    $result['Depression'] = 'Mild';
                } else if ($value >= 8 && $value <= 10) {
                    $result['Depression'] = 'Moderate';
                } else if ($value >= 11 && $value <= 14) {
                    $result['Depression'] = 'Severe';
                } else {
                    $result['Depression'] = 'Extreme';
                }
                $result['Depression_Value'] = $value;
            }
            if ($level == 'anxiety') {
                if ($value >= 0 && $value <= 4) {
                    $result['Anxiety'] = 'Normal';
                } else if ($value >= 5 && $value <= 6) {
                    $result['Anxiety'] = 'Mild';
                } else if ($value >= 7 && $value <= 8) {
                    $result['Anxiety'] = 'Moderate';
                } else if ($value >= 9 && $value <= 10) {
                    $result['Anxiety'] = 'Severe';
                } else {
                    $result['Anxiety'] = 'Extreme';
                }
                $result['Anxiety_Value'] = $value;
            }
        }
        return $result;
    }

    public function testHistory(Request $request){
        $validator = Validator::make($request->all(), [
            'patient_id' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $list = TestResult::select(DB::raw('SUM(result) AS result'),'test_name',DB::raw("DATE_FORMAT(created_at, '%d-%m-%Y') as date")) 
        ->where('patient_id',$request->patient_id)->groupBy('created_at','test_name')->get();  
    return response()->json(["message" => "Test List.", 'list' => $list, "code" => 200]);
    }

    public function getBDINBAIResultValue($value, $testName)
    {
        if ($testName == 'bdi') {
            if ($value >= 1 && $value <= 10) {
                return ['Normal', '1-10'];
            } else if ($value >= 11 && $value <= 16) {
                return ['Mild mood disturbance', '11-16'];
            } else if ($value >= 17 && $value <= 20) {
                return ['Borderline clinical depression', '17-20'];
            } else if ($value >= 21 && $value <= 30) {
                return ['Moderate depression', '21-30'];
            } else if ($value >= 31 && $value <= 40) {
                return ['Severe depression', '31-40'];
            } else {
                return ['Extreme depression', '>40'];
            }
        }
        if ($testName == 'bai') {
            if ($value >= 0 && $value <= 21) {
                return ['Low anxiety', '0-21'];
            } else if ($value >= 22 && $value <= 35) {
                return ['Moderate anxiety', '22-35'];
            } else {
                return ['Potentially concerning levels of anxiety', '>35'];
            }
        }
        if ($testName == 'atq') {
            if ($value >= 0 && $value <= 17) {
                return ['Low', '0-27'];
            } else if ($value >= 18 && $value <= 51) {
                return ['Moderate', '18-51'];
            } else {
                return ['High', '>51'];
            }
        }
        if ($testName == 'psp') {
            if ($value >= 0 && $value <= 2) {
                return ['Absent', '0-2'];
            } else if ($value >= 2 && $value <= 5) {
                return ['Mild', '2-5'];
            } else if ($value >= 6 && $value <= 8) {
                return ['Manifest', '6-8'];
            } else if ($value >= 9 && $value <= 11) {
                return ['Marked', '9-11'];
            } else if ($value >= 12 && $value <= 15) {
                return ['Severe', '12-15'];
            } else {
                return ['Very Severe', '>15'];
            }
        }
        if ($testName == 'si') {
            if ($value >= 15 && $value <= 19) {
                return ['Low Intent', '15-19'];
            } else if ($value >= 20 && $value <= 28) {
                return ['Medium Intent', '20-28'];
            } else {
                return ['High Intent', '>28'];
            }
        }
    }
}
