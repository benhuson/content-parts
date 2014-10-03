/**
 * $Id: editor_plugin.js 001 2009-03-25
 *
 * @author Ben Huson
 * @copyright Copyright 2004-2008, Ben Huson, All rights reserved.
 */

( function() {

	tinymce.create( 'tinymce.plugins.ContentParts', {

		// Init.
		init : function( ed, url ) {

			var t = this, contentPartDividerHTML;

			t.editor = ed;

			// Register commands
			contentPartDividerHTML = '<img style="border-top: 1px dashed #888; width: 100%; height: 15px; margin: 10px 0; background-image: url( ' + url + '/img/content-part-divider.gif ); background-repeat: no-repeat; background-position: top right;" src="' + url + '/img/trans.gif" class="mceContentPartDivider mceItemNoResize" title="Content Part Divider" />';
			ed.addCommand( 'mceContentParts', function() {
				ed.execCommand( 'mceInsertContent', false, contentPartDividerHTML );
			} );

			// Register buttons
			ed.addButton( 'contentparts', {
				title : 'Content Parts',
				cmd   : 'mceContentParts',
				image : url + '/img/button.png'
			} );

			// Add listeners to handle more break
			t._handleContentPartDivider( ed, url );

		},

		// Get Info
		getInfo : function() {

			return {
				longname  : 'Content Parts',
				author    : 'Ben Huson',
				authorurl : 'http://www.benhuson.co.uk',
				infourl   : 'http://www.benhuson.co.uk',
				version   : tinymce.majorVersion + "." + tinymce.minorVersion
			};

		},

		_handleContentPartDivider : function( ed, url ) {

			var moreHTML, contentPartDividerHTML;

			contentPartDividerHTML = '<img style="border-top: 1px dashed #888; width: 100%; height: 15px; margin: 10px 0; background-image: url( ' + url + '/img/content-part-divider.gif ); background-repeat: no-repeat; background-position: top right;" src="' + url + '/img/trans.gif" class="mceContentPartDivider mceItemNoResize" title="Content Part Divider" />';

			// Display morebreak instead if img in element path
			ed.onPostRender.add( function() {
				if ( ed.theme.onResolveName ) {
					ed.theme.onResolveName.add( function( th, o ) {
						if ( o.node.nodeName == 'IMG' ) {
							if ( ed.dom.hasClass( o.node, 'mceContentPartDivider' ) ) {
								o.name = 'function';
							}
						}
					} );
				}
			} );

			// Replace morebreak with images
			ed.onBeforeSetContent.add( function( ed, o ) {
				o.content = o.content.replace( /<!--contentpartdivider-->/g, contentPartDividerHTML );
			} );

			// Replace images with morebreak
			ed.onPostProcess.add( function( ed, o ) {
				if ( o.get )
					o.content = o.content.replace( /<img[^>]+>/g, function( im ) {
						if ( im.indexOf( 'class="mceContentPartDivider' ) !== -1 ) {
							im = '<!--contentpartdivider-->';
						}
						return im;
					} );
			} );

			// Set active buttons if user selected pagebreak or more break
			ed.onNodeChange.add( function( ed, cm, n ) {
				cm.setActive( 'contentparts', n.nodeName === 'IMG' && ed.dom.hasClass( n, 'mceContentPartDivider' ) );
			} );

		}

	} );

	// Register plugin
	tinymce.PluginManager.add( 'contentparts', tinymce.plugins.ContentParts );

} )();
