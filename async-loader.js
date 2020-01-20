( function( scripts ) {

	document.scriptsLoaded = {};

	function injectScript( url, resolve, reject ) {

		var script = document.createElement( 'script' );
		script.src = url;

		script.onload = resolve;
		script.onerror = reject;
		document.body.appendChild( script );

	}

	function loadScriptAndThen( index ) {

		injectScript( scripts[index].url, function() {

			document.scriptsLoaded[ scripts[index].key ] = true;
			document.dispatchEvent( new Event( scripts[index].key + '-loaded' ) );

			if ( index < scripts.length - 1 ) {
				loadScriptAndThen( index + 1 );
			} else {
				document.dispatchEvent( new Event( 'async-scripts-loaded' ) );
			}

		} );

	}

	loadScriptAndThen( 0 );

} )( window.asyncScriptStack );