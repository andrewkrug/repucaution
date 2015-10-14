<?php
/**
 * Author: Alex P.
 * Date: 24.04.14
 * Time: 17:18
 */

namespace Core\Service\Theme\Component;


use Ikantam\Theme\Abstracts\ComponentAbstract;
use simple_html_dom_node as DomNode;

class ImageComponent extends \Ikantam\Theme\Component\ImageComponent
{

    /**
     * Manipulate node
     * @param DomNode $node
     * @return mixed
     */
    public function handleNode(DomNode $node)
    {
       if ($this->getOption('editMode')) {
           $data = $this->getValue();

           $id = $node->getAttribute('id');
           if (is_array($data)) {
               $sessionHelper = get_instance()->container->get('core.service.theme.session.helper');
               foreach (array('original', 'cropped') as $type) {
                   if (isset($data[$type])) { //set current images to session
                       if (file_exists($data[$type]['path'])) {
                           $data[$type]['exist'] = true;
                           $sessionHelper->addTemplateImage($id, $data[$type], $type);
                       }
                   }
               }
           }
           if (isset($data['original']['exist'])) {
               $this->setOption('value', $data['original']['url']);
               $node->setAttribute('data-source-url', $data['original']['url']);
               if (isset($data['crop_params']['selection'])) {
                   $node->setAttribute('data-selection', str_replace('"', "'", json_encode($data['crop_params']['selection'])));
               }
               if (isset($data['crop_params']['scales'])) {
                   $node->setAttribute('data-scale', str_replace('"', "'", json_encode($data['crop_params']['scales'])));
               }
           }
       }
       parent::handleNode($node);
    }
}