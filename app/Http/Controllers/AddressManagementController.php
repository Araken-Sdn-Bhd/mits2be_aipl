<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\State;
use App\Models\Postcode;
use Validator;
use Illuminate\Support\Facades\DB;

class AddressManagementController extends Controller
{
    public function addCountry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'country_name' => 'required|string',
            'country_code' => 'required|string',
            'country_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $country = [
            'added_by' =>  $request->added_by,
            'country_name' =>  $request->country_name,
            'country_code' =>  $request->country_code,
            'country_order' =>  $request->country_order,
            'country_status' =>  "1"
        ];
        try {
            if ($this->checkDuplicateRecord('Country', ['country_name', 'country_code'], [$request->country_name, $request->country_code], 'country_status')) {
                $HOD = Country::firstOrCreate($country);
            } else {
                return response()->json(["message" => "Country Name or Code Already Exists!", "code" => 401]);
            }
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'country' => $country, "code" => 200]);
        }
        return response()->json(["message" => "Country Created", "code" => 200]);
    }

    public function checkDuplicateRecord($class, $columns, $values, $statusColumn)
    {
        $SQL = '';
        if ($class == 'Country') {
            $SQL = Country::where($statusColumn, '1');
        }
        $chkPoint =  $SQL->where(function ($query) use ($columns, $values) {
            $query->where($columns[0], '=', $values[0])->orWhere($columns[1], '=', $values[1]);
        })->count();
        return ($chkPoint == 0) ? true : false;
    }

    public function checkDuplicateStateRecord($class, $columns, $values, $statusColumn)
    {
        $SQL = State::where($statusColumn, '1');
        $chkPoint =  $SQL->where(function ($query) use ($columns, $values) {
            $query->where($columns[0], '=', $values[0])->Where($columns[1], '=', $values[1]);
        })->count();
        return ($chkPoint == 0) ? true : false;
    }

    public function addState(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'country_id' => 'required|integer',
            'state_name' => 'required|string',
            'state_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $state = [
            'added_by' =>  $request->added_by,
            'country_id' =>  $request->country_id,
            'state_name' =>  $request->state_name,
            'state_order' =>  $request->state_order
        ];
        try {
            if ($this->checkDuplicateStateRecord('State', ['state_name', 'country_id'], [$request->state_name, $request->country_id], 'state_status')) {
                $HOD = State::firstOrCreate($state);
            } else {
                return response()->json(["message" => "State Name Already Exists!", "code" => 200]);
            }
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'state' => $state, "code" => 200]);
        }
        if ($HOD->wasRecentlyCreated === true) {
            return response()->json(["message" => "State Created", "code" => 200]);
        } else {
            return response()->json(["message" => "State Already Exist", "code" => 401]);
        }
    }

    public function addPostcode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'country_id' => 'required|integer',
            'state_id' => 'required|integer',
            'city_name' => 'required|string',
            'postcode' => 'required',
            'postcode_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $countryName = Country::where('id', $request->country_id)->pluck('country_name', 'id')->toArray();
        $stateName = State::where('id', $request->state_id)->pluck('state_name', 'id')->toArray();
        $postcode = [
            'added_by' =>  $request->added_by,
            'country_id' =>  $request->country_id,
            'state_id' =>  $request->state_id,
            'city_name' =>  $request->city_name,
            'postcode' =>  $request->postcode,
            'postcode_order' =>  $request->postcode_order,
            'country_name' => $countryName[$request->country_id],
            'state_name' => $stateName[$request->state_id]
        ];
        try {
            $check = Postcode::where(['country_id' => $request->country_id, 'state_id' =>  $request->state_id, 'city_name' => $request->city_name, 'postcode' => $request->postcode])->count();
            if ($check == 0) {
                Postcode::create($postcode);
                return response()->json(["message" => "Postcode Created", "code" => 200]);
            } else
                return response()->json(["message" => "Postcode Already Exist", "code" => 401]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage(), 'state' => $postcode, "code" => 200]);
        }
    }

    public function getCountryStateList()
    {
        $users = DB::table('country')
            ->join('state', 'country.id', '=', 'state.country_id')
            ->select('country.*', 'state.*')
            ->where('state.state_status', '=', '1')
            ->get();
        return response()->json(["message" => "CountryState List", 'list' => $users, "code" => 200]);
    }

    public function getCountryList()
    {
        $list = Country::select('id', 'country_name', 'country_code', 'country_status', 'country_order')
            ->where('country_status', '=', '1')
            ->get();
        return response()->json(["message" => "Country List", 'list' => $list, "code" => 200]);
    }
    public function getStateList()
    {
        $list = State::select('id', 'country_id', 'state_name', 'state_order', 'state_status')
            ->where('state_status', '=', '1')
            ->get();
        return response()->json(["message" => "State List", 'list' => $list, "code" => 200]);
    }

    public function getPostcodeList()
    {
        $users = DB::table('postcode')
            ->join('state', 'postcode.state_id', '=', 'state.id')
            ->join('country', 'postcode.country_id', '=', 'country.id')
            ->select('postcode.id', 'postcode.city_name', 'postcode.postcode', 'postcode.postcode_order', 'country.country_name', 'state.state_name')
            ->where('postcode.postcode_status', '=', '1')
            ->get();
        return response()->json(["message" => "Post Code List", 'list' => $users, "code" => 200]);
    }

    public function getPostcodeListFiltered(Request $request)
    {
        $users = DB::table('postcode')
            ->join('state', 'postcode.state_id', '=', 'state.id')
            ->join('country', 'postcode.country_id', '=', 'country.id')
            ->select('postcode.id', 'postcode.city_name', 'postcode.postcode', 'postcode.postcode_order', 'country.country_name', 'state.state_name')
            ->where('postcode.postcode_status', '=', '1')
            ->WHERE('postcode.state_id', '=', $request->state)
            ->get();
        return response()->json(["message" => "Post Code List", 'list' => $users, "code" => 200]);
    }


    public function updateCountry(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'id' => 'required|integer',
            'country_name' => 'required|string',
            'country_code' => 'required|string',
            'country_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (Country::where(['country_name' => $request->country_name])->orWhere(['country_code' => $request->country_code])->where('id', '!=', $request->id)->count() == 0) {
            Country::where(
                ['id' => $request->id]
            )->update([
                'country_name' => $request->country_name,
                'country_code' =>  $request->country_code,
                'country_order' =>  $request->country_order,
                'added_by' => $request->added_by
            ]);
            return response()->json(["message" => "Country has updated successfully", "code" => 200]);
        } else {
            return response()->json(["message" => $request->section_value . " already exists", "code" => 200]);
        }
    }

    public function updateState(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'country_id' => 'required|integer',
            'id' => 'required|integer',
            'state_name' => 'required|string',
            'state_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (State::where(['state_name' => $request->state_name])->Where(['country_id' => $request->country_id])->where('id', '!=', $request->id)->count() == 0) {
            State::where(
                ['id' => $request->id]
            )->update([
                'country_id' => $request->country_id,
                'state_name' =>  $request->state_name,
                'state_order' =>  $request->state_order,
                'added_by' => $request->added_by
            ]);
            return response()->json(["message" => "State has updated successfully", "code" => 200]);
        } else {
            return response()->json(["message" => $request->state_name . " already exists", "code" => 200]);
        }
    }

    /*$state1 = State::find($id);

            $state1->added_by =  $request->added_by;
            $state1->country_id =  $request->country_id;
            $state1->state_name =  $request->state_name;
            $state1->state_status =  $request->state_status;
            $state1->state_order =  $request->state_order;
            $state1->update();
      return response()->json(["message" => "State updated Successfully", "code" => 200]);
      }*/
    public function updatePostcode(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'added_by' => 'required|integer',
            'state_id' => 'required|integer',
            'country_id' => 'required|integer',
            'city_name' => 'required|string',
            'postcode' => 'required|string',
            'postcode_order' => 'required|integer'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $countryName = Country::where('id', $request->country_id)->pluck('country_name', 'id')->toArray();
        $stateName = State::where('id', $request->state_id)->pluck('state_name', 'id')->toArray();

        if (Postcode::where(['city_name' => $request->city_name, 'state_id' => $request->state_id, 'country_id' => $request->country_id, 'postcode' => $request->postcode])->where('id', '!=', $request->id)->count() == 0) {
            Postcode::where(
                ['id' => $request->id]
            )->update([
                'country_id' => $request->country_id,
                'state_id' => $request->state_id,
                'city_name' =>  $request->city_name,
                'postcode' =>  $request->postcode,
                'postcode_order' =>  $request->postcode_order,
                'added_by' => $request->added_by,
                'country_name' => $countryName[$request->country_id],
                'state_name' => $stateName[$request->state_id]
            ]);
            return response()->json(["message" => "Postcode has updated successfully", "code" => 200]);
        } else {
            return response()->json(["message" => $request->section_value . " already exists", "code" => 200]);
        }
    }

    public function removeCountry(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $country = Country::where(
            ['id' => $request->id]
        );
        $country->delete();
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
    }

    public function removeState(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $state = State::where(
            ['id' => $request->id]
        );
        $state->delete();
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
    }

    public function removePostcode(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|integer',
            'added_by' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors(), "code" => 422]);
        }
        $postcode = Postcode::where(
            ['id' => $request->id]
        );
        $postcode->delete();
        return response()->json(["message" => "Deleted Successfully.", "code" => 200]);
    }

    public function editCountry(Request $request, $id)
    {
        $list = Country::select('country_name', 'country_code', 'country_status', 'country_order')
            ->where('id', '=', $id)
            ->get();
        return response()->json(["message" => "Country Details", 'list' => $list, "code" => 200]);
    }

    public function editState(Request $request, $id)
    {

        $users = DB::table('state')
            ->join('country', 'state.country_id', '=', 'country.id')
            ->select('country.country_name', 'state.country_id', 'state.state_name', 'state.state_order')
            ->where('state.id', '=', $id)
            ->get();
        return response()->json(["message" => "CountryState List", 'list' => $users, "code" => 200]);
    }

    public function countryWiseStateList(Request $request, $id)
    {
        $list = State::select('id', 'country_id', 'state_name', 'state_order', 'state_status')
            ->where('country_id', '=', $id)
            ->get();
        return response()->json(["message" => "State List", 'list' => $list, "code" => 200]);
    }

    public function editPostcode(Request $request, $id)
    {
        $users = DB::table('postcode')
            ->join('state', 'postcode.state_id', '=', 'state.id')
            ->join('country', 'postcode.country_id', '=', 'country.id')
            ->select('postcode.*', 'state.*', 'country.*')
            ->where('postcode.id', '=', $id)
            ->get();
        return response()->json(["message" => "Post Code Details", 'list' => $users, "code" => 200]);
    }

    public function stateWisePostcodeList(Request $request, $id)
    {
        $users = DB::table('state')
            ->join('postcode', 'postcode.state_id', '=', 'state.id')
            ->select('state.state_name', 'postcode.id as id', 'postcode.postcode', 'postcode.city_name')
            ->where('state.id', '=', $id)
            ->get();
        return response()->json(["message" => "stateWisePostcode List", 'list' => $users, "code" => 200]);
    }

    public function stateWisePostcodeList_()
    {
        $users = DB::table('state')
            ->join('postcode', 'postcode.state_id', '=', 'state.id')
            ->select('state.state_name', 'postcode.id as id', 'postcode.postcode', 'postcode.city_name')
            ->get();
        return response()->json(["message" => "stateWisePostcode List", 'list' => $users, "code" => 200]);
    }

    public function getStateCityByPostcode(Request $request)
    {
        $list = Postcode::select('id', 'state_id', 'country_id', 'state_name', 'city_name')
            ->where('postcode', '=', $request->postcode)
            ->get();
        if (count($list) > 0) {
            return response()->json(["message" => "Postcode List", 'list' => $list, "code" => 200]);
        } else {
            return response()->json(["message" => "No Data Found", "code" => 400]);
        }
    }

    public function getCityList(Request $request, $id)
    {
        $city = DB::table('state')
            ->join('postcode', 'postcode.state_id', '=', 'state.id')
            ->select('postcode.city_name','postcode.id')
            ->where('state.id', '=', $id)
            ->groupBy('postcode.city_name')
            ->orderBy('postcode.city_name', 'ASC')
            ->get();
        return response()->json(["message" => "city List", 'list' => $city, "code" => 200]);
    }

    public function getPostcodeListById(Request $request, $id)
    {
        $postcode = DB::table('postcode')
            ->select('id', 'postcode')
            ->where('city_name', '=', $id)
            ->orderBy('postcode', 'ASC')
            ->get();
        return response()->json(["message" => "postcode List", 'list' => $postcode, "code" => 200]);

   }

    public function getAllCityList()
    {
        $city = DB::table('state')
            ->join('postcode', 'postcode.state_id', '=', 'state.id')
            ->select('postcode.city_name', 'state.id', 'postcode.id as post_id')
            ->groupBy('postcode.city_name')
            ->orderBy('postcode.city_name', 'ASC')
            ->get();
        return response()->json(["message" => "city List", 'list' => $city, "code" => 200]);
    }
}
