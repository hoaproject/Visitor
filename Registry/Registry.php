<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2014, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

from('Hoa')

/**
 * \Hoa\Visitor\Registry\Exception
 */
-> import('Visitor.Registry.Exception')

/**
 * \Hoa\Visitor\Visit
 */
-> import('Visitor.Visit');

}

namespace Hoa\Visitor\Registry {

/**
 * Class \Hoa\Visitor\Registry.
 *
 * A registry of visitor.
 * It can register objects and methods to treat differents entries/elements. It
 * allows user to write visitor in many files and objects or to mixes many visitors
 * for one visit.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2014 Ivan Enderlin.
 * @license    New BSD License
 */

class Registry implements \Hoa\Visitor\Visit {

    /**
     * Overwrite an entry if already exists.
     *
     * @const bool
     */
    const OVERWRITE       = true;

    /**
     * Do not overwrite an entry if already exists.
     *
     * @const bool
     */
    const DO_NOT_OVERWRITE = false;

    /**
     * Registry.
     *
     * @var \Hoa\Visitor\Registry array
     */
    protected $_registry = array();



    /**
     * Add an entry in the registry.
     *
     * @access  public
     * @param   string  $entry       Entry name, i.e. element name to visit.
     * @param   array   $callback    Object and method to callback when
     *                               visiting.
     * @param   bool    $overwrite   Overwrite an entry if already exists, given
     *                               by constants self::*OVERWRITE.
     * @return  void
     * @throw   \Hoa\Visitor\Registry\Exception
     */
    public function addEntry ( $entry,
                               Array $callback,
                               $overwrite = self::DO_NOT_OVERWRITE ) {

        if(!isset($callback[0]))
            throw newException(
                'An entry in the registry must be an array with two entries : ' .
                'object (object) and method (string).', 0);

        if(!is_object($callback[0]))
            throw new Exception(
                'Cannot call a method on a non-object, given %s.',
                1, gettype($callback[0]));

        if(!isset($callback[1]))
            throw new Exception(
                'An entry in the registry must be an array with two entries : ' .
                'object (object) and method (string). Only object is given.', 2);

        if(!is_string($callback[1]))
            throw new Exception(
                'Method must be a string, given %s.', 3, gettype($callback[1]));

        if(!method_exists($callback[0], $callback[1]))
            throw new Exception(
                'Method %s does not exist on object %s.',
                4, array($callback[1], get_class($callback[0])));

        if(   true                   === $this->entryExists($entry)
           && self::DO_NOT_OVERWRITE === $overwrite)
            throw new Exception(
                'Entry %s already exists.', 5, $entry);

        $this->_registry[$entry] = $callback;

        return;
    }

    /**
     * Check if an entry already exists.
     *
     * @access  public
     * @param   string  $entry    Entry name, i.e. element name to visit.
     * @return  bool
     */
    public function entryExists ( $entry ) {

        return isset($this->_registry[$entry]);
    }

    /**
     * Remove an entry.
     *
     * @access  public
     * @param   string  $entry    Entry name, i.e. element name to visit.
     * @return  void
     */
    public function removeEntry ( $entry ) {

        unset($this->_registry[$entry]);

        return;
    }

    /**
     * Get a specific entry.
     *
     * @access  public
     * @param   string  $entry    Entry name, i.e. element name to visit.
     * @return  mixed
     */
    public function getEntry ( $entry ) {

        if(false === $this->entryExists($entry))
            return false;

        return $this->_registry[$entry];
    }

    /**
     * Get default entry.
     * A default entry is a null entry.
     *
     * @access  public
     * @return  mixed
     */
    public function getDefaultEntry ( ) {

        return $this->getEntry(null);
    }

    /**
     * Get all entries, i.e. the registry.
     *
     * @access  protected
     * @return  array
     */
    protected function getEntries ( ) {

        return $this->_registry;
    }

    /**
     * Visit a specific entry.
     *
     * @access  public
     * @param   string             $entry      Entry name, i.e. element name to
     *                                         visit.
     * @param   \Hoa\Visitor\Visit  $element    Element to visit.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (not reference).
     * @return  mixed
     * @throw   \Hoa\Visitor\Exception
     */
    public function visitEntry (  $entry, \Hoa\Visitor\Element $element,
                                 &$handle = null,
                                  $eldnah = null ) {

        if(false === $foo = $this->getEntry($entry))
            throw new Exception(
                'Entry %s does not exist.', 6, $entry);

        return $foo[0]->$foo[1]($element, $handle, $eldnah);
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   \Hoa\Visitor\Visit  $element    Element to visit.
     * @param   mixed              &$handle    Handle (reference).
     * @param   mixed              $eldnah     Handle (not reference).
     * @return  mixed
     * @throw   \Hoa\Visitor\Exception
     */
    public function visit ( \Hoa\Visitor\Element $element,
                            &$handle = null,
                             $eldnah = null ) {

        $foo          = null;
        $elementClass = get_class($element);

        foreach($this->getEntries() as $entry => $callback)
            if($elementClass == $entry)
                return $callback[0]->$callback[1]($element, $handle, $eldnah);

        if(false !== $foo = $this->getDefaultEntry())
            return $foo[0]->$foo[1]($element, $handle, $eldnah);

        throw new Exception(
            'No entry matches element %s.', 7, get_class($element));
    }
}

}

namespace {

/**
 * Flex entity.
 */
Hoa\Core\Consistency::flexEntity('Hoa\Visitor\Registry\Registry');

}
