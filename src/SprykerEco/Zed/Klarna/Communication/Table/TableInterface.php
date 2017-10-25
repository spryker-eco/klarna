<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

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
