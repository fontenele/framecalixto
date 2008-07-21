<?php
/**
 * PHP_UML (interface PHP_UML_XMI_Builder)
 *
 * PHP version 5
 *
 * @category PHP
 * @package	   Externas
 * @subpackage PHP-UML:XMI
 * @author   Baptiste Autin <ohlesbeauxjours@yahoo.fr> 
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version  SVN: $Revision: 43 $
 * @link     http://pear.php.net/package/PHP_UML
 * @since    $Date: 2008-05-13 01:15:27 +0200 (mar., 13 mai 2008) $
 */


/**
 * Interface for XMI factoring
 *
 * @category   PHP
 * @package	   Externas
 * @subpackage PHP-UML:XMI
 */
interface PHP_UML_XMI_Builder
{

    /**
     * Generates the XMI header
     * 
     * @param string &$model Name of the model (root package)
     * 
     * @return string XMI code
     */
    function getModelOpen(PHP_UML_Metamodel_Package &$model);

    /**
     * Generates the closing tag of a model
     * 
     * @return string XMI code
     */
    function getModelClose();
    
    /**
     * Generates the opening tag for a package
     * 
     * @param PHP_UML_Metamodel_Package $package Package to insert
     * 
     * @return string XMI code
     */
    function getPackageOpen(PHP_UML_Metamodel_Package $package);
    
    /**
     * Generates the closing tag of a package
     *
     * @return string XMI code
     */
    function getPackageClose();

    /**
     * Generates the opening tag for a sub-system (XMI 1), or a component (XMI 2)
     *
     * @param PHP_UML_Metamodel_Package $package Package to code as a subsystem
     * 
     * @return string XMI code
     */
    public function getSubsystemOpen(PHP_UML_Metamodel_Package $package);

    /**
     * Generates the closing tag for a subsystem / component
     * 
     * @return string XMI code
     */
    function getSubsystemClose();
    
    /**
     * Generates the XMI declarations of the main PHP types (official and unofficial)
     * 
     * @param PHP_UML_Type $type Datatype
     * 
     * @return string XMI code
     */
    function getDatatype(PHP_UML_Metamodel_Type $type);
    
    /**
     * Generates the XMI code for a class
     * 
     * @param PHP_UML_Metamodel_Class &$class Class
     * 
     * @return string XMI code
     */
    function getClass(PHP_UML_Metamodel_Class &$class);     
    
    /**
     * Generates the XMI code for an interface
     * 
     * @param PHP_UML_Metamodel_Interface &$interface Class
     * 
     * @return string XMI code
     */
    function getInterface(PHP_UML_Metamodel_Interface &$interface);
    
    /**
     * Generates the XMI code for an operation
     *
     * @param PHP_UML_Metamodel_Operation &$operation Operation
     * 
     * @return string XMI code
     */
    function getOperation(PHP_UML_Metamodel_Operation &$operation);

    /**
     * Generates the XMI code for the paramater of an operation
     * 
     * @param PHP_UML_Metamodel_Parameter &$parameter Parameter
     * 
     * @return string XMI code
     */
    function getParameter(PHP_UML_Metamodel_Parameter &$parameter);
    
    /**
     * Generates the XMI code for an artifact
     * 
     * @param PHP_UML_Metamodel_File $file File to add as an artifact
     * @param array                  &$mf  Manifested elements
     *                                     (array of PHP_UML_Metamodel_Class)
     * 
     * @return string XMI code
     */
    function getArtifact(PHP_UML_Metamodel_File $file, &$mf = array());
    
    /**
     * Generates the XMI for a component
     *
     * @param PHP_UML_Metamodel_File $file       File to add as a component
     * @param string                 $stereotype Stereotype of the file
     *
     * @return string XMI code
     */
    function getComponent(PHP_UML_Metamodel_File $file, $stereotype = '');
    
    /**
     * Generates the XMI code for a typed element (datatype, class, interface).
     * Required by getParameter(). Adds the default value (if any)
     *
     * @param PHP_UML_Metamodel_TypedElement &$parameter Element (a datatype, a class or an interface)
     * 
     * @return string XMI code           
     */
    function getParameterType(PHP_UML_Metamodel_TypedElement &$parameter);
    
    /**
     * Generates the XMI code for all the realization (interface implementations) of a given element
     *
     * @param PHP_UML_Metamodel_Class &$client Child element
     * 
     * @return string XMI code
     */
    function getRealizations(PHP_UML_Metamodel_Class &$client);
    
    /**
     * Generates the XMI code for all the inherited classes of a given element
     *
     * @param PHP_UML_Metamodel_Type &$client Child element
     * 
     * @return mixed In XMI 1.x, this returns an array, because XMI 1 needs two
     * declarations: the child element must be defined as "generalizable", in
     * addition to the generalization relationship. In XMI 2, only the relationship
     * is necessary, which is returned as a string.
     */
    function getGeneralizations(PHP_UML_Metamodel_Type &$client);
    
    /**
     * Generates the XMI code for a UML2 profile.
     *
     * @return string XMI code
     */
    function getProfile();
}

?>
