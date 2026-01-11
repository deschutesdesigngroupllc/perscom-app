<?php

declare(strict_types=1);

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\Support\Utils;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"Admin","guard_name":"web","permissions":["view_any_role","view_role","create_role","update_role","delete_role","restore_role","force_delete_role","force_delete_any_role","restore_any_role","replicate_role","reorder_role","view_any_announcement","view_announcement","create_announcement","update_announcement","delete_announcement","restore_announcement","force_delete_announcement","force_delete_any_announcement","restore_any_announcement","replicate_announcement","reorder_announcement","view_any_api_log","view_api_log","create_api_log","update_api_log","delete_api_log","restore_api_log","force_delete_api_log","force_delete_any_api_log","restore_any_api_log","replicate_api_log","reorder_api_log","view_any_api_purge_log","view_api_purge_log","create_api_purge_log","update_api_purge_log","delete_api_purge_log","restore_api_purge_log","force_delete_api_purge_log","force_delete_any_api_purge_log","restore_any_api_purge_log","replicate_api_purge_log","reorder_api_purge_log","view_any_assignment_record","view_assignment_record","create_assignment_record","update_assignment_record","delete_assignment_record","restore_assignment_record","force_delete_assignment_record","force_delete_any_assignment_record","restore_any_assignment_record","replicate_assignment_record","reorder_assignment_record","view_any_attachment","view_attachment","create_attachment","update_attachment","delete_attachment","restore_attachment","force_delete_attachment","force_delete_any_attachment","restore_any_attachment","replicate_attachment","reorder_attachment","view_any_award","view_award","create_award","update_award","delete_award","restore_award","force_delete_award","force_delete_any_award","restore_any_award","replicate_award","reorder_award","view_any_calendar","view_calendar","create_calendar","update_calendar","delete_calendar","restore_calendar","force_delete_calendar","force_delete_any_calendar","restore_any_calendar","replicate_calendar","reorder_calendar","view_any_category","view_category","create_category","update_category","delete_category","restore_category","force_delete_category","force_delete_any_category","restore_any_category","replicate_category","reorder_category","view_any_comment","view_comment","create_comment","update_comment","delete_comment","restore_comment","force_delete_comment","force_delete_any_comment","restore_any_comment","replicate_comment","reorder_comment","view_any_competency","view_competency","create_competency","update_competency","delete_competency","restore_competency","force_delete_competency","force_delete_any_competency","restore_any_competency","replicate_competency","reorder_competency","view_any_credential","view_credential","create_credential","update_credential","delete_credential","restore_credential","force_delete_credential","force_delete_any_credential","restore_any_credential","replicate_credential","reorder_credential","view_any_document","view_document","create_document","update_document","delete_document","restore_document","force_delete_document","force_delete_any_document","restore_any_document","replicate_document","reorder_document","view_any_event","view_event","create_event","update_event","delete_event","restore_event","force_delete_event","force_delete_any_event","restore_any_event","replicate_event","reorder_event","view_any_field","view_field","create_field","update_field","delete_field","restore_field","force_delete_field","force_delete_any_field","restore_any_field","replicate_field","reorder_field","view_any_form","view_form","create_form","update_form","delete_form","restore_form","force_delete_form","force_delete_any_form","restore_any_form","replicate_form","reorder_form","view_any_group","view_group","create_group","update_group","delete_group","restore_group","force_delete_group","force_delete_any_group","restore_any_group","replicate_group","reorder_group","view_any_image","view_image","create_image","update_image","delete_image","restore_image","force_delete_image","force_delete_any_image","restore_any_image","replicate_image","reorder_image","view_any_issuer","view_issuer","create_issuer","update_issuer","delete_issuer","restore_issuer","force_delete_issuer","force_delete_any_issuer","restore_any_issuer","replicate_issuer","reorder_issuer","view_any_message","view_message","create_message","update_message","delete_message","restore_message","force_delete_message","force_delete_any_message","restore_any_message","replicate_message","reorder_message","view_any_newsfeed","view_newsfeed","create_newsfeed","update_newsfeed","delete_newsfeed","restore_newsfeed","force_delete_newsfeed","force_delete_any_newsfeed","restore_any_newsfeed","replicate_newsfeed","reorder_newsfeed","view_any_position","view_position","create_position","update_position","delete_position","restore_position","force_delete_position","force_delete_any_position","restore_any_position","replicate_position","reorder_position","view_any_qualification","view_qualification","create_qualification","update_qualification","delete_qualification","restore_qualification","force_delete_qualification","force_delete_any_qualification","restore_any_qualification","replicate_qualification","reorder_qualification","view_any_rank","view_rank","create_rank","update_rank","delete_rank","restore_rank","force_delete_rank","force_delete_any_rank","restore_any_rank","replicate_rank","reorder_rank","view_any_slot","view_slot","create_slot","update_slot","delete_slot","restore_slot","force_delete_slot","force_delete_any_slot","restore_any_slot","replicate_slot","reorder_slot","view_any_specialty","view_specialty","create_specialty","update_specialty","delete_specialty","restore_specialty","force_delete_specialty","force_delete_any_specialty","restore_any_specialty","replicate_specialty","reorder_specialty","view_any_status","view_status","create_status","update_status","delete_status","restore_status","force_delete_status","force_delete_any_status","restore_any_status","replicate_status","reorder_status","view_any_submission","view_submission","create_submission","update_submission","delete_submission","restore_submission","force_delete_submission","force_delete_any_submission","restore_any_submission","replicate_submission","reorder_submission","view_any_task","view_task","create_task","update_task","delete_task","restore_task","force_delete_task","force_delete_any_task","restore_any_task","replicate_task","reorder_task","view_any_unit","view_unit","create_unit","update_unit","delete_unit","restore_unit","force_delete_unit","force_delete_any_unit","restore_any_unit","replicate_unit","reorder_unit","view_any_user","view_user","create_user","update_user","delete_user","restore_user","force_delete_user","force_delete_any_user","restore_any_user","replicate_user","reorder_user","view_any_webhook","view_webhook","create_webhook","update_webhook","delete_webhook","restore_webhook","force_delete_webhook","force_delete_any_webhook","restore_any_webhook","replicate_webhook","reorder_webhook","view_any_custom_report","view_custom_report","create_custom_report","update_custom_report","delete_custom_report","restore_custom_report","force_delete_custom_report","force_delete_any_custom_report","restore_any_custom_report","replicate_custom_report","reorder_custom_report","view_any_award_record","view_award_record","create_award_record","update_award_record","delete_award_record","restore_award_record","force_delete_award_record","force_delete_any_award_record","restore_any_award_record","replicate_award_record","reorder_award_record","view_any_page","view_page","create_page","update_page","delete_page","restore_page","force_delete_page","force_delete_any_page","restore_any_page","replicate_page","reorder_page","view_any_passport_client","view_passport_client","create_passport_client","update_passport_client","delete_passport_client","restore_passport_client","force_delete_passport_client","force_delete_any_passport_client","restore_any_passport_client","replicate_passport_client","reorder_passport_client","view_any_passport_token","view_passport_token","create_passport_token","update_passport_token","delete_passport_token","restore_passport_token","force_delete_passport_token","force_delete_any_passport_token","restore_any_passport_token","replicate_passport_token","reorder_passport_token","view_any_qualification_record","view_qualification_record","create_qualification_record","update_qualification_record","delete_qualification_record","restore_qualification_record","force_delete_qualification_record","force_delete_any_qualification_record","restore_any_qualification_record","replicate_qualification_record","reorder_qualification_record","view_any_rank_record","view_rank_record","create_rank_record","update_rank_record","delete_rank_record","restore_rank_record","force_delete_rank_record","force_delete_any_rank_record","restore_any_rank_record","replicate_rank_record","reorder_rank_record","view_any_service_record","view_service_record","create_service_record","update_service_record","delete_service_record","restore_service_record","force_delete_service_record","force_delete_any_service_record","restore_any_service_record","replicate_service_record","reorder_service_record","view_any_training_record","view_training_record","create_training_record","update_training_record","delete_training_record","restore_training_record","force_delete_training_record","force_delete_any_training_record","restore_any_training_record","replicate_training_record","reorder_training_record","view_any_webhook_log","view_webhook_log","create_webhook_log","update_webhook_log","delete_webhook_log","restore_webhook_log","force_delete_webhook_log","force_delete_any_webhook_log","restore_any_webhook_log","replicate_webhook_log","reorder_webhook_log","view_forms","view_submit","view_roster","view_widgets","view_logs","view_backups","view_billing","view_dashboard","view_fields","view_integration","view_notifications","view_organization","view_permission","view_registration","view_account_widget","view_organization_info_widget","view_users_overview","view_recent_announcements","view_recent_news","view_calendar_widget","view_any_combat_record","view_combat_record","create_combat_record","update_combat_record","delete_combat_record","restore_combat_record","force_delete_combat_record","force_delete_any_combat_record","restore_any_combat_record","replicate_combat_record","reorder_combat_record"]},{"name":"User","guard_name":"web","permissions":["view_calendar","view_any_user","view_user","view_page","view_forms","view_submit","view_roster","view_account_widget","view_organization_info_widget","view_recent_announcements","view_recent_news","view_calendar_widget","view_user_profile_widget","view_recent_records_widget"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }
}
