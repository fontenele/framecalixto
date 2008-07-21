<?php
/**
 * PHP_UML (PHP_UML_PHP_Parser)
 *
 * PHP version 5
 *
 * @category PHP
 * @package	   Externas
 * @subpackage PHP-UML:PHP
 * @author   Baptiste Autin <ohlesbeauxjours@yahoo.fr> 
 * @license  http://www.gnu.org/licenses/lgpl.html LGPL License 3
 * @version  SVN: $Revision: 43 $
 * @link     http://pear.php.net/package/PHP_UML
 * @since    $Date: 2008-05-13 01:15:27 +0200 (mar., 13 mai 2008) $
 */

/**
 * A combination of string iteration and regular expressions.
 * It stores all the elements if finds in a PHP_UML_Metamodel_Superstructure
 * 
 * Most navigabilities between associated elements are bidirectional
 * (the packages know their owned elements, and the classes know their
 * nesting package)
 * At first, relations use string references (the name of the element).
 * Once the parsing is completed, the method finalize() must be called,
 * so that the named references be replaced by PHP references (&$xxx).
 *
 * @category   PHP
 * @package	   Externas
 * @subpackage PHP-UML:PHP
 *
 */
class PHP_UML_PHP_Parser
{
    /**
     * Regular expressions for a PHP variable
     */
    const PREG_VARIABLE   = '[a-z_\\x7f-\\xff][a-z0-9_\\x7f-\\xff]*';
    const PREG_CLASS      = '[a-z:_\\x7f-\\xff][a-z:0-9_\\x7f-\\xff]*';
    const PREG_HEREDOC    = '<<<([^<\n\r]*)[\n\r]';
    const PREG_COMMENT    = '\/\/[^\n]*\n|\/\*.*\*\/|#[^\n]*\n';
    const PREG_PACKAGE    = '\*[ \t]+@package[ \t]+([^\s]+)\s';
    const PREG_SUBPACKAGE = '\*[ \t]+@subpackage[ \t]+([^\s]+)\s';

    /**
     * Reference to a PHP_UML_Metamodel_Superstructure
     * (where the parser stores all the program elements it finds)
     *
     * @var PHP_UML_Metamodel_Superstructure
     */
    public $model;

    private $_text = '';
    private $_filename;
    private $_docblocks;
    private $_dollar;
    /**
     * If set to true during parsing, suspends prg elements analysis
     *
     * @var bool
     */
    private $_cancel;

    /**
     * Current package index (which does not necessary match the last one put
     * over $packages stack)
     *
     * @var int
     */
    private $_currentPackage;

    /**
     * Last docblock (of class, or function) met
     *
     * @var string
     */
    private $_lastDcblck = '';
    
    /**
     * Current PHP "namespace" imports (set with the "use" instruction)
     *
     * @var string
     */
    private $_imports = array();
    
    /**
     * Constructor
     *
     * @param string $root      Root package name
     * @param bool   $docblocks True = docblocks are scanned
     * @param bool   $dollar    True = $ in variables is kept
     */
    public function __construct($root, $docblocks = true, $dollar = true)
    {
        $this->_docblocks = $docblocks;
        $this->_dollar    = $dollar;

        $this->model = new PHP_UML_Metamodel_Superstructure();

        $this->_currentPackage = $this->_addPackage($root);
    }

    /**
     * Parses a PHP file
     * 
     * @param string $fileBase Base directory
     * @param string $filePath Pathfile (relative to $fileBase)
     */
    public function parse($fileBase, $filePath)
    {
        $filename = $fileBase.$filePath;         // Absolute path

        if (file_exists($filename)) {
            $this->_text     = file_get_contents($filename);
            $this->_filename = $filename;
        } else {
            throw new PHP_UML_Exception('File '.$filename.' does not exist.');
        }

        $f       = new PHP_UML_Metamodel_File;
        $f->name = $filePath;
        $this->model->files->add($f);

        $set         = array();
        $lenText     = strlen($this->_text);
        $modePHP     = false;
        $modeQuotesD = false;    // double quote
        $modeQuotesS = false;    // single quote
        $modeHeredoc = false;
        $modeQuotes  = $modeQuotesD || $modeQuotesS || $modeHeredoc;
        $heredoc     = '';
        $attributes  = array(); // a collector for attributes (public, static..)
        $className   = '';
        $propName    = '';
        $funcName    = '';

        $lastCsLevel = 0; // braces level at which current class is defined
        $lastFnLevel = 0; // braces level at which current function is defined
        $lastFnPos   = 0; // character position of last visited function
        $lastPrPos   = 0; // character position of last visited prop. default. value

        $modeClass      = false;
        $modeFunction   = false;
        $modeInterface  = false;
        $modeProperty   = false;
        $modeExpression = false;
        
        $this->_imports = array();

        $i        = 0;    // current position in the text
        $level    = 0;    // curly braces level
        $levelPar = 0;    // parens level
        
        $this->_currentPackage = 0;
        if ($this->_docblocks) {
            // First, let's have a look at the file docblock :
            $package = $this->_getFilePackage();
            if ($package!='')
                $this->_currentPackage = $this->_addPackage($package, 0);
        }

        while ($i<$lenText) {
            $one    = substr($this->_text, $i, 1);
            $two    = substr($this->_text, $i, 2);
            $remain = substr($this->_text, $i);

            if ((!$modePHP)) {
                if ($two=='<?') {
                    $modePHP = true;
                    $i      += 2;
                } else {
                    $nxt = strpos($this->_text, '<?', $i);
                    if ($nxt===false)
                        $i = $lenText;
                    else
                        $i = $nxt;
                }
            } else {
                if ((!$modeQuotes) && $two=='?>') {
                    $modePHP = false;
                    $i      += 2;
                } elseif ((!$modeQuotes) && $two=='/*') {
                    $nxt = strpos($this->_text, '*/', $i+2);
                    if ($nxt===false)
                        $i = $lenText;
                    else
                        $i = ($nxt+2);
                } elseif ((!$modeQuotes) && ($two=='//' || $one=='#')) {
                    $nxt = preg_match(
                        '/(\n|\?>)/', $this->_text, $set, PREG_OFFSET_CAPTURE, $i
                    );
                    if ($nxt==0)
                        $i = $lenText;
                    else
                        $i = $set[1][1];
                } elseif ($modeQuotes && $two=='\\\\') {
                    $i += 2;
                } elseif ($modeQuotesD && $two=='\"') {
                    $i += 2;
                } elseif ($modeQuotesS && $two=='\\\'') {
                    $i += 2;
                } elseif ((!$modeQuotes)
                    && preg_match('/^'.self::PREG_HEREDOC.'/s', $remain, $set)>0
                ) {
                    $heredoc     = trim($set[1]);
                    $modeHeredoc = true;
                    $modeQuotes  = $modeQuotesD || $modeQuotesS || $modeHeredoc;
                    $i          += strlen($set[0]);
                } elseif ($modeHeredoc
                    && (preg_match('/^'.$heredoc.'/s', $remain, $set)>0)
                ) {
                    $heredoc     = '';
                    $modeHeredoc = false;
                    $modeQuotes  = $modeQuotesD || $modeQuotesS || $modeHeredoc;
                    $i          += strlen($set[0]);
                } elseif ((!($modeQuotesS || $modeHeredoc)) && $this->_text[$i]=='"') {
                    $modeQuotesD = (!$modeQuotesD);
                    $modeQuotes  = $modeQuotesD || $modeQuotesS || $modeHeredoc;
                    $i++;
                } elseif ((!($modeQuotesD || $modeHeredoc)) && $this->_text[$i]=="'") {
                    $modeQuotesS = (!$modeQuotesS);
                    $modeQuotes  = $modeQuotesD || $modeQuotesS || $modeHeredoc;
                    $i++;
                } elseif ((!$modeQuotes)) {

                    if ($one=='{') {
                        if ($modeClass && $className!='') {
                            list($tmpPkg, $className) = $this->_getCurrentPackage($className);
                            if ($modeInterface)
                                $this->_addInterface($className, $attributes, $tmpPkg);
                            else
                                $this->_addClass($className, $attributes, $tmpPkg);
                            // Classes are not always defined at 1st level :
                            $lastCsLevel = $level+1;
                            $attributes  = array();
                            $className   = '';

                            $this->_lastDcblck = '';
                        }
                        $i++;
                        $level++;
                        
                    } elseif ($one=='}') {
                        if ($modeClass && $level == $lastCsLevel)
                            $modeClass = false;
                        $level--;
                        $i++;
                    } elseif ($one=='(') {
                        $levelPar++;
                        $i++;
                    } elseif ($one==')') {

                        if ($modeFunction && $levelPar==1 && !$this->_cancel) {
                            $this->_addOperation(
                                $funcName, $attributes, $modeInterface
                            );
                            $str = substr($this->_text, $lastFnPos, $i-$lastFnPos);
                            if ($str!='')
                                $this->_addParameters($str, $this->_lastDcblck);
                            $attributes   = array();
                            $propName     = '';
                            $modeFunction = false;

                            $this->_lastDcblck = '';
                        }
                        $levelPar--;
                        $i++;

                    } elseif ($this->_findNamespace($remain, $set)>0) {
                        $this->_currentPackage = $this->_addPackage($set[1], 0);

                        $i += strlen($set[0]);
                    } elseif ($this->_findUse($remain, $set)>0) {
                        if (!isset($set[2])) {
                            list(
                                $pos, $first, $last
                            ) = $this->model->getPackagePathParts($set[1], false);
                            $set[2] = $last;
                        }
                        $this->_addImport($set[1], $set[2]);

                        $i += strlen($set[0]);
                    } elseif ($this->_findAttr($remain, $set)>0) {

                        $attributes[]       = strtolower($set[1]);
                        $this->_lastDcblck .= $this->_revDocblock(
                            substr($this->_text, 0, $i)
                        );

                        $i += strlen($set[0]);
                    } elseif ($this->_findClass($remain, $set)>0) {

                        // Class / Interface :
                        $modeClass     = true;
                        $className     = trim($set[2]);
                        $modeInterface = (strtolower($set[1])=='interface');

                        $this->_lastDcblck .= $this->_revDocblock(
                            substr($this->_text, 0, $i)
                        );

                        $i += strlen($set[0]);

                    } elseif ($modeClass && $className!=''
                    && $this->_findClassRelation($remain, $set)>0) {

                        // Superclass :
                        $attributes[$set[1]] = $set[2];

                        $i += strlen($set[0]);

                    } elseif ($modeClass && $level==$lastCsLevel
                    && $this->_findFunction($remain, $set)>0
                    && !$this->_cancel) {

                        $this->_lastDcblck .= $this->_revDocblock(
                            substr($this->_text, 0, $i)
                        );

                        $funcName     = $set[1];
                        $modeFunction = true;
                        $levelPar     = 1;
                        $i           += strlen($set[0]);
                        $lastFnPos    = $i;
                        $lastFnLevel  = $level;

                    } elseif ($modeClass && (!$modeFunction) && $level==$lastCsLevel
                    && (!$modeExpression)
                    && $this->_findProperty($remain, $set)>0) {

                        $i           += strlen($set[0]);
                        $lastPrPos    = $i;
                        $propName     = $set[1];
                        $modeProperty = true;
                        if (isset($set[2]) && $set[2]=='=')
                            $modeExpression = true;

                    } elseif ($modeProperty && $one==';' && !$this->_cancel) {

                        $default = $this->_stripComments(
                            trim(substr($this->_text, $lastPrPos, $i-$lastPrPos))
                        );
                        $this->_addProperty($propName, $attributes, $default);
                        $modeProperty   = false;
                        $modeExpression = false;
                        $attributes     = array();
                        $propName       = '';

                        $this->_lastDcblck = '';
                        $i++;

                    } else
                        $i++;
                } else
                    $i++;
            }
        }
        //print_r($this->model->packages);
    }


    /**
     * Adds a package to the $packages stack
     *
     * @param string $name    Name of the package
     * @param int    $baseIdx Nesting pkg (pkg where we want the pkg to be created)
     *
     * @return int Index of the newly created package (or of the existing one)
     */
    private function _addPackage($name, $baseIdx = null)
    {
        list($pos, $name, $following) = $this->model->getPackagePathParts($name);
        
        //$index = $this->model->packages->searchElement($name);
        if (!is_null($baseIdx)) {
            $pkg   = $this->model->packages->get($baseIdx);
            $index = $this->model->searchIntoSubpackage($pkg, $name);
        } else {
            $index = false;
        }

        // ok, pkg does not exist, let's add it:
        if ($index===false) {
            $p                 = new PHP_UML_Metamodel_Package;
            $p->name           = $name;
            $p->nestingPackage = $baseIdx;
            $p->nestedPackage  = array();
            $this->model->packages->add($p);
            $index  = $this->model->packages->key();
            $p->uri = $index;
            if (!is_null($baseIdx)) {
                $parent                  = $pkg;
                $parent->nestedPackage[] = $index;
            }               
        }

        if (!($pos===false)) {
            $index = $this->_addPackage($following, $index);
        }
        return $index;
    }

    /**
     * Retrieves the index of the current package, depending on the @package in the
     * last parsed docblock, and/or the potential presence of a package path in the
     * class name.
     * Adds new packages to the $packages stack if necessary.
     * Normally used at each new class/interface insertion.
     *
     * @param string $class Class/Interface name
     *
     * @return array Result array: [current package, class name]
     */
    private function _getCurrentPackage($class)
    {
        // Where's the class package ?  
        // Is there a package path in the class name itself ?
        list($pos, $pkg, $name) = $this->model->getPackagePathParts($class, false);
        if (!($pos===false)) {
            return array($this->_addPackage($pkg, 0), $name);
        }
        /*if (!empty($this->_imports)) {
            $class = $this->_resolveImport($class);
            list($pos, $pkg, $name) = $this->model->getPackagePathParts(
                $class, false);
            return array($this->_addPackage($pkg, 0), $class);            
        }*/
        if ($this->_docblocks) {
            // Is there a @package in the class docblock ?
            $r = $this->_findPackageInDocblock($this->_lastDcblck, $set);
            if ($r>0) {
                return array($this->_addPackage($set[1], 0), $class);
            }
        }
        // No ? Then we return the current known package:
        return array($this->_currentPackage, $class);
    }

    /**
     * Adds an interface to the "interfaces" stack
     *
     * @param string $name          Interface name
     * @param array  $attr          Some interface attributes (superclasses)
     * @param int    $classPkgIndex The index of the current nesting package
     */
    private function _addInterface($name, $attr, $classPkgIndex)
    {
        $c = new PHP_UML_Metamodel_Interface;

        if (isset($attr['extends'])) {
            $c->superClass[] = trim($attr['extends']);
        }
        $c->name       = $name;
        $c->isAbstract = in_array('abstract', $attr);
        $c->file       = &$this->model->files->current();
        $nestingPkg    = $this->model->packages->get($classPkgIndex);
        $c->package    = &$nestingPkg;
        if ($this->model->searchTypeIntoPackage($c->package, $c->name)===false) {
            $nestingPkg->ownedType[] = &$c;
            $c->implements           = null;
            $this->model->interfaces->add($c);
        }
        else
            PHP_UML_Warning::add(
                'Interface '.$c->name.' already defined, in '.$this->_filename
            );
    }

    /**
     * Adds a class to the "classes" stack ($this->model->classes)
     *
     * @param string $name          Class name
     * @param array  $attr          Some class attributes (superclasses)
     * @param int    $classPkgIndex The index of the current nesting package
     */
    private function _addClass($name, $attr, $classPkgIndex)
    {
        $c = new PHP_UML_Metamodel_Class;

        if (isset($attr['extends'])) {
            $c->superClass[] = $this->_resolveImport(trim($attr['extends']));
        }
        if (isset($attr['implements'])) {
            $imp = explode(',', $attr['implements']);
            foreach ($imp as $item) {
                $c->implements[] = $this->_resolveImport(trim($item));
            }
        }
        $c->name       = $name;
        $c->isAbstract = in_array('abstract', $attr);
        $c->file       = &$this->model->files->current();
        $nestingPkg    = $this->model->packages->get($classPkgIndex);
        $c->package    = &$nestingPkg;
        if ($this->model->searchTypeIntoPackage($c->package, $c->name)===false) {
            $nestingPkg->ownedType[] = &$c;
            $this->model->classes->add($c);
            $this->_cancel = false;
        } else {
            PHP_UML_Warning::add(
                'Class '.$c->name.' already defined, in '.$this->_filename
            );
            $this->_cancel = true;
        }
    }

    /**
     * Adds an operation to the "operations" stack
     *
     * @param string $name          Operation name
     * @param array  $attr          Some characteristics of the operation
     * @param bool   $modeInterface If yes, it belongs to an interface
     */
    private function _addOperation($name, array $attr, $modeInterface)
    {
        $f = new PHP_UML_Metamodel_Operation;

        $f->name           = trim(str_replace('&', '', $name));
        $f->isInstantiable = !in_array('static', $attr);
        $f->isAbstract     = in_array('abstract', $attr);
        $f->visibility     = self::_getVisibility($attr);
        $this->model->operations->add($f);
        $obj = null;
        if ($modeInterface)
            $obj = &$this->model->interfaces->current();
        else
            $obj = &$this->model->classes->current();

        $obj->ownedOperation[] = &$f;
        $f->class              = &$obj;
    }

    /**
     * Adds a property to the "property" stack
     *
     * @param string $name    Property name
     * @param array  $attr    Characteristics of the property
     * @param string $default Default value
     */
    private function _addProperty($name, array $attr, $default)
    {
        $p = new PHP_UML_Metamodel_Property;

        $p->name           = $name;
        $p->isReadOnly     = in_array('const', $attr);
        $p->isInstantiable = !(in_array('static', $attr) || $p->isReadOnly);
        $p->visibility     = self::_getVisibility($attr);
        $p->default        = self::_stripComments($default);
        $p->type           = self::_guessType($p->default);

        $class                   = &$this->model->classes->current();
        $class->ownedAttribute[] = &$p;
        $p->class                = &$class;
    }

    /**
     * Adds a parameter to the current operation
     *
     * @param array  $set      Array of parameters
     * @param string $docblock Nesting method's docblock where to find additional information
     */
    private function _addParameters($set, $docblock)
    {
        $function = &$this->model->operations->current();

        $docblockParameter = array();
        if ($this->_docblocks) {
            $set_comment = $this->_findParamInDocblock($docblock);
            $return      = false;
            foreach ($set_comment as $k) {
                if (substr($k[1], 0, 6)=='return' && !$return) {
                    $pr                         = new PHP_UML_Metamodel_Parameter;
                    $pr->name                   = 'return';
                    $pr->direction              = 'return';
                    $pr->type                   = $this->_resolveImport($k[2]);
                    $pr->operation              = &$function;
                    $function->ownedParameter[] = &$pr;
                    $return                     = true;
                } elseif ($k[1]=='param') {
                    $docblockParameter[self::_cleanVariable($k[3])] = $k[2];
                }
            }
        }
        $arr = explode(',', $set);
        foreach ($arr as &$parameters) {
            $parametre     = explode('=', $parameters);
            $parameterName = $this->_cleanParameter($parametre[0]);
        
            // Any default value given ?
            if (count($parametre)>1)
                $default = $this->_cleanParameter($parametre[1]);
            else
                $default = '';
            // Any @param in the method docblock ?
            $tmpParameterName = self::_cleanVariable($parameterName);
            if (isset($docblockParameter[$tmpParameterName]))
                $param = $docblockParameter[$tmpParameterName];
            else
                $param = '';
            // By ref or by value ? (inout/in)
            if (strpos($parameterName, '&')===false)
                $direction = 'in';
            else
                $direction = 'inout';

            list($name, $type) = self::_splitNameType($parameterName, $default, $param);
            
            $p            = new PHP_UML_Metamodel_Parameter;
            $p->name      = $name;
            $p->default   = $default;
            $p->type      = $type;
            $p->direction = $direction;
            $p->operation = &$function;
            $this->model->parameters->add($p);
            $function->ownedParameter[] = $p;
        }
    }

    /**
     * Regexp for matching a class or an interface
     *
     * @param string &$text Text to search in
     * @param array  &$set  Results
     * 
     * @return int
     */
    private function _findClass(&$text, &$set)
    {
        return preg_match(
            '/^\s+(class|interface)\s+('.self::PREG_CLASS.')/si', $text, $set
        );
    }

    /**
     * Regexp for matching the inheritance/implementation relations
     *
     * @param string &$text Text to search in
     * @param array  &$set  Results
     * 
     * @return int
     */
    private function _findClassRelation(&$text, &$set)
    {
        $r = preg_match(
            '/^\s+(implements)\s+(('.self::PREG_CLASS.'[ \t,]*)+)?/si', $text, $set
        );
        if ($r==0) {
            $r = preg_match(
                '/^\s+(extends)\s+('.self::PREG_CLASS.')?/si', $text, $set
            );    
            if ($r>0) {
                $set = array($set[0], 'extends', $set[2]);
            }
        } else {
            $set = array($set[0], 'implements', $set[2]);
        }
        return $r;
    }

    /**
     * Regexp for matching a namespace instruction
     *
     * @param string &$text Text to seach in
     * @param array  &$set  Results
     * 
     * @return int
     */
    private function _findNamespace(&$text, &$set)
    {
        return preg_match('/^namespace\s+('.
            self::PREG_CLASS.')[ \t]*;/si', $text, $set
        );
    }

    /**
     * Regexp for matching a use instruction
     *
     * @param string &$text Text to seach in
     * @param array  &$set  Results
     * 
     * @return int
     */
    private function _findUse(&$text, &$set)
    {
        return preg_match('/^use\s+('.
            self::PREG_CLASS.')(?:[ \t]+as[ \t]+('.self::PREG_CLASS.'))?[ \t]*;/si', $text, $set
        );
    }
    
    /**
     * Regexp for matching a package declaration in a docblock
     *
     * @param string $text Text to search in
     * @param array  &$set Results
     * 
     * @return int
     */
    private function _findPackageInDocblock($text, &$set)
    {
        $p = preg_match('/'.self::PREG_PACKAGE.'/si', $text, $set);
        $s = preg_match_all('/'.self::PREG_SUBPACKAGE.'/si', $text, $sub, PREG_SET_ORDER);
        if ($p>0 && $s>0) {
            foreach ($sub as $subItem) {
                $set[1] .= '::'.$subItem[1];
            }
        }
        return $p; 
    }

    /**
     * Regexp for matching the file docblock. It matches until it finds the 1st @package
     *
     * @param string &$text Text to search in
     * @param array  &$set  Preg result
     *
     * @return int
     */
    private function _findFileDocblock(&$text, &$set)
    {
        return preg_match('/^\s*<\?(?:php)?\s+'.
            '(\/\/[^\n\r]*\s+|'.
            '\/\*[^\n\r]*?\*\/\s+)?'.
            '\/\*(.*?)\*\//si', $text, $set);
    }

    /**
     * Regexp for matching a class property
     *
     * @param string &$text Text to seach in
     * @param array  &$set  Results
     * 
     * @return int
     */
    private function _findProperty(&$text, &$set)
    {
        return preg_match('/^(\$?'.self::PREG_VARIABLE.')\s*(=)?/si', $text, $set);
    }

    /**
     * Regexp for matching the characteristics of a typed element
     * (visibility, instantiability, abstraction, etc.)
     *
     * @param string &$text Text to seach in
     * @param array  &$set  Results
     * 
     * @return int
     */
    private function _findAttr(&$text, &$set)
    {
        return preg_match(
            '/^[\s](var|public|protected|private|const|static|abstract|final)/si',
            $text, $set
        );
    }

    /**
     * Regexp for matching a function (without its body)
     *
     * @param string &$text Text to seach in
     * @param array  &$set  Results
     * 
     * @return int
     */
    private function _findFunction(&$text, &$set)
    {
        return preg_match(
            '/^function\s([&\s]*'.self::PREG_VARIABLE.')\s*\(/si', $text, $set
        );
    }

    /**
     * Regexp for matching a docblock backwards.
     * Used to get the class/interface/method docblocks.
     *
     * @param string $text The text to search in (from the end)
     *
     * @return string The docblock (if found).
     */
    private function _revDocblock($text)
    {
        $r = preg_match('/^\s*\/\*(.*?)\*\//s', strrev($text), $set);
        if ($r>0)
            return strrev($set[1]);
        else
            return '';
    }

    /**
     * Regexp for matching a typical docblock line in a docblock
     *
     * @param string $text Text to search in
     *
     * @return array Preg results
     */
    private function _findParamInDocblock($text)
    {
        $r = preg_match_all(
            '/\*[ \t]*@([\w]+)[ \t]+([\w]+)[ \t]*([\w\$]*)\s/', $text, $set,
            PREG_SET_ORDER
        );
        return $set;
    }

    /**
     * Retrieves the docblock-level package of the current parsed file
     *
     * @return string The package name
     */
    private function _getFilePackage()
    {
        $r = $this->_findFileDocblock($this->_text, $set);
        if ($r>0) {
            $r = $this->_findPackageInDocblock($set[1], $doc);
            if ($r>0) {
                return $doc[1];    
            } else {
                $r = $this->_findPackageInDocblock($set[2], $doc);
                if ($r>0)
                    return $doc[1];
            }
        }
        return '';
    }
    
    /**
     * Cleans up a string (removes unnecessary white chrs + encodes HTML entities)
     *
     * @param string $str Text to filter
     * 
     * @return string
     */
    private function _cleanParameter($str)
    {
        if (!$this->_dollar) {
            $str = str_replace('$', '', $str);
        }
        return htmlspecialchars(preg_replace('/\s\s+/', ' ', trim($str)));
    }


    /**
     * Splits a parameter into its name and type
     *
     * @param string $parameter The parameter to analyse
     * @param string $default   Default value
     * @param string $param     Value of docblock "param"
     *
     * @return array 
     */
    private static function _splitNameType($parameter, $default = '', $param = '')
    {
        $exp_param_name = explode(' ', trim($parameter));
        $nat            = array();
        if (count($exp_param_name)>1) {
            // Parameter like "MyType $myVariable"
            $nat[0] = trim($exp_param_name[1]);
            $nat[1] = trim($exp_param_name[0]);
        } else {
            // Parameter like "$myVariable"
            $nat[0] = $exp_param_name[0];
            if ($param!='') {
                // if a @param was provided, let's use it : 
                $nat[1] = $param;
            }
            else
                $nat[1] = self::_guessType($default);
        }
        return $nat;
    }

    /**
     * Removes dollar sign, and HTML entity &amp;
     *
     * @param string $str Text
     * 
     * @return string
     */
    static private function _cleanVariable($str)
    {
        return str_replace('$', '', str_replace('&amp;', '', $str));
    }

    /**
     * Removes the PHP comment in a string
     *
     * @param string $str Text
     * 
     * @return string The filtered text
     */
    static private function _stripComments($str)
    {
        $patt = array('/'.self::PREG_COMMENT.'/s');
        $repl = array('');
        while ($str != preg_replace($patt, $repl, $str))
            $str = preg_replace($patt, $repl, $str);
        return $str;
    }
    
    /**
     * Retrieves the proper visibility in a set of characteristics
     *
     * @param array $arr The element to look into
     * 
     * @return string The visibility
     */
    static private function _getVisibility(array $arr)
    {
        if (in_array('private', $arr))
            return 'private';
        elseif (in_array('protected', $arr))
            return 'protected';
        else
            return 'public';
    }

    /**
     * Tries to determine the type of a given value
     *
     * @param string $value The element to check
     *
     * @return string The corresponding XMI DataType.
     */
    static private function _guessType($value)
    {
        $value = trim(strtolower($value));
        if (substr($value, 0, 6) == 'array(')
            $type = 'array';
        elseif (!(strpos($value, "'")===false && strpos($value, '"')===false))
            $type = 'string';
        elseif ($value=='true' || $value=='false')
            $type = 'bool';
        elseif ($value=='void')
            $type = 'void';
        elseif (is_numeric($value)) {
            if (strpos($value, '.')===false)
                $type = 'int';
            else
                $type = 'float';
        }
        else
            $type = 'mixed';
        return $type;
    }

    /**
     * Adds a namespace to the list of "imports"
     *
     * @param string $namespace An imported namespace
     * @param string $label     Label for the namespace
     */
    private function _addImport($namespace, $label)
    {
         $this->_imports[$label] = $namespace;
    }
    
    /**
     * Adds the package path, previously set by a "use" instruction,
     * to a given classpath.
     * Currently, it only takes the last to be defined...
     * 
     * @param string $path The path
     * 
     * @return string The path
     */
    private function _resolveImport($path)
    {
        if (count($this->_imports)==0 || substr($path, 0, 2)=='::')
            return $path;
        else {

            $pattern = array();
            $replace = array();
            foreach ($this->_imports as $k=>$v) {
                $pattern[] = '/::'.$k.'::/';
                $replace[] = $v.'::';
            }

            return preg_replace($pattern, $replace, '::'.$path);
        }
    }
}

?>
