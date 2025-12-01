<?php

/*
 * Safeguard correct handling of all types of namespaced function calls.
 */
\the_category_ID();
\THE_category_id();
MyNamespace\permalink_link();
\MyNamespace\get_postdata();
namespace\create_user(); // The sniff should start flagging this once it can resolve relative namespaces.
namespace\Sub\user_can_edit_post();
