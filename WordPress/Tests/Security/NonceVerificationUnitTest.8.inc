<?php

class IgnoreProperties {
	public $_GET = array( 'key' => 'something' ); // OK.
	public $_POST; // OK.
	public private(set) string $_REQUEST; // Ok.
}
