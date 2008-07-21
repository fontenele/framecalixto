<?php
/**
 * PHP Parser and UML/XMI generator. Reverse-engineering tool.
 *
 * A package to scan PHP files and directories, and get an UML/XMI representation
 * of the parsed classes/packages.
 * The XMI code can then be imported into a UML designer tool, like Rational Rose
 * or ArgoUML.
 *
 * PHP version 5
 *
 * @category PHP
 * @package	   Externas
 * @subpackage PHP-UML
 * @author   Baptiste Autin <ohlesbeauxjours@yahoo.fr> 
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version  SVN: $Revision: 43 $
 * @link     http://pear.php.net/package/PHP_UML
 * @since    $Date: 2008-05-13 01:15:27 +0200 (mar., 13 mai 2008) $
 */

require_once 'PEAR/Exception.php';
require_once 'PHP/Parser.php';
require_once 'Metamodel/EMOF.php';
require_once 'Metamodel/PHP.php';
require_once 'XMI/Builder.php';
require_once 'XMI/AbstractBuilder.php';
require_once 'XMI/BuilderImpl1.php';
require_once 'XMI/BuilderImpl2.php';


/**
 * Abstract iterator for the filesystem scan
 * 
 * @package	   Externas
 * @subpackage PHP-UML
 */
abstract class PHP_UML_Scanner
{
    /**
     * Directories to ignore during scan
     * @var array
     */
    public $ignoredDirectories = array();
 
    /**
     * Recursive search
     * @var bool
     */
    public $subDirectories = true;
 
    /**
     * Traverse recursively the directories for files to parse
     *
     * @param string $dir   Path folder to look into
     * @param int    $level Level of recursion
     *
     * @return void
     */
    protected function traverseDirectory($dir, $level = 1)
    {
        if (is_dir($dir)) {
            $this->atFolderIn($level, $dir);
            if ($dh = opendir($dir)) { 
                while (($file = readdir($dh)) !== false) {
                    if (array_search($file, $this->ignoredDirectories)===false) {
                        if (filetype($dir.$file) == 'dir') { 
                            if ($this->subDirectories) {
                                $this->traverseDirectory(
                                    $dir.$file.DIRECTORY_SEPARATOR, $level+1
                                );
                            }
                        } else { 
                            $this->atFile($level, $dir.$file);
                        }
                    }
                }
                closedir($dh);
            }
            $this->atFolderOut($level, $dir);
        }
    }
}


/**
 * The main class to instantiate
 * 
 * @package	   Externas
 * @subpackage PHP-UML
 */
class PHP_UML extends PHP_UML_Scanner
{
    const FILE      = 1;
    const DIR_OPEN  = 2;
    const DIR_CLOSE = 3;

    /**
     * Extensions of files to scan
     * @var Array
     */
    public $acceptedExtensions = array('php');

    /**
     * Filenames are added to classes and interfaces
     * @var bool
     */
    public $tagFilename = true;

    /**
     * A deployment view is created at the root level, enclosing the whole scanned
     * file system. Each file produces an artifact
     * @var bool
     */
    public $deploymentView = true;

    /**
     * A component view is created at the root level, enclosing the whole scanned
     * file system. Each file produces an component
     * @var bool
     */
    public $componentView = true;

    /**
     * Docblocks are read (package, param and return) : class, function and file
     * docblocks
     * @var bool
     */
    public $docblocks = true;

    /**
     * Keep the PHP variable prefix $
     * @var bool
     */
    public $dollar = true;
    
    /**
     * A reference to a PHP_UML_Metamodel_Superstructure object
     * Either parseFile() or parserDirectory() fill it, once their job is done.
     * Or you can set it yourself with a user-defined instance of superstructure.
     * @var PHP_UML_Metamodel_Superstructure
     */
    public $model;


    /**
     * XML Encoding (see the constructor)
     * @var string
     */
    private $_xmlEncoding;

    /**
     * The concatened XMI string
     * @var string
     */
    private $_xmi = '';

    /**
     * A reference to a PHP_UML_PHP_Parser object
     * @var PHP_UML_PHP_Parser
     */
    private $_parser;
    
    /**
     * A reference to a PHP_UML_XMI_BuilderImplX object
     * @var PHP_UML_XMI_Builder
     */
    private $_builder;

    /**
     * Base directory
     * @var string
     */
    private $_baseDir = '';

    /**
     * Stack of parsed files and folders. Used for building the phys/deplt views
     * @var array
     */
    private $_visited = array();

    /**
     * Start position of the scanned filepath
     * @var int
     */
    private $_baseDirPos = 0;


    /**
     * Constructor
     *
     */
    public function __construct()
    {
    }
       
    /**
     * Parse a PHP file, and builds the resulting XMI.
     *
     * @param mixed  $filename File(s) to parse. Can be a single file,
     *                         or an array of files.
     * @param string $model    Name of the model placed at root (enclosing pkg).
     *                         It is the "PHP global" namespace
     */
    public function parseFile($filename, $model = 'default')
    {
        $this->_parser = new PHP_UML_PHP_Parser($model, $this->docblocks, $this->dollar);
        
        $this->_visited = array();
        
        if (!is_array($filename)) {
            $filename = array($filename);
        }

        foreach ($filename as $filename_item) {
            if (file_exists($filename_item)) {
                $filename_item     = realpath($filename_item);
                $this->_baseDir    = dirname($filename_item);
                $this->_baseDirPos = strlen($this->_baseDir);

                $name = basename($filename_item);
                $path = $this->_baseDir.DIRECTORY_SEPARATOR;
                $this->atFile(1, $path.$name);
            }
            else
                throw new PHP_UML_Exception($filename_item.' : file not found.');
        }

        $this->model = &$this->_parser->model;
        $this->model->resolveAll();
    }
 
    /**
     * Parse a PHP file, and builds the resulting XMI.
     *
     * @param mixed  $path  Path(s) of the directories. Can be a single path,
     *                      or an array of pathes.
     * @param string $model Name of the model placed at root (enclosing pkg)
     */
    public function parseDirectory($path, $model = 'default')
    {
        $this->_parser = new PHP_UML_PHP_Parser($model, $this->docblocks, $this->dollar);
        
        $this->_visited = array();
        
        array_push($this->ignoredDirectories, '.');
        array_push($this->ignoredDirectories, '..'); 
        
        if (!is_array($path)) {
            $path = array($path);
        }

        foreach ($path as $path_item) {
            $this->_baseDir = realpath($path_item);
            $trailing       = substr($this->_baseDir, -1);

            if ($trailing != '/' && $trailing != '\\')
                $this->_baseDir .= DIRECTORY_SEPARATOR;

            $this->_baseDirPos = strlen($this->_baseDir);
            if ($this->_baseDir != '') {
                $this->traverseDirectory($this->_baseDir);
            }
            else
                throw new PHP_UML_Exception($path_item.' : unknown path.');
        }

        $this->model = &$this->_parser->model;
        $this->model->resolveAll();
    }

    /**
     * XMI Generator
     * Generates XMI corresponding to the PHP model stored in $this->model.
     * 
     * If you need to use this XMI Generator without any previous PHP parsing,
     * simply set $this->model with a proper PHP_UML_Metamodel_Superstructure object
     *  
     * @param float  $version     XMI Version For XMI 1.x, any value below 2.
     *                                        For XMI 2.x, any value above or equal to 2.
     * @param string $xmlEncoding XML Encoding
     */
    public function generateXMI($version = 2.1, $xmlEncoding = 'iso-8859-1')
    {
        $this->_xmlEncoding = $xmlEncoding;
        $this->_xmi         = '<?xml version="1.0" encoding="'.$this->_xmlEncoding.'"?>';
        $this->_builder     = PHP_UML_XMI_AbstractBuilder::factory($version);

        if (empty($this->model)) {
            throw new PHP_UML_Exception('No model given');
        }

        $_root       = $this->model->packages->get(0);
        $this->_xmi .= $this->_builder->getModelOpen($_root);

        foreach ($this->model->datatypes->getIterator() as $type)
            $this->_xmi .= $this->_builder->getDatatype($type);

        $this->addPackage($_root, true, 'package#');
        
        if ($this->componentView) {
            $this->_xmi .= $this->_insertComponentView();
        }

        if ($this->deploymentView) {
            $this->_xmi .= $this->_insertDeploymentView();
        }
        
        $this->_xmi .= $this->_builder->getModelClose();
    }
    
    /**
     * Save the previously generated XMI to a file.
     *
     * @param string $output_file Filename
     */
    public function saveXMI($output_file)
    {
        if ($ptr = fopen($output_file, 'w+')) {
            fwrite($ptr, $this->XMI);
            fclose($ptr);
        }
        else
            throw new PHP_UML_Exception(
                'File '.$output_file.' could not be created.'
            );
    }
 
    /**
     * Save a UML Profile XMI-suited with PHP_UML.
     *
     * THIS IS EXPERIMENTAL.
     * Only XMI and UML >= 2.x
     *
     * @param string $output_file Filename
     */
    private function _saveXMIProfile($output_file)
    {
        if ($ptr = fopen($output_file, 'w+')) {
            fwrite($ptr, '<?xml version="1.0" encoding="'.$this->_xmlEncoding.'"?>'.
                $this->_builder->getProfile()
            );
            fclose($ptr);
        } else {
            throw new PHP_UML_Exception(
                'File '.$output_file.' could not be created.'
            );
        }
    }
 
    /**
     * Accessor to the XMI.
     *
     * @param string $name Possible value(s) : "XMI" 
     *
     * @return string The XMI code, or a PHP_UML_Parser_Result object
     */
    public function __get($name)
    {
        switch($name) {
        case 'XMI':
            if (strtolower($this->_xmlEncoding)=='utf-8') {
                return utf8_encode($this->_xmi);
            } else {
                return $this->_xmi;
            }
            break;
        case 'XMIProfile':
            return $this->_builder->getProfile();
            break;
        default:
            return null;
        }
    }
    
    /**
     * Function executed each time a new file is scanned
     *
     * @param int    $level    Directories recursion level
     * @param string $filePath Current file path
     */
    protected function atFile($level, $filePath)
    {
        $pathParts = pathinfo($filePath);
        if (isset($pathParts['extension'])) {
            $extension = $pathParts['extension'];
        } else {
            $extension = '';
        }

        $relativePath = substr($filePath, $this->_baseDirPos);

        if (in_array($extension, $this->acceptedExtensions)) {
            $this->_parser->parse($this->_baseDir, $relativePath); 
        }

        $this->_visited[] = array(self::FILE => $relativePath);
    }

    /**
     * Enters a new folder
     *
     * @param int    $level Level of recursion
     * @param string $dir   Name of folder
     */
    protected function atFolderIn($level, $dir)
    {
        $this->_visited[] = array(self::DIR_OPEN => substr($dir, $this->_baseDirPos));
    }

    /**
     * Exits a folder
     *
     * @param int    $level Level of recursion
     * @param string $dir   Name of folder
     */
    protected function atFolderOut($level, $dir)
    {
        $this->_visited[] = array(self::DIR_CLOSE => substr($dir, $this->_baseDirPos));
    }

    /**
     * Traverses all packages, and adds recursively the elements found
     * to the "xmi" string property.
     * 
     * @param PHP_UML_Metamodel_Package $package  New package to traverse
     * @param bool                      $stripTag Omit package XMI tag
     * @param string                    $id       Identifier
     */
    protected function addPackage(PHP_UML_Metamodel_Package $package, $stripTag = false, $id = '')
    {
        if (!$stripTag) {
            $this->_xmi .= $this->_builder->getPackageOpen($package);
        }

        foreach ($package->ownedType as &$elt) {
            if (get_class($elt)=='PHP_UML_Metamodel_Interface')
                $this->_xmi .= $this->_builder->getInterface($elt);
            else
                $this->_xmi .= $this->_builder->getClass($elt);
        }

        foreach ($package->nestedPackage as $idx)
            $this->addPackage($this->model->packages->get($idx), false, $id.'::'.$package->name);

        if (!$stripTag) {
            $this->_xmi .= $this->_builder->getPackageClose();
        }
    }
    
    /**
     * Filename part of a given path
     *
     * @param string $x Filename
     *
     * @return string
     */
    private static function _getFilename($x)
    {
        $pathinfo = pathinfo($x);
        return $pathinfo['filename'];
    }
 
    /**
     * Basename part of a given path
     *
     * @param string $x Filename
     *
     * @return string
     */
    private static function _getBasename($x)
    {
        $pathparts = pathinfo(realpath($x));
        return $pathparts['basename'];
    }
    
    /**
     * Insert a physical view of the scanned file system, through components.
     * A file is viewed as a component.
     * Filesystem's folders are treated as subsystems in UML1, and
     * as nested components in UML2.
     *
     * @return string XMI
     */
    private function _insertComponentView()
    {
        $factory  = &$this->_builder;
        $cv       = new PHP_UML_Metamodel_Package;
        $cv->name = 'Component View';
        $cv->uri  = $cv->name;
        $str      = $factory->getSubsystemOpen($cv);
        foreach ($this->_visited as $value) {
            $keys    = array_keys($value);
            $type    = $keys[0];
            $element = $value[$type];
            if ($element!='') {
                switch($type) {
                case self::FILE :
                    $artifact       = new PHP_UML_Metamodel_File;
                    $artifact->name = $element;

                    $str .= $factory->getComponent(
                        $artifact, $factory->guessStereotype($element)
                    );
                    break;
                case self::DIR_OPEN :
                    $cv       = new PHP_UML_Metamodel_Package;
                    $cv->name = basename($element);
                    $cv->uri  = $factory->generateID();
                    $str     .= $factory->getSubsystemOpen($cv);
                    break;
                case self::DIR_CLOSE :
                    $str .= $factory->getSubsystemClose();
                    break;
                }
            }
        }
        $str .= $factory->getSubsystemClose();
        return $str;
    }

    /**
     * Insert a deployment view of the scanned file system, through artifacts.
     * A file is viewed as an artifact (artifacts exist since UML 1.4)
     * Filesystem's folders are treated as packages.
     *
     * @return string XMI
     */
    private function _insertDeploymentView()
    {
        $factory  = &$this->_builder;
        $dv       = new PHP_UML_Metamodel_Package;
        $dv->name = 'Deployment View';
        $dv->uri  = $dv->name;
        $str      = $factory->getPackageOpen($dv);
        foreach ($this->_visited as $value) {
            $keys    = array_keys($value);
            $type    = $keys[0];
            $element = $value[$type];
            if ($element!='') {
                switch($type) {
                case self::FILE :
                    $mfElts         = $this->_getManifestedElements($element);
                    $artifact       = new PHP_UML_Metamodel_File;
                    $artifact->name = $element;
                    $str           .= $factory->getArtifact($artifact, $mfElts);
                    break;
                case self::DIR_OPEN :
                    $dv       = new PHP_UML_Metamodel_Package;
                    $dv->name = basename($element);
                    $dv->uri  = $factory->generateID();
                    $str     .= $factory->getPackageOpen($dv);
                    break;
                case self::DIR_CLOSE :
                    $str .= $factory->getPackageClose();
                    break;
                }
            }
        }
        $str .= $factory->getPackageClose();
        return $str;
    }

    /**
     * Retrieves the IDs of the manifested elements of a given artifact
     * (TODO : doing the job during parsing along with new superstructure elem)
     *
     * @param string $artifact Filepath of the artifact
     * 
     * @return array
     */
    private function _getManifestedElements($artifact)
    {
        $files = &$this->model->files;
        $idx   = $files->searchElement($artifact);
        if ($idx===false)
            return array();
        $file = $files->get($idx);
        $elts = array();
        foreach ($this->model->classes->getIterator() as $item) {
            if ($item->file == $file) {
                $elts[] = $item;
            }
        }
        return $elts;
    }

}

/**
 * Subclass for PHP_UML_Exception
 * 
 * @package	   Externas
 * @subpackage PHP-UML
 * 
 */
class PHP_UML_Exception extends PEAR_Exception
{
}

/**
 * Maintains of stack of warning messages. Worth to being checked, especially
 * if multiple classes in your PHP files have the same name...
 * 
 * @package	   Externas
 * @subpackage PHP-UML
 */
class PHP_UML_Warning
{
    /**
     * The $stack to read.
     * @var array
     */
    static public $stack;
    
    /**
     * Adds a warning message to the pile
     *
     * @param string $message The warning message to add
     */
    static public function add($message)
    {
        self::$stack[] = $message;
    }
    
    /**
     * Clears the pile
     */
    static public function clear()
    {
        self::$stack = array();
    }
}

?>
