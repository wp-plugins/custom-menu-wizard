=== Custom Menu Wizard Widget ===
Contributors: wizzud
Tags: menu,widget,widgets,navigation,nav,custom menus,custom menu,partial menu,menu level,menu branch
Requires at least: 3.0.1
Tested up to: 3.5.1
Stable tag: 1.0.0
License: GPLv2 or Later

Custom Menu Wizard Widget : Show branches or levels of your menu in a widget, with full customisation.

== Description ==

This plugin is a boosted version of the WordPress "Custom Menu" widget. 
It provides full control over most of the parameters available when calling WP's [wp_nav_menu()](http://codex.wordpress.org/Function_Reference/wp_nav_menu) function, as well as providing pre-filtering of the menu items in order to be able to select a specific portion of the custom menu. It also automatically adds a couple of custom classes.

Features include:

* Display the entire menu, just a branch of it, or even just a specific level of it
* Select a branch based on a specific menu item, or the "current" item (currently displayed page)
* Specify a level to start at, and the number of levels to output
* Select hierarchicial or flat output, both options still abiding by the specified number of levels to output
* Allow the widget title to be entered but not output, or even to be set from the parent item of a menu's sub-branch
* Optionally disable widget output completely if there are no matching menu items found
* Include a sub-branch's parent and/or ancestor item(s) in the output, over and above any start level setting
* Automatically add cmw-level-N and cmw-has-submenu classes to output menu items
* Add/specify custom class(es) for the widget block, the menu container, and the menu itself
* Modify the link's output with additional HTML around the link's text and/or the link element itself
* Use Ordered Lists (OL) for the top and/or sub levels instead of Unordered Lists (UL)

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

        The dropdown list will present a "Current Item" option, followed by all the available items from the menu chosen in `Select Menu`.
        The widget will output the *children* of the selected item, always assuming that they lie within the bounds of any other parameters set.
        If you change `Select Menu`, the options presented in this dropdown will change accordingly.

    * **Starting Level** *(select, default: "1")*

        This is the level within the chosen menu (from `Select Menu`) that the widget will start looking for items to keep. Obviously, level 1
        is the root level (ie. those items that have no parent item); level 2 is all the immediate children of the root level items, and so on.

    * **For Depth** *(select, default: "unlimited")*

        This is the maximum depth of the eventual output structure after filtering, and in the case of `Flat` output being requested it is
        still applied - as if the output were `Hierarchical` and then flattened at the very last moment. You need to be aware that the
        `For Depth` setting is applied relative to the level at which the first item to be kept is found. For example, say you were to set
        `Children of` to "Current Item", `Starting Level` to "2", and `For Depth` to "2 levels"; if the current item was found at level 3,
        then you would get the current item's immediate children (from level 4), plus *their* immediate children (from level 5).

* **Output**

    * **Hierarchical** *(radio, default)*

        Output in the standard nested list format.

    * **Flat** *(radio)*

        Output in a single list format, ignoring any parent-child relationship other than to maintain the same physical order as would be
        presented in a `Hierarchical` output.

    * **Include Parent** *(checkbox)*

        If checked, include the parent item in the output. Only applies to a successful `Children of` filter.

    * **Include Ancestors** *(checkbox)*

        Same as `Include Parent` except that all ancestors, right back to root level, are included. Only applies to a successful
        `Children of` filter.

    * **Title from Parent Item** *(checkbox)*

        Again, this only applies to a successful `Children of` filter. If checked, use the title of the parent item as the widget's
        title when displaying the output. This will override (ie. ignore) the `Hide` checkbox setting!

    * **Change UL to OL**

        The standard for menus is to use UL (unordered list) elements to display the output. These settings give you the option to swap
        the ULs out for OLs (ordered lists).
        
        * **Top Level** *(checkbox)*

            If checked, swap the outermost UL for an OL.
            
        * **Sub-Levels** *(checkbox)*

            If checked, swap any nested (ie. not the outermost) ULs for an OLs.

    * **Hide Widget if Empty** *(checkbox)*

        If checked, the widget will not output *any* HTML unless it finds at least one menu item that matches the Filter settings.

* **Container**

    * **Element** *(default: "div")*

        This menu list is usually wrapped in a "container" element, and this is the tag for that element. You may change it for another
        tag, or you may clear it out and the container will be completely removed. Please note that WordPress is set by default to only
        accept "div" or "nav", but that could be changed or extended by any template or plugin.

    * **Unique ID**

        This allows you to specify you own id (which should be unique) for the container.

    * **Class**

        This allows you to add your own class to the container element.

* **Classes**

    * **Menu Class** *(default: "menu-widget")

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

== Installation ==

1. EITHER Upload the zip package via 'Plugins > Add New > Upload' in your WP Admin

    OR Extract the zip package and upload `custom-menu-wizard` folder to the `/wp-content/plugins/` directory

1. Activate the plugin through the 'Plugins' menu in your WP Admin

The widget will now be available in the 'Widgets' admin page. 
As long as you already have at least one Menu defined, you can add the new widget to a sidebar and configure it however you want.

== Frequently Asked Questions ==

If you have a question or problem, please use the integrated Support forum.

== Screenshots ==

1. Widget options (A).

2. Widget options (B).

3. Widget options (C).

== Changelog ==

= 1.0.0 =
Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release
