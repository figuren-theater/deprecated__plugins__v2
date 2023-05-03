/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';


import { 
	ToggleControl
} from '@wordpress/components';


/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	InspectorControls,
	useBlockProps,
} from '@wordpress/block-editor';


import ServerSideRender from '@wordpress/server-side-render';


/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
// import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit( {
	attributes: { showAll },
	setAttributes
})  {

	/**
	 * Prepare ServerSideRenderer
	 *
	 * makes use of the plugins native PHP shortcodes.
	 *
	 * @package isc
	 * @version 2022.05.24
	 * @author  Carsten Bach
	 *
	 * @return  ServerSideRender     Rendered output of our block
	 */
	const IscServerSideRender = () => (
	    <ServerSideRender
	        block="isc/image-source-control-isc-block"
	        attributes={ {
	        	showAll 
	        }}
	    />
	);



	return (
		<>
			<InspectorControls>
				<div className="block-editor-block-card" >
					<ToggleControl
						label={ __( 'Show sources of the whole website.', 'image-source-control-isc-block' ) }
						onChange={ ( newShowAll ) => setAttributes( { showAll: newShowAll } ) }
						checked={ showAll }
					/>
				</div>
			</InspectorControls>


			<div {...useBlockProps()}>
				<IscServerSideRender />
			</div>
		</>
	);
}
