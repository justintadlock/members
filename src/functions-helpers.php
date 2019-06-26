<?php

namespace Members;

use Members\Proxies\App;
use Members\Tools\Collection;

/**
 * The single instance of the app. Use this function for quickly working with
 * data.  Returns an instance of the `\Members\Core\Application` class. If the
 * `$abstract` parameter is passed in, it'll resolve and return the value from
 * the container.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $abstract
 * @param  array   $params
 * @return mixed
 */
function app( $abstract = '', $params = [] ) {

	return App::resolve( $abstract ?: 'app', $params );
}

/**
 * Wrapper function for the `Collection` class.
 *
 * @since  5.0.0
 * @access public
 * @param  array   $items
 * @return object
 */
function collect( $items = [] ) {

	return new Collection( $items );
}

/**
 * Returns the directory path of the plugin. If a file is passed in, it'll be
 * appended to the end of the path.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $file
 * @return string
 */
function path( $file = '' ) {

	$file = ltrim( $file, '/' );

	return $file ? App::resolve( 'path' ) . "/{$file}" : App::resolve( 'path' );
}

/**
 * Returns the directory path of the plugin. If a file is passed in, it'll be
 * appended to the end of the path.
 *
 * @since  5.0.0
 * @access public
 * @param  string  $file
 * @return string
 */
function uri( $file = '' ) {

	$file = ltrim( $file, '/' );

	return $file ? App::resolve( 'uri' ) . "/{$file}" : App::resolve( 'uri' );
}

/**
 * Returns the plugin version.
 *
 * @since  3.0.0
 * @access public
 * @return string
 */
function version() {

	return App::resolve( 'version' );
}
