<?php

namespace Members;

if ( file_exists( __DIR__ . '/../vendor/autoload.php' ) ) {
	require_once( __DIR__ . '/../vendor/autoload.php' );
}

array_map( function( $file ) {
	require_once( "{$file}.php" );
}, [
	'functions-helpers',
	'functions',
	'template'
] );
