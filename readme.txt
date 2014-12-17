=== Custom Menu Wizard Widget ===
Contributors: wizzud
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=KP2LVCBXNCEB4
Tags: menu,widget,navigation,custom menu,partial menu,current item,current page,menu level,menu branch,menu shortcode,menu widget,advanced,enhanced
Requires at least: 3.6
Tested up to: 4.1
Stable tag: 3.1.3
License: GPLv2 or Later

Show branches or levels of your menu in a widget, or in content using a shortcode, with full customisation.

== Description ==

This plugin is a boosted version of the WordPress "Custom Menu" widget. 
It provides full control over most of the parameters available when calling WP's [wp_nav_menu()](http://codex.wordpress.org/Function_Reference/wp_nav_menu) function, as well as providing pre-filtering of the menu items in order to be able to select a specific portion of the custom menu. It also automatically adds a couple of custom classes. And there's a shortcode that enables you to include the widget's output in your content.

Features include:

* Display an entire menu, just a branch of it, just certain level(s) of it, or even just specific items from it!
* Select a branch based on a specific menu item, or the current menu item (currently displayed page)
* Specify a relative or absolute level to start at, and the number of levels to output
* Include ancestor item(s) in the output, with or without siblings
* Exclude certain menu items, or levels of items
* Make the output conditional upon the current menu item being found in different stages of the filter selection process
* Automatically add cmw-level-N and cmw-has-submenu classes to output menu items
* Allow the widget title to be entered but not output, or to be set from the current menu item or selected branch item
* Select hierarchical or flat output, both options still abiding by the specified number of levels to output
* Add/specify custom class(es) for the widget block, the menu container, and the menu itself
* Modify the link's output with additional HTML around the link's text and/or the link element itself
* Use Ordered Lists (OL) for the top and/or sub levels instead of Unordered Lists (UL)
* Shortcode, `[cmwizard]`, available to run the widget from within content
* Interactive "assist" to help with the widget settings and/or shortcode definition
* Utility to find posts containing this plugin's shortcode
* **NEW** Specify an alternative configuration to use under certain conditions (dual-scenario capability)

Current documentation for the **Widget Options** can be found 
under [Other Notes](http://wordpress.org/plugins/custom-menu-wizard/other_notes/). 
The associated **Shortcode Parameters** are documented 
under [Installation](http://wordpress.org/plugins/custom-menu-wizard/installation/) (too much text to fit all on one page!).

**Please, do not be put off by the number of options available!** I suspect (and I admit that I'm guessing!) that for the majority of users 
there are probably a couple of very common scenarios:

1. Show an entire menu...
    * Drag a new Custom Menu Wizard widget into the sidebar, and give it a title (if you want one)
    * Select the menu you wish to use (if it's not already selected)
    * Save the widget!
    * *Equivalent shortcode resembles `[cmwizard menu=N title="Your Title"/]`*

2. Show the current menu item, plus any descendants...
    * Drag a new Custom Menu Wizard widget into the sidebar, and give it a title (if you want one)
    * Select the menu you wish to use (if it's not already selected)
    * Open the FILTERS section :
        * under Primary Filter, click on the *Branch* radio
    * Save the widget!
    * *Equivalent shortcode resembles `[cmwizard menu=N title="Your Title" branch=current/]`*

3. Show just the descendants of the current menu item (if there are any)...
    * Drag a new Custom Menu Wizard widget into the sidebar, and give it a title (if you want one)
    * Select the menu you wish to use (if it's not already selected)
    * Open the FILTERS section :
        * under Primary Filter, click on the *Branch* radio
        * under Secondary Filter, set *Starting at* to "+1 (children)"
    * Save the widget!
    * *Equivalent shortcode resembles `[cmwizard menu=N title="Your Title" branch=current start_at="+1"/]`*

4. Always show the top level items, but when the menu contains the current item then also show that current item, with its ancestors and immediate children...
    * Drag a new Custom Menu Wizard widget into the sidebar, and give it a title (if you want one)
    * Select the menu you wish to use (if it's not already selected)
    * Open the FILTERS section :
        * under Primary Filter, click on the *Branch* radio
        * under Secondary Filter, set *Depth* to "2 levels" (ie. current item plus immediate children)
        * under Inclusions, set *Branch Ancestors* to "to level 1 (root)", and set *Level* to "1"
    * Open the ALTERNATIVE section :
        * set *On condition* to "Current Item is NOT in..." and "Menu" (the 2nd dropdown)
        * in the *Then switch settings to* textarea, type in "[cmwizard depth=1/]" (without the quotes!)
    * Save the widget!
    * *Equivalent shortcode resembles `[cmwizard menu=N branch=current depth=2 ancestors=1 include_level="1" alternative="no-current,menu"]depth=1[/cmwizard]`*


If you like this widget (or if you don't?), please consider taking a moment or two to give it a 
[Review](http://wordpress.org/support/view/plugin-reviews/custom-menu-wizard) : it helps others, and gives me valuable feedback.

*Documentation for version 2 of the widget 
can be found [here](http://plugins.svn.wordpress.org/custom-menu-wizard/tags/2.0.6/readme.txt)
or [here](http://www.wizzud.com/v210-readme.html).*

== WIDGET OPTIONS ==

*NB. Version 2 documentation is [here](http://plugins.svn.wordpress.org/custom-menu-wizard/tags/3.0.0/v210-readme.html#Widget-Options).*

There are quite a few options, which makes the widget settings box very long. I have therefore grouped most of the options into
collapsible logical sections (with remembered state once saved).

The associated **SHORTCODE PARAMETERS** are documented 
under [Installation](http://wordpress.org/plugins/custom-menu-wizard/installation/).

***Always Visible***

* **Title** *textbox*

    Set the title for your widget.

* **Hide** *checkbox*

    Prevents the entered `Title` being displayed in the front-end widget output.

    In the Widgets admin page, I find it useful to still be able to see the `Title` in the sidebar when the widget is closed, but I
    don't necessarily want that `Title` to actually be output when the widget is displayed at the front-end. Hence this checkbox.

* **Select Menu** *select*

    Choose the appropriate menu from the dropdown list of Custom Menus currently defined in your WordPress application. The
    first one available (alphabetically) is already selected for you by default.

== Filters Section ==

Filters are applied in the order they are presented.

***Primary Filter***

* **Level** *radio (default On) & select*

    Filters by level within the selected menu, starting at the level selected here. This is the default setting
    for a new widget instance, which, if left alone and with all other options at their default, will show the entire selected menu.

    Example : If you wanted to show all the options that were at level 3 or below, you could check this radio and set the select to "3".

* **Branch** *radio & select*

    Filters by branch, with the head item of the branch being selected from the dropdown. The dropdown presents all the
    items from the selected menu, plus a "Current Item" option (the default). Selecting "Current Item" means that the head item of the
    branch is the current menu item (as indicated by WordPress), provided, of course, that the current menu item actually corresponds to
    a menu item from the currently selected menu!

* **Items** *radio & textbox*

    Filters by the menu items that you specifically pick (by menu item id, as a comma-separated list). The simplest way
    to get your list of ids is to use the "assist", and [un]check the green tick box at the right hand side of each depicted menu item that
    you want. Alternatively, just type your list of ids into the box.
    
    If the id is appended with a '+', eg. '23+', then all the item's descendants will also be included.

    Example : If you only wanted to show, say, 5 of your many available menu items, and those 5 items are not in one handy branch of the menu,
    then you might want to use this option.
    
    Example : If your menu has 6 root branches - "A" thru to "F" - and you were only interested in branches "B" (id of, say, 11) and 
    "E" (id of, say, 19), you could set `Items` to be "11+,19+", which would include "B" with all its descendants, and "E" with all its
    descendants.

***Secondary Filter*** *(not applicable to an `Items` filter)*

* **Starting at** *select*

    This is only applicable to a `Branch` filter and it allows you to shift the starting point of your output within the confines
    of the selected branch. By default it is set to the selected branch item itself, but it can be changed to a relative of the branch item (eg.
    parent, grandparent, children, etc) or to an absolute, fixed level within the branch containing the selected branch item (eg. the root
    level item for the branch, or one level below the branch's root item, etc).

    Example : If you wanted the entire "current" branch then, with `Branch` set to "Current Item", you might set `Starting at` to "1 (root)".
    Alternatively, if you wanted the children of the current menu item then `Starting at` could be set to "+1 (children)".

* **Item, if possible** *radio (default On)*

    This is the default filter mechanism whereby, if `Starting at` can only result in a single item (ie. it is the branch item itself, or
    an ancestor thereof) then only that item and its descendants are considered for filtering.

* **Level** *radio*

    Changes the default filter mechanism such that if `Starting at` results in the selection of the branch item or one of its ancestors,
    then all siblings of that resultant item are also included in the secondary filtering process.
    
    Example : If Joe and Fred are siblings (ie. they have a common parent) and Joe is the selected branch item - with `Starting at` set
    to Joe - then the secondary filter would normally only consider Joe and its descendants. However, if `Level` was enabled instead of
    `Item`, then both Joe and Fred, *and all their descendants*, would be considered for filtering.
    
    Note that there is one exception, and that is that if `Starting at` results in a root-level item, then `Allow all Root Items` must
    be enabled in order to allow the other sibling root items to be added into the filter process.

* **Allow all Root Items** *checkbox*

    In the right conditions - see `Level` above - this allows sibling root items to be considered for secondary filtering.

* **For Depth** *select*

    This the number of levels of the menu structure that will be considered for inclusion in the final output (in complete
    ignorance of any subsequent Inclusions or Exclusions).
    
    The first level of output is the starting level, regardless of
    how that starting level is determined (see `Starting at` and `Relative to Current Item` options). So if you ask
    for a Depth of 1 level, you get just the starting level; if you ask for a Depth of 2, you get the starting level and
    the one below it.

* **Relative to Current Item** *checkbox*

    By default, `For Depth` (above) is relative to the first item found, but this may be overridden to be relative to the
    current menu item ***if***  `For Depth` is not unlimited **and** the current menu item can found within the selected menu.
    If the current menu item is not within the selected menu then it falls back to being relative to the first item found.
    
    Please note that the current item must also be within the constraints set by the `Starting at` option. In other words, if
    current item is *above* the `Starting at` level in the menu structure then it will **not** be used to alter the determination of
    Depth.

***Inclusions***

These allow certain other items to be added to the output from the secondary filters.

The first 3 are only applicable to a `Branch` filter. Please note that they only come into effect when the `Branch` filter item is at 
or below the `Starting at` level, and do not include any items that would break the depth limit set in the Secondary Filter options.

* **Branch Ancestors** *select*

    Include any ancestors (parent, grandparent, etc) of the items selected as the `Branch` filter.
    Ancestors can be set to go up to an absolute level, or to go up a certain number of levels relative to the `Branch` filter item.

* **... with Siblings** *select*

    In conjunction with `Branch Ancestors`, also include all siblings of those ancestors.
    As with Ancestors, their siblings can be set to go up to an absolute level, or to go up a certain number of levels relative 
    to the `Branch` filter item. Note that while it is possibe to set a larger range for siblings than ancestors, the final output
    is limited by `Branch Ancestors` setting.
    
* **Branch Siblings** *checkbox*

    Include any siblings of the item selected as the `Branch` filter (ie. any items at the same level and within
    the same branch as the `Branch` item).

* **Level** *select*

    This allows an entire level of items to be included, optionally also including all levels either above or below it.
    This replaces the `All Root Items` checkbox (pre v3.0.4), which only allowed for the inclusion of the root level items.

***Exclusions***

* **Item Ids** *textbox*

    This is a comma-separated list of the ids of menu items that you do *not* want to appear in the final output. 
    The simplest way to get your list of ids is to use the "assist", and [un]check 
    the red cross box at the left hand side of each depicted menu item. Alternatively, just type your list of ids into the box.

    If the id is appended with a '+', eg. '23+', then all the item's descendants will also be excluded.

    Example : If you wanted to show the entire "A" branch, with the sole exception of one grandchild of "A", say "ABC", then you could
    set `Branch` to "A", and `Exclusions` to the id of the "ABC" item.
    
    Example : If you have a menu with 4 root items - "A", "B", "C" & "D" - and you wanted to show all items, with descendants, for all bar
    the "C" branch, then you could set `Level` to "1 (root)" and `Exclusions` to, say, "12+", where "12" is the menu item id for "C" and
    the "+" indicates that all the descendants of "C" should also be excluded.

* **Level** *select*

    This allows an entire level of items to be excluded, optionally also excluding all levels either above or below it.

***Qualifier***

* **Current Item is in** *select*

    This allows you to specify that there only be any output shown when/if the current menu item is one of the menu items selected
    for output at a particular stage in the filter processing.

    * *"Menu"* : the current menu item has to be somewhere within the selected menu.
    * *"Primary Filter"* : the current menu item has to be within the scope of the selected primary filter. So if you selected, say, a child
    of "A" as the `Branch` item, then if "A" was the current menu item there would be no output with this qualifier.
    * *"Secondary Filter"* : the current menu item has to be within the items as restricted by the secondary filters. So if you 
    selected `Branch` as "A", with `Starting at` set to "+1 (children)", then if "A" was the current menu item there would be no output with this qualifier.
    * *"Inclusions"* : the current menu item has to be in within the items as set by the primary and secondary filters, and the inclusions.
    * *"Final Output"* : the current menu item has to be in the final output.

== Fallbacks Section ==

The **"If Current Item has no children"** fallback gets applied at the Secondary Filter stage, and its eligibility and 
application are therefore determined and governed by the other Secondary Filter settings.

It only comes into play (possibly) when a `Branch` filter 
is set as "Current Item", and the `Starting at` and `For Depth` settings are such that the output should start at or below the current item,
and would normally include some of the current item's descendants (eg. `Starting at` "the Branch", `For Depth` "1 level" does *not* invoke 
the fallback). 
The fallback allows for the occasion when the current menu item
*does not have* any immediate children, and provides the ability to then switch the following options:

* **Starting at** *select*

    Enable the fallback, and change the `Starting at` from "+1 (children)" to either

    * *"-1 (parent)"* : the immediate parent of the current menu item.
    * *"the Current Item"* : the current menu item itself.

* **...and Include its Siblings** *checkbox*

    This will add in the siblings of the item selected above.
    
    Note : This *only* adds the siblings, not the siblings' descendants! However, if the `Level` radio (in Secondary Filter stage above) is
    set, then all the item's siblings *and their descendants* will automatically be included, and [un]setting this option will have no effect.
    Also note that if the fallback results in a root-level item being selected as the new `Starting at` item, then the inclusion of siblings
    outside the current branch depends on the setting of the `Allow all Root Items` checkbox.

* **For Depth** *select*

    Override the current `For Depth` setting. Note that any depth value set here will be relative to the current item, regardless
    of the current setting of `...Relative to`!
    
    As an example, this option may be useful in the following scenario : item A has 2 children, B and C; B is the current menu item but has
    no children, whereas C has loads of children/grandchildren. If you fallback to B's parent - A - with Unlimited depth set, then you will
    get A, B, C, and *all* C's dependents! You may well want to override depth to limit the output to, say, just A, B and C, by setting this
    fallback option to "1"? Or maybe A, B, C, and C's immediate children, by setting "2"?

The **"If no Current Item can be found"** fallback is new to v3.1.0. It gets applied right at the start of processing, when determining 
which of the menu items (if any) should be regarded as the unique "Current Item" by this widget. Under certain conditions, WordPress 
will mark an item as being the parent of a current item ... but there won't actually be a current item marked! This occurs, for example, 
when displaying a full post for which there is no specific related menu item, yet there *is* a menu item for a Category that the 
displayed post belongs to : WordPress will then mark the related Category as being the parent of the current item (the post) even though 
it can't mark the post as being the current item (because there's no specific item for it within the menu).

Enabling this fallback will make the widget look for these situations - only as a last resort! - and set (one of) the found "parent" item(s) 
as the Current Item.

== Output Section ==

* **Hierarchical** *radio (default On)*

    Output in the standard nested list format. The alternative is `Flat`.

* **Flat** *radio*

    Output in a single list format, ignoring any parent-child relationship other than to maintain the same physical order as would be
    presented in a `Hierarchical` output (which is the alternative and default).

* **Set Title from** *checkboxes*

    These allow you to set the `Title` option from a menu item, and, if brought into play, the `Hide` flag is ignored.
    Note that the item providing the `Title` only has to be within the selected menu; it does not have to be present in the final output!
    Note also that priority is the order presented (first found, first used).

    * **Current Item** : sets `Title` from the current menu item (if current menu item is in the selected menu).
    * **...or its Root** : sets `Title` from the root menu item of the branch containing the current menu item (if current menu item is in the selected menu).
    * **Branch** : only applicable to a `Branch` filter, and sets `Title` from the `Branch` item.
    * **...or its Root** : only applicable to a `Branch` filter, and sets `Title` from the branch's root menu item.

* **Change UL to OL** *checkboxes*

    The standard for menus is to use UL (unordered list) elements to display the output. These settings give you the option to 
    swap the ULs out for OLs (ordered lists).

    * **Top Level** : swap the outermost UL for an OL.
    * **Sub-Levels** : swap any nested (ie. not the outermost) ULs for an OLs.

* **Hide Widget if Empty** *checkbox*

    If checked, the widget will not output *any* HTML unless it finds at least one menu item that matches the Filter settings.

    Please note that as of WordPress v3.6, this option becomes superfluous and will **not** be presented (the wp_nav_menu() function
    has been modified to automatically suppress all HTML output if there are no items to be displayed). The widget will retain
    the setting used on earlier WP versions (in case reversion from WP v3.6 might be required) but *will not present the option
    for WP v3.6+*.

== Container Section ==

* **Element** *textbox (default "div")*

    The menu list is usually wrapped in a "container" element, and this is the tag for that element. 
    You may change it for another tag, or you may clear it out and the container will be completely removed. Please note that 
    WordPress is set by default to only accept "div" or "nav", but that could be changed or extended by any theme or plugin.

* **Unique ID** *textbox*

    This allows you to specify your own id (which should be unique) for the container.

* **Class** *textbox*

    This allows you to add your own class to the container element.

== Classes Section ==

* **Menu Class** *textbox (default "menu-widget")*

    This is the class that will be applied to the list element that holds the entire menu.

* **Widget Class** *textbox*

    This allows you to add your own class to the outermost element of the widget, the one that wraps the entire widget output.

== Links Section ==

* **Before the Link** *textbox*

    Text or HTML that will be placed immediately before each menu item's link.

* **After the Link** *textbox*

    Text or HTML that will be placed immediately after each menu item's link.

* **Before the Link Text** *textbox*

    Text or HTML that will be placed immediately before each menu item's link text.

* **After the Link Text** *textbox*

    Text or HTML that will be placed immediately after each menu item's link text.

== Alternative Section ==

This is new at v3.1.0 and provides a limited dual-scenario capability, based on a couple of conditions. For example, let's say you 
want to show the Current Item and its immediate children, *but* if there isn't a Current Item then you want to show the top 2 levels 
of the menu : previously this was not possible solely with CMW, but now you can configure the main widget settings for the "current item"
scenario, and add an Alternative setting for when no Current Item can be determined.

* **On condition** *2 selects*

    Select the appropriate condition for when your Alternative configuration should be used, and also the stage within the 
    Filter processing when this condition should be tested for (similar to the Qualifier, `Current Item is in`). You need 
    values in both selects for the Alternative to be considered.

* **Then switch settings to** *textarea*

    This should contain a CMW-generated shortcode equivalent of the configuration that you want to switch to. Please note that leaving 
    this empty will **not** prevent the Alternative kicking in if the conditions are set and met! An empty `switch to` will merely default 
    to the CMW's base settings (Level 1, unlimited Depth). Also note that Alternatives cannot be nested : a primary configuration is
    allowed one chance to switch and that's it, so providing an Alternative-that-has-an-Alternative will not work.
    
    The Assist *will work* with an Alternative - in that it displays the appropriate output - but it can get confusing as to which 
    configuration set is being used. There is a message displayed whenever the Alternative kicks in (green if successful, red if it 
    should have kicked in but couldn't due to an error in the alternative settings) so please take note of it, because the Assist 
    *cannot* be used to modify the Alternative settings, only the primary ones.

A bit more information about the **Alternative** is available 
in [this article](http://www.wizzud.com/2014/10/03/custom-menu-wizard-wordpress-plugin-version-3-1/).

== Installation ==

1. EITHER Upload the zip package via 'Plugins > Add New > Upload' in your WP Admin

    OR Extract the zip package and upload `custom-menu-wizard` folder to the `/wp-content/plugins/` directory

1. Activate the plugin through the 'Plugins' menu in your WP Admin

The widget will now be available in the 'Widgets' admin page. 
As long as you already have at least one Custom Menu defined, you can add the new widget to a sidebar and configure it however you want. 
Alternatively, you can use the shortcode in your content.

Current documentation for the **Widget Options** can be found 
under [Other Notes](http://wordpress.org/plugins/custom-menu-wizard/other_notes/). 

= SHORTCODE PARAMETERS =

*NB. Version 2 documentation is [here](http://plugins.svn.wordpress.org/custom-menu-wizard/tags/3.0.0/v210-readme.html#Shortcode-Parameters).*

The shortcode is **`[cmwizard]`** (prior to v3, shortcode was *`[custom_menu_wizard]`*, and it is still supported but with a slightly
different parameter set).
Most of the attributes reflect the options available to the widget, but some have been simplified for easier use in the shortcode format.
Please note that the `Hide Widget if Empty` option is not available to the shortcode : it is set to enabled, and if there are no menu items 
found then there will be no output from the shortcode.

The simplest way to build a shortcode is to use a widget : as you set options, the equivalent shortcode is displayed at the base of
the widget (v3+) and the base of the "assist". The widget itself need not be assigned to a widget area, so you can construct your
shortcode using a widget in the Inactive Widgets area if you have no need for an active one. Please remember that any options you play
with while constructing your shortcode ***do not have to be Saved*** to the widget itself! Just copy-paste the shortcode when
you are happy with it.

= title =
*string* : The output's `Title`, which may be overridden by **title_from**. Note that there is no shortcode equivalent of the widget's `Hide` option for the title.

= menu =
*string or integer* : Accepts a menu name or id. If not provided, the shortcode will attempt to find the first menu (alphabetically) 
that has menu items attached to it, and use that.

= level =
*integer* : Sets the `Level` filter to the specified (greater than zero) value. Defaults to 1, and is ignored if either **branch** or **items** is specified.

= branch =
*string or integer* : If not empty then `Branch` is set as the primary filter, with the branch item being set from the assigned value:

* If numeric, it is taken as being the id of a menu item.
* If set to either *"current"* or *"current-item"* then the `Branch` item is set to "Current Item".
* If any other string, it is taken to be the title of a menu item (within the selected menu). The widget will look for the first *caseless* title match, so specifying `branch="my menu item"` will match against a menu item with the title "My Menu Item".

= items =
*string* : Comma-separated list of meu item ids, where an id can optionally be followed by a '+' to include all its descendants (eg. "23+"). Takes priority over **branch**.

= start_at =
*string* : This is only relevant to a `Branch` filter, and consists of a signed or unsigned integer that indicates either a relative 
(to the selected branch item) or absolute level to start your output at (ref. the widget's `Starting at` option under *Secondary Filter*, 
[Filters Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Filters-Section)). 
By default the starting level for output is the branch item's level. A relative level is indicated by a signed (ie. preceded by 
a "+" or "-") integer, eg. `start_at="+1"`, while an absolute level is unsigned, eg. `start_at="1"`. Some examples :

* `start_at="+1"` : (relative) start at the branch item's level + 1 (also accepts `start_at="children"`)
* `start_at="-1"` : (relative) start at the branch item's level - 1 (also accepts `start_at="parent"`)
* `start_at="-2"` : (relative) would be the "grandparent" level 
* `start_at="1"` : (absolute) start at the root item of the selected branch (also accepts `start_at="root"`)
* `start_at="2"` : (absolute) start at one level below root (still within the selected branch)

= start_mode =
*string* : This has only one accepted value - "level" - and is only applicable for a `Branch` filter whose **start_at** setting returns
in an item is at or above the selected branch item (relatively or absolutely). 
Setting `start_mode="level"` forces the widget to use not only the resultant starting item 
and its relevant descendants, but also all that item's siblings *and their descendants* 
(ref. the widget's `Level` radio option under *Secondary Filter*, 
[Filters Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Filters-Section)).

= allow_all_root =
*switch, off by default, 1 to enable* : See widget's `Allow all Root Items` option, under *Secondary Filter*,
[Filters Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Filters-Section).

= depth =
*integer, default 0 (unlimited)* : See widget's `For Depth` option, under *Secondary Filter*,
[Filters Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Filters-Section).

= depth_rel_current =
*switch, off by default, 1 to enable* : See widget's `Relative to Current Item` option, under *Secondary Filter*,
[Filters Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Filters-Section).
    
= ancestors =
*integer, default 0 (off)* : Sets an absolute level (positive integer), or a relative number of levels (negative integer), for which
the ancestors of the `Branch` filter item should be included. See widget's `Branch Ancestors` option, under *Inclusions*, 
[Filters Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Filters-Section). (only relevant to a `Branch` filter)

= ancestor_siblings =
*integer, default 0 (off)* : Sets an absolute level (positive integer), or a relative number of levels (negative integer), for which
the siblings of ancestors of the `Branch` filter item should be included. See widget's `... with Siblings` option, under *Inclusions*, 
[Filters Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Filters-Section). (only relevant to a `Branch` filter)

= siblings =
*switch, off by default, 1 to enable* : See widget's `Branch Siblings` option, under *Inclusions*, 
[Filters Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Filters-Section). (only relevant to a `Branch` filter)

= include_level =
*string* : A level (1, 2, 3, etc), optionally followed by a "+" or "-" to include all subsequent (lower) or prior (higher) 
levels respectively. For example, "2" will include all items at level 2, whereas "2-" would include all level 1 **and** level 2 items, 
and "2+" would include all items at level 2 or greater.

Note that prior to v3.0.4, this was `include_root` (a switch), which only included the root level : `include_root` is still accepted, even
though now deprecated, and is equivalent to setting `include_level` to "1". However, if `include_level` is specified then it takes precedence.

= exclude =
*string* : Comma-separated list of meu item ids, where an id can optionally be followed by a '+' to include all its descendants (eg. "23+").

= exclude_level =
*string* : A level (1, 2, 3, etc), optionally followed by a "+" or "-" to include all subsequent (lower) or prior (higher) 
levels respectively. For example, "2" will exclude all items at level 2, whereas "2-" would exclude all level 1 **and** level 2 items, 
and "2+" would exclude all items at level 2 or greater.

= contains_current =
*string* : Accepted values : "menu", "primary", "secondary", "inclusions", or "output". See widget's *Qualifier* options,
under [Filters Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Filters-Section), 
for an explanation of the respective settings.

= fallback =
*string* : There are 2 main options for the *"If Current Item has no children"* fallback (ref. [Fallbacks Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Fallbacks-Section))...

* *"parent"* : Sets the widget's `Starting at` Fallback option to "-1 (parent)"
* *"current"* : Sets the widget's `Starting at` Fallback option to "the Current Item"

Either of these 2 values can be further qualified by appending a comma and a digit, eg. `fallback="current,1"` or `fallback="parent,2"`, which 
will also set the widget's `For Depth` fallback option to the value of the digit(s).

Optionally, "+siblings" can also be used (comma-separated, with or without a depth digit) to indicate that siblings of the "parent" or 
"current" fallback item should also be included. The order of the comma-separated values is not important, so "current,+siblings,1" is the 
same as "current,1,+siblings", and "2,parent" is the same as "parent,2", etc.

= fallback_ci_parent =
*switch, off by default, 1 to enable* : See widget's *"If no Current Item can be found"* fallback in the 
[Fallbacks Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Fallbacks-Section).

= flat_output =
*switch, off by default, 1 to enable* : See widget's `Flat` option, under [Output Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Output-Section).

= title_from =
*string* : Supply one or more (by separating them with a comma, eg. `title_from="branch,current"`) of the following (ref. the widget's `Set Title from` options, under [Output Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Output-Section))...

* *"current"* : enables the widget's *Current Item* `Set Title from` option
* *"current-root"* : enables the widget's *...or its Root* option that relates to the `Current Item` `Set Title from` option
* *"branch"* : enables the widget's *Branch* `Set Title from` option
* *"branch-root"* : enables the widget's *...or its Root* option that relates to the `Branch` `Set Title from` option

= ol_root =
*switch, off by default, 1 to enable* : See widget's `Top Level` option, under *Change UL to OL* in the [Output Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Output-Section).

= ol_sub =
*switch, off by default, 1 to enable* : See widget's `Sub-Levels` option, under *Change UL to OL* in the [Output Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Output-Section).

= container =
*string* : See widget's `Element` option, under [Container Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Container-Section).

= container_id =
*string* : See widget's `Unique ID` option, under [Container Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Container-Section).

= container_class =
*string* : See widget's `Class` option, under [Container Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Container-Section).

= menu_class =
*string* : See widget's `Menu Class` option, under [Classes Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Classes-Section).

= widget_class =
*string* : See widget's `Widget Class` option, under [Classes Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Classes-Section).

= wrap_link =
*string* : This is an optional tag name (eg. "div", "p", "span") that, if provided, will be made into HTML start/end tags
and sent through to the widget as its `Before the Link` and `After the Link` options (ref. [Links Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Links-Section)). 
Please note that the shortcode usage - a simple tag name - is much more restrictive than the widget's options, which allow HTML.

= wrap_link_text =
*string* : This is an optional tag name (eg. "span", "em", "strong") that, if provided, will be made into HTML start/end tags
and sent through to the widget as its `Before the Link Text` and `After the Link Text` options (ref. [Links Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Links-Section)). 
Please note that the shortcode usage - a simple tag name - is much more restrictive than the widget's options, which allow HTML.

= alternative =
*string* : This is 2 settings separated by a comma, reflecting the `On condition` options under the 
[Alternative Section](http://wordpress.org/plugins/custom-menu-wizard/other_notes/#Alternative-Section).
Possible values are:

* One of "current", "no-current" or "no-output" : the condition to test for
* One of "menu", "primary", "secondary", "inclusions", or "output" : the stage at which to test the condition

Eg. `alternative="no-current,inclusions"` would test for the absence of a Current Item in the filtered menu items, having completed 
the Inclusions stage, and attempt to switch to the Alternative settings.

The actual Alternative settings - a cut-down shortcode - are placed as content between the shortcodes start and end tags, and this is 
the only time that the use of a self-terminating shortcode is not sufficient. When specifiying the Alternative settings, *do not* 
include the square brackets, otherwise WordPress will interpret it as a nested shortcode!

For example, to set a primary configuration of "show Current Branch plus any kids", with an Alternative of "show top 2 levels" if no 
current item can be found anywhere in the menu...

`[cmwizard menu=NN branch=current alternative="no-current,menu"]depth=2[/cmwizard]`

Alternatively, you could switch it around and say the primary configuration is "show top 2 levels", with an Alternative of 
"show Current Branch plus kids" if a current item *can* be found within the menu...

`[cmwizard menu=NN depth=2 alternative="current,menu"]branch=current[/cmwizard]`

Note that Alternative (eg. "branch=current") does not require a `menu` option, because you can't change the menu so the primary
configuration's setting is always used.

As ever, the best way to construct a full shortcode, including an alternative, is to use the Assist : Use one instance of the CMW
widget to build your Alternative settings, copy the equivalent shortcode into the Alternative option of a second instance of the CMW 
widget, and then continue configuring that second instance to be your primary configuration; your final shortcode can simply be lifted
from the second instance!

A bit more information about the Alternative option is available 
in [this article](http://www.wizzud.com/2014/10/03/custom-menu-wizard-wordpress-plugin-version-3-1/).

= title_tag =
*string* : An optional tag name (eg. "h1", "h3", etc) to replace the default "h2" used to enclose the widget title.
Please note that this option has no equivalent in the widget options, because it *only* applies when a widget is instantiated via a shortcode.

= findme =
*switch, off by default, 1 to enable* : This is a utility intended for editors only, and output is restricted to those with edit_pages capability. 
If enabled it will return a list of posts that contain a CMW shortcode. If `findme` is set, the only other option that is taken any 
notice of is `title`, which will be output (if supplied) as an H3 in front of the list. The information provided by this utility is also available 
from any widget's "assist".

Example : `[cmwizard findme=1 title="Posts containing a CMW shortcode..."/]`

= SHORTCODE EXAMPLES =

* Show the entire "main" menu

    `
    [cmwizard menu=main/]
    `

* Show the children of the current menu item within the "main" menu, for unlimited depth, setting the widget title from the current menu item

    `
    [cmwizard menu=main branch=current start_at=children title_from=current/]
    `

* From the "animals" menu, show all the items *immediately* below "Small Dogs", plus "Small Dogs" and its sibling items, as ordered lists

    `
    [cmwizard menu="animals" branch="small dogs" depth=2 include="siblings" ol_root=1 ol_sub=1/]
    `

* From the "animals" menu, show the entire "Small Animals" branch, with the sole exception of the "Small Animals" item itself, whenever "Small Animals" or one of its descendants is the current menu item

    `
    [cmwizard menu="animals" branch="small animals" start_at=children contains_current=primary/]
    `

* Show the entire "main" menu entitled "Main Menu" *unless* there's a current menu item, in which case show the current menu item, its siblings and its immediate children, and entitle it "Nearest and Dearest!"

    `
    [cmwizard menu=main title="Main Menu"]title="Nearest and Dearest!" branch=current depth=2 siblings=1[/cmwizard]
    `

== Frequently Asked Questions ==
If you have a question or problem that is not covered here, please use the [integrated Support forum](http://wordpress.org/support/plugin/custom-menu-wizard).

= Are there any known problems/restrictions? =
Yep, 'fraid so :

1. The widget will only recognise one "current" item (as of v2.0.2, that's the first one encountered; prior to that it was the last one found). It is perfectly possible to have more than one menu item marked as "current", but if CMW has been configured to filter on anything related to a "current menu item" it can only choose one. The simplest example of multiple "current" items is if you add the same page to a menu more than once, but any other plugin that adds and/or manipulates menu items could potentially cause problems for CMW.
2. The widget's "assist" uses jQuery UI's Dialog, which unfortunately (in versions 1.10.3/4) has a *really* annoying bug in its handling of a draggable (ie. when you drag the Dialog's title bar to reposition it on the page) when the page has been scrolled. It is due to be fixed in UI v1.11.0, but meantime I have defaulted the Dialog to fixed position, with an option to toggle back to absolute : it's not perfect but it's the best compromise I can come up with to maintain some sort of useability.

= Why isn't it working? Why is there no output? =
I don't know. With all due respect (and a certain amount of confidence in the widget) I would venture to suggest that it is probably due to 
the option settings on the widget/shortcode. The quickest way to resolve any such issues is to use the widget's interactive "assist", and 
ensure that you set the current menu item correctly for the page(s) that you are having problems with. However, I am well aware that I not 
infallible (and it's been proven a fair few times!), so if you still have problems then please let me have as much information as possible 
(the shortcode for your settings is a good start?) and I will endeavour to help. Please note that simply reporting "It doesn't work" is not
the most useful of feedbacks, and is unlikely to get a response other than, possibly, a request for more details.

= How do I use the "assist"? =
The widget's interactive "assist" is specific to each widget instance. It is a javascript-driven *emulator* that uses the widget instance's 
option settings - including the menu selected - to build a pictorial representation of the menu and show you, in blue, which menu items will 
be output according to the current option settings. It also shows a very basic output list of those menu items, although it will not apply 
some of the more advanced HTML-modifying options such as can be found under the Container, Classes or Links sections.
Any of the displayed menu items can be designated as the "current menu item" simply by clicking on it (click again to deselect, or another 
item to change). The "current menu item" is shaded red, with its parent shaded orange and ancestors shaded yellow. All changes in the 
"current menu item" and the widget options are immediately reflected by the "assist" (text fields in the widget options simply need to lose 
focus).

The red cross to the left of each menu item toggles the Exclusions setting for the item and/or its descendants. The button has 3 settings :

* Off (dimmed)
* Just this item (white on red)
* This item *plus* all its descendants (white on red, with a small yellow plus sign)

Just click through the toggle states. When the Primary Filter is set to "Items", the green tick buttons to the right of each menu item 
work in the same way.

Note that if a green "Alternate settings" message is showing then the ticks and crosses buttons will show the approriate Alternative
settings but they will be slightly opaque and they will *not* be clickable!

Once you are happy with the results, having tested all possible settings of "current menu item" (if it applies), then simply Save the widget. 
Alternatively, copy-paste the shortcode text - at the base of either the "assist" or the widget form - straight into your post (you do **not** need to Save the widget!).
The widget does not have to Saved to *test* any of the options.

= Is there an easy way to construct the shortcode? =
Yes, use a widget form. The shortcode for all the selected/specified options is show at the base of the widget (v3+) and the base of the
"assist". The widget does not have to be placed within a widget area, it can also be used from the Inactive Widgets area. And
you do **not** need to Save the widget just to get a shortcode!

= How do I get the menu item ids for the 'Items' option? =
Use the widget's interactive "assist" (see above). Within the representative menu structure, each menu item's id is set in its title 
attribute, so should be seen when the cursor is moved over the item. A simpler way is to check the `Items` option : the "assist" will 
then show a green tick "checkbox" to the right of each menu item and you simply [un]check the items as required. Each selection will be reflected back into the 
widget's `Items` settings, and also in the shortcode texts.

The more painstaking way is to go to Appearance, Menus and select the relevant menu; hover over the *edit*, *Remove*, or *Cancel* link for an item and look in 
the URL (the link's href) for `menu-item=NNN` ... the NNN is the menu item id.

= How do I get the menu item ids for the 'Exclude Ids' option? =
The "assist" shows a red cross "checkbox" to the left of each menu item, and [un]checking the items will refelect back into the options and
shortcode texts. Otherwise, it's the same principle as outlined above for `Items` ids.

= What's the difference between including Branch Siblings (or Branch Ancestors + Siblings), and switching to 'Level' instead of 'Item' in the Secondary Filter section? =
If you elect to include Branch [Ancestor] Siblings, you will *only* get the siblings, **not** their descendants (assuming they have any). 
On the other hand, if you make `Starting at` use 'Level' instead of 'Item' then siblings *and their descendants* will be added to the filter.

For example, let's say that Bravo and Charlie are sibling items immediately below Alpha, and that Bravo is the selected Branch Item, 
with `Starting at` set to "the Branch" (ie. Bravo). If you switch from "Item" to "Level" then both Bravo, Charlie, *and all their descendants*,
will become eligible for filtering. If you left "Item" enabled, and switched on the inclusion of Branch Siblings, then Bravo and Charlie
would both still be eligible, but only *Bravo's descendants* would be; not Charlie's!

= Where is the styling of the output coming from, and how do I change it? =
The widget does not supply any ouput styling (at all!). This is because I have absolutely no idea where you are going to place either the
widget (sidebar, footer, header, ad-hoc, etc?) or the shortcode (page content, post content, widget content, custom field, etc?) and everyone's
requirements for styling are likely to be different ... possibly even within the same web page's output. So all styling is down to your theme,
and if you wish to modify it you will need to add to your theme's stylesheet.

The safest way to do this is via a child theme, so that any changes you make will not be lost if/when the main theme gets updated. The best 
way to test your changes is by utilising the developer capabilities that are available in most modern browsers (personally, I could not
do without Firefox and the Firebug extension!) and dynamically applying/modifying styles, possibly utilising the custom classes that the 
widget applies to its output, or the Container options for a user-defined id or class.

= How can I find all my posts/pages that have a CMW shortcode so that I can upgrade them? =
There is a button on the widget's "assist" - `[...]` - that will provide a list of posts/pages whose content, or meta data (custom fields), 
contains any CMW shortcode. Each entry is a link that opens the item in a new tab/window. The link's title gives a bit more information : 
post type, id, whether the shortcode(s) are in content and/or meta data, and the shortcode(s) concerned.
This utility does not check things like text widgets, plugin-specific tables, theme-provided textareas, etc.

There is also an extension to the shortcode - `[cmwizard findme=1/]` - that will output the same information, should you not be able to use 
the "assist" (for some unknown reason). You may optionally provide a title attribute; any other attributes are ignored. 
Note that output from this shortcode extension is restricted to users with edit_pages capability.


== Screenshots ==
1. Widget (all sections collapsed)
2. Filters Section
3. Fallbacks Section
4. Output Section
5. Container Section
6. Classes Section
7. Links Section
8. Alternative Section
9. Widget's "assist"

== Changelog ==

= 3.1.3 =
* tweak : minor change to css for the assist when running under the Customizer (WordPress 4.1)

= 3.1.2 =
* modified the readme : documentation for the Shortcode Parameters has been moved to the Installation page (to avoid being truncated)

= 3.1.1 =
* bugfix : only show the allow_all_root setting in the shortcode equivalent if the primary filter is by branch
* addition : work-around for when a theme inadvertently(!) de-registers the widget, which then prevents the shortcode working

= 3.1.0 =
* addition : new Alternative section which takes a cmwizard shortcode and conditionally applies it as an entirely new widget configuration
* addition : new fallback switch which enables an item marked as current_item_parent to be used as current item when no other current item is found
* bugfix : updated the determination of current item so that a paged (?paged=2, etc) Home page still shows Home page as being current
* bugfix : fixed code introduced in v3.0.4 that prevented CMW script loading on the customizer page - when the Widget Customizer plugin is loaded - for WordPress v3.8 and below
* bugfix : stop disabling selected fields based on other settings, because this caused the customizer to wipe values that may have been still required

= 3.0.4 =
* bugfix : corrected the display of the "No Current Item!" warning in the "assist"
* bugfix : corrected the enabling/disabling of a couple of fields in the widget form, and tweaked the indentation for better responsiveness
* bugfix : corrected the options setup when in accessibility mode with javascript enabled
* addition : added a warning about the accuracy of the shortcode when javascript is disabled
* addition : extended the All Root Items inclusion to be a selectable number of levels (as per the Exclusions by Level)

= 3.0.3 =
* bugfix : removed all occurrences of "Plugin " followed by "Name" from everywhere except the main plugin file to avoid update() reporting Invalid Header when activating straight from installation (rather than from the Plugins page)
* tweak : eliminate the over-use of get_title() when determining the widget title
* tweak : added self-terminating forward slash to generated shortcodes
* change : prepare for WordPress v4 (avoid use of deprecated functions)

= 3.0.2 =
* bugfix : the shortcode display on new instances of the widget (in admin) did not initially reflect the automatically-selected menu

= 3.0.1 =
* bugfix : changed the determination of pre-existing legacy widgets versus brand new widget instances, to get round problems encountered when other plugins utilise the widget_form_callback filter to inject fields into a widget
* addition : added a couple of filters

= 3.0.0 =
* **! Rewrite, and Change of Approach !** The widget has had a major rewrite! The `Children of` filter has been replaced with a `Branch` filter, with a subsequent shift in focus for the secondary filter parameters, from the children's level (0, 1 or more items) up to the branch level (a single item!). This should provide a more intuitive interface, and is definitely easier to code for. **However**, it only affects *new instances* of the widget; v2 instances are still ***fully supported***.

    Please also note that the shortcode tag for v3 has changed to **`[cmwizard]`**, with a revised set of parameters. The old shortcode tag is still supported, but only with the v2 parameter set, and only providing v2 functionality, ie. it is the shortcode tag that determines which widget version to use, and the appropriate parameter set for that version.

    There is no automatic upgrade of widget settings from v2 to v3! I suggest bringing up the "assist" for the existing v2 widget, running it side-by-side with the "assist" of a new instance of the widget, and using them to the compare the desired outputs. I would also strongly recommend that you put your old widgets into the inactive area until you are completely happy with their new replacements. If you are upgrading from version 2, and you would like a bit more information, [this article](http://www.wizzud.com/2014/06/16/custom-menu-wizard-wordpress-plugin-version-3/) might help.
* change : **the minmum requirement for WordPress is v3.6**
* addition : more options for requiring that the "current" menu item be present at some point in the filter process
* addition : Branch filter levels can be either relative (to the selected Branch item) or absolute (within the menu structure)
* addition : menu items can now be excluded from the final output, either explicitly by id (optionally including descendants), or by level
* addition : the ids of Items can be set to include all descendants
* addition : the inclusion of branch ancestors, and optionally their siblings, can be set by absolute level or relative number of levels
* addition : the widget title can now be automatically set from the root level item of the Branch item or current menu item
* addition : the shortcode for a widget's current settings is now also displayed at the base of the widget (as well as at the base of the "assist")
* addition : "title_tag" has been added to the shortcode options, enabling the default H2 to be changed without having to resort to coding a filter
* addition : as an alternative to using the "assist", "findme" has been addded to the shortcode options to aid editors with the location of posts containing a CMW shortcode ([cmwizard findme=1])
* This release includes an upgrade to v2.1.0 for all existing version 2 widgets/shortcodes - please read the v2.1.0 changes below.

= 2.1.0 (incorporated into v3.0.0 release) =
* change : **the minmum requirement for WordPress is v3.6**
* bugfix : handle duplicate menu item ids which were causing elements to be ignored
* bugfix : fix IE8 levels indentation in the "assist"
* bugfix : the "assist" is now "fixed" position (toggle-able back to absolute), mainly to get around a bug in jQuery UI draggable
* remove : take out the automatic selection of shortcode text (inconsistent cross-browser, so just triple click as usual; paste-as-text if possible!)
* addition : in the "assist", provide collapsible options for those larger menus
* addition : added utility to the "assist" enabling posts containing a CMW shortcode to be located
* change : in the "assist", swap the menu Items checkboxes for clickable Ticks
* change : in the "assist", tweak styling and make more responsive to re-sizing
* change : made compatible with Widget Customizer
* Note : there is no separate release available for this version!

= 2.0.6 =
* change : modified determination of current item to cope better with multiple occurences (still first-found, but within prioritised groups)
* change : display of the upgrade notice in the plugins list has been replaced with a simple request to read the changelog before upgrading

= 2.0.5 =
* bugfix : prevent PHP warnings of Undefined index/offset

= 2.0.4 =
* bugfix : clearing the container field failed to remove the container from the output
* addition : in the "assist", added automatic selection of the shortcode text when it is clicked
* addition : remove WordPress's menu-item-has-children class (introduced in v3.7) when the filtered item no longer has children
* change : tweaked styles and javascript in admin for WordPress v3.8

= 2.0.3 =
* bugfix : missing global when enqueuing scripts and styles for admin

= 2.0.2 =
* bugfix : the Include Ancestors option was not automatically including the Parent
* bugfix : the "assist" was incorrectly calculating Depth Relative to Current Item when the current menu item was outside the scope of the Filtered items
* behaviour change : only recognise the first "current" item found (used to allow subsequent "current" items to override any already encountered)

= 2.0.1 =
* bugfix : an incorrect test for a specific-items filter prevented show-all producing any output

= 2.0.0 =
* **! Possible Breaker !** The calculation of `Start Level` has been made consistent across the `Show all` and `Children of` filters : if you previously had a setup where you were filtering for the children of an item at level 2, with start level set to 4, there would have been no output because the immediate children (at level 3) were outside the start level. Now, there *will* be output, starting with the grand-children (at level 4).
* **! Possible Breaker !** There is now deemed to be an artificial "root" item above the level 1 items, which mean that a `Children of` filter set to "Current Parent Item" or "Current Root Item" will no longer fail for a top-level "current menu item". If you have the "no ancestor" fallback set then this change will have no impact (but you may now want to consider turning the fallback off?); if you *don't* currently use the "no ancestor" fallback, then where there was previously no output there will now be some!
* added new option : Items, a comma- or space-delimited list of menu item ids, as an alternative Filter
* added new option : Depth Relative to Current Item to the Filter section (depth_rel_current=1 in the shortcode)
* added new option : Must Contain Current Item to the Output section (contains_current=1 in the shortcode)
* changed the widget's "demo" facility to "assist" and brought it into WordPress admin, with full interactivity with the widget
* refactored code

= 1.2.2 =
* bugfix : fallback for Current Item with no children was failing because the parent's children weren't being picked out correctly

= 1.2.1 =
* added some extra custom classes, when applicable : cmw-fellback-to-current & cmw-fellback-to-parent (on outer UL/OL) and cmw-the-included-parent, cmw-an-included-parent-sibling & cmw-an-included-ancestor (on relevant LIs)
* corrected 'show all from start level 1' processing so that custom classes get applied and 'Title from "Current" Item' works (regardless of filter settings)
* changed the defaults for new widgets such that only the Filter section is open by default; all the others are collapsed
* in demo.html, added output of the shortcode applicable to the selections made
* in demo.html, added a link to the documentation page
* corrected 2 of the shortcode examples in the readme.txt, and made emulator (demo) available from the readme

= 1.2.0 =
* added custom_menu_wizard shortcode, to run the widget from within content
* moved the 'no ancestor' fallback into new Fallback collapsible section, and added a fallback for Current Item with no children
* added an option allowing setting of title from current menu item's title
* fixed a bug with optgroups/options made available for the 'Children of' selector after the widget has been saved (also affected disabled fields and styling)
* don't include menus with no items
* updated demo.html

= 1.1.0 =
* added 'Current Root Item' and 'Current Parent Item' to the `Children of` filter
* added `Fallback to Current Item` option, with subsidiary options for overriding a couple of Output options, as a means to enable Current Root & Current Parent to match a Current Item at root level
* added an Output option to include both the parent item **and** the parent's siblings (for a successful `Children of` filter)
* added max-width style (100%) to the `Children of` SELECT in the widget options
* added widget version to the admin js enqueuer
* ignore/disable Hide Empty option for WP >= v3.6 (wp_nav_menu() now does it automatically)
* included a stand-alone helper/demo html page
* rebuilt the `Children of` SELECT in the widget options to cope with IE's lack of OPTGROUP/OPTION styling
* moved the setting of 'disabled' attributes on INPUTs/SELECTs from PHP into javascript

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 3.1.3 =
Tweaked the assist's css for when running the Customizer in WordPress 4.1.

= 3.1.2 =
No code changes, just moved the readme's Shortcode Parameters documentation onto the Installation page (to avoid being truncated).

= 3.1.1 =
Added a work-around for when a theme inadvertently(!) de-registers the widget, which then prevents the shortcode working.
Fixed a trivial bug where the allow_all_root setting was being unnecessarily shown in the widget's shortcode equivalent.

= 3.1.0 =
Added an Alternative section which gives a dual-scenario capability, such as "show Config A, but if a current item is present then show Config B"
Added a new option to enable a "last resort" determination of current item as being an item marked as the parent of a current item, even though no current item is actually present.
Fixed a bug with the paging of a Home blogging page, and stopped disabling widget fields so that the customizer does not "lose" values. Also fixed customizer for WordPress pre v3.9 running Widget Customizer plugin.

= 3.0.4 =
Fixed a couple of minor bugs with the "assist" and the widget form, and corrected a bug with accessibility mode when javascript is enabled.
Extended Include Root Items to allow selection by level, as is provided for Exclusions by Level (eg. include_root=1 is now include_level="1").

= 3.0.3 =
Fixed problem with WordPress's update() reporting Invalid Header when activating immediately following installation (as opposed to activating via Plugins page).
Tweaked the generated shortcode to add a self-terminating forward slash. Please note that this tweak is merely a metter of "good practice" : there is no need to update your existing, working, shortcodes!
Also removed multiple calls to get_title() when determining the widget's title, and made some minor updates in readiness for WordPress v4.

= 3.0.2 =
Fixed a bug where the shortcode displayed on new instances of the widget (in admin) did not initially reflect the automatically-selected menu

= 3.0.1 =
Fixed a bug that created new widget instances as legacy version rather than latest; only encountered when other installed plugins inject their own fields into widgets
Also added a couple of filters.

= 3.0.0 =
**Rewrite, and change of approach** : __! Important !__ : existing (version 2) widgets and shortcodes *__are fully supported__*. Please [read the Changelog](http://wordpress.org/plugins/custom-menu-wizard/changelog/) *before* upgrading!
Version 3 swaps the *Children-Of* filter for a *Branch* filter, with secondary filters to then refine the branch items. It has better filter capabilities - relative and absolute start level, presence of a current menu item at different stages - and adds exclusion of items by id and/or level. A new shortcode - *[cmwizard]* - has been added to support the v3 functionality.
Changes that also apply to version 2 widgets include : **Minimum requirement for WordPress now v3.6!**; handling of duplicate menu ids, improved compatibility with Widget Customizer (required due to its incorporation into WordPress v3.9 core), and tweaks to the "assist".

= 2.0.6 =
Determination of the current menu item has been slightly modified to cope a bit better with occasions where multiple items have been set as "current".
The display of the upgrade notice in the plugins list has been replaced with a simple request to read the changelog before upgrading.

= 2.0.5 =
Fixed a bug to prevent PHP warnings of Undefined index/offset being output.

= 2.0.4 =
Fixed a bug that prevented the container field being removed, and added removal of the menu-item-has-children class when the filtered item no longer has children.
The admin widget styling and javascript have been tweaked to accommodate WordPress 3.8.

= 2.0.3 =
Fixed a minor bug with a missing global when enqueuing script and style for the admin.

= 2.0.2 =
Fixed a bug with the Include Ancestors option, where it was not automatically including the Parent.
Fixed a bug in the "assist", where it was incorrectly calculating Depth Relative to Current Item when the current menu item was outside the scope of the Filtered items.
Changed determination of the "current" item such that only the first one encountered is recognised, rather than allowing subsequent "current" items to override previous ones.

= 2.0.1 =
Fixed a bug whereby a test for a specific-items filter prevented show-all from producing any output.

= 2.0.0 =
**! Possible Breaker! !** My apologies if this affects you, but there are 2 possible scenarios where settings that previously resulted in no output *might* now produce output : 
+ if you have set a `Children of` filter, **and** you have changed the `Start Level` to a level greater than 2, or
+ if you have set the `Children of` filter to Current Parent/Root Item, and you have **not** set the "no ancestor" fallback.
*__If you think you may be impacted, please check the [Changelog](http://wordpress.org/plugins/custom-menu-wizard/changelog/) for a fuller explanation of what has changed.__*

New options :
+ `Items` allows specific menu item ids to be listed, as an alternative to the other filters
+ `Relative to "Current" Item` allows a limited Depth to be calculated relative to the current menu item
+ `Must Contain "Current" Item` requires that there be no output unless the resultant list contains the current menu item.
Rebuilt the "demo" facility as an "assist" wizard for the widget It is now fully interactive with the widget instance, and generates the entire shortcode according to the widget instance settings.

= 1.2.2 =
Bugfix : The fallback for Current Item with no children was failing because the parent's children weren't being picked out correctly

= 1.2.1 =
Added a few extra custom classes, and changed the defaults for new widgets such that only the Filter section is open by default. 
Fixed Show All processing so that custom classes always get applied, and 'Title from "Current" Item' works regardless of filter settings. 
Fixed a couple of the shortcode examples in the readme.txt, and added display of the applicable shortcode settings to the demo.html.

= 1.2.0 =
Added custom_menu_wizard shortcode, to run the widget from within content.
Added a new fallback for Current Item having no children, and moved all fallbacks into a collapsible Fallbacks section.
Fixed a bug with optgroups/options made available for the 'Children of' selector after the widget has been saved (also affected disabled fields and styling).
