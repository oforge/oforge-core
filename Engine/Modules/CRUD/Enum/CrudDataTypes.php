<?php

namespace Oforge\Engine\Modules\CRUD\Enum;

/**
 * Class CrudDataTypes
 *
 * @package Oforge\Engine\Modules\CRUD\Enum
 */
class CrudDataTypes {
    /**
     * Renderer: Div.<br/>
     * Editor: Text input.<br/>
     */
    public const STRING = 'string';
    /**
     * Renderer: Div.<br/>
     * Editor: Textarea.<br/>
     */
    public const TEXT = 'text';
    /**
     * Renderer: Div.<br/>
     * Editor: WYSIWYG editor.<br/>
     */
    public const HTML = 'html';
    /**
     * Renderer: Icon.<br/>
     * Editor: Checkbox.<br/>
     */
    public const BOOL = 'bool';
    /**
     * Renderer: Div with default right alinment.<br/>
     * Editor: Number input.<br/>
     */
    public const INT = 'int';
    /**
     * Renderer: Div with default right alinment.<br/>
     * Editor: Number input with step = 0.01.<br/>
     */
    public const DECIMAL = 'decimal';
    /**
     * Renderer: Div with default right alinment.<br/>
     * Editor: Number input.<br/>
     */
    public const FLOAT = 'float';
    /**
     * Renderer: Div.<br/>
     * Editor: Select.<br/>
     */
    public const SELECT = 'select';
    /**
     * Renderer: Clickable mailto link..<br/>
     * Editor: Email input.<br/>
     */
    public const EMAIL = 'email';
    /**
     * Renderer: Clickable link.<br/>
     * Editor: Url input.<br/>
     */
    public const URL = 'url';
    /**
     * Renderer: Icon with color as background.<br/>
     * Editor: Color picker.<br/>
     */
    public const COLOR = 'color';
    /**
     * Renderer: Custom render twig template path.<br/>
     * Editor: Custom editor twig template path.<br/>
     */
    public const CUSTOM = 'custom';

    private function __construct() {
    }

}
