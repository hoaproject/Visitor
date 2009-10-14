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
 * @subpackage  Hoa_Visitor_Registry_Aggregate
 *
 */

/**
 * Hoa_Framework
 */
require_once 'Framework.php';

/**
 * Hoa_Visitor_Registry_Exception
 */
import('Visitor.Registry.Exception');

/**
 * Hoa_Visitor_Registry
 */
import('Visitor.Registry');

/**
 * Class Hoa_Visitor_Registry_Aggregate.
 *
 * Represent an aggregate entry in the registry, i.e. an aggregate visitor part.
 *
 * @author      Ivan ENDERLIN <ivan.enderlin@hoa-project.net>
 * @copyright   Copyright (c) 2007, 2008 Ivan ENDERLIN.
 * @license     http://gnu.org/licenses/gpl.txt GNU GPL
 * @since       PHP 5
 * @version     0.1
 * @package     Hoa_Visitor
 * @subpackage  Hoa_Visitor_Registry_Aggregate
 */

class Hoa_Visitor_Registry_Aggregate {

    /**
     * The registry.
     *
     * @var Hoa_Visitor_Registry object
     */
    protected $_registry = null;



    /**
     * Constructor. Set the registry.
     *
     * @access  public
     * @param   Hoa_Visitor_Registry  $registry    Registry.
     * @return  void
     */
    public function __construct ( Hoa_Visitor_Registry $registry ) {

        $this->setRegistry($registry);

        return;
    }

    /**
     * Set the registry.
     *
     * @access  protected
     * @param   Hoa_Visitor_Registry  $registry    Registry.
     * @return  Hoa_Visitor_Registry
     */
    protected function setRegistry ( Hoa_Visitor_Registry $registry ) {

        $old             = $this->_registry;
        $this->_registry = $registry;

        return $old;
    }

    /**
     * Get the registry, i.e. the visitor.
     *
     * @access  public
     * @return  Hoa_Visitor_Registry
     */
    public function getVisitor ( ) {

        return $this->_registry;
    }
}
