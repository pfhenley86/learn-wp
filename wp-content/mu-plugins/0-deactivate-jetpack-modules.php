<?php
			// Disable Jetpack Protect 2FA for local auto-login purpose
			add_action( 'jetpack_active_modules', 'jetpack_deactivate_modules' );
			function jetpack_deactivate_modules( $active ) {
				if ( ( $index = array_search('protect', $active, true) ) !== false ) {
					unset( $active[ $index ] );
				}
				return $active;
			}
	