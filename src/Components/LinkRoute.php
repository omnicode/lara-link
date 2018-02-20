<?php
namespace LaraLink\Components;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use LaraLink\Contracts\ToStringMethodInterface;

class LinkRoute implements ToStringMethodInterface
{
    /**
     * @var
     */
    protected $item;

    /**
     * @var
     */
    protected $action;

    /**
     * @var
     */
    protected $data;

    /**
     * @var
     */
    private $generalConf;

    /**
     * @var
     */
    private $actions;

    /**
     * @var
     */

    /**
     * @param $options
     * @return string
     */
    public function toString(&$options)
    {
        $routeStr = $this->getRouteStr($options) . $this->getQueryStr($options);
        return trim($routeStr);
    }

    /**
     * @param $data
     * @return string
     * @throws \Exception
     */
    private function getRouteStr(&$data)
    {
        $routeStr = '';
        if(!empty($data['route'])) {
            $routeParams = [];

            if (is_string($data['route'])) {
                $routeName = $data['route'];
            } elseif (is_array($data['route'])) {
                list($routeName, $routeParams) = $this->getRouteComponents($data);
            } else {
                throw new \Exception('The value of "route" key must be false, string or array');
            }

            $routeStr = Route::has($routeName) ? route($routeName, $routeParams) : $routeName;
            unset($data['route']);
        }

        return $routeStr ? trim($routeStr) : '';
    }

    /**
     * @param $options
     * @return string
     */
    public function getSortRouteUrl(&$options)
    {
        if (is_string($options['sortable'])) {
            $options['name'] = $options['sortable'];
        }

        $params = app('request')->all();

        $linkOptions = [];
        // if current column has sorting
        if (!empty($params['column']) && $params['column'] == $options['name']) {
            $params['order'] = strtolower($params['order']);
            $params['column'] = strtolower($params['column']);

            if ($params['order'] == 'asc') {
                $params['order'] = 'desc';
                $linkOptions['icon'] = 'sort-amount-asc';
            } else {
                $params['order'] = 'asc';
                $linkOptions['icon'] = 'sort-amount-desc';
            }
        } else {
            $params['column'] = $options['name'];
            $params['order'] = 'asc';
            $linkOptions['icon'] = 'sort';
        }

        $options = $linkOptions;

        return request()->fullUrlWithQuery($params);
    }



    /**
     * @param $data
     * @return array
     * @throws \Exception
     */
    private function getRouteComponents(&$data)
    {
        $routeParams = [];
        if (empty($data['route']['name'])) {
            throw new \Exception('Options "route" array must be contain "name" key');
        } else {
            $routeName = $data['route']['name'];

            if (!empty($data['route']['params'])) {
                $routeParams = $data['route']['params'];
            }
        }

        return [$routeName, $routeParams];
    }

    private function getQueryStr(&$data)
    {
        // check query params
        if (isset($data['query'])) {
            $queryStr = '?' . http_build_query($data['query']);
            unset($data['query']);
            return $queryStr;
        }

        return '';
    }


    /*************************************************
     *          For Item dependents Action           *
     ************************************************/


    /**
     * @param string $item
     * @param string $action
     * @param array $options
     * @return bool|string
     * @throws \Exception
     */
    public function getItemActionRouteName($item = '', $action = '', &$options = [])
    {
        $this->initialize($item, $action, $options);
        $routeName =  is_array($options) ? $this->getRouteNameFromArrayData() : $this->getRouteNameFromStringData();
        unset($options['route']);
        return $routeName;
    }

    /**
     * @param string $item
     * @param string $action
     * @param array $data
     * @throws \Exception
     */
    public function initialize($item = '', $action = '', $data = [])
    {
        $this->actions = Config::has('lara_link.actions') ? Config::get('lara_link.actions') : [];
        $this->generalConf = Config::has('lara_link.general') ? Config::get('lara_link.general') : [];
        $this->item = $item;
        $this->action = $action;
        $this->data = $data;
    }

    /**
     * @return bool|string
     */
    private function getRouteNameFromArrayData()
    {
        if (empty($this->data) || !isset($this->data['route']) || $this->data['route'] === true ) {
            $routeName = $this->getDefaultRouteName();
        } elseif (is_array($this->data['route'])) {
            $routeName = $this->getRouteNameFromStringData($this->data['route']['name']);
        } else {
            $routeName = $this->getRouteNameFromStringData($this->data['route']);
        }

        return $routeName;
    }

    /**
     * @return string
     */
    private function getDefaultRouteName()
    {
        $tableName = str_plural($this->item->getTable());
        $data = $this->getReplaceSymbolArr();
        if ($data) {
            foreach ($data  as $init => $final) {
                $tableName = str_replace($init, $final, $tableName);
            }
        }
        $routeEnd = !empty($this->actions[$this->action]['route_end']) ? $this->actions[$this->action]['route_end'] : $this->action;
        $routeStart = !empty($this->data['route_prefix']) ? $this->data['route_prefix'] . '.' : '';
        return $routeStart . $tableName . '.' . $routeEnd;
    }


    /**
     * @return bool
     */
    private function getRouteNameFromStringData($data = null)
    {
        $data = ($data !== null) ? $data : $this->data;

        if($data === false) {
            $routeName = false;
        } elseif($data === true || empty(trim($data))) {
            $routeName = $this->getDefaultRouteName();
        } else {
            $routeName  = $data;
        }

        return $routeName;
    }

    /**
     * @return array|bool
     */
    private function getReplaceSymbolArr()
    {
        if(isset($this->data['replace']['symbol'])) {
            $dataArr = [];
            if($this->data['replace']['symbol'] === false) {
                return false;
            }elseif ($this->data['replace']['symbol'] === true || is_string($this->data['replace']['symbol'])) {
                $dataArr = $this->getDefaultReplaceSymbolArr($this->data['replace']['symbol']);
            } else {
                foreach ($this->data['replace']['symbol'] as $init => $final) {
                    if ($final === true) {
                        $dataArr = array_merge($dataArr, $this->getDefaultReplaceSymbolArr($final));
                    } elseif ($final !== false) {
                        $dataArr[$init] = $final;
                    }
                }
            }
        } else {
            $dataArr = $this->getDefaultReplaceSymbolArr();
        }
        return $dataArr;
    }
    /**
     * @return array
     */
    private function getDefaultReplaceSymbolArr($symbol  = true)
    {
        return !empty($this->generalConf['replace']['symbols-route']) ? $this->generalConf['replace']['symbols-route'] : $this->generalConf['replace']['symbols'];
    }


}