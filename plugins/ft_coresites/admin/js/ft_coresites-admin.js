/**
 * Registers a new block provided a unique name and an object defining its behavior.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/#registering-a-block
 */
import { registerBlockVariation } from '@wordpress/blocks';

import { customPostType, globe } from '@wordpress/icons';

/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
// import './style.scss';



/**
 * 4. Add Custom Taxonomy Terms as Blocks
 */
registerBlockVariation(
	'core/post-terms',
	{
		name: 'ft_milestone',
		title: __('Milestone of a Feature','figurentheater'),
		description: __('Shows the ft_milestone taxonomy terms.','figurentheater'),
		icon: 'editor-ol-rtl',
		// icon: customPostType,
		isDefault: false,
		attributes: { term: 'ft_milestone', prefix: __('Milestone:','figurentheater') },
		isActive: ( blockAttributes ) => blockAttributes.term === 'ft_milestone',
	},
);

// TO DEPRECATE SOON
// keep till migrated 
// old connection from ft_SALES Plugin, 
// not used anymore in 2021 data-scheme
registerBlockVariation(
	'core/post-terms',
	{
		name: 'ft_product',
		title: __('Product of a Feature','figurentheater'),
		description: __('Shows the ft_product taxonomy terms.','figurentheater'),
		icon: customPostType,
		isDefault: false,
		attributes: { term: 'ft_product', /*prefix: __('','figurentheater')*/ },
		isActive: ( blockAttributes ) => blockAttributes.term === 'ft_product',
	},
);
registerBlockVariation(
	'core/post-terms',
	{
		name: 'ft_level_shadow',
		title: __('Level of a Feature','figurentheater'),
		description: __('Shows the ft_level_shadow taxonomy terms.','figurentheater'),
		icon: 'cart', // same as post_type
		isDefault: false,
		attributes: { term: 'ft_level_shadow', prefix: __('Level:','figurentheater') },
		isActive: ( blockAttributes ) => blockAttributes.term === 'ft_level_shadow',
	},
);
registerBlockVariation(
	'core/post-terms',
	{
		name: 'ft_feature_shadow',
		title: __('Related Features','figurentheater'),
		description: __('Lists the Feature(s) related to the current content.','figurentheater'),
		icon: 'forms', // same as post_type
		isDefault: false,
		attributes: { term: 'ft_feature_shadow' },
		isActive: ( blockAttributes ) => blockAttributes.term === 'ft_feature_shadow',
	},
);

// !!!
// 
// only load with feature 'ueberregionale-inhalte'
registerBlockVariation(
	'core/post-terms',
	{
		name: 'ft_geolocation',
		title: __('Geo Location','figurentheater'),
		description: __('Shows the ft_geolocation taxonomy terms.','figurentheater'),
		// icon: 'editor-ol-rtl',
		icon: globe,
		isDefault: false,
		attributes: { term: 'ft_geolocation' },
		isActive: ( blockAttributes ) => blockAttributes.term === 'ft_geolocation',
	},
);
