<?php

/**
 * Avisota newsletter and mailing system
 * Copyright (C) 2013 Tristan Lins
 *
 * PHP version 5
 *
 * @copyright  bit3 UG 2013
 * @author     Tristan Lins <tristan.lins@bit3.de>
 * @package    avisota/contao-message-element-hyperlink
 * @license    LGPL-3.0+
 * @filesource
 */


/**
 * Table orm_avisota_message_content
 * Entity Avisota\Contao:MessageContent
 */
$GLOBALS['TL_DCA']['orm_avisota_message_content']['metapalettes']['downloads'] = array
(
	'type'      => array('cell', 'type', 'headline'),
	'downloads' => array('downloadSources'),
	'expert'    => array(':hide', 'cssID', 'space'),
	'published' => array('invisible'),
);

$GLOBALS['TL_DCA']['orm_avisota_message_content']['fields']['downloadSources'] = array
(
	'label'     => &$GLOBALS['TL_LANG']['orm_avisota_message_content']['downloadSources'],
	'exclude'   => true,
	'inputType' => 'fileTree',
	'eval'      => array(
		'fieldType' => 'checkbox',
		'files'     => true,
		'mandatory' => true,
		'multiple'  => true,
		'tl_class'  => 'clr'
	)
);