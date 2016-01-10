<?php

namespace common\components;

use yii;
use yii\web\UrlRuleInterface;
use yii\base\Object;
use yii\helpers\ArrayHelper;
use common\models\MenuTree;

class CmsUrlRule extends Object implements UrlRuleInterface
{

    public $suffix;

    public function createUrl($manager, $route, $params)
    {
        $menu = new MenuTree(Yii::$app->cache);
        $routes = $menu->getRoutes();

        $route = trim($route, '/');
        $suffix = (string)($this->suffix === null ? $manager->suffix : $this->suffix);

        $query = '';
        $pageParams = [];
        if (isset($params['page']))
        {
//            $pageParams['page'] = $params['page'];
            $query .= '/page/' . intval($params['page']);
        }

        if (isset($params['per-page']))
        {
//            $pageParams['per-page'] = $params['per-page'];
            $query .= '/per-page/' . intval($params['per-page']);
        }

        /*if ( ! empty($pageParams))
        {
            $query = '?' . http_build_query($pageParams);
        }*/

        foreach ($routes as $path => $item)
        {
            if ($route == $item['controller_id'] . '/' . $item['action_id'] || $route == $item['module_id'] . '/' . $item['controller_id'] . '/' . $item['action_id'])
            {
                if (isset($params['ctg_id']) && isset($params['id']))
                {
                    if ( !empty($item['ctg_id']) && !empty($item['item_id']) && $params['ctg_id'] == $item['ctg_id'] && $params['id'] == $item['item_id'])
                    {
                        return $path . $query . $suffix;
                    }
                }
                elseif (isset($params['ctg_id']) && !isset($params['id']))
                {
                    if ( !empty($item['ctg_id']) && empty($item['item_id']) && $params['ctg_id'] == $item['ctg_id'])
                    {
                        return $path . $query . $suffix;
                    }
                }
                elseif ( !isset($params['ctg_id']) && isset($params['id']))
                {
                    if (empty($item['ctg_id']) && !empty($item['item_id']) && $params['id'] == $item['item_id'])
                    {
                        return $path . $query . $suffix;
                    }
                }
                else
                {
                    if (empty($item['ctg_id']) && empty($item['item_id']))
                    {
                        return $path . $query . $suffix;
                    }
                }
            }
        }

        return false;
    }

    public function parseRequest($manager, $request)
    {
        $menu = new MenuTree(Yii::$app->cache);
        $routes = $menu->getRoutes();
        $params = [];

        $pathInfo = $request->getPathInfo();
        $suffix = (string)($this->suffix === null ? $manager->suffix : $this->suffix);
        if ($suffix !== '' && $pathInfo !== '')
        {
            $n = strlen($suffix);
            if (substr_compare($pathInfo, $suffix, -$n, $n) === 0)
            {
                $pathInfo = substr($pathInfo, 0, -$n);
                if ($pathInfo === '')
                {
                    // suffix alone is not allowed
                    return false;
                }
            }
            else
            {
                return false;
            }
        }

        if (preg_match_all('/\/page\/\d+/', $pathInfo, $matches, PREG_OFFSET_CAPTURE))
        {
            $matches = $matches[0];
            $matchesCnt = count($matches);
            $pageQuery = trim($matches[$matchesCnt - 1][0], '/');
            list($pageParam, $pageNum) = explode('/', $pageQuery);
            $params[$pageParam] = $pageNum;

            $n = strlen($pageQuery);
            $pos = $matches[$matchesCnt - 1][1];
            $pathInfo = substr($pathInfo, 0, $pos) . substr($pathInfo, $pos + $n + 1);
            $pathInfo = rtrim($pathInfo, '/');
        }

        if (preg_match_all('/\/per\-page\/\d+/', $pathInfo, $matches, PREG_OFFSET_CAPTURE))
        {
            $matches = $matches[0];
            $matchesCnt = count($matches);
            $pageQuery = trim($matches[$matchesCnt - 1][0], '/');
            list($pageParam, $pageNum) = explode('/', $pageQuery);
            $params[$pageParam] = $pageNum;

            $n = strlen($pageQuery);
            $pos = $matches[$matchesCnt - 1][1];
            $pathInfo = substr($pathInfo, 0, $pos) . substr($pathInfo, $pos + $n + 1);
            $pathInfo = rtrim($pathInfo, '/');
        }

        if (ArrayHelper::keyExists($pathInfo, $routes, false))
        {
            $result = false;
            $route = '';
            if ( !empty($routes[$pathInfo]['controller_id']) && !empty($routes[$pathInfo]['action_id']))
            {
                if ( !empty($routes[$pathInfo]['module_id']))
                {
                    $route .= $routes[$pathInfo]['module_id'] . '/';
                }

                $route .= $routes[$pathInfo]['controller_id'] . '/' . $routes[$pathInfo]['action_id'];

                if ( !empty($routes[$pathInfo]['ctg_id']))
                {
                    $params['ctgId'] = $routes[$pathInfo]['ctg_id'];
                }

                if ( !empty($routes[$pathInfo]['item_id']))
                {
                    $params['id'] = $routes[$pathInfo]['item_id'];
                }

                $result = [$route, $params];
            }

            return $result;
        }

        return false;
    }
}