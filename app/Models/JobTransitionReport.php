<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTransitionReport extends Model
{
    use HasFactory;

    protected $table = 'job_transition_report';
    protected $fillable = [
        'appointment_details_id',
        'added_by',
        'patient_id',
        'future_plan',
        'short_term_goal',
        'long_term_goal',
        'who_have_you_called_past',
        'my_case_manager_yes_no',
        'my_case_manager_name',
        'my_case_manager_contact',
        'my_therapist_yes_no',
        'my_therapist_name',
        'my_therapist_contact',
        'my_family_yes_no',
        'my_family_name',
        'my_family_contact',
        'my_friend_yes_no',
        'my_friend_name',
        'my_friend_contact',
        'my_significant_other_yes_no',
        'my_significant_other_name',
        'my_significant_other_contact',
        'clergy_yes_no',
        'clergy_name',
        'clergy_contact',
        'benefit_planner_yes_no',
        'benefit_planner_name',
        'benefit_planner_contact',
        'other_yes_no',
        'other_name',
        'other_contact',
        'schedule_meeting_discuss_for_transition',
        'who_check_in_with_you',
        'who_contact_you',
        'how_would_like_to_contacted',
        'coping_strategies',
        'dissatisfied_with_your_job',
        'reasons_to_re_connect_to_ips',
        'patient_name',
        'doctor_name',
        'transition_report_date',
        'date',
        'location_of_service',
        'type_of_diagnosis',
        'category_of_services',
        'services',
        'complexity_of_services',
        'outcome',
        'icd_9_code',
        'icd_9_subcode',
        'medication_prescription',
        'created_at',
        'is_deleted'
    ];
}
