<?php
/**
 * WordPress Coding Standard.
 *
 * @package WPCS\WordPressCodingStandards
 * @link    https://github.com/WordPress/WordPress-Coding-Standards
 * @license https://opensource.org/licenses/MIT MIT
 */

namespace WordPressCS\WordPress\Sniffs\WP;

use WordPressCS\WordPress\AbstractClassRestrictionsSniff;

/**
 * Verify whether references to WP native classes use the proper casing for the class name.
 *
 * @since 3.0.0
 */
final class ClassNameCaseSniff extends AbstractClassRestrictionsSniff {

	/**
	 * List of all WP native classes.
	 *
	 * List is sorted alphabetically and based on a draft sniff to autogenerate this list.
	 *
	 * Note: this list will be enhanced in the class constructor.
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.3-RC1.}
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in their "proper" case.
	 *               The constructor will add the lowercased class name as a key to each entry.
	 */
	private $wp_classes = array(
		'_WP_Dependency',
		'_WP_Editors',
		'_WP_List_Table_Compat',
		'AtomEntry',
		'AtomFeed',
		'AtomParser',
		'Automatic_Upgrader_Skin',
		'Bulk_Plugin_Upgrader_Skin',
		'Bulk_Theme_Upgrader_Skin',
		'Bulk_Upgrader_Skin',
		'Core_Upgrader',
		'Custom_Background',
		'Custom_Image_Header',
		'Featured_Content',
		'File_Upload_Upgrader',
		'ftp',
		'ftp_base',
		'ftp_pure',
		'ftp_sockets',
		'Gettext_Translations',
		'IXR_Base64',
		'IXR_Client',
		'IXR_ClientMulticall',
		'IXR_Date',
		'IXR_Error',
		'IXR_IntrospectionServer',
		'IXR_Message',
		'IXR_Request',
		'IXR_Server',
		'IXR_Value',
		'Language_Pack_Upgrader',
		'Language_Pack_Upgrader_Skin',
		'MO',
		'MagpieRSS',
		'NOOP_Translations',
		'PO',
		'POMO_CachedFileReader',
		'POMO_CachedIntFileReader',
		'POMO_FileReader',
		'POMO_Reader',
		'POMO_StringReader',
		'POP3',
		'PasswordHash',
		'PclZip',
		'Plugin_Installer_Skin',
		'Plugin_Upgrader',
		'Plugin_Upgrader_Skin',
		'Plural_Forms',
		'RSSCache',
		'Services_JSON',
		'Services_JSON_Error',
		'Snoopy',
		'Text_Diff',
		'Text_Diff_Engine_native',
		'Text_Diff_Engine_shell',
		'Text_Diff_Engine_string',
		'Text_Diff_Engine_xdiff',
		'Text_Diff_Op',
		'Text_Diff_Op_add',
		'Text_Diff_Op_change',
		'Text_Diff_Op_copy',
		'Text_Diff_Op_delete',
		'Text_Diff_Renderer',
		'Text_Diff_Renderer_inline',
		'Text_MappedDiff',
		'Theme_Installer_Skin',
		'Theme_Upgrader',
		'Theme_Upgrader_Skin',
		'Translation_Entry',
		'Translations',
		'Walker',
		'Walker_Category',
		'Walker_CategoryDropdown',
		'Walker_Category_Checklist',
		'Walker_Comment',
		'Walker_Nav_Menu',
		'Walker_Nav_Menu_Checklist',
		'Walker_Nav_Menu_Edit',
		'Walker_Page',
		'Walker_PageDropdown',
		'WP',
		'WP_Admin_Bar',
		'WP_Ajax_Response',
		'WP_Ajax_Upgrader_Skin',
		'WP_Application_Passwords',
		'WP_Application_Passwords_List_Table',
		'WP_Automatic_Updater',
		'WP_Block',
		'WP_Block_Editor_Context',
		'WP_Block_List',
		'WP_Block_Parser',
		'WP_Block_Parser_Block',
		'WP_Block_Parser_Frame',
		'WP_Block_Pattern_Categories_Registry',
		'WP_Block_Patterns_Registry',
		'WP_Block_Styles_Registry',
		'WP_Block_Supports',
		'WP_Block_Template',
		'WP_Block_Type',
		'WP_Block_Type_Registry',
		'WP_Classic_To_Block_Menu_Converter',
		'WP_Comment',
		'WP_Comment_Query',
		'WP_Comments_List_Table',
		'WP_Community_Events',
		'WP_Customize_Background_Image_Control',
		'WP_Customize_Background_Image_Setting',
		'WP_Customize_Background_Position_Control',
		'WP_Customize_Code_Editor_Control',
		'WP_Customize_Color_Control',
		'WP_Customize_Control',
		'WP_Customize_Cropped_Image_Control',
		'WP_Customize_Custom_CSS_Setting',
		'WP_Customize_Date_Time_Control',
		'WP_Customize_Filter_Setting',
		'WP_Customize_Header_Image_Control',
		'WP_Customize_Header_Image_Setting',
		'WP_Customize_Image_Control',
		'WP_Customize_Manager',
		'WP_Customize_Media_Control',
		'WP_Customize_Nav_Menu_Auto_Add_Control',
		'WP_Customize_Nav_Menu_Control',
		'WP_Customize_Nav_Menu_Item_Control',
		'WP_Customize_Nav_Menu_Item_Setting',
		'WP_Customize_Nav_Menu_Location_Control',
		'WP_Customize_Nav_Menu_Locations_Control',
		'WP_Customize_Nav_Menu_Name_Control',
		'WP_Customize_Nav_Menu_Section',
		'WP_Customize_Nav_Menu_Setting',
		'WP_Customize_Nav_Menus',
		'WP_Customize_Nav_Menus_Panel',
		'WP_Customize_New_Menu_Control',
		'WP_Customize_New_Menu_Section',
		'WP_Customize_Panel',
		'WP_Customize_Partial',
		'WP_Customize_Section',
		'WP_Customize_Selective_Refresh',
		'WP_Customize_Setting',
		'WP_Customize_Sidebar_Section',
		'WP_Customize_Site_Icon_Control',
		'WP_Customize_Theme_Control',
		'WP_Customize_Themes_Panel',
		'WP_Customize_Themes_Section',
		'WP_Customize_Upload_Control',
		'WP_Customize_Widgets',
		'WP_Date_Query',
		'WP_Debug_Data',
		'WP_Dependencies',
		'WP_Duotone',
		'WP_Embed',
		'WP_Error',
		'WP_Fatal_Error_Handler',
		'WP_Feed_Cache',
		'WP_Feed_Cache_Transient',
		'WP_Filesystem_Base',
		'WP_Filesystem_Direct',
		'WP_Filesystem_FTPext',
		'WP_Filesystem_SSH2',
		'WP_Filesystem_ftpsockets',
		'WP_HTML_Attribute_Token',
		'WP_HTML_Span',
		'WP_HTML_Tag_Processor',
		'WP_HTML_Text_Replacement',
		'WP_HTTP_Fsockopen',
		'WP_HTTP_IXR_Client',
		'WP_HTTP_Proxy',
		'WP_HTTP_Requests_Hooks',
		'WP_HTTP_Requests_Response',
		'WP_HTTP_Response',
		'WP_Hook',
		'WP_Http',
		'WP_Http_Cookie',
		'WP_Http_Curl',
		'WP_Http_Encoding',
		'WP_Http_Streams',
		'WP_Image_Editor',
		'WP_Image_Editor_GD',
		'WP_Image_Editor_Imagick',
		'WP_Importer',
		'WP_Internal_Pointers',
		'WP_Links_List_Table',
		'WP_List_Table',
		'WP_List_Util',
		'WP_Locale',
		'WP_Locale_Switcher',
		'WP_MS_Sites_List_Table',
		'WP_MS_Themes_List_Table',
		'WP_MS_Users_List_Table',
		'WP_MatchesMapRegex',
		'WP_Media_List_Table',
		'WP_Meta_Query',
		'WP_Metadata_Lazyloader',
		'WP_Nav_Menu_Widget',
		'WP_Navigation_Fallback',
		'WP_Network',
		'WP_Network_Query',
		'WP_Object_Cache',
		'WP_Paused_Extensions_Storage',
		'WP_Plugin_Install_List_Table',
		'WP_Plugins_List_Table',
		'WP_Post',
		'WP_Post_Comments_List_Table',
		'WP_Post_Type',
		'WP_Posts_List_Table',
		'WP_Privacy_Data_Export_Requests_List_Table',
		'WP_Privacy_Data_Export_Requests_Table',
		'WP_Privacy_Data_Removal_Requests_List_Table',
		'WP_Privacy_Data_Removal_Requests_Table',
		'WP_Privacy_Policy_Content',
		'WP_Privacy_Requests_Table',
		'WP_Query',
		'WP_REST_Application_Passwords_Controller',
		'WP_REST_Attachments_Controller',
		'WP_REST_Autosaves_Controller',
		'WP_REST_Block_Directory_Controller',
		'WP_REST_Block_Pattern_Categories_Controller',
		'WP_REST_Block_Patterns_Controller',
		'WP_REST_Block_Renderer_Controller',
		'WP_REST_Block_Types_Controller',
		'WP_REST_Blocks_Controller',
		'WP_REST_Comment_Meta_Fields',
		'WP_REST_Comments_Controller',
		'WP_REST_Controller',
		'WP_REST_Edit_Site_Export_Controller',
		'WP_REST_Global_Styles_Controller',
		'WP_REST_Global_Styles_Revisions_Controller',
		'WP_REST_Menu_Items_Controller',
		'WP_REST_Menu_Locations_Controller',
		'WP_REST_Menus_Controller',
		'WP_REST_Meta_Fields',
		'WP_REST_Navigation_Fallback_Controller',
		'WP_REST_Pattern_Directory_Controller',
		'WP_REST_Plugins_Controller',
		'WP_REST_Post_Format_Search_Handler',
		'WP_REST_Post_Meta_Fields',
		'WP_REST_Post_Search_Handler',
		'WP_REST_Post_Statuses_Controller',
		'WP_REST_Post_Types_Controller',
		'WP_REST_Posts_Controller',
		'WP_REST_Request',
		'WP_REST_Response',
		'WP_REST_Revisions_Controller',
		'WP_REST_Search_Controller',
		'WP_REST_Search_Handler',
		'WP_REST_Server',
		'WP_REST_Settings_Controller',
		'WP_REST_Sidebars_Controller',
		'WP_REST_Site_Health_Controller',
		'WP_REST_Taxonomies_Controller',
		'WP_REST_Templates_Controller',
		'WP_REST_Term_Meta_Fields',
		'WP_REST_Term_Search_Handler',
		'WP_REST_Terms_Controller',
		'WP_REST_Themes_Controller',
		'WP_REST_URL_Details_Controller',
		'WP_REST_User_Meta_Fields',
		'WP_REST_Users_Controller',
		'WP_REST_Widget_Types_Controller',
		'WP_REST_Widgets_Controller',
		'WP_Recovery_Mode',
		'WP_Recovery_Mode_Cookie_Service',
		'WP_Recovery_Mode_Email_Service',
		'WP_Recovery_Mode_Key_Service',
		'WP_Recovery_Mode_Link_Service',
		'WP_Rewrite',
		'WP_Role',
		'WP_Roles',
		'WP_Screen',
		'WP_Scripts',
		'WP_Session_Tokens',
		'WP_Sidebar_Block_Editor_Control',
		'WP_SimplePie_File',
		'WP_SimplePie_Sanitize_KSES',
		'WP_Site',
		'WP_Site_Health',
		'WP_Site_Health_Auto_Updates',
		'WP_Site_Icon',
		'WP_Site_Query',
		'WP_Sitemaps',
		'WP_Sitemaps_Index',
		'WP_Sitemaps_Posts',
		'WP_Sitemaps_Provider',
		'WP_Sitemaps_Registry',
		'WP_Sitemaps_Renderer',
		'WP_Sitemaps_Stylesheet',
		'WP_Sitemaps_Taxonomies',
		'WP_Sitemaps_Users',
		'WP_Style_Engine',
		'WP_Style_Engine_CSS_Declarations',
		'WP_Style_Engine_CSS_Rule',
		'WP_Style_Engine_CSS_Rules_Store',
		'WP_Style_Engine_Processor',
		'WP_Styles',
		'WP_Tax_Query',
		'WP_Taxonomy',
		'WP_Term',
		'WP_Term_Query',
		'WP_Terms_List_Table',
		'WP_Text_Diff_Renderer_Table',
		'WP_Text_Diff_Renderer_inline',
		'WP_Textdomain_Registry',
		'WP_Theme',
		'WP_Theme_Install_List_Table',
		'WP_Theme_JSON',
		'WP_Theme_JSON_Data',
		'WP_Theme_JSON_Resolver',
		'WP_Theme_JSON_Schema',
		'WP_Themes_List_Table',
		'WP_Upgrader',
		'WP_Upgrader_Skin',
		'WP_User',
		'WP_User_Meta_Session_Tokens',
		'WP_User_Query',
		'WP_User_Request',
		'WP_User_Search',
		'WP_Users_List_Table',
		'WP_Widget',
		'WP_Widget_Archives',
		'WP_Widget_Area_Customize_Control',
		'WP_Widget_Block',
		'WP_Widget_Calendar',
		'WP_Widget_Categories',
		'WP_Widget_Custom_HTML',
		'WP_Widget_Factory',
		'WP_Widget_Form_Customize_Control',
		'WP_Widget_Links',
		'WP_Widget_Media',
		'WP_Widget_Media_Audio',
		'WP_Widget_Media_Gallery',
		'WP_Widget_Media_Image',
		'WP_Widget_Media_Video',
		'WP_Widget_Meta',
		'WP_Widget_Pages',
		'WP_Widget_RSS',
		'WP_Widget_Recent_Comments',
		'WP_Widget_Recent_Posts',
		'WP_Widget_Search',
		'WP_Widget_Tag_Cloud',
		'WP_Widget_Text',
		'WP_oEmbed',
		'WP_oEmbed_Controller',
		'wp_atom_server',
		'wp_xmlrpc_server',
		'wpdb',
	);

	/**
	 * List of all WP native classes as shipped with themes included in WP Core.
	 *
	 * Note: this list will be enhanced in the class constructor.
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.3-RC1.}
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in their "proper" case.
	 *               The constructor will add the lowercased class name as a key to each entry.
	 */
	private $wp_themes_classes = array(
		'TwentyNineteen_SVG_Icons',
		'TwentyNineteen_Walker_Comment',
		'TwentyTwenty_Customize',
		'TwentyTwenty_Non_Latin_Languages',
		'TwentyTwenty_SVG_Icons',
		'TwentyTwenty_Script_Loader',
		'TwentyTwenty_Separator_Control',
		'TwentyTwenty_Walker_Comment',
		'TwentyTwenty_Walker_Page',
		'Twenty_Eleven_Ephemera_Widget',
		'Twenty_Fourteen_Ephemera_Widget',
		'Twenty_Twenty_One_Custom_Colors',
		'Twenty_Twenty_One_Customize',
		'Twenty_Twenty_One_Customize_Color_Control',
		'Twenty_Twenty_One_Customize_Notice_Control',
		'Twenty_Twenty_One_Dark_Mode',
		'Twenty_Twenty_One_SVG_Icons',
	);

	/**
	 * List of all GetID3 classes include in WP Core.
	 *
	 * Note: this list will be enhanced in the class constructor.
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.3-RC1.}
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in their "proper" case.
	 *               The constructor will add the lowercased class name as a key to each entry.
	 */
	private $getid3_classes = array(
		'AMFReader',
		'AMFStream',
		'AVCSequenceParameterSetReader',
		'getID3',
		'getid3_ac3',
		'getid3_apetag',
		'getid3_asf',
		'getid3_dts',
		'getid3_exception',
		'getid3_flac',
		'getid3_flv',
		'getid3_handler',
		'getid3_id3v1',
		'getid3_id3v2',
		'getid3_lib',
		'getid3_lyrics3',
		'getid3_matroska',
		'getid3_mp3',
		'getid3_ogg',
		'getid3_quicktime',
		'getid3_riff',
	);

	/**
	 * List of all PHPMailer classes included in WP Core.
	 *
	 * Note: this list will be enhanced in the class constructor.
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.3-RC1.}
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in their "proper" case.
	 *               The constructor will add the lowercased class name as a key to each entry.
	 */
	private $phpmailer_classes = array(
		'PHPMailer\\PHPMailer\\Exception',
		'PHPMailer\\PHPMailer\\PHPMailer',
		'PHPMailer\\PHPMailer\\SMTP',
	);

	/**
	 * List of all Requests classes included in WP Core.
	 *
	 * Note: this list will be enhanced in the class constructor.
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.3-RC1.}
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in their "proper" case.
	 *               The constructor will add the lowercased class name as a key to each entry.
	 */
	private $requests_classes = array(
		// Interfaces, Requests v1.
		'Requests_Auth',
		'Requests_Hooker',
		'Requests_Proxy',
		'Requests_Transport',

		// Interfaces, Requests v2.
		'WpOrg\\Requests\\Auth',
		'WpOrg\\Requests\\Capability',
		'WpOrg\\Requests\\HookManager',
		'WpOrg\\Requests\\Proxy',
		'WpOrg\\Requests\\Transport',

		// Classes, Requests v1.
		'Requests',
		'Requests_Auth_Basic',
		'Requests_Cookie',
		'Requests_Cookie_Jar',
		'Requests_Exception',
		'Requests_Exception_HTTP',
		'Requests_Exception_Transport',
		'Requests_Exception_Transport_cURL',
		'Requests_Exception_HTTP_304',
		'Requests_Exception_HTTP_305',
		'Requests_Exception_HTTP_306',
		'Requests_Exception_HTTP_400',
		'Requests_Exception_HTTP_401',
		'Requests_Exception_HTTP_402',
		'Requests_Exception_HTTP_403',
		'Requests_Exception_HTTP_404',
		'Requests_Exception_HTTP_405',
		'Requests_Exception_HTTP_406',
		'Requests_Exception_HTTP_407',
		'Requests_Exception_HTTP_408',
		'Requests_Exception_HTTP_409',
		'Requests_Exception_HTTP_410',
		'Requests_Exception_HTTP_411',
		'Requests_Exception_HTTP_412',
		'Requests_Exception_HTTP_413',
		'Requests_Exception_HTTP_414',
		'Requests_Exception_HTTP_415',
		'Requests_Exception_HTTP_416',
		'Requests_Exception_HTTP_417',
		'Requests_Exception_HTTP_418',
		'Requests_Exception_HTTP_428',
		'Requests_Exception_HTTP_429',
		'Requests_Exception_HTTP_431',
		'Requests_Exception_HTTP_500',
		'Requests_Exception_HTTP_501',
		'Requests_Exception_HTTP_502',
		'Requests_Exception_HTTP_503',
		'Requests_Exception_HTTP_504',
		'Requests_Exception_HTTP_505',
		'Requests_Exception_HTTP_511',
		'Requests_Exception_HTTP_Unknown',
		'Requests_Hooks',
		'Requests_IDNAEncoder',
		'Requests_IPv6',
		'Requests_IRI',
		'Requests_Proxy_HTTP',
		'Requests_Response',
		'Requests_Response_Headers',
		'Requests_Session',
		'Requests_SSL',
		'Requests_Transport_cURL',
		'Requests_Transport_fsockopen',
		'Requests_Utility_CaseInsensitiveDictionary',
		'Requests_Utility_FilteredIterator',

		// Classes, Requests v2.
		'WpOrg\Requests\Auth\Basic',
		'WpOrg\Requests\Autoload',
		'WpOrg\Requests\Cookie',
		'WpOrg\Requests\Cookie\Jar',
		'WpOrg\Requests\Exception',
		'WpOrg\Requests\Exception\ArgumentCount',
		'WpOrg\Requests\Exception\Http',
		'WpOrg\Requests\Exception\Http\Status304',
		'WpOrg\Requests\Exception\Http\Status305',
		'WpOrg\Requests\Exception\Http\Status306',
		'WpOrg\Requests\Exception\Http\Status400',
		'WpOrg\Requests\Exception\Http\Status401',
		'WpOrg\Requests\Exception\Http\Status402',
		'WpOrg\Requests\Exception\Http\Status403',
		'WpOrg\Requests\Exception\Http\Status404',
		'WpOrg\Requests\Exception\Http\Status405',
		'WpOrg\Requests\Exception\Http\Status406',
		'WpOrg\Requests\Exception\Http\Status407',
		'WpOrg\Requests\Exception\Http\Status408',
		'WpOrg\Requests\Exception\Http\Status409',
		'WpOrg\Requests\Exception\Http\Status410',
		'WpOrg\Requests\Exception\Http\Status411',
		'WpOrg\Requests\Exception\Http\Status412',
		'WpOrg\Requests\Exception\Http\Status413',
		'WpOrg\Requests\Exception\Http\Status414',
		'WpOrg\Requests\Exception\Http\Status415',
		'WpOrg\Requests\Exception\Http\Status416',
		'WpOrg\Requests\Exception\Http\Status417',
		'WpOrg\Requests\Exception\Http\Status418',
		'WpOrg\Requests\Exception\Http\Status428',
		'WpOrg\Requests\Exception\Http\Status429',
		'WpOrg\Requests\Exception\Http\Status431',
		'WpOrg\Requests\Exception\Http\Status500',
		'WpOrg\Requests\Exception\Http\Status501',
		'WpOrg\Requests\Exception\Http\Status502',
		'WpOrg\Requests\Exception\Http\Status503',
		'WpOrg\Requests\Exception\Http\Status504',
		'WpOrg\Requests\Exception\Http\Status505',
		'WpOrg\Requests\Exception\Http\Status511',
		'WpOrg\Requests\Exception\Http\StatusUnknown',
		'WpOrg\Requests\Exception\InvalidArgument',
		'WpOrg\Requests\Exception\Transport',
		'WpOrg\Requests\Exception\Transport\Curl',
		'WpOrg\Requests\Hooks',
		'WpOrg\Requests\IdnaEncoder',
		'WpOrg\Requests\Ipv6',
		'WpOrg\Requests\Iri',
		'WpOrg\Requests\Port',
		'WpOrg\Requests\Proxy\Http',
		'WpOrg\Requests\Requests',
		'WpOrg\Requests\Response',
		'WpOrg\Requests\Response\Headers',
		'WpOrg\Requests\Session',
		'WpOrg\Requests\Ssl',
		'WpOrg\Requests\Transport\Curl',
		'WpOrg\Requests\Transport\Fsockopen',
		'WpOrg\Requests\Utility\CaseInsensitiveDictionary',
		'WpOrg\Requests\Utility\FilteredIterator',
		'WpOrg\Requests\Utility\InputValidator',
	);

	/**
	 * List of all SimplePie classes included in WP Core.
	 *
	 * Note: this list will be enhanced in the class constructor.
	 *
	 * {@internal To be updated after every major release. Last updated for WordPress 6.3-RC1.}
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in their "proper" case.
	 *               The constructor will add the lowercased class name as a key to each entry.
	 */
	private $simplepie_classes = array(
		// Interfaces.
		'SimplePie_Cache_Base',

		// Classes.
		'SimplePie',
		'SimplePie_Author',
		'SimplePie_Cache',
		'SimplePie_Cache_DB',
		'SimplePie_Cache_File',
		'SimplePie_Cache_Memcache',
		'SimplePie_Cache_Memcached',
		'SimplePie_Cache_MySQL',
		'SimplePie_Cache_Redis',
		'SimplePie_Caption',
		'SimplePie_Category',
		'SimplePie_Content_Type_Sniffer',
		'SimplePie_Copyright',
		'SimplePie_Core',
		'SimplePie_Credit',
		'SimplePie_Decode_HTML_Entities',
		'SimplePie_Enclosure',
		'SimplePie_Exception',
		'SimplePie_File',
		'SimplePie_HTTP_Parser',
		'SimplePie_IRI',
		'SimplePie_Item',
		'SimplePie_Locator',
		'SimplePie_Misc',
		'SimplePie_Net_IPv6',
		'SimplePie_Parse_Date',
		'SimplePie_Parser',
		'SimplePie_Rating',
		'SimplePie_Registry',
		'SimplePie_Restriction',
		'SimplePie_Sanitize',
		'SimplePie_Source',
		'SimplePie_XML_Declaration_Parser',
		'SimplePie_gzdecode',
	);

	/**
	 * List of all WP native classes in lowercase.
	 *
	 * This array is automatically generated in the class constructor based on the $wp_classes property.
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in lowercase.
	 */
	private $wp_classes_lc = array();

	/**
	 * List of all WP native classes as shipped with themes in lowercase.
	 *
	 * This array is automatically generated in the class constructor based on the $wp_themes_classes property.
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in lowercase.
	 */
	private $wp_themes_classes_lc = array();

	/**
	 * List of all GetID3 classes in lowercase.
	 *
	 * This array is automatically generated in the class constructor based on the $phpmailer_classes property.
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in lowercase.
	 */
	private $getid3_classes_lc = array();

	/**
	 * List of all PHPMailer classes in lowercase.
	 *
	 * This array is automatically generated in the class constructor based on the $phpmailer_classes property.
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in lowercase.
	 */
	private $phpmailer_classes_lc = array();

	/**
	 * List of all Requests classes in lowercase.
	 *
	 * This array is automatically generated in the class constructor based on the $requests_classes property.
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in lowercase.
	 */
	private $requests_classes_lc = array();

	/**
	 * List of all SimplePie classes in lowercase.
	 *
	 * This array is automatically generated in the class constructor based on the $simplepie_classes property.
	 *
	 * @since 3.0.0
	 *
	 * @var string[] The class names in lowercase.
	 */
	private $simplepie_classes_lc = array();

	/**
	 * Groups names.
	 *
	 * Used to dynamically fill in some of the above properties and to generate the getGroups() array.
	 *
	 * @since 3.0.0
	 *
	 * @var array
	 */
	private $class_groups = array(
		'wp_classes',
		'wp_themes_classes',
		'getid3_classes',
		'phpmailer_classes',
		'requests_classes',
		'simplepie_classes',
	);

	/**
	 * Constructor.
	 *
	 * @since 3.0.0
	 */
	public function __construct() {
		// Adjust the class list properties to have the lowercased version of the value as a key.
		foreach ( $this->class_groups as $name ) {
			$name_lc        = $name . '_lc';
			$this->$name_lc = array_map( 'strtolower', $this->$name );
			$this->$name    = array_combine( $this->$name_lc, $this->$name );
		}
	}

	/**
	 * Groups of classes to restrict.
	 *
	 * @since 3.0.0
	 *
	 * @return array
	 */
	public function getGroups() {
		$groups = array();
		foreach ( $this->class_groups as $name ) {
			$name_lc         = $name . '_lc';
			$groups[ $name ] = array(
				'classes' => $this->$name_lc,
			);
		}

		return $groups;
	}

	/**
	 * Process a matched token.
	 *
	 * @since 3.0.0
	 *
	 * @param int    $stackPtr        The position of the current token in the stack.
	 * @param string $group_name      The name of the group which was matched. Will
	 *                                always be 'wp_classes'.
	 * @param string $matched_content The token content (class name) which was matched.
	 *                                in its original case.
	 *
	 * @return void
	 */
	public function process_matched_token( $stackPtr, $group_name, $matched_content ) {

		$matched_unqualified = ltrim( $matched_content, '\\' );
		$matched_lowercase   = strtolower( $matched_unqualified );
		$matched_proper_case = $this->get_proper_case( $matched_lowercase );

		if ( $matched_unqualified === $matched_proper_case ) {
			// Already using proper case, nothing to do.
			return;
		}

		$warning = 'It is strongly recommended to refer to classes by their properly cased name. Expected: %s Found: %s';
		$data    = array(
			$matched_proper_case,
			$matched_unqualified,
		);

		$this->phpcsFile->addWarning( $warning, $stackPtr, 'Incorrect', $data );
	}

	/**
	 * Match a lowercase class name to its proper cased name.
	 *
	 * @since 3.0.0
	 *
	 * @param string $matched_lc Lowercase class name.
	 *
	 * @return string
	 */
	private function get_proper_case( $matched_lc ) {
		foreach ( $this->class_groups as $name ) {
			$current = $this->$name; // Needed to prevent issues with PHP < 7.0.
			if ( isset( $current[ $matched_lc ] ) ) {
				return $current[ $matched_lc ];
			}
		}

		// Shouldn't be possible.
		return ''; // @codeCoverageIgnore
	}
}
