/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { _x } from '@wordpress/i18n';


/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
import { registerBlockType, createBlock } from '@wordpress/blocks';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
// import './style.scss';

/**
 * Internal dependencies
 */
import json from './block.json';
import edit from './edit';
import save from './save';
// import deprecated from './deprecations';

const {name, ...settings} = json;


/**
 * Every block starts by registering a new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-registration/
 */
registerBlockType(name, {
	...settings,
	title: _x('Image Sources','block title','image-source-control-isc-block'),
	description: _x('Show the Image-Sources managed via \'Image Source Control\'.','block description','image-source-control-isc-block'),

	/**
	 * @see ./edit.js
	 */
	edit,

	/**
	 * @see ./save.js
	 */
	save,
	// deprecated,
	//transforms: {
	//    from: [
	//        {
	//            type: 'block',
	//            blocks: [ 'core/shortcode' ],
	//            isMatch: ( { content } ) => {
	//                return ( '[isc_list_all]' === content )
	//            },
    //        	transform: ( { content } ) => {
	//                return createBlock( name, {
	//                    showAll:true
	//                } );
	//            },
	//        },
	//    ]
	//},
});
