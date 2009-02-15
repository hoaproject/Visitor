<?php

/**
 * Hoa Framework
 *
 *
 * @license
 *
 * GNU General Public License
 *
 * This file is part of Hoa Open Accessibility.
 * Copyright (c) 2007, 2008 Ivan ENDERLIN. All rights reserved.
 *
 * HOA Open Accessibility is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * HOA Open Accessibility is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with HOA Open Accessibility; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *
 * @category    Framework
 * @package     Hoa_Visitor
 * @subpackage  Hoa_Visitor_Registry
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Visitor_Exception
 */
import('Visitor.Exception');

/**
 * Hoa_Visitor_Visit
 */
import('Visitor.Visit');

/**
 * Class Hoa_Visitor_Registry.
 *
 * A registry of visitor.
 * It can register objects and methods to treat differents entries/elements. It
 * allows user to write visitor in many files and objects or to mixes many visitors
 * for one visit.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Visitor
 * @subpackage  Hoa_Visitor_Registry
 */

class Hoa_Visitor_Registry implements Hoa_Visitor_Visit {

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
    const DONOT_OVERWRITE = false;

    /**
     * Registry.
     *
     * @var Hoa_Visitor_Registry array
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
     * @throw   Hoa_Visitor_Exception
     */
    public function addEntry ( $entry,
                               Array $callback,
                               $overwrite = self::DONOT_OVERWRITE ) {

        if(!isset($callback[0]))
            throw new Hoa_Visitor_Exception(
                'An entry in the registry must be an array with two entries : ' .
                'object (object) and method (string).', 0);

        if(!is_object($callback[0]))
            throw new Hoa_Visitor_Exception(
                'Cannot call a method on a non-object, given %s.',
                1, gettype($callback[0]));

        if(!isset($callback[1]))
            throw new Hoa_Visitor_Exception(
                'An entry in the registry must be an array with two entries : ' .
                'object (object) and method (string). Only object is given.', 2);

        if(!is_string($callback[1]))
            throw new Hoa_Visitor_Exception(
                'Method must be a string, given %s.', 3, gettype($callback[1]));

        if(!method_exists($callback[0], $callback[1]))
            throw new Hoa_Visitor_Exception(
                'Method %s does not exist on object %s.',
                4, array($callback[1], get_class($callback[0])));

        if(   true                  === $this->entryExists($entry)
           && self::DONOT_OVERWRITE === $overwrite)
            throw new Hoa_Visitor_Exception(
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
     * @param   Hoa_Visitor_Visit  $element    Element to visit.
     * @param   mixed              $handle     Handle (reference).
     * @return  mixed
     * @throw   Hoa_Visitor_Exception
     */
    public function visitEntry ( $entry, Hoa_Visitor_Element $element,
                                 &$handle = null ) {

        if(false === $foo = $this->getEntry($entry))
            throw new Hoa_Visitor_Exception(
                'Entry %s does not exist.', 6, $entry);

        return $foo[0]->$foo[1]($element, $handle);
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   Hoa_Visitor_Visit  $element    Element to visit.
     * @param   mixed              $handle     Handle (reference).
     * @return  mixed
     * @throw   Hoa_Visitor_Exception
     */
    public function visit ( Hoa_Visitor_Element $element, &$handle = null ) {

        $foo          = null;
        $elementClass = get_class($element);

        foreach($this->getEntries() as $entry => $callback)
            if($elementClass == $entry)
                return $callback[0]->$callback[1]($element, $handle);

        if(false !== $foo = $this->getDefaultEntry())
            return $foo[0]->$foo[1]($element, $handle);

        throw new Hoa_Visitor_Exception(
            'No entry matches element %s.', 7, get_class($element));
    }
}
