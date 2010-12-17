<?php
/**
 * @version		$Id$
 * @package		Joomla.Site
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<H1>Document vue</H1>

<div>
        <strong>title:</strong>
    </div>
    <p>
       <?php echo $this->item->title  ?>   </p>

<div><strong>Author:</strong></div>
    <p>
        <?php echo $this->item->category?> 
    </p>
    <div><strong>categorie:</strong></div>
    <p>
        <?php echo $this->item->category ?> 
    </p>

  <div><strong>language:</strong></div>
    <p> 
      <?php  echo $this->item->language  ?>
    </p>   
	
	
<a href="<?php echo JRoute_('index.php?option=com_document&view=download&id=.$this->item->id')?>">Download here</a>
 



