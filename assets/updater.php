<?php

define('IN_MANAGER_MODE', true);
define('MODX_API_MODE', true);
include '../index.php';

$modx = EvolutionCMS();
$version = $modx->getVersionData();
if (version_compare($version['version'], '2.0.3', '<')) {
    echo 'Please update to version 2.0.3 before start this script';
    exit();
}
try {
    $dbh = new \PDO($modx->getDatabase()->getConfig('driver') . ':host=' . $modx->getDatabase()->getConfig('host') . ';dbname=' . $modx->getDatabase()->getConfig('database'), $modx->getDatabase()->getConfig('username'), $modx->getDatabase()->getConfig('password'));
} catch (PDOException $exception) {
    echo $exception->getMessage();
    exit();
}


$serverVersion = $dbh->getAttribute(\PDO::ATTR_SERVER_VERSION);
if (stristr($serverVersion, 'MySQL'))
    if (version_compare($serverVersion, '5.6.0', '<')) {
        echo '<span class="notok">Need minimum MySQL 5.6 version</span>';
        exit();
    }

$username = \EvolutionCMS\Models\ManagerUser::query()->pluck('username');
$emails = \EvolutionCMS\Models\UserAttribute::query()->pluck('email');
$checkUsername = \EvolutionCMS\Models\WebUser::query()->whereIn('username', $username);
$checkEmails = \EvolutionCMS\Models\WebUserAttribute::query()->whereIn('email', $emails);
if ($checkUsername->count() > 0 || $checkEmails->count() > 0) {
    echo 'some manager conflict with web user';
    if ($checkUsername->count() > 0) {
        echo '<table> <thead><th>Manager Id</th><th>Manager username</th><th>Web User Id</th><th>Web user username</th></thead><tbody></tbody>';
        foreach ($checkUsername->get()->toArray() as $webuser) {
            $user = \EvolutionCMS\Models\ManagerUser::query()->where('username', $webuser['username'])->first()->toArray();
            echo '<tr><td>' . $user['id'] . '</td><td>' . $user['username'] . '</td><td>' . $webuser['id'] . '</td><td>' . $webuser['username'] . '</td></tr>';
        }
    }
    if ($checkEmails->count() > 0) {
        echo '</tbody></table>';
        echo '<table> <thead><th>Manager Id</th><th>Manager email</th><th>Web User Id</th><th>Web user email</th></thead><tbody></tbody>';
        foreach ($checkEmails->get()->toArray() as $webuser) {
            $user = \EvolutionCMS\Models\UserAttribute::query()->where('email', $webuser['email'])->first()->toArray();
            echo '<tr><td>' . $user['internalKey'] . '</td><td>' . $user['email'] . '</td><td>' . $webuser['internalKey'] . '</td><td>' . $webuser['email'] . '</td></tr>';
        }
        echo '</tbody></table>';
    }
    exit();
}
$users = \EvolutionCMS\Models\ManagerUser::all();
foreach ($users as $user) {
    $user = $user->makeVisible('password');
    $userArray = $user->toArray();
    $userAttributes = $user->attributes()->first();
    if(!is_null($userAttributes)){
        $userAttributes = $userAttributes->toArray();
    }else {
        $userAttributes = [];
    }
    $userSettings = $user->settings()->get()->toArray();
    $userMemberGroup = $user->memberGroups()->get()->toArray();
    $oldId = $userArray['id'];
    unset($userArray['id']);
    unset($userAttributes['id']);
    $newUser = \EvolutionCMS\Models\WebUser::query()->create($userArray);
    $id = $newUser->getKey();
    $userAttributes['internalKey'] = $id;
    \EvolutionCMS\Models\WebUserAttribute::query()->create($userAttributes);
    foreach ($userSettings as $setting) {
        $setting['user'] = $id;
        $modx->db->insert($setting, $modx->getFullTableName('user_settings'));
    }
    foreach ($userMemberGroup as $group) {
        $group['member'] = $id;
        \EvolutionCMS\Models\MemberGroup::query()->create($group);
    }
    foreach ($user->memberGroups()->get() as $group) {
        $group->delete();
    }
    foreach ($user->settings()->get() as $item) {
        $modx->db->delete($modx->getFullTableName('user_settings'), 'user='.$oldId);
    }
}
$sql = "DROP TABLE IF EXISTS " . $modx->db->getFullTableName('migrations_install');
$modx->db->query($sql);
$sql = "

CREATE TABLE " . $modx->db->getFullTableName('migrations_install') . " (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$modx->db->query($sql);

$sql2 = "

INSERT INTO " . $modx->db->getFullTableName('migrations_install') . " (`id`, `migration`, `batch`) VALUES
(1,	'2018_06_29_182342_create_active_user_locks_table',	1),
(2,	'2018_06_29_182342_create_active_user_sessions_table',	1),
(3,	'2018_06_29_182342_create_active_users_table',	1),
(4,	'2018_06_29_182342_create_categories_table',	1),
(5,	'2018_06_29_182342_create_document_groups_table',	1),
(6,	'2018_06_29_182342_create_documentgroup_names_table',	1),
(7,	'2018_06_29_182342_create_event_log_table',	1),
(8,	'2018_06_29_182342_create_manager_log_table',	1),
(9,	'2018_06_29_182342_create_manager_users_table',	1),
(10,	'2018_06_29_182342_create_member_groups_table',	1),
(11,	'2018_06_29_182342_create_membergroup_access_table',	1),
(12,	'2018_06_29_182342_create_membergroup_names_table',	1),
(13,	'2018_06_29_182342_create_site_content_table',	1),
(14,	'2018_06_29_182342_create_site_htmlsnippets_table',	1),
(15,	'2018_06_29_182342_create_site_module_access_table',	1),
(16,	'2018_06_29_182342_create_site_module_depobj_table',	1),
(17,	'2018_06_29_182342_create_site_modules_table',	1),
(18,	'2018_06_29_182342_create_site_plugin_events_table',	1),
(19,	'2018_06_29_182342_create_site_plugins_table',	1),
(20,	'2018_06_29_182342_create_site_snippets_table',	1),
(21,	'2018_06_29_182342_create_site_templates_table',	1),
(22,	'2018_06_29_182342_create_site_tmplvar_access_table',	1),
(23,	'2018_06_29_182342_create_site_tmplvar_contentvalues_table',	1),
(24,	'2018_06_29_182342_create_site_tmplvar_templates_table',	1),
(25,	'2018_06_29_182342_create_site_tmplvars_table',	1),
(26,	'2018_06_29_182342_create_system_eventnames_table',	1),
(27,	'2018_06_29_182342_create_system_settings_table',	1),
(28,	'2018_06_29_182342_create_user_attributes_table',	1),
(29,	'2018_06_29_182342_create_user_roles_table',	1),
(30,	'2018_06_29_182342_create_user_settings_table',	1),
(31,	'2018_06_29_182342_create_web_groups_table',	1),
(32,	'2018_06_29_182342_create_web_user_attributes_table',	1),
(33,	'2018_06_29_182342_create_web_user_settings_table',	1),
(34,	'2018_06_29_182342_create_web_users_table',	1),
(35,	'2018_06_29_182342_create_webgroup_access_table',	1),
(36,	'2018_06_29_182342_create_webgroup_names_table',	1);";

$modx->db->query($sql2);
$sql3 = "ALTER TABLE " . $modx->db->getFullTableName('site_content') . " ENGINE=InnoDB;";
$modx->db->query($sql3);

echo "Site ready to update, please download latest version from github and unpack to server";
unlink(__FILE__);

