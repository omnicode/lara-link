<?php

namespace LaraLink\Links;

class CustomLink extends Link
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
     * @param $label
     * @param $options
     * @return string
     */
    public function toLink($label, $options)
    {
        $this->processOptions($options);

        $routeStr = $this->route->toString($options);
        $titleStr = $this->getTitleStr($label, $options);
        $attributesStr = $this->getAttributesStr($options);
        return sprintf('<a href="%s" %s>%s</a>', $routeStr, $attributesStr, $titleStr);
    }

    protected function initialize($label, &$options)
    {

    }

    /**
     * @param $options
     */
    private function processOptions(&$options)
    {
        if (!isset($options['class'])) {
            $options['class'] = '';
        }

        if (!empty($options['btn'])) {
            if ($options['btn'] === true) {
                $options['btn'] = config('lara_link.default.btn');
            }
            if (!empty($options['class'])) {
                $options['class'] .= ' ';
            }
            $options['class'] .= 'btn btn-' . $options['btn'];
            unset($options['btn']);
        }

        if (!empty($options['position']) && $options['position'] == 'right') {
            $options['class'] .= ' icon-right';
        }
    }

    /**
     * @param $title
     * @param $options
     * @return string
     * @TODO - right align icon margin !!
     */
    private function getTitleStr($title, &$options) {
        $icon = false;
        if (!empty($options['icon'])) {
            $icon = '<i class="fa fa-fw fa-' . $options['icon'] . '"></i>';
            unset($options['icon']);
        }

        if ($icon) {
            if (!empty($options['position']) && $options['position'] == 'right') {
                $title .= $icon;
            } else {
                $title = $icon.$title;
            }

            unset($options['position']);
        }

        return $title;
    }

    /**
     * @param $options
     * @return string
     */
    protected function getAttributesStr($options) {
        $optionsStr = '';

        foreach ($options as $attr => $value) {
            if (!empty($value)) {
                $optionsStr .= $attr . '="' . $value . '" ';
            }
        }

        return trim($optionsStr);
    }
}
