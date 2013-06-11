<?php

function my_template_tags( $post_ID, $args=null ) {} // Bad

function my_template_tags( $post_ID,$args = null ) {} // Bad

function my_template_tags( $post_ID, $args = null ) {} // Good

function my_template_tags( int $post_ID,string $args ) {} // Bad

function my_template_tags( int $post_ID, string $args ) {} // Good
