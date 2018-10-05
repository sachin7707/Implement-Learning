<?php

namespace App\Maconomy\Client\AbstractFactory;

/**
 * @author jimmiw
 * @since 2018-10-05
 */
interface Parser
{
    /**
     * Parses a single entity
     * @param mixed $data
     */
    public function parse($data);

    /**
     * Parses a list of entities
     * @param array $data
     */
    public function parseCollection(array $data);
}