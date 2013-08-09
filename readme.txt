=== Custom Menu Wizard Widget ===
Contributors: wizzud
Tags: menu,widget,widgets,navigation,nav,custom menus,custom menu,partial menu,menu level,menu branch
Requires at least: 3.0.1
Tested up to: 3.6
Stable tag: 1.2.0
License: GPLv2 or Later

Custom Menu Wizard Widget : Show branches or levels of your menu in a widget, with full customisation.

== Description ==

This plugin is a boosted version of the WordPress "Custom Menu" widget. 
It provides full control over most of the parameters available when calling WP's [wp_nav_menu()](http://codex.wordpress.org/Function_Reference/wp_nav_menu) function, as well as providing pre-filtering of the menu items in order to be able to select a specific portion of the custom menu. It also automatically adds a couple of custom classes. And there is now (v1.2.0) a shortcode that enables you to include the widget's output in your content.

Features include:

* Display the entire menu, just a branch of it, or even just a specific level of it
* Select a branch based on a specific menu item, or the "current" item (currently displayed page)
* Specify a level to start at, and the number of levels to output
* Select hierarchicial or flat output, both options still abiding by the specified number of levels to output
* Allow the widget title to be entered but not output, or even to be set from the parent item of a menu's sub-branch
* Optionally disable widget output completely if there are no matching menu items found (WordPress < v3.6)
* Include a sub-branch's parent and/or ancestor item(s) in the output, over and above any start level setting
* Automatically add cmw-level-N and cmw-has-submenu classes to output menu items
* Add/specify custom class(es) for the widget block, the menu container, and the menu itself
* Modify the link's output with additional HTML around the link's text and/or the link element itself
* Use Ordered Lists (OL) for the top and/or sub levels instead of Unordered Lists (UL)
* *As of v1.1.0* : Select a branch based on the ultimate ancestor (root level) of the "current" item
* *As of v1.2.0* : Shortcode, [custom_menu_wizard], available to run the widget from within content

**Widget Options**

There are quite a few options, which makes the widget settings box very long. I have therefore grouped most of the options into
logical sections and made each section collapsible (with remembered state, open by default).

* **Title**

    Set the title for your widget.

* **Hide** *(checkbox)*

    Prevents the entered `Title` being displayed in the front-end widget output.
    
    In the Widgets admin page, I find it useful to still be able to see the `Title` in the sidebar when the widget is collapsed, but I
    don't necessarily want that `Title` to actually be output when the widget is displayed at the front-end. Hence this checkbox.

* **Select Menu** *(select)*

    Choose the appropriate menu from the dropdown list of Custom Menus currently defined in your WordPress application. The
    first one available (alphabetically) is already selected for you by default.

* **Filter**

    * **Show all** *(radio, default)*
    
        Don't apply the `Children of` filter. If you check this option, the `Children of` select will be disabled, as will the `Include Parent`,
        `Include Ancestors` and `Title from Parent Item` checkboxes, as they are only applicable to a `Children of` filter.
    
    * **Children of** *(radio & select)*

        The dropdown list will present a "Current Item" option (default), a "Current Root Item" (v1.1.0)
        and "Current Parent Item" (v1.1.0) option, followed by all the available items from the menu chosen in `Select Menu`.
        The widget will output the *children* of the selected item, always assuming that they lie within the bounds of any other parameters set.

        * "Current Item" is the menu item that WordPress recognises as being currently on display (current menu item)

        * "Current Parent Item" (v1.1.0) is the *immediate* ancestor (within `Select Menu`) of the current menu item

        * "Current Root Item" (v1.1.0) is the *ultimate* ancestor (within `Select Menu`) of the current menu item.

        Obviously, if the current menu item (as determined by WordPress) does not
        appear in the `Select Menu` then there is going to be no output for any of these options.
        
        If you change `Select Menu`, the options presented in this dropdown will change accordingly and the selected option will revert to the default.

    * **Starting Level** *(select, default: "1")*

        This is the level within the chosen menu (from `Select Menu`) that the widget will start looking for items to keep. Obviously, level 1
        is the root level (ie. those items that have no parent item); level 2 is all the immediate children of the root level items, and so on.
        Note that for a `Children of` filter there is no difference between level 1 and level 2 (because there are no children at level 1).

    * **For Depth** *(select, default: "unlimited")*

        This is the maximum depth of the eventual output structure after filtering, and in the case of `Flat` output being requested it is
        still applied - as if the output were `Hierarchical` and then flattened at the very last moment.
        
        You need to be aware that the
        `For Depth` setting is applied relative to the level at which the first item to be kept is found. For example, say you were to set
        `Children of` to "Current Item", `Starting Level` to "2", and `For Depth` to "2 levels" : if the current item was found at level 3,
        then you would get the current item's immediate children (from level 4), plus *their* immediate children (from level 5).

* **Fallbacks**

    Fallback for `Children of` being set to either *Current Root Item* or *Current Parent Item*, and current item not having an ancestor:

    * **Switch to Current Item** *(checkbox)* as of v1.1.0

        If enabled,
        it provides a fallback of effectively switching the filter to "Current Item" if the current menu item 
        (as determined by WordPress) is at Level 1
        (top level) of the selected menu. For example, say you were to set `Children of` to "Current Parent Item", with `Starting Level`
        at "1" and `For Depth` at "unlimited" : if the current menu item was found at level 1 (root level of the menu) then ordinarily
        there would be no output because the current item has no parent! If you were to enable the `Switch to Current Item` fallback then you
        *would* have some output - the entire branch below the current item.

        * **Include Parent...** *(checkbox)* as of v1.1.0

            This option extends the `Switch to Current Item` option (above). If the enabled fallback is actually used, this option can
            temporarily override the equivalent **Output** option to ON. Note that if the **Output** options are already set to include
            the parent item (with or without siblings), this option has absolutely no effect.

        * **& its Siblings** *(checkbox)* as of v1.1.0

            This option extends the `Switch to Current Item` option (above). If the enabled fallback is actually used, this option can
            temporarily override the equivalent **Output** option to ON. Note that if the equivalent **Output** option is already enabled,
            this option has absolutely no effect.

    Fallback for `Children of` being set to *Current Item*, and current item not having any children:

    * **Switch to Current Parent Item** *(checkbox)* as of v1.2.0

        If enabled, it provides a fallback of effectively switching the filter to "Current Parent Item" if looking for children
        of Current Item and there aren't any. For example, say you were to set `Children of` to "Current Item", with `Starting Level`
        at "1" and `For Depth` at "unlimited" : if the current menu item has no children then ordinarily
        there would be no output! If you were to enable the `Switch to Current Item` fallback then you
        *would* have some output - the current item and its siblings.
        
        Please note that there is one difference between this fallback and the normal "Current Parent Item" filter : if the current item
        has no ancestor (as well as no children) then you will always get the current item and its siblings, regardless of any other settings!

        * **Include Parent...** *(checkbox)* as of v1.2.0

            This option extends the `Switch to Current Parent Item` option (above). If the enabled fallback is actually used, this option can
            temporarily override the equivalent **Output** option to ON. Note that if the **Output** options are already set to include
            the parent item (with or without siblings), this option has absolutely no effect.

        * **& its Siblings** *(checkbox)* as of v1.2.0

            This option extends the `Switch to Current Parent Item` option (above). If the enabled fallback is actually used, this option can
            temporarily override the equivalent **Output** option to ON. Note that if the equivalent **Output** option is already enabled,
            this option has absolutely no effect.

* **Output**

    * **Hierarchical** *(radio, default)*

        Output in the standard nested list format.

    * **Flat** *(radio)*

        Output in a single list format, ignoring any parent-child relationship other than to maintain the same physical order as would be
        presented in a `Hierarchical` output.

    * **Include Parent...** *(checkbox)*

        If checked, include the parent item in the output. Only applies to a successful `Children of` filter.

    * **& its Siblings** *(checkbox)* as of v1.1.0

        If checked, include the parent item **and** its siblings in the output. Only applies to a successful `Children of` filter.

    * **Include Ancestors** *(checkbox)*

        Same as `Include Parent` except that all ancestors, right back to root level, are included. Only applies to a successful
        `Children of` filter.

    * **Title from Parent** *(checkbox)*

        Again, this only applies to a successful `Children of` filter. If checked, use the title of the parent item as the widget's
        title when displaying the output. This will override (ie. ignore) the `Hide` checkbox setting!

    * **Title from "Current" Item** *(checkbox)*

        If checked, use the title of the current menu item (as determined by WordPress) as the widget's
        title when displaying the output. This will override (ie. ignore) the `Hide` checkbox setting!
        
        Note that the current menu item is not required to be within the resultant output, merely within the `Select Menu`.
        Also, `Title from Parent` (if applicable, and if available) takes priority over this option.

    * **Change UL to OL**

        The standard for menus is to use UL (unordered list) elements to display the output. These settings give you the option to swap
        the ULs out for OLs (ordered lists).
        
        * **Top Level** *(checkbox)*

            If checked, swap the outermost UL for an OL.
            
        * **Sub-Levels** *(checkbox)*

            If checked, swap any nested (ie. not the outermost) ULs for an OLs.

    * **Hide Widget if Empty** *(checkbox)*

        If checked, the widget will not output *any* HTML unless it finds at least one menu item that matches the Filter settings.
        
        Please note that as of WordPress v3.6, this option becomes superfluous and will **not** be presented (the wp_nav_menu() function
        has been modified to automatically suppress all HTML output if there are no items to be displayed). The widget will retain
        the setting used on earlier WP versions (in case reversion from WP v3.6 might be required) but *will not present the option
        for WP v3.6+*.

* **Container**

    * **Element** *(default: "div")*

        This menu list is usually wrapped in a "container" element, and this is the tag for that element. You may change it for another
        tag, or you may clear it out and the container will be completely removed. Please note that WordPress is set by default to only
        accept "div" or "nav", but that could be changed or extended by any template or plugin.

    * **Unique ID**

        This allows you to specify your own id (which should be unique) for the container.

    * **Class**

        This allows you to add your own class to the container element.

* **Classes**

    * **Menu Class** *(default: "menu-widget")*

        This is the class that will be applied to the list element that holds the entire menu.
        
    * **Widget Class**

        This allows you to add your own class to the outermost element of the widget, the one that wraps the entire widget output.

* **Links**

    * **Before the Link**

        Text or HTML that will be placed immediately before each menu item's link.

    * **After the Link**

        Text or HTML that will be placed immediately after each menu item's link.

    * **Before the Link Text**

        Text or HTML that will be placed immediately before each menu item's link text.

    * **After the Link Text**

        Text or HTML that will be placed immediately after each menu item's link text.

**Shortcode**

The shortcode is **`[custom_menu_wizard]`**. Most of the attributes reflect the options available to the widget, but some have been simplified for 
easier use in the shortcode format.
Please note that the `Hide Widget if Empty` option is not available to the shortcode : it is set to enabled, and if there are no menu items
found then there will be no output from the shortcode.

* **title** *(string)*

    The output's `Title`, which may be overridden by `title_from`. Note that there is no shortcode equivalent of the widget's `Hide` option for the title.

* **menu** *(string | integer)*

    Accepts a menu name (most likely usage) or id. If not provided, the shortcode will attempt to find the first menu (ordered by name) 
    that has menu items attached to it, and use that.

* **children_of** *(string | integer)*

    If empty, or not provided, this is the same as enabling `Show All` (see above) for the widget. Anything else is a `Children of` filter :
  
    * If numeric, it is taken as being the id of a menu item. The widget will look for the `Children of` that menu item (within `menu`).
      (Hint : In Menus Admin, hover over the item's **Remove** link and note the number after *menu-item=* in the URL)
  
    * Certain specific strings have the following meanings:
  
        * *'current'* or *'current-item'* : a `Children of` "Current Item" filter

        * *'parent'* or *'current-parent'* : a `Children of` "Current Parent Item" filter

        * *'root'* or *'current-ancestor'* : a `Children of` "Current Root Item" filter

    * If any other string, it is taken to be the title of a menu item. The widget will look for the `Children of` that menu item
      (within `menu`). Please note that the code looks for a *caseless* title match, so specifying `children_of="my menu item"` will
      match against a menu item with the title "My Menu Item".

* **fallback_parent** *(string | integer)*

    This is the fallback option for when `Children of` is set to either *Current Root Item* or *Current Parent Item*, and
    the current item has no ancestors (see `Switch to Current Item` under **Fallbacks** above).

    * Any "truthy" value (eg. 1, *'true'*, *'on'*, *'parent'*, *'siblings'*) : Enables widget's `Switch to Current Item` **Fallbacks** option
    
    * *'parent'* : Enables widget's `Include Parent...` **Fallbacks** extension option (in addition to the above)

    * *'siblings'* : Enables widget's `& its Siblings` **Fallbacks** extension option (in addition to the above)

* **fallback_current** *(string | integer)*

    This is the fallback option for when `Children of` is set to *Current Item*, and
    the current item has no children (see `Switch to Current Parent Item` under **Fallbacks** above).

    * Any "truthy" value (eg. 1, *'true'*, *'on'*, *'parent'*, *'siblings'*) : Enables widget's `Switch to Current Parent Item` **Fallbacks** option
    
    * *'parent'* : Enables widget's `Include Parent...` **Fallbacks** extension option (in addition to the above)

    * *'siblings'* : Enables widget's `& its Siblings` **Fallbacks** extension option (in addition to the above)

* **start_level** *(integer, default 1)* See widget's `Starting Level` option, under **Filter** above.

* **depth** *(integer, default 0)* See widget's `For Depth` option, under **Filter** above.
    
* **flat_output** *(switch, off by default, 1 to enable)* See widget's `Flat` option, under **Output** above.

* **include** *(string)*

    * *'parent'* : Enables widget's `Include Parent...` **Output** option

    * *'siblings'* : Enables widget's `& its Siblings` **Output** option

    * *'ancestors'* : Enables widget's `Include Ancestors` **Output** option
    
    Supply more than one by separating them with a comma, space or hyphen, eg. `include="siblings ancestors"`.

* **title_from** *(string)*

    * *'parent'* : Enables widget's `Title from Parent` **Output** option

    * *'current'* : Enables widget's `Title from "Current" Item` **Output** option

    Supply more than one by separating them with a comma, space or hyphen, eg. `title_from="parent,current"`.

* **ol_root** *(switch, off by default, 1 to enable)* See widget's `Top Level` option, under **Output** above.

* **ol_sub** *(switch, off by default, 1 to enable)* See widget's `Sub-Levels` option, under **Output** above.

* **container** *(string)* See widget's `Element` option, under **Container** above.

* **container_id** *(string)* See widget's `Unique ID` option, under **Container** above.

* **container_class** *(string)* See widget's `Class` option, under **Container** above.

* **menu_class** *(string)* See widget's `Menu Class` option, under **Classes** above.

* **widget_class** *(string)* See widget's `Widget Class` option, under **Classes** above.

* **wrap_link** *(string)*

    This is an optional tag name (eg. *'div'*, *'p'*, *'span*') that, if provided, will be made into HTML start/end tags
    and sent through to the widget as its `Before the Link` and `After the Link` options. Please note that the shortcode usage - a simple
    tag name - is much more restrictive than the widget's options, which allow HTML.

* **wrap_link_text** *(string)*

    This is an optional tag name (eg. *'span*', *'em'*, '*strong*') that, if provided, will be made into HTML start/end tags
    and sent through to the widget as its `Before the Link Text` and `After the Link Text` options. Please note that the shortcode usage - a
    simple tag name - is much more restrictive than the widget's options, which allow HTML.

**Shortcode Examples**

* Show the entire "main" menu :

    `[custom_menu_wizard menu=main]`

* Show the children of the Current Item within the "main" menu, for unlimited depth, and include the Current Item's parent :

    `[custom_menu_wizard menu=main children_of=current include_parent=1]`

* From the "animals" menu, show all the items *immediately* below (depth=1) "Small Dogs", plus "Small Dogs" and its sibling items, as ordered lists :

    `[custom_menu_wizard menu="animals" children_of="small dogs" depth=1 include_parent_siblings=1 ol_root=1 ol_sub=1]`

== Installation ==

1. EITHER Upload the zip package via 'Plugins > Add New > Upload' in your WP Admin

    OR Extract the zip package and upload `custom-menu-wizard` folder to the `/wp-content/plugins/` directory

1. Activate the plugin through the 'Plugins' menu in your WP Admin

The widget will now be available in the 'Widgets' admin page. 
As long as you already have at least one Menu defined, you can add the new widget to a sidebar and configure it however you want.

== Frequently Asked Questions ==

If you have a question or problem, please use the integrated Support forum.

== Screenshots ==

1. Widget options

2. More widget options

3. Even more widget options

4. Demo / Helper

== Changelog ==

= 1.2.0 =

* added custom_menu_wizard shortcode, to run the widget from within content

* moved the 'no ancestor' fallback into new Fallback collapsible section, and added a fallback for Current Item with no children

* added an option allowing setting of title from current menu item's title

* fixed a bug with optgroups/options made available for the 'Children of' selector after the widget has been saved (also affected disabled fields and styling)

* don't include menus with no items

* updated demo.html

= 1.1.0 =

* added 'Current Root Item' and 'Current Parent Item' to the `Children of` filter

* added `Fallback to Current Item` option, with subsibiary options for overriding a couple of Output options, as a means to enable Current Root & Current Parent to match a Current Item at root level

* added an Output option to include both the parent item **and** the parent's siblings (for a successful `Children of` filter)

* added max-width style (100%) to the `Children of` SELECT in the widget options

* added widget version to the admin js enqueuer

* ignore/disable Hide Empty option for WP >= v3.6 (wp_nav_menu() now does it automatically)

* included a stand-alone helper/demo html page

* rebuilt the `Children of` SELECT in the widget options to cope with IE's lack of OPTGROUP/OPTION styling

* moved the setting of 'disabled' attributes on INPUTs/SELECTs from PHP into javascript

= 1.0.0 =

Initial release

== Upgrade Notice ==

= 1.2.0 =

Added custom_menu_wizard shortcode, to run the widget from within content. Also added a new fallback for Current Item having no children, and
moved all fallbacks into a collapsible Fallbacks section. Fixed a bug with optgroups/options made available for the 'Children of' selector 
after the widget has been saved (also affected disabled fields and styling).

= 1.1.0 =

`Children of` has 2 extra options, for filtering using the Current Item's parent (immediate ancestor) or root (ultimate ancestor) item, 
with fallbacks for when a Current Item has no ancestor. There is also an additional Output option which extends Include Parent to also 
include the parent's siblings. An interactive helper/demo page is now available, which will hopefully assist in deciding which options 
need setting for particular requirements. Other changes include tweaks to the management of the `Children of` SELECT (to allow for IE), 
a few style enhancements to the presentation of the widget in admin, and removal of the `Hide Widget if Empty` option for WP v3.6 (still 
presented for WP < 3.6 though!).
