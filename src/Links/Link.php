<?php
namespace LaraLink\Links;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use LaraLink\Components\LinkRoute;
use LaraLink\Contracts\LinkInterface;
use LaraLink\Contracts\ToStringMethodInterface;

abstract class Link implements LinkInterface
{
    /**
     * @var LinkRoute
     */
    protected $route;

    /**
     * @var
     */
    protected $options;

    /**
     * Link constructor.
     * @param LinkRoute $linkRoute
     */
    public function __construct(LinkRoute $linkRoute)
    {
        $this->route = $linkRoute;
    }

    /**
     * @param $label
     * @param $options
     * @return mixed
     */
    abstract protected function initialize($label, &$options);

    /**
     * @param $title
     * @param $options
     * @return mixed
     */
    abstract public function toLink($title, $options);

    /**
     * @param $routeName
     * @return bool
     */
     protected function checkPermission($routeName) {
         if (empty($this->options['check'])) {
             return true;
         }

         if (is_string($this->options['check'])) {
             $this->options['check'] = [$this->options['check']];
         }


         foreach ($this->options['check'] as $className) {
             $checkClass = app()->make($className);
             if (!$checkClass::check($routeName)) {
                 return false;
             }
         }

         return true;
     }

    /**
     *
     */
    protected function getRouteName()
    {

    }

    /**
     *
     */
    protected function getIcon()
    {

    }

    /**
     *
     */
    protected function getTitleAttr()
    {

    }
}