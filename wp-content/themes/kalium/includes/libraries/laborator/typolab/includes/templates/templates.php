<?php
/**
 * TypoLab - ultimate font management library.
 *
 * Underscore.js templates.
 *
 * @author Laborator
 * @link   https://laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<script type="text/template" id="tmpl-font-selector-item">
    <div class="font-selector-item">
        <span class="checked"><i class="kalium-admin-icon-check"></i></span>
        {{ data.title }}
    </div>
</script>

<script type="text/template" id="tmpl-font-family-name">
    <div class="font-preview-row" id="font-family-name">
        <div class="font-preview-row-bg">
            <span class="block-title">Font Family:</span>
            <p class="font-family-name">{{ data.fontFamily }}</p>
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-font-variants-select-container">
    <div class="font-preview-container">
        {{{ data.content }}}
    </div>
</script>

<script type="text/template" id="tmpl-select-font-variants">
    <# if ( data.stylesheet ) { #>
    <link href="{{ data.stylesheet }}" rel="stylesheet">
    <# } #>

    <# if ( data.style ) { #>
    {{{ data.style }}}
    <# } #>

    <div class="font-preview-row">
        <div class="font-preview-row-bg">
            <h3 class="block-title">Select Variants</h3>

            <# if ( data.variants ) { #>
            <# _.each( data.variants, function( variant, i ) { #>
            <#
            var id = 'variant-entry-' + (i + 1);
            var disabled = variant.disabled;
            var checked = _.contains( data.selected, variant.value );
            var fontFamily = variant.fontFamily;

            if ( 'string' === typeof fontFamily ) {
            fontFamily = "'" + fontFamily + "'";
            } else if ( fontFamily instanceof Array ) {
            fontFamily = fontFamily.join( ', ' );
            }
            #>
            <div class="variant-entry{{{ checked ? ' is-checked' : '' }}}">
                <# if ( variant.value ) { #>
                <div class="variant-checkbox">
                    <input type="checkbox" name="font_variants[]" id="{{ id }}" value="{{ variant.value }}" {{{ disabled ? ' disabled' : '' }}}{{{ checked ? ' checked' : '' }}}>
                </div>
                <# } #>
                <label class="variant-preview"<# if ( variant.value ) { #> for="{{ id }}"<# } #>>
                <# if ( variant.hasOwnProperty( 'image' ) ) { #>
                <div class="variant-preview-image">
                    <# if ( variant.image ) { #>
                    <img src="{{ variant.image }}"/>
                    <# } else { #>
                    Font preview image not available!
                    <# } #>
                </div>
                <# } else { #>
                <span class="variant-preview-text" style="font-family: {{ fontFamily }}; font-style: {{ variant.style }}; font-weight: {{ variant.weight }};">{{ data.previewText }}</span>
                <# } #>
                <span class="variant-title">{{ variant.title }}</span>
                </label>
            </div>
            <# } ); #>
            <# } else { #>
            <p class="loading-font-preview">Loading variants&hellip;</p>
            <# } #>

            {{{ data.footer }}}
        </div>
    </div>
</script>

<script type="text/template" id="tmpl-font-details-footer">
    <div class="font-details-link">
        <# _.each( data.details, function( content, i ) { #>
        <# if ( 0 < i ) { #>
        <span class="sep"></span>
        <# } #>

        {{{content}}}
        <# } ); #>
    </div>
</script>

<script type="text/template" id="tmpl-font-variant-form">
    <#
    var entryId = data.id;
    var toggledClass = data.isToggled ? 'typolab-toggle--toggled' : '';
    var browseButtonText = '<i class="far fa-folder"></i>Browse';
    var advancedOptionClass = 'advanced-option';
    var advancedOptionsShowText = 'Show more options';
    var advancedOptionsHideText = 'Hide advanced options';

    if ( data.advancedOptionsVisible ) {
    advancedOptionClass += ' is-visible';
    }
    #>
    <table class="typolab-table {{ toggledClass }}">
        <thead>
        <th colspan="2" class="typolab-toggle-body">
            Font Variant

            <span class="toggle-indicator"></span>
            <a href="#delete" class="delete">Remove</a>
        </th>
        </thead>
        <tbody>
        <tr class="hover vtop">
            <th width="35%">
                <label for="{{ entryId }}-style">Font Style:</label>
            </th>
            <td class="no-bg">
                <select class="select-font-style" name="font_variants[{{ data.id }}][style]" id="{{ entryId }}-style" tabindex="1">
                    {{{ data.fontStyleOptions }}}
                </select>
            </td>
        </tr>
        <tr class="hover vtop">
            <th>
                <label for="{{ entryId }}-weight">Font Weight:</label>
            </th>
            <td class="no-bg">
                <select class="select-font-weight" name="font_variants[{{ data.id }}][weight]" id="{{ entryId }}-weight" tabindex="1">
                    {{{ data.fontWeightOptions }}}
                </select>
            </td>
        </tr>
        <tr class="hover vtop">
            <th>
                <label for="font_url">Font Files:</label>
            </th>
            <td class="no-bg">
                <div class="font-file-entry font-file-woff2">
                    <label for="{{ entryId }}-file-woff2">WOFF2 Font File:</label>
                    <input type="text" name="font_variants[{{ data.id }}][src][woff2]" id="{{ entryId }}-file-woff2" tabindex="1" value="{{ data.files.woff2 }}" placeholder="Recommended">
                    <button class="button" type="button">{{{ browseButtonText }}}</button>
                </div>

                <div class="font-file-entry font-file-woff">
                    <label for="{{ entryId }}-file-woff">WOFF Font File:</label>
                    <input type="text" name="font_variants[{{ data.id }}][src][woff]" id="{{ entryId }}-file-woff" tabindex="1" value="{{ data.files.woff }}" placeholder="Not necessary if WOFF2 is provided">
                    <button class="button" type="button">{{{ browseButtonText }}}</button>
                </div>

                <div class="font-file-entry-message {{ advancedOptionClass }}">
                    Legacy Browser Support
                </div>

                <div class="font-file-entry font-file-ttf {{ advancedOptionClass }}">
                    <label for="{{ entryId }}-file-ttf">TTF Font File:</label>
                    <input type="text" name="font_variants[{{ data.id }}][src][ttf]" id="{{ entryId }}-file-ttf" tabindex="1" value="{{ data.files.ttf }}" placeholder="">
                    <button class="button" type="button">{{{ browseButtonText }}}</button>
                </div>

                <div class="font-file-entry font-file-svg {{ advancedOptionClass }}">
                    <label for="{{ entryId }}-file-svg">SVG Font File:</label>
                    <input type="text" name="font_variants[{{ data.id }}][src][svg]" id="{{ entryId }}-file-svg" tabindex="1" value="{{ data.files.svg }}" placeholder="">
                    <button class="button" type="button">{{{ browseButtonText }}}</button>
                </div>

                <div class="font-file-entry font-file-eot {{ advancedOptionClass }}">
                    <label for="{{ entryId }}-file-eot">EOT Font File:</label>
                    <input type="text" name="font_variants[{{ data.id }}][src][eot]" id="{{ entryId }}-file-eot" tabindex="1" value="{{ data.files.eot }}" placeholder="">
                    <button class="button" type="button">{{{ browseButtonText }}}</button>
                </div>

            </td>
        </tr>
        <tr class="hover vtop {{ advancedOptionClass }}">
            <th>
                <label for="{{ entryId }}-display">Font Display</label>
            </th>
            <td class="no-bg">
                <select class="select-font-display" name="font_variants[{{ data.id }}][display]" id="{{ entryId }}-display" tabindex="1">
                    {{{ data.fontDisplayOptions }}}
                </select>
            </td>
        </tr>
        <tr class="hover vtop hidden-row {{ advancedOptionClass }}">
            <th>
                <label for="{{ entryId }}-unicode-range">Unicode Range</label>
            </th>
            <td class="no-bg">
                <input type="text" class="input-font-unicode-range" name="font_variants[{{ data.id }}][unicode_range]" id="{{ entryId }}-unicode-range" tabindex="1" value="{{ data.unicodeRange }}" placeholder="Optional">
                <small>
                    Comma separated values. Can be single hex values and/or ranges separated with hyphens.
                    Example: "0100-024F,2020,20AD-20CF,2113".
                    <a href="https://developer.mozilla.org/en-US/docs/Web/CSS/@font-face/unicode-range" target="_blank" rel="noreferrer noopener">Learn
                        more</a>
                </small>
            </td>
        </tr>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="2" class="show-advanced-options-column">
                <a href="#" class="show-advanced-options" data-show-text="{{ advancedOptionsShowText }}" data-hide-text="{{ advancedOptionsHideText }}">
                    {{ data.advancedOptionsVisible ? advancedOptionsHideText : advancedOptionsShowText }}
                </a>
            </td>
        </tr>
        </tfoot>
    </table>
</script>

<script type="text/template" id="tmpl-custom-font-variant-form">
    <#
    var entryId = data.id;
    var toggledClass = data.isToggled ? 'typolab-toggle--toggled' : '';
    #>
    <table class="typolab-table {{ toggledClass }}">
        <thead>
        <th colspan="2" class="typolab-toggle-body">
            Font Variant

            <span class="toggle-indicator"></span>
            <a href="#delete" class="delete">Remove</a>
        </th>
        </thead>
        <tbody>
        <tr class="hover vtop">
            <th width="35%">
                <label for="{{ entryId }}-name">Font Family:</label>
            </th>
            <td class="no-bg">
                <input type="text" class="input-font-name" name="font_variants[{{ data.id }}][name]" id="{{ entryId }}-name" value="{{ data.fontFamily }}" placeholder="(Single font family name, no quotes required)" tabindex="1">
            </td>
        </tr>
        <tr class="hover vtop">
            <th width="35%">
                <label for="{{ entryId }}-style">Font Style:</label>
            </th>
            <td class="no-bg">
                <select class="select-font-style" name="font_variants[{{ data.id }}][style]" id="{{ entryId }}-style" tabindex="1">
                    {{{ data.fontStyleOptions }}}
                </select>
            </td>
        </tr>
        <tr class="hover vtop">
            <th>
                <label for="{{ entryId }}-weight">Font Weight:</label>
            </th>
            <td class="no-bg">
                <select class="select-font-weight" name="font_variants[{{ data.id }}][weight]" id="{{ entryId }}-weight" tabindex="1">
                    {{{ data.fontWeightOptions }}}
                </select>
            </td>
        </tr>
        </tbody>
    </table>
</script>

<script type="text/template" id="tmpl-conditional-statement-entry">
    <tr data-statment-id="{{ data.id }}">
        <td colspan="4" class="or-label"><span>OR</span></td>
    </tr>
    <tr data-statment-id="{{ data.id }}">
        <td class="statement">
            <select name="conditional_statements[{{ data.id }}][type]" class="select-statement-type">
            </select>
        </td>
        <td class="operator">
            <select name="conditional_statements[{{ data.id }}][operator]" class="select-statement-operator">
                {{{ data.operatorOptions }}}
            </select>
        </td>
        <td class="criteria">
            <select name="conditional_statements[{{ data.id }}][value]" class="select-statement-value">
            </select>
        </td>
        <td class="actions">
            <a href="#" class="remove-conditional-statement">
                <i class="kalium-admin-icon-remove kalium-icon-size-12"></i>
            </a>
        </td>
    </tr>
</script>

<script type="text/template" id="tmpl-select-options-list">
    <# _.each( data.optionsList, function( option ) { #>
    <#
    var selected = data.default && data.default === option.value;
    if ( data.selected ) {
    selected = data.selected.toString() === option.value.toString();
    }

    var selectedAttr = selected ? 'selected' : '';
    #>
    <option value="{{ option.value }}" {{ selectedAttr }}>{{{ option.title || option.value }}}</option>
    <# } ); #>
</script>

<script type="text/template" id="tmpl-button-group-button">
    <#
    var classes = [
    'button',
    data.checked ? 'button-primary' : 'button-alt',
    ];
    #>
    <button type="button" class="{{ classes.join( ' ' ) }}">
        <# if ( data.icon ) { #>
        <span class="{{ data.icon }}"></span>
        <# } #>
        <# if ( data.text ) { #>
        {{ data.text }}
        <# } #>
    </button>
</script>

<script type="text/template" id="tmpl-custom-selector-empty">
    <tr>
        <td colspan="8" class="no-selectors">No custom selectors.</td>
    </tr>
</script>

<script type="text/template" id="tmpl-custom-selector">
    <tr data-selector="{{ data.id }}">
        <td class="column-sort">
            <a href="#" title="Drag to reorder">
                <i class="kalium-admin-icon-drag kalium-icon-size-14"></i>
            </a>
        </td>
        <td class="column-selectors"></td>
        <td class="column-font-variant"></td>
        <td class="column-font-case"></td>
        <td class="column-font-size"></td>
        <td class="column-line-height"></td>
        <td class="column-letter-spacing"></td>
        <td class="column-actions">
            <a href="#" class="remove-custom-selector">
                <i class="kalium-admin-icon-remove kalium-icon-size-10"></i>
            </a>
        </td>
    </tr>
</script>

<script type="text/template" id="tmpl-font-appearance-element">
    <tr data-element-id="{{ data.id }}">
        <td class="column-element">
            <label>{{ data.name }}</label>
        </td>
        <td class="column-font-size"></td>
        <td class="column-line-height"></td>
        <td class="column-letter-spacing"></td>
        <td class="column-font-case"></td>
    </tr>
</script>

<script type="text/template" id="tmpl-custom-selectors-input">
    <select name="{{ data.inputName }}" multiple="true" class="selectors-select">
        <# _.each( data.predefinedSelectors, function( selector, id ) { #>
        <option value="{{ selector.value }}">{{ selector.name }}</option>
        <# } ); #>
    </select>
</script>

<script type="text/template" id="tmpl-size-unit-input">
    <div class="size-unit-input">
        <input type="number" class="value-input" value="{{ data.value }}" min="{{ data.min }}" max="{{ data.max }}" step="{{ data.step }}">
        <div class="units">
            <select class="units-select">
                <# _.each( data.units, function( title, value ) { #>
                <option value="{{ value }}">{{ title }}</option>
                <# } ); #>
            </select>
            <span class="unit-text"></span>
        </div>
    </div>
</script>
