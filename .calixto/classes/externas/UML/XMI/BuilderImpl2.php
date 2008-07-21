<?php
/**
 * PHP_UML (PHP_UML_XMI_BuilderImpl2)
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
 * Implementation class to create XMI in version 2. See interface Builder for comments.
 *
 * @category   PHP
 * @package	   Externas
 * @subpackage PHP-UML:XMI
 * 
 */
class PHP_UML_XMI_BuilderImpl2 extends PHP_UML_XMI_AbstractBuilder
{
    const XMI_VERSION = '2.1';
    const UML_VERSION = '2.1.2';

    const DEFAULT_CLASSIFIER_ATT = ' visibility="public" isAbstract="false" ';
    
    /**
     * PHP_UML UML Profile (TODO) 
     * @var string
     */
    public $profile = '';

    public function getModelOpen(PHP_UML_Metamodel_Package &$model)
    {
        return '<xmi:XMI xmi:version="'.self::XMI_VERSION.'" 
            xmlns:uml="http://schema.omg.org/spec/UML/'.self::UML_VERSION.'"
              xmlns:xmi="http://schema.omg.org/spec/XMI/'.self::XMI_VERSION.'">
                <xmi:Documentation exporter="'.self::EXPORTER_NAME.'"
                    exporterVersion="0.2" /> 
                <uml:Model xmi:type="uml:Model" name="'.$model->name.'"
                xmi:id="'.self::generateID().'" '.
                self::DEFAULT_CLASSIFIER_ATT.'>';
    }

    public function getModelClose()
    {
        return '</uml:Model></xmi:XMI>';
    }
    
    public function getPackageOpen(PHP_UML_Metamodel_Package $package)
    {
        return '<packagedElement xmi:type="uml:Package" xmi:id="'.$package->uri.
            '" name="'.$package->name.'">';
    }
    
    public function getPackageClose()
    {
        return '</packagedElement>';
    }
    
    public function getDatatype(PHP_UML_Metamodel_Type $type)
    {
        return '<packagedElement xmi:type="uml:DataType"'.
            ' xmi:id="'.self::generateID('datatype', $type->name).'"'.
            ' name="'.$type->name.'" />';
    }
    
    public function getSubsystemOpen(PHP_UML_Metamodel_Package $package)
    {
        return '<packagedElement xmi:type="uml:Component" xmi:id="'.
            $package->uri.'" name="'.$package->name.
            '" '.self::DEFAULT_CLASSIFIER_ATT.'>';
    }
    
    public function getSubsystemClose()
    {
        return '</packagedElement>';
    }

    public function getClass(PHP_UML_Metamodel_Class &$class)
    {
        $strRealization = '';

        $cn  = $class->name;
        $nn  = $class->package->uri;
        $str = '<packagedElement xmi:type="uml:Class" name="'.$cn.'" xmi:id="'.
            self::generateID('class', $nn.'#'.$cn).'" visibility="package"
            isAbstract="'.($class->isAbstract?'true':'false').'">';

        $str .= $this->getGeneralizations($class);

        $strRealization .= $this->getRealizations($class);

        foreach ($class->ownedAttribute as &$property)
            $str .= $this->getProperty($property);

        foreach ($class->ownedOperation as &$operation)
            $str .= $this->getOperation($operation);

        /*
        if ($tagFilename)
            $str .= $this->getComment('src_path', $class->file->name);
        */

        $str .= '</packagedElement>';

        return $str.$strRealization;
    }
 
    public function getInterface(PHP_UML_Metamodel_Interface &$interface)
    {
        $in  = $interface->name; 
        $nn  = $interface->package->uri;
        $str = '<packagedElement xmi:type="uml:Interface" name="'.$in.'"'.
            ' xmi:id="'.self::generateID('class', $nn.'#'.$in).'"'.
            ' visibility="package" isAbstract="true">';

        foreach ($interface->ownedOperation as &$operation)
            $str .= $this->getOperation($operation, $nn, $in);

        $str .= $this->getGeneralizations($interface);

        /*
        if ($tagFilename)
            $str .= $this->getComment('src_path', $interface->file->name);
        */

        $str .= '</packagedElement>';
        return $str;
    }

    public function getRealizations(PHP_UML_Metamodel_Class &$client)
    {
        $str = '';
        $set = $client->implements;
        $cn  = $client->name;
        $nn  = $client->package->uri;
        
        foreach ($set as &$rclass) {
            if (!empty($rclass)) {
                $rcn  = $rclass->name;
                $rnn  = $rclass->package->uri;
                $str .= '<packagedElement xmi:type="uml:Realization" '.
                'xmi:id="'.self::generateID().'" '.
                'client="'.self::generateID('class', $nn.'#'.$cn).'" '.
                'supplier="'.self::generateID('class', $rnn.'#'.$rcn).'" '.
                'realizingClassifier="'.self::generateID('class', $rnn.'#'.$rcn).'"/>';
            }
        }
        return $str;
    }

    public function getGeneralizations(PHP_UML_Metamodel_Type &$client)
    {
        $str = '';
        $set = $client->superClass;
        $cn  = $client->name;
        $nn  = $client->package->uri;

        foreach ($set as &$gclass) {
            if (!empty($gclass)) {
                $gcn  = $gclass->name;
                $gnn  = $gclass->package->uri;
                $str .= '<generalization xmi:type="uml:Generalization" '.
                    'xmi:id="'.self::generateID().'" '.
                    'general="'.self::generateID('class', $gnn.'#'.$gcn).'"/> ';
            }
        }
        return $str;
    }

    public function getProperty(PHP_UML_Metamodel_Property &$property)
    {
        $str = '<ownedAttribute xmi:type="uml:Property"'.
            ' name="'.$property->name.'"'.
            ' xmi:id="'.self::generateID().'"'.
            ' visibility="'.$property->visibility.'" ';
 
        if (!$property->isInstantiable)
            $str .= ' isStatic="true"';
        if ($property->isReadOnly)
            $str .= ' isReadOnly="true" ';

        $str .= '>'.
            $this->getParameterType($property).
            '</ownedAttribute>';
        return $str;
    }

    public function getOperation(PHP_UML_Metamodel_Operation &$operation)
    {
        $str = '<ownedOperation xmi:id="'.self::generateID().'" 
        	name="'.$operation->name.'" visibility="'.$operation->visibility.'" ';

        if (!$operation->isInstantiable)
            $str .= ' isStatic="true"';
        if ($operation->isAbstract)
            $str .= ' isAbstract="true"';

        $str .= '>';

        foreach ($operation->ownedParameter as &$parameter) {
            $str .= $this->getParameter($parameter);
        }

        $str .= '</ownedOperation>';

        return $str;
    }
        
    public function getParameter(PHP_UML_Metamodel_Parameter &$parameter)
    {   
        return '<ownedParameter xmi:id="'.self::generateID().'" '.
            'name="'.$parameter->name.'" direction="'.$parameter->direction.'">'.
            $this->getParameterType($parameter).
            '</ownedParameter>';
    }
    
    public function getParameterType(PHP_UML_Metamodel_TypedElement &$parameter)
    {
        // Exception to MOF : a PHP class can have the name of a datatype
        $str = '';
        $id  = self::generateID();

        if (get_class($parameter->type)=='PHP_UML_Metamodel_Class') {

            $cn   = $parameter->type->name;
            $nn   = $parameter->type->package->uri;
            $str .= '<type xmi:idref="'.self::generateID('class', $nn.'#'.$cn).'"/>';

        } elseif (get_class($parameter->type)=='PHP_UML_Metamodel_Type') {

            $cn   = $parameter->type->name;
            $str .= '<type xmi:idref="'.self::generateID('datatype', $cn).'"/>';
        }

        if ($parameter->default!='')
            $str .= '<defaultValue xmi:type="uml:LiteralString" xmi:id="'.$id.'"'.
                ' value="'.htmlentities($parameter->default, ENT_QUOTES, "UTF-8").'" />';

        return $str;
    }
    
    public function getArtifact(PHP_UML_Metamodel_File $file, &$mf = array())
    {
        $file = htmlspecialchars($file->name);
        $id   = self::generateID('artifact', $file);
        $name = basename($file);
        $str  = '
        	<packagedElement xmi:type="uml:Artifact"'.
            ' xmi:id="'.$id.'"'.
            ' name="'.$name.'" '.
            ' stereotype="'.self::generateID('stereotype', self::PHP_FILE).'" '.
            self::DEFAULT_CLASSIFIER_ATT.' >';

        foreach ($mf as $item) {        
            $nn = $item->package->uri;
            $cn = $item->name;
 
            $id_supplier = self::generateID('class', $nn.'#'.$cn);
            $str        .= self::_addManifestation($id, $id_supplier, 'source');
        }

        $str .= '</packagedElement>';
        return $str;
    }
    
    public function getComponent(PHP_UML_Metamodel_File $file, $stereotype = '')
    {
        $file = htmlspecialchars($file->name);
        $id   = self::generateID('component', $file);
        $name = basename($file);
        
        return '
        	<packagedElement xmi:type="uml:Component"'.
            ' xmi:id="'.$id.'"'.
            ' name="'.$name.'" '.
            self::DEFAULT_CLASSIFIER_ATT.' />';
    }
    
    /**
     * Formates a Profile adapted to PHP_UML.
     *
     * TODO. Experimental.
     *
     * @return string XMI Code
     */
    public function getProfile()
    {
        $str = '
        <uml:Profile xmi:version="'.self::XMI_VERSION.'"
        nsURI="http://PHP_UML" nsPrefix="PHP_UML"
        xmlns:uml="http://schema.omg.org/spec/UML/'.self::UML_VERSION.'/uml.xml"
        xmlns:xmi="http://schema.omg.org/spec/XMI/'.self::XMI_VERSION.'"
        xmi:id="'.self::generateID('profile', 'PHP_UML').'" name="PHP_UML"
        metamodelReference="PHP_UML_Metamodel">
        <packageImport xmi:id="PHP_UML_Metamodel">
        <importedPackage href="http://schema.omg.org/spec/UML/'.self::UML_VERSION.'/uml.xml"/>
        </packageImport>
        <ownedMember xmi:type="uml:Stereotype" xmi:id="'.
        self::generateID('stereotype', self::PHP_FILE).'" '.
        ' name="'.self::PHP_FILE.'" '.
        self::DEFAULT_CLASSIFIER_ATT.' />
        </uml:Profile>';
        return $str;
    }

    /**
     * Adds a comment
     *
     * @param string $name Title of the comment
     * @param string $body Content
     * 
     * @return string XMI code
     */
    public function getComment($name, $body)
    {
        $body = '';
        return '<ownedComment xmi:type="uml:Comment"
            xmi:id="'.self::generateID().'" name="'.$name.'" body="'.$body.'"/>';
    }

    /**
     * Generates a manifestation element (link between a class and the artifact where it is defined)
     *
     * @param string $client   Name of the client
     * @param string $supplier Name of the supplier
     * @param string $name     Name of the relation
     * 
     * @return string XMI code
     */
    private function _addManifestation($client, $supplier, $name)
    {
        return '<manifestation xmi:type="uml:Manifestation" '.
            'xmi:id="'.self::generateID().'" '.
            'client="'.$client.'" supplier="'.$supplier.'" '.
            'utilizedElement="'.$supplier.'" name="'.$name.'"/>'; 
    }
    
}
?>
