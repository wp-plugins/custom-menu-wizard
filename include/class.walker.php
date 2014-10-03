<?php
/*
 * Custom Menu Wizard plugin
 *
 * Custom Menu Wizard Walker class
 * NB: Walker_Nav_Menu class is in wp-includes/nav-menu-template.php, and is itself an 
 *     extension of the Walker class (wp-includes/class-wp-walker.php)
 */
class Custom_Menu_Wizard_Walker extends Walker_Nav_Menu {

	/**
	 * CMW custom variables
	 */
	private $_cmw_tree;
	private $_cmw_lowest;
	private $_cmw_highest;

	/**
	 * opens a sub-level with a UL or OL start-tag
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {

		$indent = str_repeat("\t", $depth);
		$listtag = empty( $args->_custom_menu_wizard['ol_sub'] ) ? 'ul' : 'ol';
		$output .= "\n$indent<$listtag class=\"sub-menu\">\n";

	} //end start_lvl()

	/**
	 * closes a sub-level with a UL or OL end-tag
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int $depth Depth of page. Used for padding.
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {

		$indent = str_repeat("\t", $depth);
		$listtag = empty( $args->_custom_menu_wizard['ol_sub'] ) ? 'ul' : 'ol';
		$output .= "$indent</$listtag>\n";

	} //end end_lvl()

	/**
	 * pre-filters elements then calls parent::walk()
	 * 
	 * @filters : custom_menu_wizard_walker_items          array of filtered menu elements; array of args
	 * 
	 * @param array $elements Menu items
	 * @param integer $max_depth
	 * @return string
	 */
	public function walk($elements, $max_depth){

		$args = array_slice(func_get_args(), 2);
		$args = $args[0];

		if( $max_depth >= -1 && !empty( $elements ) && isset($args->_custom_menu_wizard) ){

			if( empty( $args->_custom_menu_wizard['cmwv'] ) ){
				$elements = $this->_cmw_walk_legacy( $args, $elements );
			}else{
				$elements = $this->_cmw_walk( $args, $elements );
			}

			//since we've done all the depth filtering, set max_depth to unlimited (unless flat output was requested!)...
			if( empty( $args->_custom_menu_wizard['flat_output'] ) ){
				$max_depth = 0;
			}else{
				//for v3.1.0 we now need to specifically reset max_depth in case we're using the alternative and it has changed
				//from hierarchic output to flat output...
				$max_depth = -1;
			}

		} //ends the check for bad max depth, empty elements, or empty cmw args

		return empty( $elements ) ? '' : parent::walk( apply_filters( 'custom_menu_wizard_walker_items', $elements, $args ), $max_depth, $args );

	} //end walk()

	/**
	 * current & legacy : finds and returns ID of current menu item while creating the tree and levels arrays
	 * 
	 * @param array $elements Array of menu items
	 * @param array $cmw Array of widget instance settings
	 * @return integer ID of current menu item (false if not found)
	 */
	private function _cmw_find_current_item( &$elements, &$cmw ){
		//$elements is an array of objects, indexed by position within the menu (menu_order),
		//starting at 1 and incrementing sequentially regardless of parentage (ie. first item is [1],
		//second item is [2] whether it's at root or subordinate to first item)

		$id_field = $this->db_fields['id']; //eg. = 'db_id'
		$parent_field = $this->db_fields['parent']; //eg. = 'menu_item_parent'
		$currentItem = array();

		$this->_cmw_tree = array(
			0 => array(
				'level' => 0,
				'ancestors' => array(),
				'kids' => array(),
				'element' => array(),
				'keepCount' => 0,
				'keep' => false
				)
			);
		$this->_cmw_levels = array(
			array() //for the artificial level-0
			);

		if( isset( $cmw['_exclude'] ) ){ //only came in at v3.0.0!
			//v3+ allows for inheritance on the $cmw items and exclude csv fields, which means that the _items and _exclude
			//arrays do not necessarly contain all the relevant items ids, eg. an element such as '23+' in the _exclude array means
			//that menu item id = 23 is excluded ... AND so are all its descendants!
			$cmw['__items'] = array();
			$cmw['__exclude'] = array();
			$inheritItems = array();
			$inheritExclude = array();
			foreach( $cmw['_items'] as $i ){
				if( substr( $i, -1 ) == '+' ){
					$i = substr( $i, 0, -1 );
					$inheritItems[] = $i;
				}
				$cmw['__items'][] = $i;
			}
			foreach( $cmw['_exclude'] as $i ){
				if( substr( $i, -1 ) == '+' ){
					$i = substr( $i, 0, -1 );
					$inheritExclude[] = $i;
				}
				$cmw['__exclude'][] = $i;
			}
		}

		foreach( $elements as $i => $item ){
			$itemID = $item->$id_field;
			$parentID = empty( $item->$parent_field ) ? 0 : $item->$parent_field;

			//if $this->_cmw_tree[] for the parent hasn't been set then it's an orphan...
			//NOTE The parent Walker will only handle orphans in a "hierarchical Show All" situation (ie. non-flat output, unlimited depth)
			//     and it does so by appending each orphan as a new top-level item (and note that a child of an orphan is also an orphan!).
			//     However, CMW excludes all orphans (which includes descendants of orphans) so they never reach the parent Walker.
			//     If orphans are required, use WordPress's own Custom Menu widget.
			if( isset( $this->_cmw_tree[ $parentID ] ) ){
				//check for current item (as a menu item ID, ie. a key into the tree)...
				if( $item->current || 
						( isset( $inheritItems ) && ( $hasCurrentClass = in_array( 'current-menu-item', (array)$item->classes ) ) === true ) ||
						( isset( $inheritItems ) && !empty( $cmw['fallback_ci_parent'] ) && $item->current_item_parent )
						){
					//should(!) never get either parent and/or ancestor on an item marked as "current", but unfortunately it does occur (grrr!).
					//so this has to cope, not only with more than 1 "current" item, but also with "current" items that are incorrectly marked
					//as (their own?!) parent and/or ancestor.
					//
					//v3.1.0 : there are also occasions when the item is given a class of current-menu-item but the 'current' property is not set
					//on the item - one such occasion being when the home page is set to latest posts (the default 'blogging' setting for Front
					//page displays, see Settings/Reading page) and you navigate to the second (or subsequent) page of listed posts. A menu item
					//that has the "Home Page" url (it's a "custom" menu item type) will fail to match any sort of current url (.../page/2/
					//or .../?paged=2) but does get recognised as being a "front page" url, so gets the class but no property! ON BY DEFAULT!
					//v3.1.0 : also occasions where $item->current_item_parent is set without there being any item with $item->current!
					//eg. open a post, and if there is a Category menu item available (the post has that category) then Category menu item
					//gets marked as current_item_parent. HAS TO BE ENABLED!
					//
					//we're going to look for correctly (solely) marked "current" items and take the first one found
					//failing that, look for a "current" item that is also marked as parent, and, again, use the first one found
					//failing that, look for a "current" item that is also marked as an ancestor, and, again, use the first one found
					//failing that, look for an item classed as "current-menu-item", again using the first one found
					//finally, if enabled, look for an item marked as parent (if it gets used then there's no current!), using first one found
					//
					//array keys, priority order : 
					// just current -> parent, not ancestor -> parent and ancestor -> ancestor -> "current-menu-item" -> parent
					// first found...
					// - a001 : just current
					// - b001 : current & parent (not ancestor)
					// - c001 : current & parent & ancestor
					// - d001 : current & ancestor (not parent)
					// - e001 : "current-menu-item"
					// - f001 : parent (not current)
					// next found...
					// - a002 : just current
					// - b002 : current & parent (not ancestor)
					// - c002 : current & parent & ancestor
					// - d002 : current & ancestor (not parent)
					// - e002 : "current-menu-item"
					// - f002 : parent (not current)
					// etc
					//example : 
					// - 1st found : current & ancestor = d001
					// - 2nd found : current & parent & ancestor = c002
					// - 3rd found : just current = a003
					// - 4th found : current & parent = b004
					// - 5th found : just current = a005
					//reverse sort keys alphabetically and a003 comes out on bottom, so third found gets used! (copes with 999 "current" 
					//items; should be enough!)
					if( $item->current ){
						if( $item->current_item_ancestor ){
							if( $item->current_item_parent ){
								$j = 'c';
							}else{
								$j = 'd';
							}
						}elseif( $item->current_item_parent ){
							$j = 'b';
						}else{
							$j = 'a';
						}
					}elseif( $hasCurrentClass ){
						$j = 'e';
					}else{
						$j = 'f';
					}
					$currentItem[ $j . sprintf( '%03d' , count( $currentItem ) + 1 ) ] = $itemID;
				}

				//because it is possible for $itemID not to be unique, we have to play canny :
				//we need to be able to store multiple references back into $elements for each $itemID, and we therefore have to
				//check whether we already have a $this->_cmw_tree entry for the $itemID, and if we do then simply store the pointer
				//back to $elements in the existing $this->_cmw_tree entry for the item ID.
				//This has consequences :
				// - the individual menu items (for non-unique item IDs) will be in or out as a group, not as individual items
				// - if we happen to need, say, the branch title from the branch being filtered, we have to take it from the
				//   first of the group and there need not be any relation between that item's title and the original menu item's 
				//   title (as set on the WP Menu Admin page). Can't help that.
				// - any child will be a child of the group, which actually means it *should* become (at some point?!) the child
				//   of the last member of the group(?!). BUT what if the child is actually intended to be the child of the item it follows,
				//   eg. itemID N, child, itemID N, child, itemID N, itemID N, child, child, itemID N, etc?
				//   CMW can't cope with this : its position is that if WP's Walker can't cope with it then there is no reason why CMW should
				//   attempt to rectify the few (rare) occasions where these situations may arise. (It's possible, but I have chosen not to).
				//   NB The parent Walker assigns children of a non-unique-id menu item to the 1st occurence of the item ID (1st in group)
				if( isset( $this->_cmw_tree[ $itemID ] ) ){
					//it's a non-unique item ID! just add the $elements pointer to the element array...
					$this->_cmw_tree[ $itemID ]['element'][] = $i;
				}else{
					//this level...
					$thisLevel = $this->_cmw_tree[ $parentID ]['level'] + 1;
					if( empty( $this->_cmw_levels[ $thisLevel ] ) ){
						$this->_cmw_levels[ $thisLevel ] = array();
					}
					$this->_cmw_levels[ $thisLevel ][] = $itemID;

					$this->_cmw_tree[ $itemID ] = array(
						//level within structure...
						'level' => $thisLevel,
						//ancestors (from the artificial level-0, right down to parent, inclusive) within structure...
						'ancestors' => $this->_cmw_tree[ $parentID ]['ancestors'],
						//kids within structure, ie array of itemID's...
						'kids' => array(),
						//index of item within elements...
						'element' => array( $i ),
						//classes added by widget...
						'classes' => array(),
						//assume no match...
						'keep' => false
						);
					//append this item's parent onto its ancestors...
					$this->_cmw_tree[ $itemID ]['ancestors'][] = $parentID;
					//add this item to its parent's kids...
					$this->_cmw_tree[ $parentID ]['kids'][] = $itemID;

					//check for inheritance on items and exclude...
					if( isset( $inheritItems ) ){
						if( in_array( $parentID, $inheritItems ) ){
							$inheritItems[] = $itemID;
							$cmw['__items'][] = $itemID;
						}
						if( in_array( $parentID, $inheritExclude ) ){
							$inheritExclude[] = $itemID;
							$cmw['__exclude'][] = $itemID;
						}
					}
				}
			}
		} //end foreach
		
		if( isset( $inheritItems ) ){
			unset( $inheritItems, $inheritExclude );
		}

		if( empty( $currentItem ) ){
			$currentItem = false;
		}else{
			krsort( $currentItem );
			$currentItem = array_pop( $currentItem );
		}

		return $currentItem;

	} //end _cmw_find_current_item()

	/**
	 * current: test for one item being below another item and/or its siblings
	 * 
	 * @param integer $lookBelow Menu item ID that needs checking, possibly its siblings as well
	 * @param integer $searchFor Menu item ID that is being looked for
	 * @param boolean $andSiblings Whether to check in $lookBelow's siblings as well
	 * @return boolean Found (or not)
	 */
	private function _cmw_check_contains_item( $lookBelow, $searchFor, $andSiblings ){

		$rtn = in_array( $lookBelow, $this->_cmw_tree[ $searchFor ]['ancestors'] );
		if( !$rtn && $andSiblings ){
			foreach( $this->_cmw_tree[ $this->_cmw_get_parent( $lookBelow ) ]['kids'] as $kid ){
				//check whether one of $searchFor's ancestors is $kid... 
				if( $kid != $lookBelow && in_array( $kid, $this->_cmw_tree[ $searchFor ]['ancestors'] ) ){
					$rtn = true;
					break;
				}
			}
		}
		return $rtn;
		
	}

	/**
	 * clear any keep flags currently set in the tree
	 */
	private function _cmw_clear_down_tree(){

		if( $this->_cmw_tree[0]['keepCount'] > 0 ){
			foreach( $this->_cmw_tree as $k => $v ){
				$this->_cmw_tree[ $k ]['keep'] = false;
				$this->_cmw_tree[ $k ]['classes'] = array();
			}
			$this->_cmw_tree[0]['keepCount'] = 0;
		}

	}

	/**
	 * resolve digit(s) optionally followed by a plus/minus into a 'from' level an a 'to' level
	 * IMPORTANT : 'from' is inclusive, 'to' is exclusive, so a for() would be for( $i = $rtn['from']; $i < $rtn['to']; $i++ )
	 * 
	 * @param {string} $option Level with optional +/- appended
	 * @return {array} False if $option doesn't parse
	 */
	private function _cmw_decipher_plusminus_level( $option ){

		$rtn = array();
		if( !empty( $option ) && preg_match( '/^(\d+)(\+|-)?$/', $option, $m ) > 0 ){
			$m[1] = intval( $m[1] );
			if( $m[1] > 0 ){
				if( empty( $m[2] ) ){
					//no plus/minus : 'from' is the level, 'to' is the next level...
					$rtn['from'] = $m[1];
					$rtn['to'] = $m[1] + 1;
				}elseif( $m[2] == '+' ){
					//plus : 'from' is the level, 'to' is the number of levels 
					//NB: there is an artificial level zero, so if the menu has 10 levels, a count of levels will give 11!
					$rtn['from'] = $m[1];
					$rtn['to'] = count( $this->_cmw_levels );
				}else{
					//minus : 'from' is level 1, 'to' is the level plus 1
					$rtn['from'] = 1;
					$rtn['to'] = $m[1] + 1;
				}
			}
		}
		return empty( $rtn ) ? false : $rtn;

	}

	/**
	 * returns the menu item id if an item's parent
	 * 
	 * @param integer $kid Menu item ID
	 * @return integer Menu item ID of kid's parent
	 */
	private function _cmw_get_parent( $kid ){

		$immediateParent = array_slice( $this->_cmw_tree[ $kid ]['ancestors'], -1, 1);
		return $immediateParent[0];

	}

	/**
	 * current: set keep flag of an item's siblings
	 * 
	 * @param integer $itemID Menu item ID
	 * @param string $classSuffix Suffix of class to be added to the kept items
	 */
	private function _cmw_include_siblings_of( $itemID, $classSuffix='sibling' ){

		foreach( $this->_cmw_tree[ $this->_cmw_get_parent( $itemID ) ]['kids'] as $i ){
			if( !$this->_cmw_tree[ $i ]['keep'] ){
				$this->_cmw_tree[ $i ]['keep'] = true;
				$this->_cmw_tree[0]['keepCount']++;
				if( !empty( $classSuffix ) ){
					$this->_cmw_tree[ $i ]['classes'][] = 'cmw-an-included-' . $classSuffix;
				}
			}
		}

	} //end _cmw_include_siblings_of()

	/**
	 * runs exclusions, if there are any
	 * 
	 * @param {array} $cmw Settings
	 * @return {boolean} keepCount > 0?
	 */
	private function _cmw_run_exclusions( &$cmw ){

		$rtn = $this->_cmw_tree[0]['keepCount'] > 0;
		if( $rtn && !empty( $cmw['__exclude'] )){
			foreach( $cmw['__exclude'] as $itemID ){
				if( $itemID > 0 && isset( $this->_cmw_tree[ $itemID ] ) && $this->_cmw_tree[ $itemID ]['keep'] ){
					$this->_cmw_tree[ $itemID ]['keep'] = false;
					$this->_cmw_tree[0]['keepCount']--;
				}
			}
			$rtn = $this->_cmw_tree[0]['keepCount'] > 0;
		}
		if( $rtn && ( $fromTo = $this->_cmw_decipher_plusminus_level( $cmw['exclude_level'] ) ) !== false ){
			while( isset( $this->_cmw_levels[ $fromTo['from'] ] ) && $fromTo['from'] < $fromTo['to'] ){
				foreach( $this->_cmw_levels[ $fromTo['from'] ] as $itemID ){
					if( $this->_cmw_tree[ $itemID ]['keep'] ){
						$this->_cmw_tree[ $itemID ]['keep'] = false;
						$this->_cmw_tree[0]['keepCount']--;
					}
				}
				$fromTo['from']++;
			}
			$rtn = $this->_cmw_tree[0]['keepCount'] > 0;
		}
		return $rtn;

	}

	/**
	 * current : recursively set the keep flag if within specified level/depth
	 * if item passed in is eligible, sets that item as kept and runs through its kids recursively
	 * uses _cmw_lowest & _cmw_highest : note that _cmw_lowest is the lowest level in the structure - *not*
	 *                                   the numerically lowest value of level - and that both are inclusive!
	 * 
	 * @param integer $itemID Menu item ID
	 */
	private function _cmw_set_keep_recursive( $itemID ){

		//at or above lowest?...
		if( $this->_cmw_tree[ $itemID ]['level'] <= $this->_cmw_lowest ){
			//at or below highest?...
			if( $this->_cmw_tree[ $itemID ]['level'] >= $this->_cmw_highest ){
				//keep (if not already)...
				if( !$this->_cmw_tree[ $itemID ]['keep'] ){
					$this->_cmw_tree[ $itemID ]['keep'] = true;
					$this->_cmw_tree[0]['keepCount']++;
				}
			}
			//unless this item is above the lowest level there's no point checking its kids...
			if( $this->_cmw_tree[ $itemID ]['level'] < $this->_cmw_lowest ){
				for( $i = 0, $ct = count( $this->_cmw_tree[ $itemID ]['kids'] ); $i < $ct; $i++ ){
					$this->_cmw_set_keep_recursive( $this->_cmw_tree[ $itemID ]['kids'][ $i ] );
				}
			}
		}

	} //end _cmw_set_keep_recursive()

	/**
	 * switch the current settings for those indicated by a cmwizard shortcode
	 * 
	 * @param object $args The args passed into walk()
	 * @param string $at The current processing stage
	 * @param boolean $hasCurrent Whether or not current item is in this stage
	 * @param boolean $hasOutput Whether or not there will be any output (as best we know so far)
	 * @return boolean True if we can use alternative settings
	 */
	private function _cmw_switch_settings( &$args, $at = '', $hasCurrent = false, $hasOutput = false ){

		if( $args->_custom_menu_wizard['switch_at'] == $at && (
				( $args->_custom_menu_wizard['switch_if'] == 'current' && $hasCurrent ) ||
				( $args->_custom_menu_wizard['switch_if'] == 'no-current' && !$hasCurrent ) || 
				( $args->_custom_menu_wizard['switch_if'] == 'no-output' && !$hasOutput )
				) ){

			$plugin = Custom_Menu_Wizard_Plugin::init();

			//if switch_to is empty, it gets defaulted to a minimum
			//trim off square brackets, self-terminators, and spaces...
			$switchTo = trim( $args->_custom_menu_wizard['switch_to'], ' ][/' );
			//if it doesn't start with our shortcode, prepend it...
			if( substr( $switchTo . ' ', 0, 9 ) != 'cmwizard ' ){
				$switchTo = 'cmwizard ' . $switchTo;
			}
			//append our current menu so that it will be used when the shortcode atts are parsed...
			$switchTo = trim( $switchTo ) . ' menu=' . $args->_custom_menu_wizard['menu'];

			if( ( $new_cmw = $plugin->encode_shortcode( $switchTo ) ) !== false ){
				//store old...
				$old_cmw = array_merge( array(), $args->_custom_menu_wizard );
				//merge new into old, overriding _walker...
				$new_cmw = array_merge( $old_cmw, (array)$new_cmw, array('_walker' => array('alternative' => true)) );
				//overwrite current with new...
				$args->_custom_menu_wizard = $new_cmw;
				//put new and old into the current _walker, so that they become available to the widget
				//instance (as long as there's some output!)...
				$args->_custom_menu_wizard['_walker']['instances'] = array( 'old' => $old_cmw, 'new' => $new_cmw );
				unset( $old_cmw );

				return true;
			}
		}

		return false;

	} //end _cmw_switch_settings()

	/**
	 * legacy : recursively set the keep flag if within specified level/depth
	 * runs through kids of item passed in : if kid is eligible, sets kid to kept; if grandkids might be eligible, recurse with kid
	 * 
	 * @param integer $itemID Menu item ID
	 * @param integer $topLevel Uppermost level that can be kept
	 * @param integer $bottomLevel Lowermost level that can be kept
	 */
	private function _cmw_legacy_set_keep_kids( $itemId, $topLevel, $bottomLevel ){

		for( $i = 0, $ct = count( $this->_cmw_tree[ $itemId ]['kids'] ); $i < $ct; $i++ ){
			$j = $this->_cmw_tree[ $itemId ]['kids'][ $i ];
			if( $this->_cmw_tree[ $j ]['level'] <= $bottomLevel ){
				if( ( $this->_cmw_tree[ $j ]['keep'] = $this->_cmw_tree[ $j ]['level'] >= $topLevel ) !== false){
					$this->_cmw_tree[0]['keepCount']++;
				}
			}
			if( $this->_cmw_tree[ $j ]['level'] < $bottomLevel ){
				$this->_cmw_legacy_set_keep_kids( $j, $topLevel, $bottomLevel );
			}
		}

	} //end _cmw_legacy_set_keep_kids()

	/**
	 * pre-filters elements
	 * 
	 * @filters : custom_menu_wizard_walker_change_settings    array of current CMW settings; id of current menu item; array of original menu elements
	 *            gets applied immediately after determination of the current item, and can be used to provide an alternate set of CMW settings
	 *            based, maybe, on the value (presence/absence?) of a current menu item, or some other specific value in the current settings.
	 *            if the returned settings don't exactly match those currently in use, then the new ones are used and current item is re-determined.
	 * 
	 * @param {object} $args Params supplied to wp_nav_menu()
	 * @param {array} $elements Menu items
	 * @return {array} Modified menu items
	 */
	private function _cmw_walk( &$args, $elements ){

		$id_field = $this->db_fields['id']; //eg. = 'db_id'
		$parent_field = $this->db_fields['parent']; //eg. = 'menu_item_parent'
		$unlimited = 65532;
		//max number of run-throughs is 2!...
		$runCount = 2;

		$cmw =& $args->_custom_menu_wizard;

		while( $runCount > 0 ){

			$runCount--;
			$topOfBranch = -1;
			$continue = true;

			//find the current menu item (ID of the menu item) while creating the tree and levels arrays...
			$currentItem = $this->_cmw_find_current_item( $elements, $cmw );

			//allow (once only!) a filter to change the cmw settings based on the presence (or absence) of a current item...
			//note that not all changes to settings will have any influence; if the walker doesn't use them, and
			//the widget processing subsequent to the wp_nav_menu() call doesn't use them, then they will have no effect!
			//also note that utilising this filter will prevent any subsequent switchable from being actioned (because of the runCount).
			//BE AWARE : indiscriminate changes to the cmw settings have the potential to totally screw up the output!
			$new_cmw = $runCount > 0 ? apply_filters( 'custom_menu_wizard_walker_change_settings', $cmw, $currentItem, $elements ) : false;
			if( $new_cmw !== false && $new_cmw !== $cmw ){
				//store old...
				$old_cmw = array_merge( array(), $cmw );
				//merge new into old, and don't allow modification of _walker...
				$new_cmw = array_merge( $old_cmw, (array)$new_cmw, array('_walker' => array()) );
				//overwrite current with new...
				$args->_custom_menu_wizard = $new_cmw;
				//put new and old into the current _walker, so that they become available to the widget 
				//instance (as long as there's some output!)...
				$cmw['_walker']['instances'] = array( 'old' => $old_cmw, 'new' => $new_cmw );
				unset( $old_cmw );
				//back to top of while loop...
				continue;
			}
			unset( $new_cmw );

			$cmw['_walker']['fellback'] = false;

			$find_items = $cmw['filter'] == 'items';
			$find_branch = $cmw['filter'] == 'branch';
			$find_level = !$find_items && !$find_branch;
			$find_current = $find_branch && empty( $cmw['branch'] );

			//measuring depth relative to the current item only applies if depth is *not* unlimited...
			$depth = intval( $cmw['depth'] );
			$depth_rel_current = $cmw['depth_rel_current'] && $depth > 0 && $currentItem !== false; //v2.0.0
			//no-kids fallback?...
			$canFallback = $find_current && in_array( $cmw['fallback'], array('current', 'parent', 'quit') );
			//switchable?...
			//note that switchable does not require switch_to to contain a value!
			$canSwitch = $runCount > 0 && !empty( $cmw['switch_if'] ) && !empty( $cmw['switch_at'] );

			//check for current item and switch...
			$hasCurrent = $currentItem !== false;
			//no current item means that a current branch filter CAN'T produce output...
			if( $find_current ){
				$continue = $hasCurrent;
			}
			if( $continue && $cmw['contains_current'] == 'menu' ){
				$continue = $hasCurrent;
			}
			if( $canSwitch && $this->_cmw_switch_settings( $args, 'menu', $hasCurrent, $continue ) ){
				continue;
			}

			//PRIMARY FILTERS...
			if( $continue ){
				//levels...
				if( $find_level ){
					$continue = $cmw['level'] < count( $this->_cmw_levels );
				}
				//items...
				if( $find_items ){
					$continue = !empty( $cmw['__items'] );
				}
				//branch...
				if( $find_branch ){
					//topOfBranch gets set to -1 if it can't be determined...
					$topOfBranch = $find_current
						? ( $currentItem === false ? -1 : $currentItem )
						: ( isset( $this->_cmw_tree[ $cmw['branch'] ] ) ? $cmw['branch'] : -1 );
					$theBranchItem = $topOfBranch;
					$continue = $topOfBranch > 0;
				}
			} //end PRIMARIES

			//check for current item and switch...
			$hasCurrent = $currentItem !== false;
			if( $hasCurrent ){
				if( ( $find_level && $this->_cmw_tree[ $currentItem ]['level'] < $cmw['level'] ) ||
						( $find_items && !in_array( $currentItem, $cmw['__items'] ) ) ||
						( $find_branch && $topOfBranch !== $currentItem && !in_array( $topOfBranch, $this->_cmw_tree[ $currentItem ]['ancestors'] ) )
						){
					$hasCurrent = false;
				}
			}
			if( $continue && $cmw['contains_current'] == 'primary' ){
				$continue = $hasCurrent;
			}
			if( $canSwitch && $this->_cmw_switch_settings( $args, 'primary', $hasCurrent, $continue ) ){
				continue;
			}

			//SECONDARY FILTERS...
			if( $continue ){
				//right, let's set some keep flags
				//for specific items, go straight in on the item id (levels and depth don't apply here)...
				if( $find_items ){
					foreach( $cmw['__items'] as $itemID ){
						$itemID = intval( $itemID );
						//avoid double counting of duplicates...
						if( !empty( $itemID ) && isset( $this->_cmw_tree[ $itemID ] ) && !$this->_cmw_tree[ $itemID ]['keep'] ){
							$this->_cmw_tree[ $itemID ]['keep'] = true;
							$this->_cmw_tree[0]['keepCount']++;
						}
					}
				}
				//for by-level filter, use the levels...
				if( $find_level ){
					//prior to v2.0.0, depth was always related to the first item found, and still is *unless* depth_rel_current is set
					if( $depth_rel_current && $this->_cmw_tree[ $currentItem ]['level'] >= $cmw['level'] ){
						$bottomLevel = $this->_cmw_tree[ $currentItem ]['level'] + $depth - 1;
					}else{
						$bottomLevel = $depth > 0 ? $cmw['level'] + $depth - 1 : $unlimited;
					}
					for( $i = $cmw['level']; isset( $this->_cmw_levels[ $i ] ) && $i <= $bottomLevel; $i++ ){
						foreach( $this->_cmw_levels[ $i ] as $itemID ){
							$this->_cmw_tree[ $itemID ]['keep'] = true;
							$this->_cmw_tree[0]['keepCount']++;
						}
					}
				}
				//for branch filters, run a recursive through the structure...
				if( $find_branch ){
					$i = $this->_cmw_tree[ $topOfBranch ]['level'];
					//convert branch_start to an actual level...
					$j = intval( $cmw['branch_start'] );
					//convert relative to absolute (max'd against 1)...
					$j = empty( $j ) ? $i : ( preg_match( '/^(\+|-)/', $cmw['branch_start'] ) > 0 ? max( 1, $i + $j ) : $j );

					//do we have a current-item-no-kids fallback?...
					if( $canFallback && empty( $this->_cmw_tree[ $currentItem ]['kids'] ) ){
						//yes, we do...
						$cmw['_walker']['fellback'] = 'to-' . $cmw['fallback'];
						//is it a copout?...
						if( $cmw['fallback'] == 'quit' ){
							//just set the secondary start level beyond the maximum level available...
							$j = count( $this->_cmw_levels );
						}else{
							//for current, fall back to primary start level; for parent, fall back to primary start level - 1, ensuring
							//that we don't fall back further than root...
							$j = $cmw['fallback'] == 'current' || $i < 2 ? $i : $i - 1;
							//if fallback_depth is specified, override depth and set to depth-rel-current...
							if( !empty( $cmw['fallback_depth'] ) ){
								$depth = intval( $cmw['fallback_depth'] );
								$depth_rel_current = true;
							}
						}
					}

					//$i is the primary level, and $j is the secondary start level
					//easy result : if secondary start level > max level then there are no matches...
					if( $j < count( $this->_cmw_levels ) ){
						//if secondary start level is higher up the tree than the primary level, then
						//reset the tob to be the current tob's ancestor at the level of $j...
						if( $j < $i ){
							$topOfBranch = array_slice( $this->_cmw_tree[ $topOfBranch ]['ancestors'], $j, 1 );
							$topOfBranch = $topOfBranch[0];
							//NB $theBranchItem is still set to the original branch item!
						}

						$this->_cmw_lowest = $unlimited;
						$this->_cmw_highest = $j;

						//$topOfBranch is a menu item id. 
						//if secondary start is at or above primary, and start_mode is set to "level" then it effectively means that
						//the top of branch becomes the current topOfBranch *plus* all its siblings. the one qualifier for
						//this is that if topOfBranch's current level is 1 (root) then the allow_all_root switch must be
						//enabled in order to expand to all root items
						$forceLevel = $j <= $i && $cmw['start_mode'] == 'level' && ( $j > 1 || $cmw['allow_all_root'] );

						//prior to v2.0.0, depth was always related to the first item found, and still is *unless* depth_rel_current is set
						//NB for depth_rel_current to be applicable we need a current item that is : 
						//   (a) at or below the secondary start level, and
						//   (b) within the (modified?) branch (ie. has topOfBranch - or possibly one of its siblings! - as an ancestor)
						if( $depth_rel_current
								//...this is the (a) part... 
								&& $this->_cmw_tree[ $currentItem ]['level'] >= $this->_cmw_highest
								//...this is the (b) part, and it might be complicated by the setting of $forceLevel, which would
								//   require the testing, not only of topOfBranch, but also topOfBranch's siblings...
								&& $this->_cmw_check_contains_item( $topOfBranch, $currentItem, $forceLevel ) ){
							$this->_cmw_lowest = $this->_cmw_tree[ $currentItem ]['level'];
						}elseif( $depth > 0 ){
							$this->_cmw_lowest = $this->_cmw_highest;
						}
						$this->_cmw_lowest += $depth - 1;
						//$this->_cmw_tree[0]['keepCount'] gets incremented during this recursive...
						if( $forceLevel ){
							foreach( $this->_cmw_tree[ $this->_cmw_get_parent( $topOfBranch ) ]['kids'] as $k ){
								$this->_cmw_set_keep_recursive( $k );
							}
						}else{
							$this->_cmw_set_keep_recursive( $topOfBranch );
						}
						//if falling back and siblings are required, add them in...
						//note that root level sibling inclusion is still governed by allow_all_root!
						if( !empty( $cmw['_walker']['fellback'] ) && $cmw['fallback_siblings'] && $this->_cmw_tree[0]['keepCount'] > 0
								&& ( $j > 1 || $cmw['allow_all_root'] ) ){
							$this->_cmw_include_siblings_of( $topOfBranch );
						}
					}
				}
				$continue = $this->_cmw_tree[0]['keepCount'] > 0;
			} //end SECONDARIES

			//check for current item and switch...
			$hasCurrent = $currentItem !== false && $this->_cmw_tree[ $currentItem ]['keep'];
			if( $continue && $cmw['contains_current'] == 'secondary' ){
				$continue = $hasCurrent;
			}
			if( $canSwitch && $this->_cmw_switch_settings( $args, 'secondary', $hasCurrent, $continue ) ){
				continue;
			}

			//INCLUSIONS...
			if( $continue ){
				if( $find_branch ){
					//branch ancestors, possibly with their siblings : but only if the original branch item is either being kept or
					//is below the lowest visible level; ALSO, do not keep any ancestors below the lowest visible level...
					if( $cmw['ancestors'] != 0 && ( $this->_cmw_tree[ $theBranchItem ]['keep'] || $this->_cmw_tree[ $theBranchItem ]['level'] > $this->_cmw_lowest ) ){
						//convert relative to absolute...
						$absAncestors = $cmw['ancestors'] < 0 
							? max( 1, $this->_cmw_tree[ $theBranchItem ]['level'] + $cmw['ancestors'] )
							: $cmw['ancestors'];
						//convert relative to absolute...
						$absSiblings = $cmw['ancestor_siblings'] < 0
							? max( 1, $this->_cmw_tree[ $theBranchItem ]['level'] + $cmw['ancestor_siblings'] )
							: $cmw['ancestor_siblings'];
						foreach( $this->_cmw_tree[ $theBranchItem ]['ancestors'] as $itemID ){
							if( $itemID > 0
									&& $this->_cmw_tree[ $itemID ]['level'] >= $absAncestors
									&& $this->_cmw_tree[ $itemID ]['level'] <= $this->_cmw_lowest ){
								if( !$this->_cmw_tree[ $itemID ]['keep'] ){
									$this->_cmw_tree[ $itemID ]['keep'] = true;
									$this->_cmw_tree[ $itemID ]['classes'][] = 'cmw-an-included-ancestor';
									$this->_cmw_tree[0]['keepCount']++;
								}
								//only keep ancestor siblings if the ancestor itself is being kept
								if( $absSiblings > 0
										&& $this->_cmw_tree[ $itemID ]['level'] >= $absSiblings
										&& $this->_cmw_tree[ $itemID ]['keep'] ){
									$this->_cmw_include_siblings_of( $itemID, 'ancestor-sibling' );
								}
							}
						}
					}
					//branch siblings : only if the original branch item is being kept...
					if( $cmw['siblings'] && $this->_cmw_tree[ $theBranchItem ]['keep'] ){
						$this->_cmw_include_siblings_of( $theBranchItem );
					}
				}
				//include_level (replacement/extension of include_root, as of v3.0.4)...
				if( ( $fromTo = $this->_cmw_decipher_plusminus_level( $cmw['include_level'] ) ) !== false ){
					while( isset( $this->_cmw_levels[ $fromTo['from'] ] ) && $fromTo['from'] < $fromTo['to'] ){
						foreach( $this->_cmw_levels[ $fromTo['from'] ] as $itemID ){
							if( !$this->_cmw_tree[ $itemID ]['keep'] ){
								$this->_cmw_tree[ $itemID ]['keep'] = true;
								$this->_cmw_tree[ $itemID ]['classes'][] = 'cmw-an-included-level';
								$this->_cmw_tree[0]['keepCount']++;
							}
						}
						$fromTo['from']++;
					}
				}
			} //end INCLUSIONS

			//check for current item and switch...
			$hasCurrent = $currentItem !== false && $this->_cmw_tree[ $currentItem ]['keep'];
			if( $continue && $cmw['contains_current'] == 'inclusions' ){
				$continue = $hasCurrent;
			}
			if( $canSwitch && $this->_cmw_switch_settings( $args, 'inclusions', $hasCurrent, $continue ) ){
				continue;
			}

			//EXCLUSIONS...
			if( $continue){
				$continue = $this->_cmw_run_exclusions( $cmw );
			} //end EXCLUSIONS

			//check for current item and switch...
			$hasCurrent = $currentItem !== false && $this->_cmw_tree[ $currentItem ]['keep'];
			if( $continue && $cmw['contains_current'] == 'output' ){
				$continue = $hasCurrent;
			}
			if( $canSwitch && $this->_cmw_switch_settings( $args, 'output', $hasCurrent, $continue ) ){
				continue;
			}

			//check for title_from...
			if( $continue ){
				//might we want the (original) branch item's, or root item's, title as the widget title?...
				if( $find_branch && $theBranchItem > 0 ){
					$cmw['_walker']['branch_title'] = $elements[ $this->_cmw_tree[ $theBranchItem ]['element'][0] ]->title;
					if( $this->_cmw_tree[ $theBranchItem ]['level'] > 1 ){
						$topOfBranch = array_slice( $this->_cmw_tree[ $theBranchItem ]['ancestors'], 1, 1 );
						$topOfBranch = $topOfBranch[0];
						$cmw['_walker']['branch_root_title'] = $elements[ $this->_cmw_tree[ $topOfBranch ]['element'][0] ]->title;
					}else{
						$cmw['_walker']['branch_root_title'] = $cmw['_walker']['branch_title'];
					}
				}
				//might we want the current item's, or root item's, title as the widget title?...
				if( $currentItem !== false ){
					$cmw['_walker']['current_title'] = $elements[ $this->_cmw_tree[ $currentItem ]['element'][0] ]->title;
					if( $this->_cmw_tree[ $currentItem ]['level'] > 1 ){
						$topOfBranch = array_slice( $this->_cmw_tree[ $currentItem ]['ancestors'], 1, 1 );
						$topOfBranch = $topOfBranch[0];
						$cmw['_walker']['current_root_title'] = $elements[ $this->_cmw_tree[ $topOfBranch ]['element'][0] ]->title;
					}else{
						$cmw['_walker']['current_root_title'] = $cmw['_walker']['current_title'];
					}
				}
			}

			$this->_cmw_levels = null;
			$substructure = array();
			if( $continue ){
				//now we need to gather together all the 'keep' items from the tree;
				//while doing so, we need to set up levels and kids, ready for adding classes...
				foreach( $this->_cmw_tree as $k => $v ){
					if( $v['keep'] ){
						$substructure[ $k ] = $v;
						//use kids as a has-submenu flag...
						$substructure[ $k ]['kids'] = 0;
						//any surviving parent (except the artificial level-0) should have submenu class set on it...
						array_shift( $v['ancestors'] ); //remove the level-0
						for( $i = count( $v['ancestors'] ) - 1; $i >= 0; $i-- ){
							if( isset( $substructure[ $v['ancestors'][ $i ] ] ) ){
								$substructure[ $v['ancestors'][ $i ] ]['kids']++;
							}else{
								//not a 'kept' ancestor so remove it...
								array_splice( $v['ancestors'], $i, 1 );
							}
						}
						//ancestors now only has 'kept' ancestors...
						$substructure[ $k ]['level'] = count( $v['ancestors'] ) + 1;
						//need to ensure that the parent_field of all the new top-level (ie. root) items is set to
						//zero, otherwise the parent::walk() will assume they're orphans.
						//however, we also need to check that parent_field of a child actually points to the closest 
						//'kept' ancestor; otherwise, given A (kept) > B (not kept) > C (kept) the parent_field of C 
						//would point to a non-existent B and would subsequently be considered an orphan!
						if( $substructure[ $k ]['level'] == 1){
							$ancestor = 0;
						}else{
							//set to the closest ancestor, ie. the new(?) parent...
							$ancestor = array_slice( $v['ancestors'], -1, 1 );
							$ancestor = $ancestor[0];
						}
						//take a copy of the elements item(s)...
						$substructure[ $k ]['element'] = array();
						foreach( $v['element'] as $i => $j ){
							$elements[ $j ]->$parent_field = $ancestor;
							$substructure[ $k ]['element'][] = $elements[ $j ];
						}
					}
				}
			}
			$this->_cmw_tree = null;

			//put substructure's elements back into $elements (remember that $elements is a 1-based array!)...
			$elements = array();
			$n = 1;
			foreach( $substructure as $k => $v ){
				$ct = count( $v['element'] ) - 1;
				foreach( $v['element'] as $i => $j ){
					$elements[ $n ] = $j;
					//add the level class...
					$elements[ $n ]->classes[] = 'cmw-level-' . $v['level'];
					//add the submenu class? (only to last in group!)...
					if( $v['kids'] > 0 && $i == $ct ){
						$elements[ $n ]->classes[] = 'cmw-has-submenu';
					}else{
						//3.7 adds a menu-item-has-children class to (original) menu items that have kids : remove it as the item is now childless...
						$elements[ $n ]->classes = array_diff( $elements[ $n ]->classes, array('menu-item-has-children') );
					}
					//add any other CMW classes...
					$elements[ $n ]->classes = array_merge( $elements[ $n ]->classes, $v['classes'] );
					$n++;
				}
			}
			unset( $substructure );

			//kill the run count...
			$runCount--;

		} //end while

		return $elements;

	} //end _cmw_walk()

	/**
	 * pre-filters elements (v2.1.0)
	 * 
	 * @param {object} $args Params supplied to wp_nav_menu()
	 * @param {array} $elements Menu items
	 * @return {array} Modified menu items
	 */
	private function _cmw_walk_legacy( &$args, $elements ){

			//NB : $elements is an array of objects, indexed by position within the menu (menu_order),
			//starting at 1 and incrementing sequentially regardless of parentage (ie. first item is [1],
			//second item is [2] whether it's at root or subordinate to first item)

			$cmw =& $args->_custom_menu_wizard;

			$cmw['_walker']['fellback'] = false;

			$find_kids_of = $cmw['filter'] > 0;
			$find_specific_items = $cmw['filter'] < 0; //v2.0.0 //v2.0.1:bug fixed (changed < 1 to < 0)
			$find_current_item = $find_kids_of && empty( $cmw['filter_item'] );
			$find_current_parent = $find_kids_of && $cmw['filter_item'] == -1; //v1.1.0
			$find_current_root = $find_kids_of && $cmw['filter_item'] == -2; //v1.1.0
			$depth_rel_current = $cmw['depth_rel_current'] && $cmw['depth'] > 0; //v2.0.0
			//these could change depending on whether a fallback comes into play (v1.1.0)
			$include_parent = $cmw['include_parent'] || $cmw['include_ancestors'];
			$include_parent_siblings = $cmw['include_parent_siblings'];

			$id_field = $this->db_fields['id']; //eg. = 'db_id'
			$parent_field = $this->db_fields['parent']; //eg. = 'menu_item_parent'

			//find the current menu item while creating the tree and levels arrays...
			$currentItem = $this->_cmw_find_current_item( $elements, $cmw );

			$allLevels = 9999;
			$startWithKidsOf = -1;

			//no point doing much more if we need the current item and we haven't found it, or if we're looking for specific items with none given...
			$continue = true;
			if( empty( $currentItem ) && ( $find_current_item || $find_current_parent || $find_current_root || $cmw['contains_current'] ) ){
				$continue = false;
			}elseif( $find_specific_items && empty( $cmw['items'] ) ){
				$continue = false;
			}

			// IMPORTANT : as of v2.0.0, start level has been rationalised so that it acts the same across all filters (except for specific items!). 
			// Previously ...
			//   start level for a show-all filter literally started at the specified level and reported all levels until depth was reached.
			//   however, start level for a kids-of filter specified the level that the *immediate* kids of the selected filter had to be at
			//   or below. That was consistent for a specific item, current-item and current-parent filter, but for a current-root filter what
			//   it actually did was test the current item against the start level, not the current item's root ancestor! Inconsistent!
			//   But regardless of the current-root filter's use of start level, there was still the inconsistency between show-all and
			//   kids-of usage.
			// Now (as of v2.0.0) ...
			//   start level and depth have been changed to definitively be secondary filters to the show-all & kids-of primary filter.
			//   The primary filter - show-all, or a kids-of - will provide the initial set of items, and the secondary - start level & depth -
			//   will further refine that set, with start level being an absolute, and depth still being relative to the first item found.
			//   The sole exception to this is when Depth Relative to Current Menu Item is set, which modifies the calculation of depth (only)
			//   such that it becomes relative to the level at which the current menu item can be found (but only if it can be found at or
			//   below start level).
			// The effects of this change are that previously, filtering for kids of an item that was at level 2, with a start level of 4,
			// would fail to return any items because the immediate kids (at level 3) were outside the start level. Now, the returned items
			// will begin with the grand-kids (ie. those at level 4).
			// Note that neither start level nor depth are applicable to a specific items filter (also new at v2.0.0)!
			
			//the kids-of filters...
			if( $continue && $find_kids_of ){
				//specific item...
				if( $cmw['filter_item'] > 0 && isset( $this->_cmw_tree[ $cmw['filter_item'] ] ) && !empty( $this->_cmw_tree[ $cmw['filter_item'] ]['kids'] ) ){
					$startWithKidsOf = $cmw['filter_item'];
				}
				if( $find_current_item ){
					if( !empty( $this->_cmw_tree[ $currentItem ]['kids'] ) ){
						$startWithKidsOf = $currentItem;
					}elseif( $cmw['fallback_no_children'] ){
						//no kids,  and fallback to current parent is set...
						//note that there is no "double fallback", so current parent "can" be the artifical zero element (level-0) *if*
						//     the current item is a singleton( ie. no kids & no ancestors)!
						$ancestor = array_slice( $this->_cmw_tree[ $currentItem ]['ancestors'], -1, 1 );
						$startWithKidsOf = $ancestor[0]; //can be zero!
						$include_parent = $include_parent || $cmw['fallback_nc_include_parent'];
						$include_parent_siblings = $include_parent_siblings || $cmw['fallback_nc_include_parent_siblings'];
						$cmw['_walker']['fellback'] = 'to-parent';
					}
				}elseif( $find_current_parent || $find_current_root ){
					//as of v2.0.0 the fallback to current item - for current menu items at the top level - is deprecated, but
					//retained for a while to maintain backward compatibility
					//if no parent : fall back to current item (if set)...
					if( $this->_cmw_tree[ $currentItem ]['level'] == 1 && $cmw['fallback_no_ancestor'] ){
						$startWithKidsOf = $currentItem;
						$include_parent = $include_parent || $cmw['fallback_include_parent'];
						$include_parent_siblings = $include_parent_siblings || $cmw['fallback_include_parent_siblings'];
						$cmw['_walker']['fellback'] = 'to-current';
					}else{
						//as of v2.0.0, the artificial level-0 counts as parent of a top-level current menu item...
						if( $find_current_parent ){
							$ancestor = -1;
						}elseif( $this->_cmw_tree[ $currentItem ]['level'] > 1 ){
							$ancestor = 1;
						}else{
							$ancestor = 0;
						}
						$ancestor = array_slice( $this->_cmw_tree[ $currentItem ]['ancestors'], $ancestor, 1 );
						if( !empty( $ancestor ) ){
							$startWithKidsOf = $ancestor[0]; //as of v2.0.0, this can now be zero!
						}
					}
				}
			}

			if( $continue ){
				//right, let's set the keep flags
				//for specific items, go straight in on the item id (start level and depth do not apply here)...
				if( $find_specific_items ){
					foreach( preg_split('/[,\s]+/', $cmw['items'] ) as $itemID ){
						if( isset( $this->_cmw_tree[ $itemID ] ) ){
							$this->_cmw_tree[ $itemID ]['keep'] = true;
							$this->_cmw_tree[0]['keepCount']++;
						}
					}
				//for show-all filter, just use the levels...
				}elseif( !$find_kids_of ){
					//prior to v2.0.0, depth was always related to the first item found, and still is *unless* depth_rel_current is set
					if( $depth_rel_current && !empty( $currentItem ) && $this->_cmw_tree[ $currentItem ]['level'] >= $cmw['start_level'] ){
						$bottomLevel = $this->_cmw_tree[ $currentItem ]['level'] + $cmw['depth'] - 1;
					}else{
						$bottomLevel = $cmw['depth'] > 0 ? $cmw['start_level'] + $cmw['depth'] - 1 : $allLevels;
					}
					for( $i = $cmw['start_level']; isset( $this->_cmw_levels[ $i ] ) && $i <= $bottomLevel; $i++ ){
						foreach( $this->_cmw_levels[ $i ] as $itemID ){
							$this->_cmw_tree[ $itemID ]['keep'] = true;
							$this->_cmw_tree[0]['keepCount']++;
						}
					}
				//for kids-of filters, run a recursive through the structure's kids...
				}elseif( $startWithKidsOf > -1 ){
					//prior to v2.0.0, depth was always related to the first item found, and still is *unless* depth_rel_current is set
					//NB the in_array() of ancestors prevents depth_rel_current when startWithKidsOf == currentItem
					if( $depth_rel_current && !empty( $currentItem ) && $this->_cmw_tree[ $currentItem ]['level'] >= $cmw['start_level'] 
							&& in_array( $startWithKidsOf, $this->_cmw_tree[ $currentItem ]['ancestors'] ) ){
						$bottomLevel = $this->_cmw_tree[ $currentItem ]['level'] - 1 + $cmw['depth'];
					}else{
						$bottomLevel = $cmw['depth'] > 0 
							? max( $this->_cmw_tree[ $startWithKidsOf ]['level'] + $cmw['depth'], $cmw['start_level'] + $cmw['depth'] - 1 ) 
							: $allLevels;
					}
					//$this->_cmw_tree[0]['keepCount'] gets incremented in this recursive method...
					$this->_cmw_legacy_set_keep_kids( $startWithKidsOf, $cmw['start_level'], $bottomLevel );
				}
			
				if( $this->_cmw_tree[0]['keepCount'] > 0 ){
					//we have some items! we now may need to set some more keep flags, depending on the include settings...

					//do we need to include parent, parent siblings, and/or ancestors?...
					//NB these are not restricted by start_level!
					if( $find_kids_of && $startWithKidsOf > 0 ){
						if( $include_parent ){
							$this->_cmw_tree[ $startWithKidsOf ]['keep'] = true;
							$this->_cmw_tree[ $startWithKidsOf ]['classes'][] = 'cmw-the-included-parent';
						}
						if( $include_parent_siblings ){
							$ancestor = array_slice( $this->_cmw_tree[ $startWithKidsOf ]['ancestors'], -1, 1);
							foreach($this->_cmw_tree[ $ancestor[0] ]['kids'] as $itemID ){
								//may have already been kept by include_parent...
								if( !$this->_cmw_tree[ $itemID ]['keep'] ){
									$this->_cmw_tree[ $itemID ]['keep'] = true;
									$this->_cmw_tree[ $itemID ]['classes'][] = 'cmw-an-included-parent-sibling';
								}
							}
						}
						if( $cmw['include_ancestors'] ){
							foreach( $this->_cmw_tree[ $startWithKidsOf ]['ancestors'] as $itemID ){
								if( $itemID > 0 && !$this->_cmw_tree[ $itemID ]['keep'] ){
									$this->_cmw_tree[ $itemID ]['keep'] = true;
									$this->_cmw_tree[ $itemID ]['classes'][] = 'cmw-an-included-parent-ancestor';
								}
							}
						}
					}
				}
			}

			//check that (a) we have items, and (b) if we must have current menu item, we've got it...
			$continue = $this->_cmw_tree[0]['keepCount'] > 0 && ( !$cmw['contains_current'] || $this->_cmw_tree[ $currentItem ]['keep'] );
			//check for title_from...
			if( $continue ){
				//might we want the parent's title as the widget title?...
				if( $find_kids_of && $cmw['title_from_parent'] && $startWithKidsOf > 0 ){
					$cmw['_walker']['parent_title'] = apply_filters(
						'the_title',
						$elements[ $this->_cmw_tree[ $startWithKidsOf ]['element'][0] ]->title,
						$elements[ $this->_cmw_tree[ $startWithKidsOf ]['element'][0] ]->ID
						);
				}
				//might we want the current item's title as the widget title?...
				if( !empty( $currentItem ) && $cmw['title_from_current'] ){
					$cmw['_walker']['current_title'] = apply_filters(
						'the_title',
						$elements[ $this->_cmw_tree[ $currentItem ]['element'][0] ]->title,
						$elements[ $this->_cmw_tree[ $currentItem ]['element'][0] ]->ID
						);
				}
			}

			$this->_cmw_levels = null;
			$substructure = array();
			if( $continue ){
				//now we need to gather together all the 'keep' items from the tree;
				//while doing so, we need to set up levels and kids, ready for adding classes...
				foreach( $this->_cmw_tree as $k => $v ){
					if( $k > 0 && $v['keep'] ){
						$substructure[ $k ] = $v;
						//use kids as a has-submenu flag...
						$substructure[ $k ]['kids'] = 0;
						//any surviving parent (except the artificial level-0) should have submenu class set on it...
						array_shift( $v['ancestors'] ); //remove the level-0
						for( $i = count( $v['ancestors'] ) - 1; $i >= 0; $i-- ){
							if( isset( $substructure[ $v['ancestors'][ $i ] ] ) ){
								$substructure[ $v['ancestors'][ $i ] ]['kids']++;
							}else{
								//not a 'kept' ancestor so remove it...
								array_splice( $v['ancestors'], $i, 1 );
							}
						}
						//ancestors now only has 'kept' ancestors...
						$substructure[ $k ]['level'] = count( $v['ancestors'] ) + 1;
						//need to ensure that the parent_field of all the new top-level (ie. root) items is set to
						//zero, otherwise the parent::walk() will assume they're orphans.
						//however, we also need to check that parent_field of a child actually points to the closest 
						//'kept' ancestor; otherwise, given A (kept) > B (not kept) > C (kept) the parent_field of C 
						//would point to a non-existent B and would subsequently be considered an orphan!
						if( $substructure[ $k ]['level'] == 1){
							$ancestor = 0;
						}else{
							//set to the closest ancestor, ie. the new(?) parent...
							$ancestor = array_slice( $v['ancestors'], -1, 1 );
							$ancestor = $ancestor[0];
						}
						//take a copy of the elements item(s)...
						$substructure[ $k ]['element'] = array();
						foreach( $v['element'] as $i => $j ){
							$elements[ $j ]->$parent_field = $ancestor;
							$substructure[ $k ]['element'][] = $elements[ $j ];
						}
					}
				}
			}
			$this->_cmw_tree = null;

			//put substructure's elements back into $elements (remember that $elements is a 1-based array!)...
			$elements = array();
			$n = 1;
			foreach( $substructure as $k => $v ){
				$ct = count( $v['element'] ) - 1;
				foreach( $v['element'] as $i => $j ){
					$elements[ $n ] = $j;
					//add the level class...
					$elements[ $n ]->classes[] = 'cmw-level-' . $v['level'];
					//add the submenu class? (only to last in group!)...
					if( $v['kids'] > 0 && $i == $ct ){
						$elements[ $n ]->classes[] = 'cmw-has-submenu';
					}elseif( $v['kids'] == 0 ){
						//3.7 adds a menu-item-has-children class to (original) menu items that have kids : remove it as the item is now childless...
						$elements[ $n ]->classes = array_diff( $elements[ $n ]->classes, array('menu-item-has-children') );
					}
					//add any other CMW classes...
					$elements[ $n ]->classes = array_merge( $elements[ $n ]->classes, $v['classes'] );
					$n++;
				}
			}
			unset( $substructure );

		return $elements;

	} //end _cmw_walk_legacy()

} //end Custom_Menu_Wizard_Walker class
?>