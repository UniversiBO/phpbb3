<?php

namespace Universibo\Sorting;

/**
 * Bubble sorting algorithm
 */
class BubbleSort
{
    /**
     * Comparing function
     * @var callable
     */
    private $compare;

    /**
     * Swapping function
     * @var callable
     */
    private $swap;

    /**
     *
     * @param callback $compare
     * @param callback $swap
     */
    public function __construct($compare, $swap)
    {
        $this->compare = $compare;
        $this->swap    = $swap;
    }
    
    /**
     * Sorting algorithm
     * 
     * @param array $items
     */
    public function sort (array &$items)
    {
        $n = count($items);
        do {
            $swapped = 0;
            
            for($i=1; $i<$n; ++$i) {
                if(call_user_func($this->compare, $items[$i-1], $items[$i]) > 0) {
                    call_user_func_array($this->swap, array(&$items[$i-1], &$items[$i]));
                    $swapped++;
                }
            }
        } while($swapped > 0);
    }
}
