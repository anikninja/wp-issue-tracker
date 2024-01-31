/**
 * Static Assets Compiler
 *
 * @package WPIssueTracker
 * @version 1.0.0
 */
const webpack = require('webpack');
const path = require( 'path' );
const TerserPlugin = require( 'terser-webpack-plugin' )
/*eslint-disable */
const { join } = require( 'path' );

/**
 * Given a string, returns a new string with dash separators converted to
 * camel-case equivalent. This is not as aggressive as `_.camelCase`, which
 * which would also upper-case letters following numbers.
 *
 * @param {string} string Input dash-delimited string.
 *
 * @return {string} Camel-cased string.
 */
const camelCaseDash = string => string.replace( /-([a-z])/g, ( match, letter ) => letter.toUpperCase() );

/**
 * Define externals to load components through the wp global.
 */
const externals = {
	wp: 'wp',
	jquery: 'jQuery', // import $ from 'jquery' // Use the WordPress version after enqueuing it.
};
/*eslint-enable */

module.exports = ( env, argv ) => {
	const isDev = ( env.MODE === 'development' );
	return {
		entry: {
			scripts: [ './assets/src/js/scripts.js', './assets/src/scss/styles.scss' ],
			admin:   [ './assets/src/js/admin.js', './assets/src/scss/admin.scss' ],
			// styles: './src/scss/styles.scss',
			// admin: './src/scss/admin.scss',
		},
		devtool: isDev ? 'inline-sourcemap' : false,
		output: {
			// Add /* filename */ comments to generated require()s in the output.
			pathinfo: true,
			// [name] allows for the entry object keys to be used as file names.
			// filename: ( chunkData ) => {
			// 	return -1 === chunkData.chunk.name.indexOf( 'styles' ) ? 'js/[name].js' : 'css/[name].css';
			// },
			// filename: '[ext]/[name].[ext]',
			filename: 'js/[name].js',
			// Specify the path to the JS files.
			path: path.resolve( __dirname, 'assets' ),
			// path: path.resolve( __dirname ),
		},
		// Setup a loader to transpile down the latest and great JavaScript so older browsers can understand it.
		module: {
			rules: [
				{
					// Look for any .js files.
					test: /\.js$/,
					// Exclude the node_modules folder.
					exclude: /node_modules/,
					// Use babel loader to transpile the JS files.
					use: {
						loader: 'babel-loader',
						options: {
							"presets": [
								["@babel/preset-env", {"modules": false}]
							]
						}
					}
				},
				{
					test: /\.(sa|sc|c)ss$/,
					exclude: /node_modules/,
					use: [
						{
							loader: 'file-loader',
							options: {
								name: './css/[name].css',
							},
						},
						{ loader: 'extract-loader' },
						{ loader: 'css-loader?-url' },
						{ loader: 'postcss-loader' },
						{
							loader: 'sass-loader',
							options: {
								sourceMap: isDev,
								sassOptions: {
									outputStyle: 'compressed',
									includePaths: [
										require('path').resolve(__dirname, 'node_modules')
									],
								},
							}
						},
					],
				},
			],
		},
		optimization: {
			minimize: ! isDev,
			chunkIds: 'named',
			minimizer: [ new TerserPlugin( {
				extractComments: /^\**!|@preserve|@license|@cc_on/i,
			} ) ],
		},
		stats: 'verbose',
		// stats: 'errors-only',
		// Add externals.
		externals,
		plugins: [
			// Provides jQuery for other JS bundled with Webpack
			new webpack.ProvidePlugin({
				$: 'jquery',
				jQuery: 'jquery'
			})
		]
	};
};
