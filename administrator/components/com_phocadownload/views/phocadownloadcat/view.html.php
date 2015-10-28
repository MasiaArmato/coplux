<?php
/* @package Joomla
 * @copyright Copyright (C) Open Source Matters. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * @extension Phoca Extension
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );

class PhocaDownloadCpViewPhocaDownloadCat extends JViewLegacy
{
	protected $state;
	protected $item;
	protected $form;
	protected $t;

	public function display($tpl = null) {
	
		$this->t			= PhocaDownloadUtils::setVars('cat');
		
		$this->state	= $this->get('State');
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$user 			= JFactory::getUser();
		$model			= $this->getModel();

		JHTML::_('behavior.calendar');
		JHTML::stylesheet( $this->t['s'] );
		
		//Data from model
		//$this->item	=& $this->get('Data');

		$lists 	= array();		
		$isNew	= ((int)$this->item->id == 0);

		// Edit or Create?
		if (!$isNew) {
			$model->checkout( $user->get('id') );
		} else {
			// Initialise new record
			$this->item->approved 		= 1;
			$this->item->published 		= 1;
			$this->item->order 			= 0;
			$this->item->access			= 0;
		}

		$this->addToolbar();
		parent::display($tpl);
	}
	
	
	protected function addToolbar() {
		
		require_once JPATH_COMPONENT.'/helpers/'.$this->t['tasks'].'.php';
		JRequest::setVar('hidemainmenu', true);
		$bar 		= JToolBar::getInstance('toolbar');
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$class		= ucfirst($this->t['tasks']).'Helper';
		$canDo		= $class::getActions($this->t, $this->state->get('filter.category_id'));

		$text = $isNew ? JText::_( $this->t['l'].'_NEW' ) : JText::_($this->t['l'].'_EDIT');
		JToolBarHelper::title(   JText::_( $this->t['l'].'_CATEGORY' ).': <small><small>[ ' . $text.' ]</small></small>' , 'folder');

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')){
			JToolBarHelper::apply($this->t['task'].'.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save($this->t['task'].'.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::addNew($this->t['task'].'.save2new', 'JTOOLBAR_SAVE_AND_NEW');
			
		}
		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			//JToolBarHelper::custom($this->t['c'].'cat.save2copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}
		if (empty($this->item->id))  {
			JToolBarHelper::cancel($this->t['task'].'.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel($this->t['task'].'.cancel', 'JTOOLBAR_CLOSE');
		}
		JToolBarHelper::divider();
		JToolBarHelper::help( 'screen.'.$this->t['c'], true );
	}
}
?>
