const defaultConfig = require('@wordpress/scripts/config/webpack.config');
module.exports = {
	...defaultConfig,
	entry: {
		'shadow-terms':
			'./src/block-editor/variations/shadow-terms',
		'subsites-query':
			'./src/block-editor/variations/subsites-query',
		'shadow-related-query':
			'./src/block-editor/variations/shadow-related-query',


		'premiere/premiere':
			'./src/block-editor/blocks/premiere',
		'duration/duration':
			'./src/block-editor/blocks/duration',
		'targetgroup/targetgroup':
			'./src/block-editor/blocks/targetgroup',


		'document-setting-panel':
			'./src/block-editor/plugins/document-setting-panel',
	},
};
