(function () {
    "use strict";

    function ready(fn) {
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", fn);
        } else {
            fn();
        }
    }

    function closestLi(el) {
        return el && el.closest ? el.closest("li") : null;
    }

    function insertAfter(referenceNode, newNode) {
        if (!referenceNode || !referenceNode.parentNode) {
            return false;
        }

        referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
        return true;
    }

    function cleanText(text) {
        return (text || "").replace(/\s+/g, " ").trim().toLowerCase();
    }

    function findLinkByText(textCandidates) {
        var links = document.querySelectorAll("a");

        for (var i = 0; i < links.length; i += 1) {
            var t = cleanText(links[i].textContent);

            for (var j = 0; j < textCandidates.length; j += 1) {
                if (t === textCandidates[j] || t.indexOf(textCandidates[j]) !== -1) {
                    return links[i];
                }
            }
        }

        return null;
    }

    function findHomeItem() {
        /*
         * Default theme: the desktop Shaarli title is the most stable anchor.
         * Vintage theme: #shaarli_title wraps the title link.
         */
        var defaultTitle = closestLi(document.getElementById("shaarli-title-desktop"));
        if (defaultTitle) {
            return defaultTitle;
        }

        var vintageTitle = document.getElementById("shaarli_title");
        var vintageTitleItem = closestLi(vintageTitle);
        if (vintageTitleItem) {
            return vintageTitleItem;
        }

        var homeLink = findLinkByText(["shaarli home", "home"]);
        return closestLi(homeLink);
    }

    function findShaareItem() {
        var defaultShaare = closestLi(document.getElementById("shaarli-menu-shaare"));
        if (defaultShaare) {
            return defaultShaare;
        }

        var link = findLinkByText(["shaare", "add link"]);
        return closestLi(link);
    }

    function findToolsItem() {
        var defaultTools = document.getElementById("shaarli-menu-tools");
        if (defaultTools) {
            return defaultTools;
        }

        var link = findLinkByText(["tools"]);
        return closestLi(link);
    }

    function findMainList(item) {
        var home = findHomeItem();

        if (home && home.parentNode) {
            return home.parentNode;
        }

        return item && item.closest ? item.closest("ul") : null;
    }

    function findRightList() {
        /*
         * Default theme desktop right action area.
         * Vintage has no dedicated right area; callers will fall back.
         */
        return document.querySelector(".header-buttons .pure-menu-list");
    }

    function findFirstMobileMainItem(mainList) {
        if (!mainList) {
            return null;
        }

        return (
            mainList.querySelector("#shaarli-menu-mobile-rss") ||
            mainList.querySelector(".shaarli-menu-mobile") ||
            mainList.querySelector(".pure-u-lg-0")
        );
    }

    function placeBeforeAnchor(item, anchorItem) {
        if (!anchorItem || !anchorItem.parentNode) {
            return false;
        }

        anchorItem.parentNode.insertBefore(item, anchorItem);
        return true;
    }

    function placeAfterAnchor(item, anchorItem) {
        if (!anchorItem) {
            return false;
        }

        return insertAfter(anchorItem, item);
    }

    function placeMainFirst(item) {
        var mainList = findMainList(item);
        var home = findHomeItem();

        if (!mainList) {
            return false;
        }

        if (home && home.parentNode === mainList) {
            mainList.insertBefore(item, home);
        } else {
            mainList.insertBefore(item, mainList.firstChild);
        }

        return true;
    }

    function placeMainLast(item) {
        var mainList = findMainList(item);

        if (!mainList) {
            return false;
        }

        /*
         * In the default theme, mobile-only RSS/login/logout entries live at
         * the end of the same UL. Insert before them so "main-last" remains
         * the last desktop-visible main item.
         */
        var firstMobile = findFirstMobileMainItem(mainList);

        if (firstMobile) {
            mainList.insertBefore(item, firstMobile);
        } else {
            mainList.appendChild(item);
        }

        return true;
    }

    function placeRightFirst(item) {
        var rightList = findRightList();

        if (!rightList) {
            return false;
        }

        rightList.insertBefore(item, rightList.firstChild);
        return true;
    }

    function placeRightLast(item) {
        var rightList = findRightList();

        if (!rightList) {
            return false;
        }

        rightList.appendChild(item);
        return true;
    }

    function placeCenter(item) {
        var mainList = findMainList(item);

        if (!mainList) {
            return false;
        }

        var toolbar = document.querySelector("#shaarli-menu .pure-menu-horizontal") || mainList.parentNode || mainList;
        toolbar.classList.add("custom-toolbar-link-centered-parent");
        item.classList.add("custom-toolbar-link-centered");

        // Keep it in the main menu so it inherits normal menu item CSS.
        mainList.appendChild(item);
        return true;
    }

    function placeBySemanticPosition(item, position) {
        if (position === "native") {
            return true;
        }

        if (position === "before-home") {
            return placeBeforeAnchor(item, findHomeItem()) || placeMainFirst(item);
        }

        if (position === "after-home") {
            return placeAfterAnchor(item, findHomeItem()) || placeMainFirst(item);
        }

        if (position === "before-shaare") {
            return placeBeforeAnchor(item, findShaareItem()) || placeMainLast(item);
        }

        if (position === "after-shaare") {
            return placeAfterAnchor(item, findShaareItem()) || placeMainLast(item);
        }

        if (position === "before-tools") {
            return placeBeforeAnchor(item, findToolsItem()) || placeMainLast(item);
        }

        if (position === "after-tools") {
            return placeAfterAnchor(item, findToolsItem()) || placeMainLast(item);
        }

        if (position === "main-first") {
            return placeMainFirst(item);
        }

        if (position === "main-last") {
            return placeMainLast(item);
        }

        if (position === "right-first") {
            return placeRightFirst(item) || placeMainLast(item);
        }

        if (position === "right-last") {
            return placeRightLast(item) || placeMainLast(item);
        }

        if (position === "center") {
            return placeCenter(item);
        }

        return placeBeforeAnchor(item, findHomeItem()) || placeMainFirst(item);
    }

    function parseRgb(value) {
        if (!value || value === "transparent") {
            return null;
        }

        var match = value.match(/rgba?\((\d+),\s*(\d+),\s*(\d+)(?:,\s*([0-9.]+))?\)/i);

        if (!match) {
            return null;
        }

        var alpha = match[4] === undefined ? 1 : parseFloat(match[4]);

        if (alpha === 0) {
            return null;
        }

        return {
            r: parseInt(match[1], 10),
            g: parseInt(match[2], 10),
            b: parseInt(match[3], 10)
        };
    }

    function relativeLuminance(rgb) {
        function channel(c) {
            c = c / 255;
            return c <= 0.03928 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4);
        }

        return 0.2126 * channel(rgb.r) + 0.7152 * channel(rgb.g) + 0.0722 * channel(rgb.b);
    }

    function findVisibleBackground(el) {
        var node = el;

        while (node && node !== document.documentElement) {
            var bg = window.getComputedStyle(node).backgroundColor;
            var rgb = parseRgb(bg);

            if (rgb) {
                return rgb;
            }

            node = node.parentElement;
        }

        return { r: 255, g: 255, b: 255 };
    }

    function applyAutoContrast(link) {
        if (!link.classList.contains("custom-toolbar-link-style-auto")) {
            return;
        }

        var bg = findVisibleBackground(link.parentElement || link);
        var lum = relativeLuminance(bg);

        link.classList.remove("custom-toolbar-link-auto-light-bg");
        link.classList.remove("custom-toolbar-link-auto-dark-bg");

        /*
         * Low luminance = dark background, so use light text.
         */
        if (lum < 0.48) {
            link.classList.add("custom-toolbar-link-auto-dark-bg");
        } else {
            link.classList.add("custom-toolbar-link-auto-light-bg");
        }
    }

    ready(function () {
        var link = document.getElementById("custom-toolbar-link");

        if (!link) {
            return;
        }

        var item = closestLi(link);

        if (!item) {
            return;
        }

        item.id = "custom-toolbar-link-item";
        item.classList.add("custom-toolbar-link-item");

        /*
         * Keep default theme styling even when moved into another toolbar zone.
         */
        link.classList.add("pure-menu-link");

        var position = (link.getAttribute("data-custom-toolbar-position") || "before-home").toLowerCase();

        if (!placeBySemanticPosition(item, position)) {
            placeMainFirst(item);
        }

        applyAutoContrast(link);
    });
}());
