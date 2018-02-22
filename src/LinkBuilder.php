<?php

namespace LaraLink;

use LaraLink\Components\LinkRoute;
use LaraLink\Links\ConfirmationLink;
use LaraLink\Links\CustomLink;
use LaraLink\Links\DeleteLink;
use LaraLink\Links\ItemActionLink;

class LinkBuilder
{
    /**
     * @var LinkRoute
     */
    protected $route;

    /**
     * @var
     */
    protected $itmActionLink;

    /**
     * @var
     */
    protected $actions;

    /**
     * @var
     */
    protected $generalConf;

    /**
     * LinkBuilder constructor.
     * @param LinkRoute $route
     * @param ItemActionLink $itemActionLink
     */
    public function __construct(LinkRoute $route, ItemActionLink $itemActionLink)
    {
        $this->route = $route;
        $this->itemActionLink = $itemActionLink;
    }

    /**
     * @param string $label
     * @param array $options
     * @return string
     */
    public function link($label = '', $options = [])
    {
        $customLink = new CustomLink($this->route);
        return $customLink->toLink($label, $options);
    }

    /**
     * @param $title
     * @param array $options
     * @return string
     *
     * use it with lara-tools
     *
     */
    public function sortLink($title = '', $options = [])
    {
        if (empty($title) && !empty($options['label'])) {
            $title = $options['label'];
            unset($options['label']);
        }
        $options['route'] = $this->route->getSortRouteUrl($options);
        return $this->link($title, $options);
    }

    /**
     * @param bool $label
     * @param array $options
     * @return string
     */
    public function deleteLink($label = false, $options = [])
    {
        $deleteLink = new DeleteLink($this->route);
        return $deleteLink->toLink($label, $options);
    }

    /**
     * @param $label
     * @param $options
     * @return string
     */
    public function confirmationLink($label = false, $options = [])
    {
        $confirmationLink = new ConfirmationLink($this->route);
        return $confirmationLink->toLink($label, $options);
    }

    /**
     * @param bool $label
     * @param string $item
     * @param string $action
     * @param array $options
     * @return string
     */
    public function itemActionLink($label = false, $item = '', $action = '', $options = [])
    {
        if ($options === false) {
            return '';
        }
        $options['action'] = $action;
        $options['item'] = $item;
        return $this->itemActionLink->toLink($label, $options);
    }
}
