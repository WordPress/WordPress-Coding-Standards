<?php

extract( array( 'a' => 1 ) ); // Bad.

// Similarly named functions or methods however are fine.
my_extract(); // Ok.
My_Object::extract(); // Ok.
$this->extract(); // Ok.
$my_object->extract(); // Ok.
