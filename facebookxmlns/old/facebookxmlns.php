<?php
/**
 * @version		$Id: example.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * Example Content Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	Content.example
 * @since		1.5
 */
class plgContentFacebookmeta extends JPlugin
{


      function plgContentFacebookmeta( &$subject, $params )
      {
            parent::__construct( $subject, $params );
      }
      /**
       * Example after delete method.
       *
       * @param	string	The context for the content passed to the plugin.
       * @param	object	The data relating to the content that was deleted.
       * @return	boolean
       * @since	1.6
       */
      public function onContentAfterDelete($context, $data)
      {

            return true;
      }

      /**
       * Example after display content method
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
      public function onContentAfterDisplay($context, &$article, &$params, $limitstart)
      {
            $app = JFactory::getApplication();

            return '';
      }

      /**
       * Example after save content method
       * Article is passed by reference, but after the save, so no changes will be saved.
       * Method is called right after the content is saved
       *
       * @param	string		The context of the content passed to the plugin (added in 1.6)
       * @param	object		A JTableContent object
       * @param	bool		If the content is just about to be created
       * @since	1.6
       */
      public function onContentAfterSave($context, &$article, $isNew)
      {
            $app = JFactory::getApplication();

            return true;
      }

      /**
       * Example after display title method
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
      public function onContentAfterTitle($context, &$article, &$params, $limitstart)
      {
            $app = JFactory::getApplication();

            return '';
      }

      /**
       * Example before delete method.
       *
       * @param	string	The context for the content passed to the plugin.
       * @param	object	The data relating to the content that is to be deleted.
       * @return	boolean
       * @since	1.6
       */
      public function onContentBeforeDelete($context, $data)
      {
            return true;
      }

      /**
       * Example before display content method
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





            return '';
      }

      /**
       * Example before save content method
       *
       * Method is called right before content is saved into the database.
       * Article object is passed by reference, so any changes will be saved!
       * NOTE:  Returning false will abort the save with an error.
       *You can set the error by calling $article->setError($message)
       *
       * @param	string		The context of the content passed to the plugin.
       * @param	object		A JTableContent object
       * @param	bool		If the content is just about to be created
       * @return	bool		If false, abort the save
       * @since	1.6
       */
      public function onContentBeforeSave($context, &$article, $isNew)
      {
            $app = JFactory::getApplication();

            return true;
      }

      /**
       * Example after delete method.
       *
       * @param	string	The context for the content passed to the plugin.
       * @param	array	A list of primary key ids of the content that has changed state.
       * @param	int		The value of the state that the content has been changed to.
       * @return	boolean
       * @since	1.6
       */
      public function onContentChangeState($context, $pks, $value)
      {
            return true;
      }

      /**
       * Example prepare content method
       *
       * Method is called by the view
       *
       * @param	string	The context of the content being passed to the plugin.
       * @param	object	The content object.  Note $article->text is also available
       * @param	object	The content params
       * @param	int		The 'page' number
       * @since	1.6
       */
      public function onContentPrepare($context, &$article, &$params, $limitstart)
      {
            $app = JFactory::getApplication();
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

      private function addGeneralMeta($article)
      {
            $document =& JFactory::getDocument();
            $config =& JFactory::getConfig();
            //set data
            $document->setMetaData( 'og:site_name',  $config->getValue( 'config.sitename' ) );
            $document->setMetaData( 'fb:admins', $this->params->get('admins') );
            $document->setMetaData( 'fb:app_id', $this->params->get('app_id')  );
      }
}
