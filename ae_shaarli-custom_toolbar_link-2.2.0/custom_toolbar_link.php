<?php
/**
 * AE Custom Toolbar Link plugin for Shaarli.
 *
 * Version 2.2.0
 * 
 * Fabio Lichinchi (mukka) - https://alterego.cc/
 *
 * Generic configurable toolbar link plugin for Shaarli v0.16.x.
 *
 * Adds one configurable link to Shaarli's top toolbar without editing Shaarli
 * core or theme files.
 *
 * Configure from:
 *   Tools -> Plugin administration -> custom_toolbar_link parameters
 */

function custom_toolbar_link_escape_attr($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function custom_toolbar_link_conf($conf, $key, $default)
{
    try {
        if (is_object($conf) && method_exists($conf, 'get')) {
            $value = $conf->get('plugins.' . $key, $default);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }
    } catch (Exception $e) {
        return $default;
    } catch (Throwable $e) {
        return $default;
    }

    return $default;
}

function custom_toolbar_link_allow($value, array $allowed, $default)
{
    $value = strtolower(trim((string) $value));
    return in_array($value, $allowed, true) ? $value : $default;
}

function custom_toolbar_link_allowed_position($position)
{
    return custom_toolbar_link_allow(
        $position,
        array(
            'before-home',
            'after-home',
            'before-shaare',
            'after-shaare',
            'before-tools',
            'after-tools',
            'main-first',
            'main-last',
            'right-first',
            'right-last',
            'center',
            'native',
        ),
        'before-home'
    );
}

function custom_toolbar_link_allowed_style($style)
{
    return custom_toolbar_link_allow(
        $style,
        array(
            'theme',
            'auto',
            'white',
            'bold-white',
            'black',
            'dark',
            'light',
            'muted-light',
            'muted-dark',
            'blue',
            'cyan',
            'green',
            'yellow',
            'orange',
            'red',
            'purple',
            'button',
            'button-light',
            'button-dark',
        ),
        'theme'
    );
}

function custom_toolbar_link_allowed_target($target)
{
    return custom_toolbar_link_allow(
        $target,
        array(
            'same',
            'new',
        ),
        'same'
    );
}

function custom_toolbar_link_allowed_weight($weight)
{
    return custom_toolbar_link_allow(
        $weight,
        array(
            'theme',
            'normal',
            'bold',
            'lighter',
            'bolder',
            '100',
            '200',
            '300',
            '400',
            '500',
            '600',
            '700',
            '800',
            '900',
        ),
        'theme'
    );
}

function custom_toolbar_link_allowed_italic($italic)
{
    return custom_toolbar_link_allow(
        $italic,
        array(
            'theme',
            'normal',
            'italic',
        ),
        'theme'
    );
}

function custom_toolbar_link_allowed_underline($underline)
{
    return custom_toolbar_link_allow(
        $underline,
        array(
            'theme',
            'none',
            'hover',
            'always',
        ),
        'theme'
    );
}

function custom_toolbar_link_allowed_case($case)
{
    return custom_toolbar_link_allow(
        $case,
        array(
            'theme',
            'normal',
            'uppercase',
            'lowercase',
            'capitalize',
        ),
        'theme'
    );
}

function custom_toolbar_link_allowed_size($size)
{
    return custom_toolbar_link_allow(
        $size,
        array(
            'theme',
            'small',
            'normal',
            'large',
        ),
        'theme'
    );
}

/**
 * Validate/sanitise parameters when saved in Shaarli's plugin admin.
 *
 * @param array $data POST data.
 * @return array Sanitised POST data.
 */
function hook_custom_toolbar_link_save_plugin_parameters($data)
{
    if (isset($data['CUSTOM_LINK_TEXT'])) {
        $data['CUSTOM_LINK_TEXT'] = trim((string) $data['CUSTOM_LINK_TEXT']);
    }

    if (isset($data['CUSTOM_LINK_TOOLTIP'])) {
        $data['CUSTOM_LINK_TOOLTIP'] = trim((string) $data['CUSTOM_LINK_TOOLTIP']);
    }

    if (isset($data['CUSTOM_LINK_URL'])) {
        $data['CUSTOM_LINK_URL'] = trim((string) $data['CUSTOM_LINK_URL']);

        // Keep absolute http(s), mailto, tel, root-relative and Shaarli-relative URLs.
        if (
            $data['CUSTOM_LINK_URL'] !== ''
            && !preg_match('~^(https?://|mailto:|tel:|/|\./|\../)~i', $data['CUSTOM_LINK_URL'])
        ) {
            $data['CUSTOM_LINK_URL'] = 'https://' . $data['CUSTOM_LINK_URL'];
        }
    }

    if (isset($data['CUSTOM_LINK_POSITION'])) {
        $data['CUSTOM_LINK_POSITION'] = custom_toolbar_link_allowed_position($data['CUSTOM_LINK_POSITION']);
    }

    if (isset($data['CUSTOM_LINK_STYLE'])) {
        $data['CUSTOM_LINK_STYLE'] = custom_toolbar_link_allowed_style($data['CUSTOM_LINK_STYLE']);
    }

    if (isset($data['CUSTOM_LINK_TARGET'])) {
        $data['CUSTOM_LINK_TARGET'] = custom_toolbar_link_allowed_target($data['CUSTOM_LINK_TARGET']);
    }

    if (isset($data['CUSTOM_LINK_WEIGHT'])) {
        $data['CUSTOM_LINK_WEIGHT'] = custom_toolbar_link_allowed_weight($data['CUSTOM_LINK_WEIGHT']);
    }

    if (isset($data['CUSTOM_LINK_ITALIC'])) {
        $data['CUSTOM_LINK_ITALIC'] = custom_toolbar_link_allowed_italic($data['CUSTOM_LINK_ITALIC']);
    }

    if (isset($data['CUSTOM_LINK_UNDERLINE'])) {
        $data['CUSTOM_LINK_UNDERLINE'] = custom_toolbar_link_allowed_underline($data['CUSTOM_LINK_UNDERLINE']);
    }

    if (isset($data['CUSTOM_LINK_CASE'])) {
        $data['CUSTOM_LINK_CASE'] = custom_toolbar_link_allowed_case($data['CUSTOM_LINK_CASE']);
    }

    if (isset($data['CUSTOM_LINK_SIZE'])) {
        $data['CUSTOM_LINK_SIZE'] = custom_toolbar_link_allowed_size($data['CUSTOM_LINK_SIZE']);
    }

    return $data;
}

/**
 * Add the structured toolbar item.
 *
 * Shaarli v0.16.x templates expect buttons_toolbar items as arrays:
 * [
 *   'attr' => ['href' => '...', 'title' => '...'],
 *   'html' => 'Visible label'
 * ]
 *
 * @param array $data Template data.
 * @param mixed $conf Config manager.
 * @return array
 */
function hook_custom_toolbar_link_render_header($data, $conf)
{
    if (!isset($data['buttons_toolbar']) || !is_array($data['buttons_toolbar'])) {
        $data['buttons_toolbar'] = array();
    }

    $text = custom_toolbar_link_conf($conf, 'CUSTOM_LINK_TEXT', '← Alterego.cc');
    $url = custom_toolbar_link_conf($conf, 'CUSTOM_LINK_URL', 'https://alterego.cc/');
    $tooltip = custom_toolbar_link_conf($conf, 'CUSTOM_LINK_TOOLTIP', '');
    $position = custom_toolbar_link_allowed_position(custom_toolbar_link_conf($conf, 'CUSTOM_LINK_POSITION', 'before-home'));
    $style = custom_toolbar_link_allowed_style(custom_toolbar_link_conf($conf, 'CUSTOM_LINK_STYLE', 'auto'));
    $target = custom_toolbar_link_allowed_target(custom_toolbar_link_conf($conf, 'CUSTOM_LINK_TARGET', 'same'));

    $weight = custom_toolbar_link_allowed_weight(custom_toolbar_link_conf($conf, 'CUSTOM_LINK_WEIGHT', 'theme'));
    $italic = custom_toolbar_link_allowed_italic(custom_toolbar_link_conf($conf, 'CUSTOM_LINK_ITALIC', 'theme'));
    $underline = custom_toolbar_link_allowed_underline(custom_toolbar_link_conf($conf, 'CUSTOM_LINK_UNDERLINE', 'theme'));
    $case = custom_toolbar_link_allowed_case(custom_toolbar_link_conf($conf, 'CUSTOM_LINK_CASE', 'theme'));
    $size = custom_toolbar_link_allowed_size(custom_toolbar_link_conf($conf, 'CUSTOM_LINK_SIZE', 'theme'));

    // If no custom tooltip is provided, use the visible text as the browser tooltip.
    $title = trim((string) $tooltip) !== '' ? $tooltip : $text;

    $classes = array(
        'custom-toolbar-link',
        'custom-toolbar-link-style-' . $style,
        'custom-toolbar-link-weight-' . $weight,
        'custom-toolbar-link-italic-' . $italic,
        'custom-toolbar-link-underline-' . $underline,
        'custom-toolbar-link-case-' . $case,
        'custom-toolbar-link-size-' . $size,
    );

    $attr = array(
        'href' => custom_toolbar_link_escape_attr($url),
        'title' => custom_toolbar_link_escape_attr($title),
        'id' => 'custom-toolbar-link',
        'class' => implode(' ', array_map('custom_toolbar_link_escape_attr', $classes)),
        'data-custom-toolbar-position' => custom_toolbar_link_escape_attr($position),
        'data-custom-toolbar-style' => custom_toolbar_link_escape_attr($style),
        'data-custom-toolbar-weight' => custom_toolbar_link_escape_attr($weight),
        'data-custom-toolbar-italic' => custom_toolbar_link_escape_attr($italic),
        'data-custom-toolbar-underline' => custom_toolbar_link_escape_attr($underline),
        'data-custom-toolbar-case' => custom_toolbar_link_escape_attr($case),
        'data-custom-toolbar-size' => custom_toolbar_link_escape_attr($size),
        'rel' => $target === 'new' ? 'noopener noreferrer' : 'noopener',
    );

    if ($target === 'new') {
        $attr['target'] = '_blank';
    }

    $button = array(
        // Top-level class exists only to trigger Shaarli default template class handling.
        'class' => 'custom-toolbar-link',
        'attr' => $attr,
        // Plugin admin text is plain text, not HTML.
        'html' => htmlspecialchars((string) $text, ENT_QUOTES, 'UTF-8'),
    );

    $data['buttons_toolbar'][] = $button;

    return $data;
}

function hook_custom_toolbar_link_render_includes($data, $conf)
{
    if (!isset($data['css_files']) || !is_array($data['css_files'])) {
        $data['css_files'] = array();
    }

    $data['css_files'][] = 'plugins/custom_toolbar_link/custom_toolbar_link_220.css';

    return $data;
}

function hook_custom_toolbar_link_render_footer($data, $conf)
{
    if (!isset($data['js_files']) || !is_array($data['js_files'])) {
        $data['js_files'] = array();
    }

    $data['js_files'][] = 'plugins/custom_toolbar_link/custom_toolbar_link_220.js';

    return $data;
}
