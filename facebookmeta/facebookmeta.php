<?php
/**
 * @version		$Id: plgContentFacebookmeta.php 1.0 $
 * @copyright	Copyright (C) 2005 - 2011 Itamar Elharar. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * plgContentFacebookmeta
 *
 * @package		Itamar.Facebook
 * @subpackage	system.xmlns
 * @since		1.5
 */
class plgContentFacebookmeta extends JPlugin
{


      function plgContentFacebookmeta( &$subject, $params )
      {
            parent::__construct( $subject, $params );
      }
    

      /**
       *  before display content method
       *
       * Method is called by the view and the results are imploded and displayed in a placeholder
       *
       * @param	string		The context for the content passed to the plugin.
       * @param	object		The content object.  Note $article->text is also available
       * @param	object		The content params
       * @param	int			The 'page' number
       * @return	string
       * @since	1.6
       */
      public function onContentBeforeDisplay($context, &$article, &$params, $limitstart)
      {
            if($option = JRequest::getCmd('option') != "com_content")
            {                   return;             }


            switch( JRequest::getCmd('view') )
            {
                  case 'category' :        $this->addCategoryMeta($article);                                  break;
                  case 'article' :
                        defualt;            $this->addContentMeta($article);                                  break;
            }


            $this->addGeneralMeta();


            return '';
      }


      private function addCategoryMeta($article)
      {

            $db =& JFactory::getDbo();
            $query = "SELECT * FROM #__categories WHERE id=$article->catid";
            $db->setQuery($query);

            $cat = $db->loadObjectList();
            $cat = $cat[0];

            $document =& JFactory::getDocument();

            //get & set url
            $url = JRoute::_(JURI::base().ContentHelperRoute::getCategoryRoute($cat->catid));
            $document->setMetaData( 'og:url', $url) ;

            //get & set image
            $jsondata = json_decode($cat->params);
            $src =$jsondata->image;
            if($src == '')
            {
               $src = 'images/'.$this->params->get('default_image');
            }
            $document->setMetaData( 'og:image',  htmlspecialchars(JURI::base(). $src ) );

            //set data
            $document->setMetaData( 'og:title', $cat->title );
            $document->setMetaData( 'og:type' ,"content" );
      }

      private function addContentMeta($article)
      {
            $document =& JFactory::getDocument();
            //make url
            $url = JRoute::_(JURI::base().ContentHelperRoute::getArticleRoute($article->id.':'.$article->alias, $article->catid));
            //make image
            preg_match('@<img.+src="(.*)".*>@Uims', $article->introtext.$article->fulltext, $matches);
            $src = $matches[1];
            if($src == '')
            {
                  $src = 'images/'.$this->params->get('default_image');
            }
            
            //set data
            $document->setMetaData( 'og:title', $article->title );
            $document->setMetaData( 'og:type' ,"content" );
            $document->setMetaData( 'og:url', $url) ;

            $document->setMetaData( 'og:image',  htmlspecialchars(JURI::base().$src ) );


      }

      private function addGeneralMeta()
      {
            $document =& JFactory::getDocument();
            $config =& JFactory::getConfig();
            //set data
            $document->setMetaData( 'og:site_name',  $config->getValue( 'config.sitename' ) );
            $document->setMetaData( 'fb:admins', $this->params->get('admins') );
            $document->setMetaData( 'fb:app_id', $this->params->get('app_id')  );
      }
}
