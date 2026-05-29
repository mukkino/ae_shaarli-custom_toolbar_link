# AE Custom Toolbar Link for Shaarli

Version 2.2.0

Generic configurable toolbar link plugin for Shaarli v0.16.x.

This version adds more readable colour/style presets, including an `auto` option that attempts to choose black or white text based on the toolbar background.

Fabio Lichinchi (mukka), https://alterego.cc/, 29th of May 2026

## Install

Upload the folder:

    custom_toolbar_link

to:

    /public_html/shaarli/plugins/custom_toolbar_link/

Then enable it in Shaarli:

    Tools → Plugin administration

After installing/updating, clear compiled template files inside:

    /public_html/shaarli/tmp/

Do not delete the `tmp` folder itself.

## Configure

In Shaarli:

    Tools → Plugin administration → custom_toolbar_link parameters

Set:

### CUSTOM_LINK_TEXT

Visible text.

Examples:

    ← Alterego.cc
    Main site
    Back home

### CUSTOM_LINK_URL

Destination URL.

Examples:

    https://alterego.cc/
    /wp/
    ./admin/tools

### CUSTOM_LINK_TOOLTIP

Tooltip/title shown when hovering the link.

Leave empty to reuse the visible link text as the tooltip.

### CUSTOM_LINK_POSITION

Allowed values:

    before-home
    after-home
    before-shaare
    after-shaare
    before-tools
    after-tools
    main-first
    main-last
    right-first
    right-last
    center
    native

Recommended default:

    before-home

### CUSTOM_LINK_STYLE

Colour/style preset:

    theme
    auto
    white
    bold-white
    black
    dark
    light
    muted-light
    muted-dark
    blue
    cyan
    green
    yellow
    orange
    red
    purple
    button
    button-light
    button-dark

Recommended:

    auto

For your the green Shaarli toolbar:

    white

or:

    bold-white

Use `theme` to inherit the Shaarli theme styling with minimal override.

### CUSTOM_LINK_WEIGHT

Font weight:

    theme
    normal
    bold
    lighter
    bolder
    100
    200
    300
    400
    500
    600
    700
    800
    900

### CUSTOM_LINK_ITALIC

Font style:

    theme
    normal
    italic

### CUSTOM_LINK_UNDERLINE

Underline behaviour:

    theme
    none
    hover
    always

### CUSTOM_LINK_CASE

Text case:

    theme
    normal
    uppercase
    lowercase
    capitalize

### CUSTOM_LINK_SIZE

Text size:

    theme
    small
    normal
    large

### CUSTOM_LINK_TARGET

Allowed values:

    same
    new

## Good default examples

    CUSTOM_LINK_TEXT: ← Alterego.cc
    CUSTOM_LINK_URL: https://alterego.cc/
    CUSTOM_LINK_TOOLTIP: Return to Alterego.cc
    CUSTOM_LINK_POSITION: before-home
    CUSTOM_LINK_STYLE: white
    CUSTOM_LINK_WEIGHT: bold
    CUSTOM_LINK_ITALIC: normal
    CUSTOM_LINK_UNDERLINE: none
    CUSTOM_LINK_CASE: normal
    CUSTOM_LINK_SIZE: theme
    CUSTOM_LINK_TARGET: same
