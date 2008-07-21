<?php
/**
 * PHP_UML (PHP_UML_XMI_AbstractBuilder)
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
 * Abstract class to build UML elements through XMI code.
 * Only basic UML concepts are available.
 * To deal with the two different versions of XMI (1.4 and 2.1), you must use one of
 * the two specialized versions:
 * PHP_UML_XMI_BuilderImpl1, or PHP_UML_XMI_BuilderImpl2
 *
 * @package	   Externas
 * @subpackage PHP-UML:XMI
 */
abstract class PHP_UML_XMI_AbstractBuilder implements PHP_UML_XMI_Builder
{
    const EXPORTER_NAME = 'PEAR::PHP_UML';
    const PHP_FILE      = 'PHP File';

    static public $stereotypes = array('File', self::PHP_FILE);
    static public $extensions  = array(''=>'File', 'php'=>self::PHP_FILE);

    static private $_id = 0;
    
    /**
     * Generates an ID for an element. A partial identifier can be provided
     * (used for classes and their idrefs)
     *
     * @param string $type Type
     * @param string $id   Identifier
     * 
     * @return string ID
     */
    static public function generateID($type = null, $id = null)
    {
        if (is_null($id))
            return md5(self::EXPORTER_NAME.'#'.self::$_id++);
        else
            return md5(self::EXPORTER_NAME.'#'.$type.'#'.$id);
    }
    
    /**
     * Retrieves the ID of a stereotype, given a filename
     *
     * @param string $filename The file name
     *
     * @return string The PHP_UML ID of the matching extension 
     */
    static public function guessStereotype($filename = '')
    {
        $pathParts = pathinfo($filename);
        $extension = isset($pathParts['extension']) ? $pathParts['extension'] : '';

        if (isset(self::$extensions[$extension]))
            return self::generateID('stereotype', self::$extensions[$extension]);
        else
            return self::generateID('stereotype', self::$extensions['']);
    }

    /**
     * Retrieves all the source files defining the elements nested in a package
     * @param array $package
     * 
     * @return array
     
    protected static function getFilesInPackage(Array &$package)
    {
        $filesList = array();
        if (!empty($package)) {
            foreach ($package as $c => &$value) {
                if (!in_array($value['file'], $filesList))
                    $filesList[] = $value['file'];
            }
        }
        return $filesList;
    }
    */

    /**
     * Factory method. Retrieves a proper implementation class,
     * matching the XMI version.
     *
     * @param int $version XMI version
     * 
     * @return PHP_UML_XMI_Builder An XMI builder object 
     */
    static function factory($version)
    {
        if ($version < 2)
            return new PHP_UML_XMI_BuilderImpl1();
        else
            return new PHP_UML_XMI_BuilderImpl2();
    }
    
}
?>
