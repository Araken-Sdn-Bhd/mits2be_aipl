<?php

use App\Http\Controllers\ActivityReportController;
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
use App\Http\Controllers\ExternalReferralFormController;
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
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VonAppointmentController;
use App\Http\Controllers\AreasOfInvolvementController;
use App\Http\Controllers\SelfHarmController;
use App\Http\Controllers\SuicidalIntentController;
use App\Http\Controllers\PatientGetIdeaAboutMethodController;
use App\Http\Controllers\LocationServicesController;
use App\Http\Controllers\PatientIndexFormController;
use App\Http\Controllers\EtpProgressNoteController;
use App\Http\Controllers\JobClubProgressNoteController;
use App\Http\Controllers\SeProgressNoteController;
use App\Http\Controllers\RehabDischargeNoteController;
use App\Http\Controllers\RehabReferralAndClinicalFormController;
use App\Http\Controllers\CpsProgressNoteController;
use App\Http\Controllers\CpsDischargeNoteController;
use App\Http\Controllers\CpsPoliceReferralFormController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralReportController;
use App\Http\Controllers\JobInterestChecklistController;
use App\Http\Controllers\ListJobClubController;
use App\Http\Controllers\ListOfETPController;
use App\Http\Controllers\LogMeetingWithEmployerController;
use App\Http\Controllers\WorkAnalysisFormController;
use App\Http\Controllers\ListOfJobSearchController;
use App\Http\Controllers\ListPreviousCurrentJobController;
use App\Http\Controllers\RequestAppointmentReportController;
use App\Http\Controllers\PatientByAgeReportController;
use App\Http\Controllers\ForgetpasswordController;
use App\Http\Controllers\EmailSettingController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\DefaultRoleAccessController;
use App\Models\DefaultRoleAccess;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Mail;

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

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/loginEmployer', [AuthController::class, 'loginEmployer']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});
Route::group(['prefix' => 'pass'], function () {
    Route::post('/forgetpass', [ForgetpasswordController::class, 'forgetpass']);
    Route::post('/validatePasswordRule',[PasswordController::class, 'passwordRule']);
});
Route::group(['prefix' => 'email-setting'], function () {
    Route::post('/add', [EmailSettingController::class, 'store']);
    Route::post('/getEmail', [EmailSettingController::class, 'getEmail']);
    Route::post('/testEmail', [EmailSettingController::class, 'testEmail']);
});
Route::group(['middleware' => ['jwt.verify']], function () {
    Route::get('/users/{from}/{to}', [UsersController::class, 'user_list']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);
    Route::post('/allowed-modules', [UsersController::class, 'get_user_role']);
});
Route::group(['prefix' => 'roles'], function () {
    Route::get('/list', [RolesController::class, 'index']);
    Route::get('/branch-viewlist', [RolesController::class, 'branch_view_list']);
    Route::get('/system-admin-role', [RolesController::class, 'system_admin_role']);
    Route::post('/add', [RolesController::class, 'store']);
    Route::post('/update', [RolesController::class, 'update']);
    Route::post('/remove', [RolesController::class, 'delete']);
    Route::post('/assign', [RolesController::class, 'set_role']);
    Route::post('/role-byId', [RolesController::class, 'role_byId']);
});
Route::group([ 'prefix' => 'modules'], function () {
    Route::get('/list', [ModulesController::class, 'index']);
    Route::post('/add', [ModulesController::class, 'store']);
    Route::post('/update', [ModulesController::class, 'update']);
    Route::post('/remove', [ModulesController::class, 'delete']);
    Route::get('/get-child/{type}', [ModulesController::class, 'get_child_from_type']);
}); //'middleware' => ['jwt.verify'],
Route::group(['prefix' => 'module-settings'], function () {
    Route::get('/fetch', [ModuleSettingController::class, 'index']);
    Route::post('/update', [ModuleSettingController::class, 'update']);
});

Route::group(['prefix' => 'system-settings'], function () {
    Route::post('/insertOrupdate', [SystemSettingController::class, 'store']);
    Route::get('/get-setting/{section}', [SystemSettingController::class, 'get_setting']);
});
Route::group(['prefix' => 'hospital'], function () {
    Route::post('/add', [HospitalManagementController::class, 'store']);
    Route::post('/updatehospital', [HospitalManagementController::class, 'updatehospital']);
    Route::post('/add-branch', [HospitalManagementController::class, 'storeBranch']);
    Route::post('/add-branch-team', [HospitalManagementController::class, 'storeBranchTeam']);
    Route::post('/get-branch-by-hospital-code', [HospitalManagementController::class, 'getBranchByHospitalCode']);
    Route::get('/list', [HospitalManagementController::class, 'getHospitalList']);
    Route::get('/list/{hospital_id}', [HospitalManagementController::class, 'getHospitalListById']);
    Route::get('/branch-list', [HospitalManagementController::class, 'getHospitalBranchList']);
    Route::get('/branch-team-list', [HospitalManagementController::class, 'getHospitalBranchTeamList']);
    Route::get('/branch-team-serv-div-list', [HospitalManagementController::class, 'getHospitalBranchTeamByServiceDivisionList']);
    Route::get('/team-list', [HospitalManagementController::class, 'getTeamList']);
    Route::get('/assigned-team', [HospitalManagementController::class, 'getAssignedTeamList']);
    Route::post('/branch-list-by-hospital', [HospitalManagementController::class, 'getHospitalBranchListByHospital']);
    Route::post('/hospital-branch-team-list', [HospitalManagementController::class, 'getHospitalBranchTeamListByBranch']);
    Route::get('/branchlist/{branch_id}', [HospitalManagementController::class, 'get_branch_by_id']);
    Route::post('/updateHospitalBranch', [HospitalManagementController::class, 'updateHospitalBranch']);
    Route::post('/removeBranch', [HospitalManagementController::class, 'removeBranch']);
    Route::get('/get_team_by_id/{team_id}', [HospitalManagementController::class, 'get_team_by_id']);
    Route::get('/get_hospitalBranchTeam_by_id/{service_id}', [HospitalManagementController::class, 'get_hospitalBranchTeam_by_id']);
    Route::post('/updateHospitalBranchTeam', [HospitalManagementController::class, 'updateHospitalBranchTeam']);
    Route::post('/removeBranchTeam', [HospitalManagementController::class, 'removeBranchTeam']);
    Route::get('/getServiceByBranchId', [HospitalManagementController::class, 'getHospitalBranchTeamListPatient']);
    Route::get('/getServiceByTeamId', [HospitalManagementController::class, 'getServiceByTeamId']);
    Route::get('/getStaffNamebyPatientTeamBranch', [HospitalManagementController::class, 'getStaffNamebyPatientTeamBranch']);
    Route::get('/getServiceByBranchTeamId', [HospitalManagementController::class, 'getServiceByBranchTeamId']);
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
    Route::get('/getUserMatrixList', [ScreenModuleController::class, 'getUserMatrixList']);
    Route::post('/getUserMatrixListById', [ScreenModuleController::class, 'getUserMatrixListById']);
    Route::post('/updatescreenRole', [ScreenModuleController::class, 'UpdateScreenRole']);
    Route::post('/getScreenByModuleId', [ScreenModuleController::class, 'getScreenByModuleId']);
    Route::post('/assign-screen-byRoleId', [ScreenModuleController::class, 'addScreenByRolesId']);
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
    Route::get('/postcodelistfiltered', [AddressManagementController::class, 'getPostcodeListFiltered']);
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
    Route::post('/getStateCityByPostcode', [AddressManagementController::class, 'getStateCityByPostcode']);
    Route::get('/stateWisePostcodeList_', [AddressManagementController::class, 'stateWisePostcodeList_']);
    Route::post('/{id}/getCityList', [AddressManagementController::class, 'getCityList']);
    Route::post('/{id}/getPostcodeListById', [AddressManagementController::class, 'getPostcodeListById']);
    Route::get('/getAllCityList', [AddressManagementController::class, 'getAllCityList']);

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
    Route::get('/servicelist', [ServiceSettingController::class, 'getServiceList']);
    Route::get('/getServiceListByBranch', [ServiceSettingController::class, 'getServiceListByBranch']);

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
    Route::post('/downloadFile', [AnnouncementManagementController::class, 'downloadFile']);
    Route::post('/update', [AnnouncementManagementController::class, 'updateAnnouncementManagement']);
    Route::post('/remove', [AnnouncementManagementController::class, 'remove']);
    Route::post('/getAnnouncementListById', [AnnouncementManagementController::class, 'getAnnouncementListById']);
    Route::post('/publish-list', [AnnouncementManagementController::class, 'getAnnouncementPublishedList']);
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

    return;
});

Route::group(['prefix' => 'designation'], function () {
    Route::post('/addDesignation', [DesignationController::class, 'addDesignation']);
    Route::get('/getDesignationList', [DesignationController::class, 'getDesignationList']);
    Route::post('/getDesignationListById', [DesignationController::class, 'getDesignationListById']);
    Route::post('/delete', [DesignationController::class, 'delete']);
    Route::post('/update', [DesignationController::class, 'update']);
});
Route::group(['prefix' => 'citizenship'], function () {
    Route::post('/addCitizenship', [CitizenshipController::class, 'addCitizenship']);
    Route::get('/getCitizenshipList', [CitizenshipController::class, 'getCitizenshipList']);
    Route::post('/getCitizenshipListById', [CitizenshipController::class, 'getCitizenshipListById']);
    Route::post('/delete', [CitizenshipController::class, 'delete']);
    Route::post('/update', [CitizenshipController::class, 'update']);
});
Route::group(['prefix' => 'staff-management'], function () {
    Route::post('/addstaff', [StaffManagementController::class, 'store']);
    Route::get('/getList', [StaffManagementController::class, 'getStaffManagementList']);
    Route::get('/getListByBranchId/{branch_id}', [StaffManagementController::class, 'getStaffManagementListByBranchId']);
    Route::post('/getListById', [StaffManagementController::class, 'getStaffManagementListById']);
    Route::post('/getDetailsById', [StaffManagementController::class, 'getStaffManagementDetailsById']);
    Route::post('/editDetailsById', [StaffManagementController::class, 'editStaffManagementDetailsById']);
    Route::post('/update', [StaffManagementController::class, 'updateStaffManagement']);
    Route::post('/remove', [StaffManagementController::class, 'remove']);
    Route::post('/transferstaff', [StaffManagementController::class, 'transferstaff']);
    Route::post('/getStaffManagementListOrById', [StaffManagementController::class, 'getStaffManagementListOrById']);
    Route::post('/checknricno', [StaffManagementController::class, 'checknricno']);
    Route::post('/getUserlist', [StaffManagementController::class, 'getUserlist']);
    Route::get('/getStaffManagementListOrById_', [StaffManagementController::class, 'getStaffManagementListOrById']);
    Route::get('/getListBy', [StaffManagementController::class, 'getStaffManagementList']);
    Route::get('/getStaffDetailById',[StaffManagementController::class,'getStaffDetailById']);
    Route::post('/getAdminList', [StaffManagementController::class, 'getAdminList']);
    Route::post('/setSystemAdmin', [StaffManagementController::class, 'setSystemAdmin']);
    Route::post('/removeUserAccess', [StaffManagementController::class, 'removeUserAccess']);
    Route::post('/getRoleCode', [StaffManagementController::class, 'getRoleCode']);
});

Route::group(['prefix' => 'calendar-management'], function () {
    Route::post('/add', [CalendarExceptionController::class, 'addexception']);
    Route::post('/update', [CalendarExceptionController::class, 'update']);
    Route::post('/remove', [CalendarExceptionController::class, 'remove']);
    Route::post('/getAnnouncementListById', [CalendarExceptionController::class, 'getAnnouncementListById']);
    Route::get('/getAnnouncementList', [CalendarExceptionController::class, 'getAnnouncementList']);
    Route::post('/upload-exception', [CalendarExceptionController::class, 'readExceptions']);
    Route::post('/download-excel', [CalendarExceptionController::class, 'getExcel']);
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
    Route::post('/getPatientRegistrationListByScreening', [PatientRegistrationController::class, 'getPatientRegistrationListByScreening']);
    Route::get('/validatePatientNric', [PatientRegistrationController::class, 'validatePatientNric']);
    Route::post('/getTransactionlog', [PatientRegistrationController::class, 'getTransactionlog']);
    Route::post('/getPatientRegistrationByIdShortDetails', [PatientRegistrationController::class, 'getPatientRegistrationByIdShortDetails']);
    Route::get('/getPatientRegistrationListMobile', [PatientRegistrationController::class, 'getPatientRegistrationListMobile']);
    Route::post('/getPatientRegistrationListbyBranch', [PatientRegistrationController::class, 'getPatientRegistrationListbyBranch']);
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
    Route::post('/addByPID', [PatientAppointmentDetailsController::class, 'storeByPID']);
    Route::get('/list', [PatientAppointmentDetailsController::class, 'getPatientAppointmentDetailsList']);
    Route::post('/todaylist', [PatientAppointmentDetailsController::class, 'getPatientAppointmentDetailsTodayList']);
    Route::post('/update', [PatientAppointmentDetailsController::class, 'update']);
    Route::post('/remove', [PatientAppointmentDetailsController::class, 'remove']);
    Route::post('/getPatientAppointmentDetailsListById', [PatientAppointmentDetailsController::class, 'getPatientAppointmentDetailsListById']);
    Route::post('/checkNricNoORPassport', [PatientAppointmentDetailsController::class, 'checkNricNoORPassport']);
    Route::post('/search', [PatientAppointmentDetailsController::class, 'searchPatientListByBranchIdOrServiceIdOrByName']);
    Route::post('/searchbybranch', [PatientAppointmentDetailsController::class, 'searchPatientListByBranchIdOrByName']);
    Route::post('/updateappointmentstatus', [PatientAppointmentDetailsController::class, 'updateappointmentstatus']);
    Route::post('/cancelappointmentstatus', [PatientAppointmentDetailsController::class, 'cancelappointmentstatus']);
    Route::post('/getPatientAppointmentDetailsOfPatient', [PatientAppointmentDetailsController::class, 'getPatientAppointmentDetailsOfPatient']);
    Route::post('/get-next-prev', [PatientAppointmentDetailsController::class, 'getNextPrev']);
    Route::post('/updateTeam', [PatientAppointmentDetailsController::class, 'updateTeamDoctor']);
    Route::post('/fetchViewHistoryList', [PatientAppointmentDetailsController::class, 'fetchViewHistoryList']);
    Route::post('/fetchViewHistoryListDetails', [PatientAppointmentDetailsController::class, 'fetchViewHistoryListDetails']);
    Route::post('/fetchPatientStaffById', [PatientAppointmentDetailsController::class, 'fetchPatientStaffById']);
    Route::post('/endappointmentDate', [PatientAppointmentDetailsController::class, 'endappointmentDate']);
    Route::post('/fetchPatientListByStaffId', [PatientAppointmentDetailsController::class, 'fetchPatientListByStaffId']);
    Route::post('/updatePatientListByStaffId', [PatientAppointmentDetailsController::class, 'updatePatientListByStaffId']);
    Route::post('/deletePatientListByStaffId', [PatientAppointmentDetailsController::class, 'deletePatientListByStaffId']);
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
    Route::post('/resultdetail', [AttemptTestController::class, 'resultdetail']);
    Route::post('/test-history-show', [AttemptTestController::class, 'testHistoryResultShow']);
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
    Route::post('/get', [AppointmentRequestController::class, 'getRequestList']);
});

Route::group(['prefix' => 'occt-referral'], function () {
    Route::post('/add', [OcctReferralFormController::class, 'store']);
});
Route::group(['prefix' => 'external-referral'], function () {
    Route::post('/add', [ExternalReferralFormController::class, 'store']);
});

Route::group(['prefix' => 'rehab-referral'], function () {
    Route::post('/add', [RehabReferralAndClinicalFormController::class, 'store']);
});
Route::group(['prefix' => 'internal-referral'], function () {
    Route::post('/add', [InternalReferralFormController::class, 'store']);
});
Route::group(['prefix' => 'von'], function () {
    Route::post('/add', [VounteerIndividualApplicationFormController::class, 'addVon']);
    Route::get('/list', [VounteerIndividualApplicationFormController::class, 'getList']);
    Route::get('/listByBranchId', [VounteerIndividualApplicationFormController::class, 'getListByBranchId']);
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
    Route::post('/addJob', [JobOfferController::class, 'addJob']);
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
    Route::post('/getAlertbyAlertId', [PatientAlertController::class, 'alertListbyAlertId']);
    Route::post('/resolved', [PatientAlertController::class, 'resolved']);
    Route::post('/alertLastbyPatientId', [PatientAlertController::class, 'alertLastbyPatientId']);
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
    Route::post('/detail', [PatientDetailsController::class, 'patientDetails']);
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
    Route::post('/update', [JobCompaniesController::class, 'update']);
    Route::post('/add-person', [JobCompaniesController::class, 'addContactPerson']);
    Route::post('/job-list', [JobOfferController::class, 'CompaniesJobs']);
    Route::post('/search-job-list', [JobOfferController::class, 'CompaniesJobsSearch']);
    Route::post('/company-details', [JobCompaniesController::class, 'getCompanyDetails']);
    Route::post('/approval-list', [JobCompaniesController::class, 'getApprovalList']);
});
Route::group(['prefix' => 'intervention'], function () {
    Route::get('/job-record', [JobOfferController::class, 'jobRecordList']);
    Route::post('/job-record-search', [JobOfferController::class, 'jobRecordSearchList']);
    Route::post('/get-se-form', [JobOfferController::class, 'getSEForm']);
    Route::post('/se-form', [JobOfferController::class, 'setSEConsentForm']);
    Route::post('/cps-form', [JobOfferController::class, 'setCPSReferralForm']);
    Route::Post('/laser-form', [JobOfferController::class, 'setLASERReferralForm']);
    Route::Post('/patient-care-plan', [JobOfferController::class, 'setPatientCarePlan']);
    Route::get('/category-discharge', [JobOfferController::class, 'dischareCategory']);
    Route::get('/screening-types', [JobOfferController::class, 'screeningTypes']);
    //abdus//
    Route::post('/get-homevisit-consent-form', [JobOfferController::class, 'getCpsHomevisitForm']);
    Route::post('/post-homevisit-consent-form', [JobOfferController::class, 'postCpsHomevisitForm']);
    Route::post('/homevisit-form', [JobOfferController::class, 'setCpsHomevisitConsentForm']);
    Route::post('/get-photography-consent-form', [JobOfferController::class, 'getPhotographyForm']);
    Route::post('/photography-form', [JobOfferController::class, 'setPhotographyConsentForm']);
    Route::post('/get-homevisit-withdrawal-form', [JobOfferController::class, 'getCpsHomevisitWithdrawalForm']);
    Route::post('/Etp-form', [JobOfferController::class, 'setEtpConsentForm']);
    Route::post('/get-etp-consent-form', [JobOfferController::class, 'getEtpForm']);
    Route::post('/Job-Club-form', [JobOfferController::class, 'setJobClubConsentForm']);
    Route::post('/get-job-club-consent-form', [JobOfferController::class, 'getJobClubForm']);
    Route::post('/homevisit-withdrawal-form', [JobOfferController::class, 'setCpsHomevisitWithdrawalForm']);
    Route::Post('/job-start-form', [JobOfferController::class, 'setJobStartForm']);
    Route::Post('/job-report-end', [JobOfferController::class, 'setJobEndReport']);
    Route::Post('/job-transition-report', [JobOfferController::class, 'setJobTransitionReport']);
    Route::get('/job-start-form-list', [JobOfferController::class, 'GetJobStartList']);
    Route::get('/job-start-form', [JobOfferController::class, 'GetJobStartForm']);
});
Route::group(['prefix' => 'report'], function () {
    Route::post('/shharp', [ReportController::class, 'getSharpReport']);
    Route::post('/total-patient-type-refferal', [ReportController::class, 'getTotalPatientTypeRefferalReport']);
    Route::post('/activity/patient', [ReportController::class, 'getPatientActivityReport']);
    Route::post('/activity/von', [ReportController::class, 'getVONActivityReport']);
    Route::post('/general', [ReportController::class, 'getGeneralReport']);
    Route::post('/getPatientByAgeReport', [PatientByAgeReportController::class, 'getPatientByAgeReport']);
    Route::post('/kpi', [ReportController::class, 'getKPIReport']);
});
Route::group(['prefix' => 'von-appointment'], function () {
    Route::post('/add', [VonAppointmentController::class, 'store']);
    Route::post('/getVonAppointmentById', [VonAppointmentController::class, 'geyVonAppointmentById']);
    Route::post('/update', [VonAppointmentController::class, 'update']);
    Route::post('/list', [VonAppointmentController::class, 'listAppointment']);
    Route::post('/set-status', [VonAppointmentController::class, 'setStatus']);
});
Route::group(['prefix' => 'job-companies'], function () {
    Route::get('/list', [JobCompaniesController::class, 'list']);
    Route::get('/getListById', [JobCompaniesController::class, 'getListById']);
});
Route::group(['prefix' => 'assigned-interviwer'], function () {
    Route::get('/list', [JobCompaniesController::class, 'getInterviewerList']);
});
Route::group(['prefix' => 'areas-of-involvement'], function () {
    Route::post('/add', [AreasOfInvolvementController::class, 'add']);
    Route::get('/list', [AreasOfInvolvementController::class, 'getAreasOfInvolvementList']); //
});
Route::group(['prefix' => 'self-harm'], function () {
    Route::post('/add', [SelfHarmController::class, 'add']);
    Route::get('/list', [SelfHarmController::class, 'getSelfHarmList']);
});
Route::group(['prefix' => 'suicidal-intent'], function () {
    Route::post('/add', [SuicidalIntentController::class, 'add']);
    Route::get('/list', [SuicidalIntentController::class, 'getSuicidalList']);
});
Route::group(['prefix' => 'patient-get-idea'], function () {
    Route::post('/add', [PatientGetIdeaAboutMethodController::class, 'add']);
    Route::get('/list', [PatientGetIdeaAboutMethodController::class, 'getPatientGetIdeaAboutMethodList']);
});
Route::group(['prefix' => 'diagnosis'], function () {
    Route::get('/getIcd10codeList', [IcdSettingManagementController::class, 'getIcd10codeList']);
    Route::get('/getIcd10codeById', [IcdSettingManagementController::class, 'getIcd10codeById']);
    Route::get('/getIcd9codeList', [IcdSettingManagementController::class, 'getIcd9codeList']);
    Route::get('/getIcd9codeById', [IcdSettingManagementController::class, 'getIcd9codeById']);
    Route::post('/getIcd9subcodeList', [IcdSettingManagementController::class, 'getIcd9subcodeList']);
    Route::get('/getIcd9subcodeById', [IcdSettingManagementController::class, 'getIcd9subcodeById']);
    Route::get('/getIcd10codeList2', [IcdSettingManagementController::class, 'getIcd10codeList2']);
    Route::get('/getIcd9subcodeList_', [IcdSettingManagementController::class, 'getIcd9subcodeList_']);
});
Route::group(['prefix' => 'location-services'], function () {
    Route::post('/add', [LocationServicesController::class, 'add']);
    Route::get('/list', [LocationServicesController::class, 'getLocationServicesList']);
});
Route::group(['prefix' => 'patient-index'], function () {
    Route::post('/add', [PatientIndexFormController::class, 'store']);
});
Route::group(['prefix' => 'etp-progress'], function () {
    Route::post('/add', [EtpProgressNoteController::class, 'store']);
});

Route::group(['prefix' => 'job-club-progress'], function () {
    Route::post('/add', [JobClubProgressNoteController::class, 'store']);
});
Route::group(['prefix' => 'se-progress-note'], function () {
    Route::post('/add', [SeProgressNoteController::class, 'store']);
    Route::get('/activitylist', [SeProgressNoteController::class, 'GetActivityList']);
    Route::get('/senamelist', [SeProgressNoteController::class, 'GetSENamelist']);
});

Route::group(['prefix' => 'rehab-discharge-note'], function () {
    Route::post('/add', [RehabDischargeNoteController::class, 'store']);
});

Route::group(['prefix' => 'cps-progress-note'], function () {
    Route::post('/add', [CpsProgressNoteController::class, 'store']);
});

Route::group(['prefix' => 'cps-discharge-note'], function () {
    Route::post('/add', [CpsDischargeNoteController::class, 'store']);
});

Route::group(['prefix' => 'cps-police-referral-form'], function () {
    Route::post('/add', [CpsPoliceReferralFormController::class, 'store']);
});

Route::group(['prefix' => 'job-interest-checklist'], function () {
    Route::post('/add', [JobInterestChecklistController::class, 'store']);
});

Route::group(['prefix' => 'list-job-club'], function () {
    Route::post('/add', [ListJobClubController::class, 'store']);
});

Route::group(['prefix' => 'list-of-etp'], function () {
    Route::post('/add', [ListofETPController::class, 'store']);
});

Route::group(['prefix' => 'log-employer-meeting'], function () {
    Route::post('/add', [LogMeetingWithEmployerController::class, 'store']);
    Route::get('/employerlist', [LogMeetingWithEmployerController::class, 'GetEmployerList']);
});
Route::group(['prefix' => 'work-analysis'], function () {
    Route::post('/add', [WorkAnalysisFormController::class, 'store']);
});
Route::group(['prefix' => 'job-search-list'], function () {
    Route::post('/add', [ListOfJobSearchController::class, 'store']);
});

Route::group(['prefix' => 'previous-current-job'], function () {
    Route::post('/add', [ListPreviousCurrentJobController::class, 'store']);
});

//report
Route::group(['prefix' => 'activity-report'], function () {
    Route::post('/activity', [ActivityReportController::class, 'getActivityReport']);
});
Route::group(['prefix' => 'request-appointment-report'], function () {
    Route::post('/get', [RequestAppointmentReportController::class, 'getRequestAppointmentReport']);
});

Route::group(['prefix' => 'general-report'], function () {
    Route::post('/general', [GeneralReportController::class, 'getGeneralReport']);
});

Route::group(['prefix' => 'mails'], function () {
    Route::post('/forgot-password', [MailController::class, 'sendForgotPasswordEmail']);
    Route::post('/registerEmployee', [MailController::class, 'registerEmployee']);
});
Route::group(['prefix' => 'reset'], function () {
    Route::post('/password', [PasswordController::class, 'resetPassword']);
    Route::post('/verifyAccount', [PasswordController::class, 'verifyAccount']);
    Route::post('/changePassword', [PasswordController::class, 'changePassword']);
});
Route::group(['prefix' => 'access'], function () {
    Route::post('/sidebar', [ScreenModuleController::class, 'getAccessScreenByUserId']);
    Route::post('/sidebarReport', [ScreenModuleController::class, 'getAccessScreenByUserIdforReport']);//faiz
});
Route::group(['prefix' => 'shharp-patient-list'], function () {
    Route::post('/list', [PatientDetailsController::class, 'getSharrpPatientList']);
});

Route::group(['prefix' => 'employer-job'], function () {
    Route::post('/add', [JobController::class, 'store']);
    Route::post('/repeat', [JobController::class, 'repeat']);
    Route::post('/update', [JobController::class, 'update']);
    Route::post('/list', [JobController::class, 'JobListByCompany']);
    Route::post('/repeat-list', [JobController::class, 'RepeatList']);
    Route::post('/pending-approval', [JobController::class, 'getPendingApprovalList']);
    Route::post('/job-list', [JobController::class, 'JobList']);
    Route::post('/setAvailable', [JobController::class, 'setStatus']);
    Route::post('/view-detail', [JobController::class, 'ViewJobDetails']);
});

Route::group(['prefix' => 'default-role-access'], function () {
    Route::post('/add', [DefaultRoleAccessController::class, 'store']);
    Route::post('/listbyId', [DefaultRoleAccessController::class, 'listbyId']);
    Route::post('/{id}/delete', [DefaultRoleAccessController::class, 'delete']);
});


//----------------------------------//////////////////////////////////////////////////-----------------
Route::group(['prefix' => 'systemadmin'], function () {

    Route::get('/get', [DashboardController::class, 'getsystemadmin']);

});

Route::group(['prefix' => 'all-mentari-staff'], function () {

   Route::get('/get', [DashboardController::class, 'getallmentaristaff']);
});

Route::group(['prefix' => 'user-admin-clerk'], function () {

   Route::get('/get', [DashboardController::class, 'getuseradminclerk']);
   Route::get('/get_data', [DashboardController::class, 'AdminSpeciallist']);
});

Route::group(['prefix' => 'shharp'], function () {

   Route::get('/get', [DashboardController::class, 'getshharp']);
});

Route::group(['prefix' => 'high-level-mgt'], function () {

    Route::post('get', [DashboardController::class, 'gethighlevelMgt']);
});
Route::group(['prefix' => 'years'], function () {

    Route::get('get', [DashboardController::class, 'getYears']);
});
Route::group(['prefix' => 'Notification'], function () {

    Route::post('get', [NotificationController::class, 'getNotification']);
    Route::post('delete', [NotificationController::class, 'deleteNotification']);

});
Route::group(['prefix' => 'staffDesignatioDetail'], function () {
    Route::post('/get', [PatientDetailsController::class, 'staffDesignatioDetail']);
    Route::post('/staffInchargeDetail', [PatientDetailsController::class, 'staffInchargeDetail']);
 });


