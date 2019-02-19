CREATE DATABASE IF NOT EXISTS oforge;
USE oforge;


-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `oforge`;

DROP TABLE IF EXISTS `oforge_auth_backend_user`;
CREATE TABLE `oforge_auth_backend_user` (
                                          `id` int(11) NOT NULL AUTO_INCREMENT,
                                          `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                          `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                          `role` int(11) NOT NULL,
                                          PRIMARY KEY (`id`),
                                          UNIQUE KEY `UNIQ_E2EA19C7E7927C74` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- insert default user with password geheim
INSERT INTO `oforge_auth_backend_user` (`id`, `email`, `password`, `role`) VALUES
(1,	'admin@local.host',	'$2y$10$fnI/7By7ojrwUv51JRi.K.yskzFSy0N4iiE6VheIJUh6ln1EsYWSi',	1);


DROP TABLE IF EXISTS `oforge_i18n_language`;
CREATE TABLE `oforge_i18n_language` (
                                      `id` int(11) NOT NULL AUTO_INCREMENT,
                                      `iso` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                      `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                      `active` tinyint(1) NOT NULL,
                                      PRIMARY KEY (`id`),
                                      UNIQUE KEY `UNIQ_88C35DDF61587F41` (`iso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `oforge_i18n_language` (`id`, `iso`, `name`, `active`) VALUES
(1,	'de',	'Deutsch',	1),
(2,	'en',	'English',	1);

DROP TABLE IF EXISTS `oforge_i18n_snippet`;
CREATE TABLE `oforge_i18n_snippet` (
                                     `id` int(11) NOT NULL AUTO_INCREMENT,
                                     `scope` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                     `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                     `value` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                     PRIMARY KEY (`id`),
                                     UNIQUE KEY `scope_name_unique` (`scope`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `oforge_i18n_snippet` (`id`, `scope`, `name`, `value`) VALUES
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
(34,	'de',	'cms_page_builder_page_tree_title',	'Seiten'),
(35,	'de',	'cms_page_builder_content_title',	'Inhalt'),
(36,	'de',	'cms_page_builder_content_no_page_selected',	'Keine Seite ausgewählt'),
(37,	'de',	'cms_page_builder_content_types_title',	'Inhaltstypen'),
(38,	'de',	'system',	'System'),
(39,	'de',	'backend',	'Backend'),
(40,	'de',	'mailer',	'Mailer'),
(41,	'de',	'backend_settings_group',	'Einstellungsgruppe'),
(42,	'de',	'Debug aktivieren',	'Debug aktivieren'),
(43,	'de',	'submit',	'Abschicken'),
(44,	'de',	'Projektname',	'Projektname'),
(45,	'de',	'Projektkürzel',	'Projektkürzel'),
(46,	'de',	'Copyright',	'Copyright'),
(47,	'de',	'Footer Text',	'Footer Text'),
(48,	'de',	'E-Mail Server',	'E-Mail Server'),
(49,	'de',	'E-Mail Username',	'E-Mail Username'),
(50,	'de',	'E-Mail Server Port',	'E-Mail Server Port'),
(51,	'de',	'E-Mail Exceptions',	'E-Mail Exceptions'),
(52,	'de',	'SMTP Password',	'SMTP Password'),
(53,	'de',	'STMP Debug',	'STMP Debug'),
(54,	'de',	'SMTP Auth',	'SMTP Auth'),
(55,	'de',	'Enable TLS encryption',	'Enable TLS encryption'),
(56,	'de',	'Mailer From',	'Mailer From'),
(57,	'de',	'crud_add_new',	'Hinzufügen'),
(58,	'de',	'backend_crud_header_id',	'ID'),
(59,	'de',	'backend_crud_header_iso',	'ISO'),
(60,	'de',	'backend_crud_header_name',	'Name'),
(61,	'de',	'backend_crud_header_active',	'Aktiv'),
(62,	'de',	'backend_crud_save',	'Speichern'),
(63,	'de',	'backend_crud_header_scope',	'Scope'),
(64,	'de',	'backend_crud_header_value',	'Wert'),
(65,	'de',	'usermanagement_title',	'Usermanagement'),
(66,	'de',	'user_add',	'Hinzufügen'),
(67,	'de',	'email',	'E-Mail'),
(68,	'de',	'update',	'Aktualisieren'),
(69,	'de',	'delete',	'Löschen'),
(70,	'de',	'user_delete_title',	'Benutzer löschen?'),
(71,	'de',	'user_delete_message',	'Wollen Sie diesen Nutzer wirklich löschen?'),
(72,	'de',	'yes',	'Ja'),
(73,	'de',	'no',	'Nein'),
(74,	'de',	'password',	'Passwort'),
(75,	'de',	'cancel',	'Abbrechen'),
(76,	'de',	'profile_title',	'Profil'),
(77,	'de',	'add',	'Hinzufügen');

-- 2019-02-19 14:52:49