<?php

/**
 * Avisota newsletter and mailing system
 * Copyright © 2016 Sven Baumann
 *
 * PHP version 5
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-download
 * @license    LGPL-3.0+
 * @filesource
 */

namespace Avisota\Contao\Message\Element\Downloads;

use Avisota\Contao\Message\Core\Event\AvisotaMessageEvents;
use Avisota\Contao\Message\Core\Event\RenderMessageContentEvent;
use Contao\Doctrine\ORM\EntityAccessor;
use Contao\File;
use Contao\FilesModel;
use Contao\System;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DefaultRenderer
 *
 * @copyright  way.vision 2016
 * @author     Sven Baumann <baumann.sv@gmail.com>
 * @package    avisota/contao-message-element-download
 */
class DefaultRenderer implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2'))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return array(
            AvisotaMessageEvents::RENDER_MESSAGE_CONTENT => array(
                array('renderContent'),
            ),
        );
    }

    /**
     * Render a single message content element.
     *
     * @param RenderMessageContentEvent $event
     *
     * @return string
     */
    public function renderContent(RenderMessageContentEvent $event)
    {
        global $container;

        $content = $event->getMessageContent();

        if ($content->getType() != 'downloads' || $event->getRenderedContent()) {
            return;
        }

        /** @var EntityAccessor $entityAccessor */
        $entityAccessor = $container['doctrine.orm.entityAccessor'];

        $context          = $entityAccessor->getProperties($content);
        $context['files'] = array();

        System::loadLanguageFile('default');
        foreach ($context['downloadSources'] as $index => $downloadSource) {
            $context['downloadSources'][$index] = FilesModel::findByUuid($downloadSource)->path;

            $file = new File($context['downloadSources'][$index], true);

            if (!$file->exists()) {
                unset($context['downloadSources'][$index]);
                continue;
            }


            $context['files'][$index] = array(
                'url'   => $file->path,
                'size'  => System::getReadableSize($file->size),
                'icon'  => 'assets/contao/images/' . $file->icon,
                'title' => $file->path
            );
        }

        if (empty($context['files'])) {
            return;
        }

        $template = new \TwigTemplate('avisota/message/renderer/default/mce_downloads', 'html');
        $buffer   = $template->parse($context);

        $event->setRenderedContent($buffer);
    }
}
