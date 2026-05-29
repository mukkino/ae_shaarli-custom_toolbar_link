# Shaarli Custom Toolbar Link

A small configurable plugin for Shaarli that adds a custom hyperlink to the top toolbar without modifying Shaarli core files or theme templates.

The plugin is designed to be upgrade-safe and theme-friendly. It uses Shaarli’s plugin system to inject a toolbar item, then positions it according to semantic placement options such as `before-home`, `after-home`, `main-last`, `right-first`, or `right-last`.

It is useful when you want to add a permanent navigation link, for example back to your main website, blog, admin area, documentation, or another service hosted on the same domain.

## Features

* Add a custom toolbar link to Shaarli.
* Configure the visible link text.
* Configure the destination URL.
* Configure a tooltip/title shown on hover.
* Choose semantic placement options:

  * `before-home`
  * `after-home`
  * `before-shaare`
  * `after-shaare`
  * `before-tools`
  * `after-tools`
  * `main-first`
  * `main-last`
  * `right-first`
  * `right-last`
  * `center`
  * `native`
* Choose whether the link opens in the same tab or a new tab.
* Select colour/style presets:

  * `theme`
  * `auto`
  * `white`
  * `bold-white`
  * `black`
  * `dark`
  * `light`
  * `muted-light`
  * `muted-dark`
  * `blue`
  * `cyan`
  * `green`
  * `yellow`
  * `orange`
  * `red`
  * `purple`
  * `button`
  * `button-light`
  * `button-dark`
* Optional formatting controls:

  * font weight
  * italic/normal
  * underline behaviour
  * text case
  * text size
* No Shaarli core edits required.
* No theme file edits required.
* Safer across Shaarli updates.

## Example Use Case

Add a link from your Shaarli instance back to your main website:

```text
CUSTOM_LINK_TEXT: ← Main Site
CUSTOM_LINK_URL: https://example.com/
CUSTOM_LINK_TOOLTIP: Return to the main website
CUSTOM_LINK_POSITION: before-home
CUSTOM_LINK_STYLE: white
CUSTOM_LINK_WEIGHT: bold
CUSTOM_LINK_TARGET: same
```

## Why This Plugin Exists

Shaarli’s toolbar can be customised through plugins, but manually editing templates or theme files is fragile because changes can be lost during updates.

This plugin keeps the custom link separate from Shaarli core and allows the link text, URL, tooltip, placement, style, and formatting to be managed from Shaarli’s plugin parameters.

## Recommended Positioning

For the most predictable result, use semantic positions such as:

```text
before-home
after-home
main-first
main-last
right-first
right-last
```

Positions such as `before-tools` or `after-shaare` depend on whether those menu items are visible for the current user/session. If the target item is not available, the plugin falls back to a sensible default.

## Installation

Upload the plugin folder to:

```text
/path/to/shaarli/plugins/custom_toolbar_link/
```

Then enable it from:

```text
Shaarli → Tools → Plugin administration
```

After installing or updating the plugin, clear Shaarli’s compiled template cache by deleting the generated files inside:

```text
/path/to/shaarli/tmp/
```

Do not delete the `tmp` folder itself.

## Compatibility

Built for Shaarli v0.16.x and tested against the Shaarli v0.16.3 template structure.
