/**
 * BLOCK: core-featuretable
 *
 * Registering a basic block with Gutenberg.
 *
 * 1. basically referred to the handbook,
 * @see https://developer.wordpress.org/block-editor/
 *
 * 2. the official examples
 * @see https://github.com/WordPress/gutenberg-examples/
 *
 * 3. and the source code
 * @see https://github.com/WordPress/gutenberg/tree/master/packages/block-library/src/
 *
 */

//  Import CSS.
//  importing thoose over here,
//  trigger the css-pre-processor to jump in, on save
import './editor.scss';
import './style.scss';

/**
 * WordPress dependencies
 */
const {registerBlockType} = wp.blocks; //Blocks API
const {
	createElement,
	useState
} = wp.element; //React.createElement
const {__} = wp.i18n; //translation functions
const {
	BlockControls,
	useBlockProps
} = wp.blockEditor; //Block inspector wrapper
const ServerSideRender = wp.serverSideRender; //WordPress server-side renderer
const {
	Disabled,
	Placeholder,
	SelectControl,
	Spinner,
	TextControl,
	ToolbarGroup
} = wp.components; //
const { withSelect, select, useSelect  } = wp.data;
//const icon = wp.components..megaphone;


/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'ft/block-core-featuretable', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'core-featuretable' ), // Block title.
	icon: 'megaphone', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'common', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'core-featuretable' ),
		__( 'CGB Example' ),
		__( 'create-guten-block' ),
	],


	/**
	 * Block Supports is the API that allows a block to declare features used in the editor.
	 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-supports/
	 * @type {Object}
	 */
	supports: {

		// Declare support for anchor links.
//	    anchor: true,

	    // Declare support for block's alignment.
	    // This adds support for all the options:
	    // left, center, right, wide, and full.
	//    align: true,
	    // Declare support for specific alignment options.
	    align: [ 'center', 'wide', 'full' ],



	    color: { // This also enables text and background UI controls.
	        gradients: true // Enable gradients UI control.
	    },



	    html: false,

	},




	attributes: {
		availableTerms: {
			type: 'object',
		},
		taxonomy: {
			type: 'string',
			default: 'category'
		},
		term: {
			type: 'string',
//			default: 'design'
		},
		align: {
			type: 'string',
//			default: 'wide'
		},
		className: {
			type: 'string',
		},
		// backgroundColor: {
		// 	type: 'string',
		// }

	},

	edit : ( { attributes, isSelected, setAttributes } ) => {

		const blockProps = useBlockProps();

		const {
			availableTerms,
			blockLayout,
			taxonomy,
			term
		} = attributes;

		const [ isEditing, setIsEditing ] = useState( ! term );

		/** copied from ...
		 *
		 *  [...] getEntityRecords() should be used with wp.data.select( 'core/data' ).isResolving()
		 *  https://wordpress.stackexchange.com/a/363379
		 */
		const { categories, isRequesting } = useSelect( ( select ) => {
			const { getEntityRecords } = select( 'core' );
			const { isResolving } = select( 'core/data' );
			const query = { per_page: -1, hide_empty: true };
			return {
				categories: getEntityRecords( 'taxonomy', 'category', query ),
				isRequesting: isResolving( 'core', 'getEntityRecords', [
					'taxonomy',
					'category',
					query,
				] ),
			};
		}, [] );
/*
		const getCategoryListClassName = ( level ) => {
			return `wp-block-categories__list wp-block-categories__list-level-${ level }`;
		};*/

		const getCategoriesList = ( ) => {
			if ( ! categories.length ) {
				return [];
			}
			return categories;
		};
		const renderCategoryName = ( name ) =>
			! name ? __( '(Untitled)' ) : unescape( name ).trim();


		/**
		 * Alternative: https://github.com/WordPress/gutenberg-examples/issues/34#issuecomment-437689749
		 */
		const renderCategoryDropdown = () => {
			const categoriesList = getCategoriesList();
			return (
					<SelectControl
						label={ __( 'Display features for category' ) }
			            options={ categoriesList.map(({id, name}) => ({label: name, value: id}))}
			            onChange={(selected) => {
			                // I haven't tested this code so I'm not sure what onChange returns.
			                // But assuming it returns an array of selected values:
			                setAttributes( {
			                	term: selected})
			            }}
			            value={ term }
					/>
			);
		};


//		if ( isEditing ) {
		if ( isSelected ) {
			return (
				<div { ...blockProps }>

					{ isRequesting && (
						<Placeholder icon="megaphone" label={ __( 'Features table' ) }>
							<Spinner />
						</Placeholder>
					) }
					{ ! isRequesting && categories.length === 0 && (
						<p>
							{ __(
								'Your site does not have any posts, so there is nothing to display here at the moment.'
							) }
						</p>
					) }
					{ ! isRequesting &&
						categories.length > 0 &&
						renderCategoryDropdown()
					}
				</div>
			);
		}




		return (
			<div { ...blockProps }>
				<Disabled>
					<ServerSideRender
						block="ft/block-core-featuretable"
						attributes={ attributes }
					/>
				</Disabled>
			</div>
		);

	}, //


	/**
	 * The save function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Frontend HTML.
	 */
	// We're going to be rendering in PHP, so save() can just return null.
	save: function() {
		return null;
	},
} );


// https://github.com/WordPress/gutenberg/issues/13811#issuecomment-500930584
wp.domReady( function() {

	// Before saving, get the generated className for this block and save it into attributes

	function saveClassNameToAttributes( props ) {
		return lodash.assign( props, { attributes: { className: props.className } } );
	}
	wp.hooks.addFilter(
		'blocks.getSaveContent.extraProps',
		'ft/block-core-featuretable',
		saveClassNameToAttributes
	);
} );
