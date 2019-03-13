CREATE DATABASE IF NOT EXISTS oforge CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE oforge;


-- Adminer 4.7.1 MySQL dump

SET NAMES utf8mb4;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `oforge`;

DROP TABLE IF EXISTS `frontend_user_management_user`;
CREATE TABLE `frontend_user_management_user` (
                                               `id` int(11) NOT NULL AUTO_INCREMENT,
                                               `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                               `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                               `guid` char(36) COLLATE utf8_unicode_ci NOT NULL COMMENT '(DC2Type:guid)',
                                               `created_at` datetime NOT NULL,
                                               `updated_at` datetime NOT NULL,
                                               `active` tinyint(1) NOT NULL,
                                               PRIMARY KEY (`id`),
                                               UNIQUE KEY `UNIQ_CDBBD59EE7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `frontend_user_management_user` (`id`, `email`, `password`, `guid`, `created_at`, `updated_at`, `active`) VALUES
(1,	'testuser@oforge.com',	'$2y$10$Dz8M/0r1RWNLV0AGVlx0be8BaEF2dQhJVdY54ShKefg9yXZPs4t8G',	'00000000-0000-4000-8000-000000000000',	NOW(),	NOW(),	1);

DROP TABLE IF EXISTS `oforge_auth_backend_user`;
CREATE TABLE `oforge_auth_backend_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_E2EA19C7E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- insert default user with password geheim
INSERT INTO `oforge_auth_backend_user` (`id`, `email`, `password`, `role`, `created_at`, `updated_at`, `active`) VALUES
(1,	'admin@local.host',	'$2y$10$fnI/7By7ojrwUv51JRi.K.yskzFSy0N4iiE6VheIJUh6ln1EsYWSi',	1, NOW(), NOW(), 1);


DROP TABLE IF EXISTS `oforge_i18n_language`;
CREATE TABLE `oforge_i18n_language` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `iso` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                      `language` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                      `active` tinyint(1) NOT NULL,
                                      PRIMARY KEY (`id`),
                                      UNIQUE KEY `UNIQ_88C35DDF61587F41` (`iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_i18n_language` (`id`, `iso`, `language`, `active`) VALUES
(1,	'de',	'Deutsch',	1),
(2,	'en',	'English',	1);

DROP TABLE IF EXISTS `oforge_i18n_snippet`;
CREATE TABLE `oforge_i18n_snippet` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `scope` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `snippet_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `value` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     PRIMARY KEY (`id`),
                                     UNIQUE KEY `scope_name_unique` (`scope`,`snippet_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_i18n_snippet` (`id`, `scope`, `snippet_name`, `value`) VALUES
(1,	'de',	'site_name',	'Webseiten Name'),
(2,	'de',	'legal_rights',	'Impressum'),
(3,	'de',	'admin_label',	'Admin'),
(4,	'de',	'login',	'Login'),
(5,	'de',	'email_placeholder',	'E-Mail'),
(6,	'de',	'password_placeholder',	'Passwort'),
(7,	'de',	'signin',	'Anmelden'),
(8,	'de',	'role',	'Rolle'),
(9,	'de',	'profile',	'Profil'),
(10,	'de',	'signout',	'Abmelden'),
(11,	'de',	'backend_content',	'Inhalt'),
(12,	'de',	'backend_content_pages',	'Inhaltsseiten'),
(13,	'de',	'backend_content_elements',	'Inhaltselemente'),
(14,	'de',	'backend_content_types',	'Inhaltstypen'),
(15,	'de',	'admin',	'Admin'),
(16,	'de',	'backend_plugins',	'Plugins'),
(17,	'de',	'backend_template_settings',	'Template Einstellungen'),
(18,	'de',	'backend_settings',	'Einstellungen'),
(19,	'de',	'backend_i18n',	'Internationalisierung'),
(20,	'de',	'backend_i18n_language',	'Sprachen'),
(21,	'de',	'backend_i18n_snippets',	'Textschnipsel'),
(22,	'de',	'user_management',	'Benutzermanagement'),
(23,	'de',	'backend_documentation',	'Dokumentation'),
(24,	'de',	'backend_ui_elements',	'UI-Elemente'),
(25,	'de',	'backend_documentation_ui_general',	'General'),
(26,	'de',	'backend_documentation_ui_icons',	'Icons'),
(27,	'de',	'backend_documentation_ui_buttons',	'Buttons'),
(28,	'de',	'backend_documentation_ui_sliders',	'Slider'),
(29,	'de',	'backend_documentation_ui_timeline',	'Timeline'),
(30,	'de',	'backend_documentation_ui_modals',	'Modale'),
(31,	'de',	'backend_toggle_title',	'Title'),
(32,	'de',	'favorites_title',	'Deine Favoriten'),
(33,	'de',	'dashboard',	'Dashboard'),
(34,	'de',	'system',	'System'),
(35,	'de',	'backend',	'Backend'),
(36,	'de',	'mailer',	'Mailer'),
(37,	'de',	'backend_settings_group',	'Einstellungsgruppe'),
(38,	'de',	'Debug aktivieren',	'Debug aktivieren'),
(39,	'de',	'submit',	'Abschicken'),
(40,	'de',	'Projektname',	'Projektname'),
(41,	'de',	'Projektkürzel',	'Projektkürzel'),
(42,	'de',	'Copyright',	'Copyright'),
(43,	'de',	'Footer Text',	'Footer Text'),
(44,	'de',	'E-Mail Server',	'E-Mail Server'),
(45,	'de',	'E-Mail Username',	'E-Mail Username'),
(46,	'de',	'E-Mail Server Port',	'E-Mail Server Port'),
(47,	'de',	'E-Mail Exceptions',	'E-Mail Exceptions'),
(48,	'de',	'SMTP Password',	'SMTP Password'),
(49,	'de',	'STMP Debug',	'STMP Debug'),
(50,	'de',	'SMTP Auth',	'SMTP Auth'),
(51,	'de',	'Enable TLS encryption',	'Enable TLS encryption'),
(52,	'de',	'Mailer From',	'Mailer From'),
(53,	'de',	'crud_add_new',	'Hinzufügen'),
(54,	'de',	'backend_crud_header_id',	'ID'),
(55,	'de',	'backend_crud_header_iso',	'ISO'),
(56,	'de',	'backend_crud_header_name',	'Name'),
(57,	'de',	'backend_crud_header_active',	'Aktiv'),
(58,	'de',	'backend_crud_save',	'Speichern'),
(59,	'de',	'backend_crud_header_scope',	'Scope'),
(60,	'de',	'backend_crud_header_value',	'Wert'),
(61,	'de',	'usermanagement_title',	'Usermanagement'),
(62,	'de',	'user_add',	'Hinzufügen'),
(63,	'de',	'email',	'E-Mail'),
(64,	'de',	'update',	'Aktualisieren'),
(65,	'de',	'delete',	'Löschen'),
(66,	'de',	'user_delete_title',	'Benutzer löschen?'),
(67,	'de',	'user_delete_message',	'Wollen Sie diesen Nutzer wirklich löschen?'),
(68,	'de',	'yes',	'Ja'),
(69,	'de',	'no',	'Nein'),
(70,	'de',	'password',	'Passwort'),
(71,	'de',	'cancel',	'Abbrechen'),
(72,	'de',	'profile_title',	'Profil'),
(73,	'de',	'add',	'Hinzufügen'),
(74,	'de',	'cms_page_builder_page_tree_title',	'Seiten'),
(75,	'en',	'cms_page_builder_page_tree_title',	'Pages'),
(76,	'de',	'cms_page_builder_content_title',	'Inhalt'),
(77,	'en',	'cms_page_builder_content_title',	'Content'),
(78,	'de',	'cms_page_builder_content_language_selection',	'Sprache'),
(79,	'en',	'cms_page_builder_content_language_selection',	'Language'),
(80,	'de',	'cms_page_builder_content_page_active_checkbox_label',	'Aktiv'),
(81,	'en',	'cms_page_builder_content_page_active_checkbox_label',	'Active'),
(82,	'de',	'cms_page_builder_content_globals_link_label',	'Globals'),
(83,	'en',	'cms_page_builder_content_globals_link_label',	'Globals'),
(84,	'de',	'cms_page_builder_content_preview',	'Vorschau'),
(85,	'en',	'cms_page_builder_content_preview',	'Preview'),
(86,	'de',	'cms_page_builder_content_no_page_selected',	'Wählen Sie eine Seite zum Editieren!'),
(87,	'en',	'cms_page_builder_content_no_page_selected',	'Select a page for editing!'),
(88,	'de',	'cms_page_builder_content_types_title',	'Typen'),
(89,	'en',	'cms_page_builder_content_types_title',	'Types'),
(90,	'de',	'or_register',	'Oder registrieren'),
(91,	'de',	'forgot_password',	'Passwort vergessen?'),
(92,	'de',	'forgot_password_title',	'Passwort Vergessen?'),
(93,	'de',	'reset_password',	'Passwort zurücksetzen'),
(94,	'de',	'password_confirm',	'Passwort bestätigen'),
(95,	'de',	'change_password',	'Passwort ändern'),
(96,	'de',	'cms_page_builder_edit_content_types_title',	'Content Type Editieren'),
(97,	'en',	'cms_page_builder_edit_content_types_title',	'Edit Content Type'),
(98,	'de',	'cms_page_builder_create_new_root_page',	'Hier klicken um eine neue Root-Seite zu erstellen ...'),
(99,	'en',	'cms_page_builder_create_new_root_page',	'Click here to create a new root page ...');

DROP TABLE IF EXISTS `oforge_cms_content_type_group`;
CREATE TABLE `oforge_cms_content_type_group` (
                                     `id` int(11) NOT NULL,
                                     `content_type_group_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `description` varchar(255) COLLATE utf8mb4_unicode_ci,
                                     PRIMARY KEY (`id`),
                                     UNIQUE KEY `content_type_group_name_unique` (`content_type_group_name`),
                                     UNIQUE KEY `description_unique` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_cms_content_type_group` (`id`, `content_type_group_name`, `description`) VALUES
(1,	'container',	'Container'),
(2,	'basic',	'Basic'),
(3,	'media',	'Media');

DROP TABLE IF EXISTS `oforge_cms_content_type`;
CREATE TABLE `oforge_cms_content_type` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `content_type_group_id` int(11) NOT NULL,
                                     `content_type_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `content_type_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `content_type_icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `description` varchar(255) COLLATE utf8mb4_unicode_ci,
                                     `class_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     PRIMARY KEY (`id`),
                                     UNIQUE KEY `content_type_name_unique` (`content_type_name`),
                                     INDEX `content_type_group_id_index` (`content_type_group_id`),
				     FOREIGN KEY `content_type_group_id_foreign_key` (`content_type_group_id`)
				     REFERENCES `oforge_cms_content_type_group` (`id`)
				     ON UPDATE RESTRICT
				     ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_cms_content_type` (`id`, `content_type_group_id`, `content_type_name`, `content_type_path`, `content_type_icon`, `description`, `class_path`) VALUES
(1,	1,	'row',		'Row',		'/Themes/Base/ContentTypes/__assets/img/row.png',	'Row',		'Oforge\\Engine\\Modules\\CMS\\ContentTypes\\Row'),
(2,	2,	'richtext',	'RichText',	'/Themes/Base/ContentTypes/__assets/img/richtext.png',	'RichText',	'Oforge\\Engine\\Modules\\CMS\\ContentTypes\\RichText'),
(3,	3,	'image',	'Image',	'/Themes/Base/ContentTypes/__assets/img/image.png',	'Image',	'Oforge\\Engine\\Modules\\CMS\\ContentTypes\\Image');

DROP TABLE IF EXISTS `oforge_cms_content`;
CREATE TABLE `oforge_cms_content` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `content_type_id` int(11) NOT NULL,
                                     `parent_id` int(11) NOT NULL,
                                     `content_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `css_class` varchar(255) COLLATE utf8mb4_unicode_ci,
                                     `content_data` longtext COLLATE utf8mb4_unicode_ci,
                                     PRIMARY KEY (`id`),
                                     INDEX `content_type_id_index` (`content_type_id`),
				     FOREIGN KEY `content_type_id_foreign_key` (`content_type_id`)
				     REFERENCES `oforge_cms_content_type` (`id`)
				     ON UPDATE RESTRICT
				     ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_cms_content` (`id`, `content_type_id`, `parent_id`, `content_name`, `css_class`, `content_data`) VALUES
(1,	2,	0,	'text_home_de',		'home-class',		's:10:\"Startseite\";'),
(2,	2,	0,	'text_home_en',		'home-class',		's:4:\"Home\";'),
(3,	2,	0,	'text_imprint_de',	'imprint-class',	's:9:\"Impressum\";'),
(4,	2,	0,	'text_imprint_en',	'imprint-class',	's:7:\"Imprint\";'),
(5,	2,	0,	'text_privacy_de',	'privacy-class',	's:11:\"Datenschutz\";'),
(6,	2,	0,	'text_privacy_en',	'privacy-class',	's:7:\"Privacy\";'),
(7,	3,	0,	'world_image_de',	'image-class',		's:34:\"/Tests/dummy_media/deutschland.png\";'),
(8,	3,	0,	'world_image_en',	'image-class',		's:26:\"/Tests/dummy_media/usa.png\";'),
(9,	3,	0,	'multilanguage_image',	'image-class',		's:36:\"/Tests/dummy_media/multilanguage.png\";'),
(10,	2,	0,	'test_text1_de',	'row-text1-class',	's:107:\"Die größte Tragödie im Leben ist, dass wir zu schnell alt und zu spät weise werden. - Benjamin Franklin\";'),
(11,	2,	0,	'test_text1_en',	'row-text1-class',	's:87:\"Life biggest tragedy is that we get old too soon and wise too late. - Benjamin Franklin\";'),
(12,	2,	0,	'test_text2_de',	'row-text2-class',	's:147:\"Die größte Entscheidung deines Lebens liegt darin, dass du dein Leben ändern kannst, indem du deine Geisteshaltung änderst. - Albert Schweitzer\";'),
(13,	2,	0,	'test_text2_en',	'row-text2-class',	's:104:\"The biggest decision in life is about changing your life through changing your mind. - Albert Schweitzer\";'),
(14,	3,	0,	'landscape_image',	'row-image-class',	's:32:\"/Tests/dummy_media/landscape.png\";'),
(15,	3,	0,	'person_image',		'row-image-class',	's:29:\"/Tests/dummy_media/person.png\";'),
(16,	1,	0,	'test_row1_de',		'test-row1-class',	''),
(17,	1,	0,	'test_row1_en',		'test-row1-class',	''),
(18,	1,	0,	'test_row2_de',		'test-row2-class',	''),
(19,	1,	0,	'test_row2_en',		'test-row2-class',	''),
(20,	1,	0,	'test_row3_de',		'test-row3-class',	''),
(21,	1,	0,	'test_row3_en',		'test-row3-class',	'');

DROP TABLE IF EXISTS `oforge_cms_content_type_row`;
CREATE TABLE `oforge_cms_content_type_row` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `row_id` int(11) NOT NULL,
                                     `content_id` int(11) NOT NULL,
                                     `sort_order` int(11) NOT NULL,
                                     PRIMARY KEY (`id`),
                                     INDEX `content_id_index` (`content_id`),
				     FOREIGN KEY `content_id_foreign_key` (`content_id`)
				     REFERENCES `oforge_cms_content` (`id`)
				     ON UPDATE RESTRICT
				     ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_cms_content_type_row` (`id`, `row_id`, `content_id`, `sort_order`) VALUES
(1,	16,	14,	1),
(2,	16,	18,	2),
(3,	16,	14,	3),
(4,	18,	10,	1),
(5,	18,	20,	2),
(6,	20,	15,	1),
(7,	20,	12,	2),
(8,	17,	14,	1),
(9,	17,	19,	2),
(10,	17,	14,	3),
(11,	19,	11,	1),
(12,	19,	21,	2),
(13,	21,	15,	1),
(14,	21,	13,	2);

DROP TABLE IF EXISTS `oforge_cms_layout`;
CREATE TABLE `oforge_cms_layout` (
  					`id` int(11) NOT NULL AUTO_INCREMENT,
					`layout_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  					PRIMARY KEY (`id`),
  					UNIQUE KEY `layout_name_unique` (`layout_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_cms_layout` (`id`, `layout_name`) VALUES
(1,	'oforge');

DROP TABLE IF EXISTS `oforge_cms_site`;
CREATE TABLE `oforge_cms_site` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `site_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     `default_language_id` int(11) NOT NULL,
                                     PRIMARY KEY (`id`),
                                     UNIQUE KEY `site_name_unique` (`site_name`),
                                     UNIQUE KEY `domain_unique` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_cms_site` (`id`, `site_name`, `domain`, `default_language_id`) VALUES
(1,	'oforge',	'localhost',	1);

DROP TABLE IF EXISTS `oforge_cms_page`;
CREATE TABLE `oforge_cms_page` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `layout_id` int(11) NOT NULL,
                                     `site_id` int(11) NOT NULL,
                                     `parent_id` int(11) NOT NULL,
                                     `page_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     PRIMARY KEY (`id`),
                                     UNIQUE KEY `page_name_unique` (`page_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_cms_page` (`id`, `layout_id`, `site_id`, `parent_id`, `page_name`) VALUES
(1,	1,	1,	0,	'home'),
(2,	1,	1,	1,	'imprint'),
(3,	1,	1,	1,	'privacy');

DROP TABLE IF EXISTS `oforge_cms_page_path`;
CREATE TABLE `oforge_cms_page_path` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `page_id` int(11) NOT NULL,
                                     `language_id` int(11) NOT NULL,
                                     `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                                     PRIMARY KEY (`id`),
                                     INDEX `page_id_index` (`page_id`),
                                     INDEX `language_id_index` (`language_id`),
				     FOREIGN KEY `page_id_foreign_key` (`page_id`)
				     REFERENCES `oforge_cms_page` (`id`)
				     ON UPDATE RESTRICT
				     ON DELETE RESTRICT,
				     FOREIGN KEY `language_id_foreign_key` (`language_id`)
				     REFERENCES `oforge_i18n_language` (`id`)
				     ON UPDATE RESTRICT
				     ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_cms_page_path` (`id`, `page_id`, `language_id`, `path`) VALUES
(1,	1,	1,	'/'),
(2,	1,	2,	'/en'),
(3,	2,	1,	'/impressum'),
(4,	2,	2,	'/en/imprint'),
(5,	3,	1,	'/datenschutz'),
(6,	3,	2,	'/en/privacy');

DROP TABLE IF EXISTS `oforge_cms_page_content`;
CREATE TABLE `oforge_cms_page_content` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `page_path_id` int(11) NOT NULL,
                                     `content_id` int(11) NOT NULL,
                                     `sort_order` int(11) NOT NULL,
                                     PRIMARY KEY (`id`),
                                     INDEX `page_path_id_index` (`page_path_id`),
                                     INDEX `content_id_index` (`content_id`),
				     FOREIGN KEY `page_path_id_foreign_key` (`page_path_id`)
				     REFERENCES `oforge_cms_page_path` (`id`)
				     ON UPDATE RESTRICT
				     ON DELETE RESTRICT,
				     FOREIGN KEY `content_id_foreign_key` (`content_id`)
				     REFERENCES `oforge_cms_content` (`id`)
				     ON UPDATE RESTRICT
				     ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oforge_cms_page_content` (`id`, `page_path_id`, `content_id`, `sort_order`) VALUES
(1,	1,	1,	1),
(2,	1,	7,	2),
(3,	1,	9,	3),
(4,	1,	16,	4),
(5,	2,	2,	1),
(6,	2,	8,	2),
(7,	2,	9,	3),
(8,	2,	17,	4),
(9,	3,	3,	1),
(10,	3,	7,	2),
(11,	3,	9,	3),
(12,	4,	4,	1),
(13,	4,	8,	2),
(14,	4,	9,	3),
(15,	5,	5,	1),
(16,	5,	7,	2),
(17,	5,	9,	3),
(18,	6,	6,	1),
(19,	6,	8,	2),
(20,	6,	9,	3);


-- 2019-02-19 14:52:49
