<?php
/**
 * @version		$Id: facebookxmlns.php 1.0 $
 * @copyright	Copyright (C) 2005 - 2011 Itamar Elharar. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * plgSystemFacebookxmlns
 *
 * @package		Itamar.Facebook
 * @subpackage	system.xmlns
 * @since		1.5
 */
class plgSystemFacebookxmlns extends JPlugin
{


      function plgSystemFacebookxmlns( &$subject, $params )
      {
            parent::__construct( $subject, $params );
      }

      function onAfterRender()
      {

            $data = JResponse::getBody();
            $data=     str_replace('<html ', '<html xmlns:og="http://ogp.me/ns#" xmlns:fb="http://www.facebook.com/2008/fbml" ', $data );


            JResponse::setBody($data);
      }

}
