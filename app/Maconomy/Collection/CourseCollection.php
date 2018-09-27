<?php

namespace App\Maconomy\Collection;

use App\Maconomy\Model\Course;

/**
 * @author jimmiw
 * @since 2018-09-27
 */
class CourseCollection implements \Iterator, \Countable
{
    /** @var array */
    private $courses;
    /** @var int */
    private $index = 0;

    /**
     * OrderCollection constructor.
     * @param array $courses the list of courses
     */
    public function __construct(array $courses)
    {
        $this->courses = $courses;
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return \count($this->courses);
    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return Course Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->courses[$this->index];
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->index++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->index;
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return isset($this->courses[$this->index]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->index = 0;
    }
}