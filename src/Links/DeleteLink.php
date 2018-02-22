<?php

namespace LaraLink\Links;

class DeleteLink extends Link
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
        // set class
        $options['form-options']['class'] .= ' delete-form';

        // set delete method
        $options['form-options']['method'] = 'delete';

        // set icon
        if (!isset($options['submit-options']['icon'])) {
            $options['submit-options']['icon'] = 'trash';
        }
        $confirmationLink = new ConfirmationLink($this->route);
        return $confirmationLink->toLink($label, $options);
    }

    protected function initialize($label, &$options)
    {

    }
}
