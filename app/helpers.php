<?php
if (!function_exists('adminProfileData')) {
    function adminProfileData($profile)
    {
        $personalInfos = json_decode($profile->personal_infos, true) ?? '{}';
        $demographic_infos = json_decode($profile->demographic_infos, true) ?? '{}';
        $religious_infos = json_decode($profile->religious_infos, true) ?? '{}';
        $educational_infos = json_decode($profile->educational_infos, true) ?? '{}';
        $employment_infos = json_decode($profile->employment_infos, true) ?? '{}';
        $marital_infos = json_decode($profile->marital_infos, true) ?? '{}';

        return [
            'uuid' => $profile->uuid,
            'user_id' => $profile->user_id,
            'name' => $personalInfos['name'],
            'age' => $personalInfos['age'],
            'gender' => $personalInfos['gender'],
            'district' => $personalInfos['district'],
            'lives_in' => $personalInfos['lives_in'],
            'height' => $demographic_infos['height'],
            'skin_color' => $demographic_infos['skin_color'],
            'religion' => $religious_infos['religion'],
            'sect' => $religious_infos['sect'],
            'education_level' => $educational_infos['education_level'],
            'designation' => $employment_infos['designation'],
            'marital_status' => $marital_infos['marital_status'],
            'is_verified' => $profile->is_verified,
            'status' => $profile->status,
        ];
    }
}

if (!function_exists('extractProfileData')) {
    function extractProfileData($profile)
    {
        $personalInfos = json_decode($profile->personal_infos, true);
        $demographic_infos = json_decode($profile->demographic_infos, true);
        $religious_infos = json_decode($profile->religious_infos, true);
        $educational_infos = json_decode($profile->educational_infos, true);
        $employment_infos = json_decode($profile->employment_infos, true);
        $marital_infos = json_decode($profile->marital_infos, true);

        return [
            'uuid' => $profile->uuid,
            'user_id' => $profile->user_id,
            'name' => $personalInfos['name'],
            'age' => $personalInfos['age'],
            'gender' => $personalInfos['gender'],
            'district' => $personalInfos['district'],
            'lives_in' => $personalInfos['lives_in'],
            'height' => $demographic_infos['height'],
            'skin_color' => $demographic_infos['skin_color'],
            'religion' => $religious_infos['religion'],
            'sect' => $religious_infos['sect'],
            'education_level' => $educational_infos['education_level'],
            'designation' => $employment_infos['designation'],
            'marital_status' => $marital_infos['marital_status'],
        ];
    }
}


if (!function_exists('extractProfileDatafullviewformachedcustomer')) {
    function extractProfilecustomerfullData($profile)
    {
        $personalInfos = json_decode($profile->personal_infos, true);
        $educational_infos = json_decode($profile->educational_infos, true);
        $employment_infos = json_decode($profile->employment_infos, true);

        return [
            'uuid' => $profile->uuid,
            'user_id' => $profile->user_id,
            'personal_infos' => [
                'name' => $personalInfos['name'],
                'age' => $personalInfos['age'],
                'gender' => $personalInfos['gender'],
                'upazila' => $personalInfos['upazila'],
                'district' => $personalInfos['district'],
                'lives_in' => $personalInfos['lives_in'],
                'country_name_if_abroad' => $personalInfos['country_name_if_abroad'],
                'number_of_sibling' => $personalInfos['number_of_sibling'],
            ],
            'demographic_infos' => json_decode($profile->demographic_infos, true),
            'educational_infos' => [
                'education_level' => $educational_infos['education_level'],
                'major' => $educational_infos['major'],
            ],
            'employment_infos' => [
                'employment_status' => $employment_infos['employment_status'],
                'employment_type' => $employment_infos['employment_type'],
                'job_type' => $employment_infos['job_type'],
                'designation' => $employment_infos['designation'],
                'org_type' => $employment_infos['org_type'],
                'job_experience' => $employment_infos['job_experience'],
                'salary' => $employment_infos['salary'],
            ],
            'marital_infos' => json_decode($profile->marital_infos, true),
            'religious_infos' => json_decode($profile->religious_infos, true),
        ];
    }
}
