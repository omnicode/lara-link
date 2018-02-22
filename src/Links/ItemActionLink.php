<?php

namespace LaraLink\Links;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class ItemActionLink extends Link
{
    /**
     * @var
     */
    private $actions;

    /**
     * @var
     */
    private $generalConf;

    /**
     * @var
     */
    private $item;

    /**
     * @var
     */
    private $action;

    /**
     * @param $title
     * @param $options
     * @return string
     */
    public function toLink($title, $options)
    {
        $this->initialize($title, $options);
        $routeName = $this->route->getItemActionRouteName($this->item, $this->action, $options);
        $linkOptions['route'] = [
            'name' => $routeName,
            'params' => !empty($options['params']) ? $options['params'] : []
        ];

        $isPermitted = $this->checkPermission($routeName);
        if($isPermitted) {

            $label = $this->getLabel($options);
            $icon = $this->getIcon();
            $titleAttr  = $this->getTitleAttr();
            $isConfirmation = false;
            if (in_array('confirmation', $options)) {
                $isConfirmation = true;
            } elseif(isset($options['confirmation'])) {
                if ($options['confirmation']) {
                    $isConfirmation = true;
                }
            } elseif (!empty($this->actions[$this->action]) && in_array('confirmation', $this->actions[$this->action])
                || !empty($this->actions[$this->action]['confirmation'])) {
                    $isConfirmation = true;
            }

            if ($isConfirmation || $this->action == 'destroy') {

                $confirmationMessage = $this->getConfirmationMessage();
                $method = !empty($options['method']) ? $options['method'] : 'post';
                $linkOptions['form-options'] = [
                    'class' => 'ml5 delete-form',
                    'method' => $method
                ];

                $linkOptions['submit-options'] = [
                    'message' => $confirmationMessage,
                    'icon' => $icon
                ];

                if ($this->action == 'destroy') {
                    // set class
                    $linkOptions['form-options']['class'] .= ' delete-form';

                    // set delete method
                    $linkOptions['form-options']['method'] = 'delete';

                    // set icon
                    if (!isset($options['submit-options']['icon'])) {
                        $options['submit-options']['icon'] = 'trash';
                    }
                }

                if (!empty($options['data-step'])) {
                    $linkOptions['form-options']['data-step'] = $options['data-step'];
                }

                if (!empty($options['data-intro'])) {
                    $linkOptions['form-options']['data-intro'] = $options['data-intro'];
                }

                // TODO Use ConfirmationLink class
                $confirmationLink = new ConfirmationLink($this->route);
                return $confirmationLink->toLink($label, $linkOptions);
            } else {
                $linkOptions['title'] = $titleAttr;
                $linkOptions['class'] = 'ml5';
                $linkOptions['icon'] = $icon;
                $linkOptions['icon'] = $icon;

                if (!empty($options['data-step'])) {
                    $linkOptions['data-step'] = $options['data-step'];
                }

                if (!empty($options['data-intro'])) {
                    $linkOptions['data-intro'] = $options['data-intro'];
                }

                $customLink = new CustomLink($this->route);
                return $customLink->toLink($label, $linkOptions);
            }
        }


        $item = $options['item'];
        $action = $options['action'];
        unset($options['item']);
        unset($options['action']);
    }

    protected function initialize($label, &$options)
    {
        if (!Config::has('lara_link')) {
            throw new \Exception('In config file must be lara_link file');
        }

        $this->actions = Config::has('lara_link.actions') ? Config::get('lara_link.actions') : [];
        $this->generalConf = Config::has('lara_link.general') ? Config::get('lara_link.general') : [];

        if (empty($options['item']) || empty($options['action'])) {
            throw new \Exception('The options must be contain action and item key');
        }

        $item = $options['item'];
        $action = $options['action'];

        if (!empty($item) && !($item instanceof Model)) {
            throw new \Exception('The $item must be Illuminate\Database\Eloquent\Model');
        }

        if (!is_string($action) || (is_string($action) && empty($action))) {
            throw new \Exception('The $action must be only string and not empty');
        }

        $this->checkPermittedArrayStructure($options);

        if (isset($options['replace']['symbol'])) {
            if ($options['replace']['symbol'] !== false && $options['replace']['symbol'] !== true
                && !is_array($options['replace']['symbol']) && !is_string($options['replace']['symbol'])
                && (is_array($options['replace']['symbol']) || !$this->array_contains_only_char($options['replace']['symbol']))) {
                throw new \Exception('The $options[\'replace\'][\'symbol\'] value mast be true, false, string or array');
            }
//            else {
//                if($options['replace']['symbol'] !== true &&
//                    array_diff(array_values($options['replace']['symbol']) , ['-', '.', true])) {
//                        throw new \Exception('In this time accessible only ".", "-" symbols or true');
//                }
//            }
        }

        if (isset($options['replace']['plural'])) {
            if ($options['replace']['plural'] !== true && $options['replace']['plural'] !== false) {
                throw new \Exception('The $options["replace"]["plural"] value mast be true or false');
            }
        }

        $this->item = $item;
        $this->action = $action;
        $this->options = $options;
    }

    /**
     * @param $options
     * @param string $param
     * @throws \Exception
     */
    private function checkPermittedArrayStructure($options, $param = 'data')
    {
        if ( $options !== false && $options !== true && !is_string($options) && !is_array($options)) {
            throw new \Exception(__('The $:param mast be true, false, string or array', ['param' => $param]));
        }
    }

    /**
     * @param $array
     * @return bool
     */
    private function array_contains_only_char($array){
        foreach($array as $value){
            if(is_string($value) || $value === true || $value === false) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param $routeName
     * @return bool
     */
    protected function checkPermission($routeName)
    {
        $isPermitted = parent::checkPermission($routeName);

        if (is_array($this->options)) {
            if(isset($this->options['properties'])) {
                $properties = $this->options['properties'];
                foreach ($properties as $property => $values) {
                    if(!in_array($this->item->{$property}, $values)) {
                        $isPermitted = false;
                        break;
                    }
                }
            }
        }

        return $isPermitted;
    }

    private function getLabel($options)
    {
        $label = '';
        if (empty($this->label)) {
            if (isset($options['label'])) {
                if ($options['label'] === false ) {
                    $label = '';
                } elseif ($options['label'] === true) {
                    $label = $this->action; // TODO last part action
                } else {
                    $label = $options['label'];
                }
                unset($options['label']);
            }
        } elseif ($this->label === true) {
            $label = $this->action; // TODO last part action
        }

        return $label;
    }

    /**
     * @return string
     */
    protected function getIcon()
    {
        $icon = '';

        if (!empty($this->options['icon'])) {
            $icon = $this->options['icon'];
        } elseif (!empty($this->actions[$this->action]['icon'])) {
            $icon = $this->actions[$this->action]['icon'];
        }

        return $icon;
    }

    protected function getTitleAttr()
    {

        // TODO it depends item properties name and value
        if (!empty($this->options['title'])) {
            $titleAttr = $this->options['title'];
        } elseif (!empty($this->actions[$this->action]['title'])) {
            $titleAttr = $this->actions[$this->action]['title'];
        } else {
            $titleAttr = ucfirst($this->action);
        }
        return $titleAttr;
    }

    // TODO move it to ConfirmationLink
    protected function getConfirmationMessage ()
    {
        if (!empty($this->options['confirmation'])) {
            return $this->getMessageFromData($this->options['confirmation']);
        }

        if (!empty($this->actions[$this->action]['confirmation'])) {
            return $this->getMessageFromData($this->actions[$this->action]['confirmation']);
        }

        return$this->getConfirmationMessageBy();
    }

    private function getMessageFromData($data) {
        if (is_string($data)) {
            $template = $data;
        } elseif (!empty($data['message'])) {
            $template = $data['message'];
        } else {
            $template = false;
        }

        return $this->getConfirmationMessageBy($template);
    }

    /**
     * @param string $template
     * @return string|\Symfony\Component\Translation\TranslatorInterface
     */
    private function getConfirmationMessageBy($template = '')
    {
        if (empty($template)) {
            $template = $this->generalConf['confirmation']['message'];
        }

        $itemTableName = $this->item->getTable();

        foreach (array_keys($this->generalConf['replace']['symbols']) as $symbol) {
            $itemTableName = str_replace($symbol, ' ', $itemTableName);
        }

        $action = $this->action;
        foreach (array_keys($this->generalConf['replace']['symbols']) as $symbol) {
            $action = str_replace($symbol, ' ', $action);
        }

        return __($template, ['action' => $action, 'item' => $itemTableName]);
    }
}
