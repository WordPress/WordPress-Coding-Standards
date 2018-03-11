<?php

// @codingStandardsChangeSetting WordPress.CSRF.NonceVerification customNonceVerificationFunctions my_nonce_check
do_something( $_POST['test'] ); // Bad.

// @codingStandardsChangeSetting WordPress.CSRF.NonceVerification customNonceVerificationFunctions false
