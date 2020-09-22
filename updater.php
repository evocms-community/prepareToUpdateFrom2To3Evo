<?php

define('IN_MANAGER_MODE', true);
define('MODX_API_MODE', true);
include 'index.php';

$modx = EvolutionCMS();
$version = $modx->getVersionData();
if (version_compare($version['version'], '2.0.3', '<')) {
    echo 'Please update to version 2.0.3 before start this script';
} else {
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
}
