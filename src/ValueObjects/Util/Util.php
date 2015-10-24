<?php
namespace PHPVCSControl\ValueObjects\Util;

/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2013 Nicolò Pignatelli
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of
 * the Software, and to permit persons to whom the Software is furnished to do so,
 * subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS
 * FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER
 * IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN
 * CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
class Util
{
    /**
     * Tells whether two objects are of the same class
     *
     * @param  object $object_a
     * @param  object $object_b
     * @return bool
     */
    public static function classEquals($object_a, $object_b)
    {
        return \get_class($object_a) === \get_class($object_b);
    }

    /**
     * Returns full namespaced class as string
     *
     * @param $object
     * @return string
     */
    public static function getClassAsString($object)
    {
        return \get_class($object);
    }
}
