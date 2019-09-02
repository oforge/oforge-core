<?php

namespace Oforge\Engine\Modules\CRUD\Enum;

/**
 * Class CrudDataTypes
 *
 * @package Oforge\Engine\Modules\CRUD\Enum
 */
class CrudDataTypes {
    /**
     * Renderer: Icon.<br/>
     * Editor: Checkbox.<br/>
     */
    public const BOOL = 'bool';
    /**
     * Renderer: Icon with color as background.<br/>
     * Editor: Color picker.<br/>
     */
    public const COLOR = 'color';
    /**
     * Renderer: Preview of colors with optional text listeners<br/>
     * Editor: Not editable.<br/>
     */
    public const COLORS_PREVIEW = 'colorsPreview';
    /**
     * Renderer: Custom render twig template path.<br/>
     * Editor: Custom editor twig template path.<br/>
     */
    public const CUSTOM = 'custom';
    /**
     * For DateTimeInterface values only!
     * Renderer: Div.<br/>
     * Editor: Date-Picker.<br/>
     */
    public const DATE = 'date';
    /**
     * For DateTimeInterface values only!
     * Renderer: Div.<br/>
     * Editor: Datetime-Picker.<br/>
     */
    public const DATETIME = 'dateTime';
    /**
     * For DateTimeInterface values only!
     * Renderer: Div.<br/>
     * Editor: Time-Picker.<br/>
     */
    public const TIME = 'time';
    /**
     * Renderer: Div with default right alinment.<br/>
     * Editor: Number input with step = 0.01.<br/>
     */
    public const DECIMAL = 'decimal';
    /**
     * Renderer: Clickable mailto link..<br/>
     * Editor: Email input.<br/>
     */
    public const EMAIL = 'email';
    /**
     * Renderer: Div with default right alinment.<br/>
     * Editor: Number input.<br/>
     */
    public const FLOAT = 'float';
    /**
     * Renderer: Div.<br/>
     * Editor: WYSIWYG editor.<br/>
     */
    public const HTML = 'html';
    /**
     * Renderer: Image element.<br/>
     * Editor: Image file input.<br/>
     */
    public const IMAGE = 'image';
    /**
     * Renderer: Div with default right alinment.<br/>
     * Editor: Number input.<br/>
     */
    public const INT = 'int';
    /**
     * Renderer: Div.<br/>
     * Editor: Select.<br/>
     */
    public const SELECT = 'select';
    /**
     * Renderer: Div.<br/>
     * Editor: Textarea.<br/>
     */
    public const TEXT = 'text';
    /**
     * Renderer: Div.<br/>
     * Editor: Text input.<br/>
     */
    public const STRING = 'string';
    /**
     * Renderer: Clickable link.<br/>
     * Editor: Url input.<br/>
     */
    public const URL = 'url';

    private function __construct() {
        // prevent instance
    }

}
