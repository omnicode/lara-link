<?php

namespace LaraLink\Contracts;

interface LinkInterface
{
    /**
     * @param $title
     * @param $options
     * @return mixed
     */
    public function toLink($title, $options);
}
