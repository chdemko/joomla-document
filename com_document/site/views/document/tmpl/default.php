<?php
/**
 * @version		$Id: default.php 158 2010-12-17 13:38:40Z hammihicham $
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
        <?php echo $this->item->author ?> 
    </p>
    <div><strong>Description:</strong></div>
    <p>
        <?php echo $this->item->description ?> 
    </p>
	 <div><strong>Keywords:</strong></div>
    <p>
        <?php echo $this->item->keywords ?> 
    </p>

  <div><strong>language:</strong></div>
    <p> 
      <?php  echo $this->item->language  ?>
    </p>   
	
	
<a href="<?php echo JRoute_('index.php?option=com_document&view=download&id=.$this->item->id')?>">Download here</a>
 



