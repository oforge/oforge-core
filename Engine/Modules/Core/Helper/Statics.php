<?php

namespace Oforge\Engine\Modules\Core\Helper;

class Statics
{
    public const ENGINE_DIR = "Engine";
    public const PLUGIN_DIR = "Plugins";
    public const VIEW_DIR = "Views";
    public const TEMPLATE_DIR = "Themes";
    public const VAR_DIR = "var";
    public const CACHE_DIR = DIRECTORY_SEPARATOR . Statics::VAR_DIR . DIRECTORY_SEPARATOR . "cache";
    public const THEME_CACHE_DIR = Statics::CACHE_DIR . DIRECTORY_SEPARATOR . "theme";
    public const DB_CACHE_DIR = Statics::CACHE_DIR . DIRECTORY_SEPARATOR . "db";
    public const ASSET_CACHE_DIR = Statics::CACHE_DIR . DIRECTORY_SEPARATOR . "__assets";
    public const ASSETS_DIR = "__assets";
    public const ASSETS_ALL_SCSS = "all.scss";
    public const ASSETS_SCSS = "scss";
    public const ASSETS_IMPORT_JS = "imports.cfg";
    public const ASSETS_JS = "js";
    public const DB_CACHE_FILE = Statics::DB_CACHE_DIR . DIRECTORY_SEPARATOR . "db.cache";
}
