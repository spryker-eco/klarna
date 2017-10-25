<?php

namespace SprykerEco\Zed\Klarna\Communication\Table;

interface TableInterface
{
    /**
     * @return string
     */
    public function render();

    /**
     * @return array
     */
    public function fetchData();
}
