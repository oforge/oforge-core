<?php

namespace Oforge\Engine\Modules\Core\Helper;

/**
 * Class Statics
 *
 * @package Oforge\Engine\Modules\Core
 */
class Statics {
    /**
     * Default order value for all order properties.
     */
    public const DEFAULT_ORDER = 1337;
    public const ENGINE_DIR         = "Engine";
    public const PLUGIN_DIR         = "Plugins";
    public const VIEW_DIR           = "Views";
    public const TEMPLATE_DIR       = "Themes";
    public const VAR_DIR            = "var";
    public const ASSETS_DIR         = "__assets";
    public const PUBLIC_DIR         = DIRECTORY_SEPARATOR . Statics::VAR_DIR . DIRECTORY_SEPARATOR . "public";
    public const IMAGES_DIR           = Statics::PUBLIC_DIR . DIRECTORY_SEPARATOR . "images";
    public const CACHE_DIR          = DIRECTORY_SEPARATOR . Statics::VAR_DIR . DIRECTORY_SEPARATOR . "cache";
    public const THEME_CACHE_DIR    = Statics::PUBLIC_DIR . DIRECTORY_SEPARATOR . "theme";
    public const DB_CACHE_DIR       = Statics::CACHE_DIR . DIRECTORY_SEPARATOR . "db";
    public const ENDPOINT_CACHE_DIR = ROOT_PATH . self::CACHE_DIR . DIRECTORY_SEPARATOR . 'endpoints';
    public const ASSET_CACHE_DIR    = Statics::PUBLIC_DIR . DIRECTORY_SEPARATOR . Statics::ASSETS_DIR;
    public const ASSETS_ALL_SCSS    = "all.scss";
    public const ASSETS_SCSS        = "scss";
    public const ASSETS_IMPORT_JS   = "imports.cfg";
    public const ASSETS_JS          = "js";
    public const DB_CACHE_FILE      = Statics::DB_CACHE_DIR . DIRECTORY_SEPARATOR . "db.cache";
    public const IMPORTS_DIR        = DIRECTORY_SEPARATOR . Statics::VAR_DIR . DIRECTORY_SEPARATOR . "imports";
    public const DEFAULT_THEME      = "Base";
    public const MAIL_TEMPLATE_DIR  = Statics::TEMPLATE_DIR . DIRECTORY_SEPARATOR . "allyourhorses" . DIRECTORY_SEPARATOR . 'MailTemplates';
    //public const MAIL_TEMP_CACHE_DIR  = Statics::CACHE_DIR . DIRECTORY_SEPARATOR . 'mail';
    /**
     * Relative path of logs folder.
     */
    public const LOGS_DIR = DIRECTORY_SEPARATOR . Statics::VAR_DIR . DIRECTORY_SEPARATOR . 'logs';
    /**
     * TODO: Maybe find a better way to define global default scss variables
     */
    public const DEFAULT_SCSS_VARIABLES = [
        '$color-primary'                 => 'rgba(60, 60, 59, 1)',
        '$color-primary-light'           => 'rgba(112, 112, 112, 1)',
        '$color-primary-lighter'         => '#e8e8e8',
        '$color-primary-transparent-1'   => 'transparentize($color-primary, .3)',
        '$color-primary-transparent-2'   => 'rgba(0, 0, 0, 0.54)',
        '$header-top'                    => 'rgba(230,230,230,.8)',
        '$color-secondary'               => '#708e2b',
        '$color-secondary-light'         => '#90a75c',
        '$color-secondary-lighter'       => '#bac6a0',
        '$color-secondary-transparent-1' => 'transparentize($color-secondary, .25)',
        '$color-secondary-transparent-2' => 'rgba(169, 185, 134, .75)',
        '$color-light'                   => 'rgba(255, 255, 255, 1)',
        '$color-secondary-transparent-3' => 'transparentize($color-light, .3)',
        '$text-light'                    => '$color-light',
        '$text-dark'                     => '$color-primary',
        '$success'                       => '#0d0',
        '$info'                          => '#fc0',
        '$warning'                       => '#d00',
        '$success-font'                  => '$text-dark',
        '$info-font'                     => '$text-dark',
        '$warning-font'                  => '$text-light',
        '$font-family'                   => 'sans-serif',
        '$font-size-1'                   => '3rem',
        '$font-size-2'                   => '2.4rem',
        '$font-size-3'                   => '2rem',
        '$font-size-4'                   => '1.8rem',
        '$font-size-5'                   => '1.6rem',
        '$font-size-6'                   => '1.2rem',
        '$font-biggest'                  => '$font-size-1 $font-family',
        '$font-bigger'                   => '$font-size-2 $font-family',
        '$font-big'                      => '$font-size-3 $font-family',
        '$font-normal'                   => '$font-size-4 $font-family',
        '$screen-medium'                 => '640px',
        '$screen-large'                  => '960px',
        '$screen-max'                    => '1920px',
        '$content-max'                   => '1280px',
        '$size-small'                    => '.4rem',
        '$size-medium'                   => '.6rem',
        '$size-large'                    => '1.2rem',
        '$radius-small'                  => '$size-small',
        '$radius-medium'                 => '$size-medium',
        '$radius-large'                  => '$size-large',
    ];
}
