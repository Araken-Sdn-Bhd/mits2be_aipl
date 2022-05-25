<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GeneralSettingController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\RolesController;
use App\Http\Controllers\ModulesController;
use App\Http\Controllers\ModuleSettingController;
use App\Http\Controllers\SystemSettingController;
use App\Http\Controllers\HospitalManagementController;
use App\Http\Controllers\ScreenModuleController;
use App\Http\Controllers\AddressManagementController;
use App\Http\Controllers\IcdSettingManagementController;
use App\Http\Controllers\ServiceSettingController;
use App\Http\Controllers\EtpSettingController;
use App\Http\Controllers\ClubSettingController;
use App\Http\Controllers\AnnouncementManagementController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use App\Http\Controllers\CitizenshipController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\StaffManagementController;
use App\Http\Controllers\CalendarExceptionController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\PatientRegistrationController;
use App\Http\Controllers\PatientClinicalInfoController;
use App\Http\Controllers\PatientAppointmentTypeController;
use App\Http\Controllers\PatientAppointmentVisitController;
use App\Http\Controllers\PatientAppointmentCategoryController;
use App\Http\Controllers\PatientAppointmentDetailsController;
use App\Http\Controllers\PsychiatryClerkingNoteController;
use App\Http\Controllers\PatientCounsellorClerkingNotesController;
use App\Http\Controllers\PatientCbiOnlineTestController;
use App\Http\Controllers\AttemptTestController;
use App\Http\Controllers\PatientSuicidalRiskAssessmentController;
use App\Http\Controllers\AppointmentRequestController;
use App\Http\Controllers\OcctReferralFormController;
use App\Http\Controllers\InternalReferralFormController;
use App\Http\Controllers\VounteerIndividualApplicationFormController;
use App\Http\Controllers\PatientShharpRegistrationSelfHarmController;
use App\Http\Controllers\PatientShharpRegistrationRiskProtectiveController;
use App\Http\Controllers\PatientShharpRegistrationHospitalManagementController;
use App\Http\Controllers\PatientShharpRegistrationDataProducerController;
use App\Http\Controllers\PsychiatricProgressNoteController;
use App\Http\Controllers\CounsellingProgressNoteController;
use App\Http\Controllers\JobOfferController;
use App\Http\Controllers\PatientAttachmentController;
use App\Http\Controllers\PatientAlertController;
use App\Http\Controllers\PsychiatristController;
use App\Http\Controllers\PatientDetailsController;
use App\Http\Controllers\TestResultSuicidalRiskController;
use App\Http\Controllers\ExternalCauseInjuryController;
use App\Http\Controllers\TriageFormController;
use App\Http\Controllers\ConsultationDischargeNoteController;
use App\Http\Controllers\PsychologyReferralController;
use App\Http\Controllers\PatientRiskProtectiveAnswerController;
use App\Http\Controllers\JobCompaniesController;
use GuzzleHttp\Middleware;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::get('/test',function(){dd('check');});
Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('/users/{from}/{to}', [UsersController::class, 'user_list']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/allowed-modules', [UsersController::class, 'get_user_role']);
});
Route::group(['prefix' => 'roles'], function () {
    Route::get('/list', [RolesController::class, 'index']);
    Route::post('/add', [RolesController::class, 'store']);
    Route::post('/update', [RolesController::class, 'update']);
    Route::post('/remove', [RolesController::class, 'delete']);
    Route::post('/assign', [RolesController::class, 'set_role']);
});
Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'modules'], function () {
    Route::get('/list', [ModulesController::class, 'index']);
    Route::post('/add', [ModulesController::class, 'store']);
    Route::post('/update', [ModulesController::class, 'update']);
    Route::post('/remove', [ModulesController::class, 'delete']);
    Route::get('/get-child/{type}', [ModulesController::class, 'get_child_from_type']);
});
Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'module-settings'], function () {
    Route::get('/fetch', [ModuleSettingController::class, 'index']);
    Route::post('/update', [ModuleSettingController::class, 'update']);
});

Route::group(['middleware' => ['jwt.verify'], 'prefix' => 'system-settings'], function () {
    Route::post('/insertOrupdate', [SystemSettingController::class, 'store']);
    Route::post('/get-setting', [SystemSettingController::class, 'get_setting']);
});
Route::group(['prefix' => 'hospital'], function () {
    Route::post('/add', [HospitalManagementController::class, 'store']);
    Route::post('/add-branch', [HospitalManagementController::class, 'storeBranch']);
    Route::post('/add-branch-team', [HospitalManagementController::class, 'storeBranchTeam']);
    Route::post('/get-branch-by-hospital-code', [HospitalManagementController::class, 'getBranchByHospitalCode']);
    Route::get('/list', [HospitalManagementController::class, 'getHospitalList']);
    Route::get('/list/{hospital_id}', [HospitalManagementController::class, 'getHospitalListById']);
    Route::get('/branch-list', [HospitalManagementController::class, 'getHospitalBranchList']);
    Route::get('/branch-team-list', [HospitalManagementController::class, 'getHospitalBranchTeamList']);
    Route::post('/branch-list-by-hospital', [HospitalManagementController::class, 'getHospitalBranchListByHospital']);
    Route::post('/hospital-branch-team-list', [HospitalManagementController::class, 'getHospitalBranchTeamListByBranch']);
    Route::get('/branchlist/{branch_id}', [HospitalManagementController::class, 'get_branch_by_id']);
    Route::post('/updateHospitalBranch', [HospitalManagementController::class, 'updateHospitalBranch']);
    Route::post('/removeBranch', [HospitalManagementController::class, 'removeBranch']);
    Route::get('/get_team_by_id/{team_id}', [HospitalManagementController::class, 'get_team_by_id']);
    Route::post('/updateHospitalBranchTeam', [HospitalManagementController::class, 'updateHospitalBranchTeam']);
    Route::post('/removeBranchTeam', [HospitalManagementController::class, 'removeBranchTeam']);
});
Route::group(['prefix' => 'screen-module'], function () {
    Route::post('/add', [ScreenModuleController::class, 'storeModule']);
    Route::post('/add-sub-module', [ScreenModuleController::class, 'storeSubModule']);
    Route::post('/add-screen-page', [ScreenModuleController::class, 'storeScreen']);
    Route::post('/get-branch-by-hospital-code', [ScreenModuleController::class, 'getBranchByHospitalCode']);
    Route::get('/list', [ScreenModuleController::class, 'getModuleList']);
    Route::get('/sub-module-list', [ScreenModuleController::class, 'getSubModuleList']);
    Route::post('/sub-module-list-by-module-id', [ScreenModuleController::class, 'getSubModuleListByModuleId']);
    Route::post('/sub-module-list-by-sub-module-id', [ScreenModuleController::class, 'getSubModuleListBySubModuleId']);
    Route::post('/get-screen', [ScreenModuleController::class, 'getScreenByModuleAndSubModule']);
    Route::post('/assign-screen', [ScreenModuleController::class, 'addScreenRoles']);
    Route::post('/module-list-by-module-id', [ScreenModuleController::class, 'getModuleListByModuleId']);
    Route::post('/updateModule', [ScreenModuleController::class, 'updateModule']);
    Route::post('/removeModule', [ScreenModuleController::class, 'removeModule']);
    Route::post('/updateSubModule', [ScreenModuleController::class, 'updateSubModule']);
    Route::post('/removeSubModule', [ScreenModuleController::class, 'removeSubModule']);
    Route::get('/getScreenPageList', [ScreenModuleController::class, 'getScreenPageList']);
    Route::get('/getScreenModuleListById', [ScreenModuleController::class, 'getScreenModuleListById']);
    Route::post('/updateScreenModule', [ScreenModuleController::class, 'updateScreenModule']);
    Route::post('/removeScreenModule', [ScreenModuleController::class, 'removeScreenModule']);
    Route::post('/getScreenPageListByModuleIdAndSubModuleId', [ScreenModuleController::class, 'getScreenPageListByModuleIdAndSubModuleId']);
    Route::post('/getTeamListByHospitalIdAndBranchId', [ScreenModuleController::class, 'getTeamListByHospitalIdAndBranchId']);
});
Route::group(['prefix' => 'general-setting'], function () {
    Route::post('/add', [GeneralSettingController::class, 'add']);
    Route::get('/list', [GeneralSettingController::class, 'getList']);
    Route::post('/fetch', [GeneralSettingController::class, 'getSettingById']);
    Route::post('/update', [GeneralSettingController::class, 'update']);
    Route::post('/remove', [GeneralSettingController::class, 'remove']);
});
Route::group(['prefix' => 'address'], function () {
    Route::post('/country/add', [AddressManagementController::class, 'addCountry']);
    Route::post('/state/add', [AddressManagementController::class, 'addState']);
    Route::post('/postcode/add', [AddressManagementController::class, 'addPostcode']);
    Route::get('/list', [AddressManagementController::class, 'getCountryStateList']);
    Route::get('country/list', [AddressManagementController::class, 'getCountryList']);
    Route::get('state/list', [AddressManagementController::class, 'getStateList']);
    Route::get('/postcodelist', [AddressManagementController::class, 'getPostcodeList']);
    Route::post('/{id}/updateCountry', [AddressManagementController::class, 'updateCountry']);
    Route::post('/{id}/updateState', [AddressManagementController::class, 'updateState']);
    Route::post('/{id}/updatePostcode', [AddressManagementController::class, 'updatePostcode']);
    Route::post('/{id}/removeCountry', [AddressManagementController::class, 'removeCountry']);
    Route::post('/{id}/removeState', [AddressManagementController::class, 'removeState']);
    Route::post('/{id}/removePostcode', [AddressManagementController::class, 'removePostcode']);
    Route::post('/{id}/editCountry', [AddressManagementController::class, 'editCountry']);
    Route::post('/{id}/editState', [AddressManagementController::class, 'editState']);
    Route::post('/{id}/editPostcode', [AddressManagementController::class, 'editPostcode']);
    Route::post('/{id}/countryWiseStateList', [AddressManagementController::class, 'countryWiseStateList']);
    Route::post('/{id}/stateWisePostcodeList', [AddressManagementController::class, 'stateWisePostcodeList']);
});

Route::group(['prefix' => 'service'], function () {
    Route::post('/register', [ServiceSettingController::class, 'store']);
    Route::post('/update', [ServiceSettingController::class, 'update']);
    Route::post('/remove', [ServiceSettingController::class, 'remove']);
    Route::get('/list', [ServiceSettingController::class, 'getSerivceList']);
    Route::post('/insertOrupdate-division', [ServiceSettingController::class, 'storeDivision']);
    Route::get('/division-list', [ServiceSettingController::class, 'getDivisionList']);
    Route::post('/get-division', [ServiceSettingController::class, 'getDivision']);
    Route::post('/update-division', [ServiceSettingController::class, 'updateDivision']);
    Route::post('/remove-division', [ServiceSettingController::class, 'removeDivision']);
    Route::post('/getServiceListById', [ServiceSettingController::class, 'getServiceListById']);
});
Route::group(['prefix' => 'icd-setting'], function () {
    Route::post('/icdtype/add', [IcdSettingManagementController::class, 'addIcdType']);
    Route::get('/icdtype/getIcdTypeCodeList', [IcdSettingManagementController::class, 'getIcdTypeCodeList']);
    Route::post('/icdcategory/addIcdCategory', [IcdSettingManagementController::class, 'addIcdCategory']);
    Route::post('/addIcdCode', [IcdSettingManagementController::class, 'addIcdCode']);
    Route::post('/getIcdTypeWiseCategoryCodeList/{id}', [IcdSettingManagementController::class, 'getIcdTypeWiseCategoryCodeList']);
    Route::get('/getIcdCategoryList', [IcdSettingManagementController::class, 'getIcdCategoryList']);
    Route::get('/getIcdcodeList', [IcdSettingManagementController::class, 'getIcdcodeList']);
    Route::post('/{id}/updateIcd_type', [IcdSettingManagementController::class, 'updateIcd_type']);
    Route::post('/{id}/updateIcd_category', [IcdSettingManagementController::class, 'updateIcd_category']);
    Route::post('/{id}/updateIcd_code', [IcdSettingManagementController::class, 'updateIcd_code']);
    Route::post('/{id}/editIcdType', [IcdSettingManagementController::class, 'editIcdType']);
    Route::post('/{id}/editIcdCategory', [IcdSettingManagementController::class, 'editIcdCategory']);
    Route::post('/{id}/editIcdcode', [IcdSettingManagementController::class, 'editIcdcode']);
    Route::post('/{id}/removeIcdType', [IcdSettingManagementController::class, 'removeIcdType']);
    Route::post('/{id}/removeIcdCategory', [IcdSettingManagementController::class, 'removeIcdCategory']);
    Route::post('/{id}/removeIcdCode', [IcdSettingManagementController::class, 'removeIcdCode']);
});

Route::group(['prefix' => 'etp'], function () {
    Route::post('/register', [EtpSettingController::class, 'store']);
    Route::post('/update', [EtpSettingController::class, 'update']);
    Route::post('/remove', [EtpSettingController::class, 'remove']);
    Route::get('/list', [EtpSettingController::class, 'getEtpList']);
    Route::post('/insertOrupdate-division', [EtpSettingController::class, 'storeDivision']);
    Route::get('/division-list', [EtpSettingController::class, 'getDivisionList']);
    Route::post('/get-division', [EtpSettingController::class, 'getDivision']);
    Route::post('/update-division', [EtpSettingController::class, 'updateDivision']);
    Route::post('/remove-division', [EtpSettingController::class, 'removeDivision']);
    Route::post('/{id}/editEtpType', [EtpSettingController::class, 'editEtpType']);
});

Route::group(['prefix' => 'club'], function () {
    Route::post('/register', [ClubSettingController::class, 'store']);
    Route::post('/update', [ClubSettingController::class, 'update']);
    Route::post('/remove', [ClubSettingController::class, 'remove']);
    Route::get('/list', [ClubSettingController::class, 'getClubList']);
    Route::post('/insertOrupdate-division', [ClubSettingController::class, 'storeDivision']);
    Route::get('/division-list', [ClubSettingController::class, 'getDivisionList']);
    Route::post('/get-division', [ClubSettingController::class, 'getDivision']);
    Route::post('/update-division', [ClubSettingController::class, 'updateDivision']);
    Route::post('/remove-division', [ClubSettingController::class, 'removeDivision']);
    Route::post('/{id}/getClubListByID', [ClubSettingController::class, 'getClubListByID']);
});

Route::group(['prefix' => 'announcement'], function () {
    Route::post('/add', [AnnouncementManagementController::class, 'store']);
    Route::post('/list', [AnnouncementManagementController::class, 'getAnnouncementList']);
    Route::post('/getAnnouncementDetails', [AnnouncementManagementController::class, 'getAnnouncementDetails']);
    Route::post('/update', [AnnouncementManagementController::class, 'updateAnnouncementManagement']);
    Route::post('/remove', [AnnouncementManagementController::class, 'remove']);
});

Route::get('/storage/{folder}/{filename}', function ($folder, $filename) {
    $path = storage_path('app/public/' . $folder . '/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::group(['prefix' => 'designation'], function () {
    Route::post('/addDesignation', [DesignationController::class, 'addDesignation']);
    Route::get('/getDesignationList', [DesignationController::class, 'getDesignationList']);
});
Route::group(['prefix' => 'citizenship'], function () {
    Route::post('/addCitizenship', [CitizenshipController::class, 'addCitizenship']);
    Route::get('/getCitizenshipList', [CitizenshipController::class, 'getCitizenshipList']);
});
Route::group(['prefix' => 'staff-management'], function () {
    Route::post('/addstaff', [StaffManagementController::class, 'store']);
    Route::get('/getList', [StaffManagementController::class, 'getStaffManagementList']);
    Route::post('/getListById', [StaffManagementController::class, 'getStaffManagementListById']);
    Route::post('/getDetailsById', [StaffManagementController::class, 'getStaffManagementDetailsById']);
    Route::post('/editDetailsById', [StaffManagementController::class, 'editStaffManagementDetailsById']);
    Route::post('/update', [StaffManagementController::class, 'updateStaffManagement']);
    Route::post('/remove', [StaffManagementController::class, 'remove']);
    Route::post('/transferstaff', [StaffManagementController::class, 'transferstaff']);
    Route::post('/getStaffManagementListOrById', [StaffManagementController::class, 'getStaffManagementListOrById']);
});

Route::group(['prefix' => 'calendar-management'], function () {
    Route::post('/add', [CalendarExceptionController::class, 'addexception']);
    Route::post('/update', [CalendarExceptionController::class, 'update']);
    Route::post('/remove', [CalendarExceptionController::class, 'remove']);
    Route::post('/getAnnouncementListById', [CalendarExceptionController::class, 'getAnnouncementListById']);
    Route::get('/getAnnouncementList', [CalendarExceptionController::class, 'getAnnouncementList']);
    Route::post('/upload-exception', [CalendarExceptionController::class, 'readExceptions']);
});

Route::group(['prefix' => 'mentari-staff-transfer'], function () {
    Route::post('/add', [MentariStaffTransferController::class, 'store']);
});

Route::group(['prefix' => 'file-upload'], function () {
    Route::post('/add', [FileUploadController::class, 'upload']);
    Route::get('/get', [FileUploadController::class, 'getFileList']);
});

Route::group(['prefix' => 'patient-registration'], function () {
    Route::post('/add', [PatientRegistrationController::class, 'store']);
    Route::post('/getPatientRegistrationById', [PatientRegistrationController::class, 'getPatientRegistrationById']);
    Route::post('/update', [PatientRegistrationController::class, 'updatePatientRegistration']);
    Route::get('/getPatientRegistrationList', [PatientRegistrationController::class, 'getPatientRegistrationList']);
});
Route::group(['prefix' => 'patient-clinicalinfo'], function () {
    Route::post('/add', [PatientClinicalInfoController::class, 'store']);
    Route::get('/list', [PatientClinicalInfoController::class, 'getPatientClinicalList']);
    Route::get('/getPatientClinicalListOfPatient', [PatientClinicalInfoController::class, 'getPatientClinicalListOfPatient']);
    Route::post('/remove', [PatientClinicalInfoController::class, 'remove']);
});

Route::group(['prefix' => 'patient-appointment-type'], function () {
    Route::post('/add', [PatientAppointmentTypeController::class, 'addPatientType']);
    Route::get('/list', [PatientAppointmentTypeController::class, 'getAppointmentPatientTypeList']);
});

Route::group(['prefix' => 'patient-appointment-visit'], function () {
    Route::post('/add', [PatientAppointmentVisitController::class, 'addPatientVisit']);
    Route::get('/list', [PatientAppointmentVisitController::class, 'getAppointmentPatientVisitList']);
});

Route::group(['prefix' => 'patient-appointment-category'], function () {
    Route::post('/add', [PatientAppointmentCategoryController::class, 'addPatientCategory']);
    Route::get('/list', [PatientAppointmentCategoryController::class, 'getAppointmentPatientCategoryList']);
});

Route::group(['prefix' => 'patient-appointment-details'], function () {
    Route::post('/add', [PatientAppointmentDetailsController::class, 'store']);
    Route::get('/list', [PatientAppointmentDetailsController::class, 'getPatientAppointmentDetailsList']);
    Route::post('/update', [PatientAppointmentDetailsController::class, 'update']);
    Route::post('/remove', [PatientAppointmentDetailsController::class, 'remove']);
    Route::post('/getPatientAppointmentDetailsListById', [PatientAppointmentDetailsController::class, 'getPatientAppointmentDetailsListById']);
    Route::post('/checkNricNoORPassport', [PatientAppointmentDetailsController::class, 'checkNricNoORPassport']);
    Route::post('/search', [PatientAppointmentDetailsController::class, 'searchPatientListByBranchIdOrServiceIdOrByName']);
    Route::post('/updateappointmentstatus', [PatientAppointmentDetailsController::class, 'updateappointmentstatus']);
    Route::post('/getPatientAppointmentDetailsOfPatient', [PatientAppointmentDetailsController::class, 'getPatientAppointmentDetailsOfPatient']);
    Route::post('/get-next-prev', [PatientAppointmentDetailsController::class, 'getNextPrev']);
});

Route::group(['prefix' => 'patient-psychiatry-clerkingnote'], function () {
    Route::post('/add', [PsychiatryClerkingNoteController::class, 'store']);
});
Route::group(['prefix' => 'patient-counsellor-clerkingnote'], function () {
    Route::post('/add', [PatientCounsellorClerkingNotesController::class, 'store']);
});

Route::group(['prefix' => 'patient-online-self-test'], function () {
    Route::post('/cbi-add', [PatientCbiOnlineTestController::class, 'store']);
    Route::post('/list-type', [PatientCbiOnlineTestController::class, 'getPatientOnlineSelfTestList']);
    Route::post('/cbi-update', [PatientCbiOnlineTestController::class, 'update']);
    Route::post('/cbi-remove', [PatientCbiOnlineTestController::class, 'remove']);
});
Route::group(['prefix' => 'patient'], function () {
    Route::post('/online-test', [AttemptTestController::class, 'store']);
    Route::get('/test-history', [AttemptTestController::class, 'testHistory']);
});
Route::group(['prefix' => 'patient-suicidal-risk-assessment'], function () {
    Route::post('/add', [PatientSuicidalRiskAssessmentController::class, 'store']);
    Route::post('/list-type', [PatientSuicidalRiskAssessmentController::class, 'getPatientOnlineTestList']);
    Route::post('/getTestById', [PatientSuicidalRiskAssessmentController::class, 'getPatientOnlineTestListById']);
    Route::post('/remove', [PatientSuicidalRiskAssessmentController::class, 'remove']);
    Route::post('/update', [PatientSuicidalRiskAssessmentController::class, 'update']);
});
Route::group(['prefix' => 'appointment-request'], function () {
    Route::post('/add', [AppointmentRequestController::class, 'addRequest']);
    Route::get('/get', [AppointmentRequestController::class, 'getRequestList']);
});

Route::group(['prefix' => 'occt-referral'], function () {
    Route::post('/add', [OcctReferralFormController::class, 'store']);
});
Route::group(['prefix' => 'internal-referral'], function () {
    Route::post('/add', [InternalReferralFormController::class, 'store']);
});
Route::group(['prefix' => 'von'], function () {
    Route::post('/add', [VounteerIndividualApplicationFormController::class, 'addVon']);
    Route::get('/list', [VounteerIndividualApplicationFormController::class, 'getList']);
    Route::post('/get-record', [VounteerIndividualApplicationFormController::class, 'getRecord']);
    Route::post('/update-record', [VounteerIndividualApplicationFormController::class, 'updateRecord']);
    Route::post('/set-status', [VounteerIndividualApplicationFormController::class, 'setStatus']);
    Route::post('/search-list', [VounteerIndividualApplicationFormController::class, 'searchList']);
    Route::post('/search-collaboration-list', [VounteerIndividualApplicationFormController::class, 'searchCollList']);
});

Route::group(['prefix' => 'shharp-registration-risk-protective-questions'], function () {
    Route::post('/add', [PatientShharpRegistrationRiskProtectiveController::class, 'generateQuestion']);
    Route::post('/list', [PatientShharpRegistrationRiskProtectiveController::class, 'getList']);
});
Route::group(['prefix' => 'shharp-registration'], function () {
    Route::post('/register-self-harm', [PatientShharpRegistrationSelfHarmController::class, 'registerselfharm']);
});
Route::group(['prefix' => 'shharp-registration-hospital-management'], function () {
    Route::post('/add', [PatientShharpRegistrationHospitalManagementController::class, 'addHospitalManagemnt']);
});

Route::group(['prefix' => 'shharp-registration-data-producer'], function () {
    Route::post('/add', [PatientShharpRegistrationDataProducerController::class, 'store']);
});

Route::group(['prefix' => 'progress-note'], function () {
    Route::post('/add', [PsychiatricProgressNoteController::class, 'store']);
});

Route::group(['prefix' => 'counselling-progress-note'], function () {
    Route::post('/add', [CounsellingProgressNoteController::class, 'store']);
});

Route::group(['prefix' => 'consultation-discharge-note'], function () {
    Route::post('/add', [ConsultationDischargeNoteController::class, 'store']);
});

Route::group(['prefix' => 'triage-form'], function () {
    Route::post('/add', [TriageFormController::class, 'store']);
});

Route::group(['prefix' => 'psychology-referral'], function () {
    Route::post('/add', [PsychologyReferralController::class, 'store']);
});

Route::group(['prefix' => 'intervention-job'], function () {
    Route::post('/add', [JobOfferController::class, 'store']);
    Route::post('/list', [JobOfferController::class, 'JobList']);
    Route::post('/getListByTitle', [JobOfferController::class, 'getListByTitle']);
    Route::post('/setStatus', [JobOfferController::class, 'setStatus']);
    Route::post('/getJobApprovalRequest', [JobOfferController::class, 'JobRequestList']);
    Route::post('/jobListById', [JobOfferController::class, 'jobListById']);
    Route::post('/jobApproveOrReject', [JobOfferController::class, 'jobApproveOrReject']);
    Route::post('/getCompanyJobApprovalList', [JobOfferController::class, 'getCompanyJobApprovalList']);
});
Route::group(['prefix' => 'patient-attachment'], function () {
    Route::post('/add', [PatientAttachmentController::class, 'store']);
    Route::get('/list', [PatientAttachmentController::class, 'getAttachmentList']);
});
Route::group(['prefix' => 'patient-alert'], function () {
    Route::post('/add', [PatientAlertController::class, 'store']);
    Route::post('/alertListbyPatientId', [PatientAlertController::class, 'alertListbyPatientId']);
});

Route::group(['prefix' => 'psychiatrist'], function () {
    Route::post('/add', [PsychiatristController::class, 'store']);
    Route::get('/list', [PsychiatristController::class, 'getPsychiatristList']);
});

Route::group(['prefix' => 'patient'], function () {
    Route::post('/demographic', [PatientDetailsController::class, 'demmographicDetails']);
    Route::get('/list', [PatientDetailsController::class, 'getPatientClinicalList']);
    Route::post('/search', [PatientDetailsController::class, 'serachPatient']);
    Route::post('/remove', [PatientDetailsController::class, 'remove']);
});
Route::group(['prefix' => 'test-result-suicidal-risk'], function () {
    Route::post('/add', [TestResultSuicidalRiskController::class, 'store']);
});
Route::group(['prefix' => 'external-cause-injury'], function () {
    Route::post('/add', [ExternalCauseInjuryController::class, 'store']);
    Route::get('/list', [ExternalCauseInjuryController::class, 'getExternalCauseList']);
});

Route::group(['prefix' => 'sharp-mgmt'], function () {
    Route::post('/store/risk-factor', [PatientRiskProtectiveAnswerController::class, 'store']);
    Route::post('/store/protective-factor', [PatientRiskProtectiveAnswerController::class, 'storeProtective']);
    Route::post('/store/self-harm', [PatientRiskProtectiveAnswerController::class, 'storeSelfHarmResult']);
    Route::post('/store/suicide-risk', [PatientRiskProtectiveAnswerController::class, 'storeSuicideRisk']);
    Route::post('/store/hospital-mgmt', [PatientShharpRegistrationHospitalManagementController::class, 'addHospitalManagemnt']);
    Route::post('/store/data-producer', [PatientShharpRegistrationDataProducerController::class, 'store']);
    Route::post('/list', [PatientRiskProtectiveAnswerController::class, 'fetchList']);
    Route::post('/get-record', [PatientRiskProtectiveAnswerController::class, 'fetchRecord']);
});

Route::group(['prefix' => 'intervention-company'], function () {
    Route::post('/add', [JobCompaniesController::class, 'store']);
    Route::post('/add-person', [JobCompaniesController::class, 'addContactPerson']);
    Route::post('/job-list', [JobOfferController::class, 'CompaniesJobs']);
    Route::post('/search-job-list', [JobOfferController::class, 'CompaniesJobsSearch']);
});
Route::group(['prefix' => 'intervention'], function () {
    Route::get('/job-record', [JobOfferController::class, 'jobRecordList']);
    Route::post('/job-record-search', [JobOfferController::class, 'jobRecordSearchList']);
});
