<?php

namespace LaraLink\Links;

use LaraForm\Facades\LaraForm;

class ConfirmationLink extends Link
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


    public function toLink($label, $options)
    {

        $formOptions = isset($options['form-options']) ? $options['form-options'] : [];
        $submitOptions = isset($options['submit-options']) ? $options['submit-options'] : [];

        $formOptions['action'] = $this->route->toString($options);

        if (!isset($formOptions['class'])) {
            $formOptions['class'] = '';
        } else {
            $formOptions['class'] .= ' ';
        }

        // set class
//        $formOptions['class'] .= 'delete-form';

        // set method
        if (!isset($formOptions['method'])) {
            $formOptions['method'] = 'get';
        }



        // set icon
        if (!isset($submitOptions['icon'])) {
            $submitOptions['icon'] = 'trash';
        }

        if (!isset($submitOptions['class'])) {
            $submitOptions['class'] = '';
        }
        if (!isset($submitOptions['div'])) {
            $submitOptions['div'] = false;
        }

        if (!isset($submitOptions['confirm']) || $submitOptions['confirm'] !== false) {
            $submitOptions['class'] .= ' confirmBox';
        }
        unset($submitOptions['confirm']);

        if (!isset($submitOptions['message'])) {
            $submitOptions['message'] = __('Please confirm item deletion');
        }

        if (empty($label)) {
            if (!empty($submitOptions['title'])) {
                $label = $submitOptions['title'];

                unset($submitOptions['title']);
            } else {
                $label = false;
            }
        }
        $form = '' . LaraForm::create([], $formOptions);
        $form .= LaraForm::submit($label, $submitOptions);
        $form .= LaraForm::end();
        return  $form;
    }

    protected function initialize($label, &$options)
    {

    }
}
