<?php
namespace Diagro\Backend\Http\Resources;

use closure;

/**
 * To be used in the functions of the group in resources/collections
 */
abstract class GroupedDefinition
{


    /**
     * This returns a closure which returns the results for the group values.
     * The values are given as a parameter to the closure.
     * If values contains one item, the one item is passed otherwhise the complete array of values.
     *
     * @return closure
     */
    abstract public function collector(): closure;

    /**
     * If string is returned, then this is the name in the collectors results.
     * If closure is returned, the collector item is passed and value is passed.
     * If the values matches, this is replaced in the resource with the collector item.
     *
     * @return string|closure
     */
    abstract public function compare(): string|closure;


}