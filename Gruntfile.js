/* jshint node:true */
module.exports = function ( grunt ) {

	MessagesDirs = {
		"wikimania/scholarships": "data/i18n"
	};
	grunt.loadNpmTasks( 'grunt-contrib-jshint' );
	grunt.loadNpmTasks( 'grunt-jsonlint' );
	grunt.loadNpmTasks( 'grunt-banana-checker' );

	grunt.initConfig( {
		banana: MessagesDirs,
		jshint: {
			options: {
				jshintrc: true
			},
			all: '.',
		},
		jsonlint: {
			all: [
				'**/*.json',
				'!node_modules/**',
				'!vendor/**'
			]
		}
	} );

	grunt.registerTask( 'test', [ 'jsonlint', 'jshint', 'banana' ] );
	grunt.registerTask( 'default', 'test' );
};
