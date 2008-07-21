<?php
/**
 * PHP_UML (PHP_UML_XMI_BuilderImpl1)
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
 * Implementation class to create XMI in version 1
 *
 * @category   PHP
 * @package	   Externas
 * @subpackage PHP-UML:XMI
 */
class PHP_UML_XMI_BuilderImpl1 extends PHP_UML_XMI_AbstractBuilder
{
    const XMI_VERSION = '1.2';
    const UML_VERSION = '1.4';

    const DEFAULT_CLASSIFIER_ATT = ' visibility="public" isAbstract="false" 
        isSpecification="false" isRoot="false" isLeaf="false" ';

    public function getModelOpen(PHP_UML_Metamodel_Package &$model)
    {
        $str = '<XMI xmi.version="'.self::XMI_VERSION.'"
            xmlns:UML="http://www.omg.org/spec/UML/1.4">
            <XMI.header>
                <XMI.documentation>
                    <XMI.exporter>'.self::EXPORTER_NAME.'</XMI.exporter>
                </XMI.documentation>
                <XMI.metamodel XMI.name="UML" XMI.version="'.self::XMI_VERSION.'" />
            </XMI.header>
            <XMI.content>
            <UML:Model name="'.$model->name.'"
                xmi.id="'.self::generateID().'" '.
                self::DEFAULT_CLASSIFIER_ATT.'>
                <UML:Namespace.ownedElement>';

        foreach (self::$stereotypes as $item)
            $str .= '<UML:Stereotype xmi.id="'.self::generateID('stereotype', $item).'"
                name="'.$item.'" '.self::DEFAULT_CLASSIFIER_ATT.' />';
        
        $str .= '<UML:Stereotype xmi.id="'.self::generateID('stereotype', 'realize').'"
            name="realize" '.self::DEFAULT_CLASSIFIER_ATT.'>
            <UML:Stereotype.baseClass>Abstraction</UML:Stereotype.baseClass>
            </UML:Stereotype>';
        return $str;             
    }

    public function getModelClose()
    {
        return '</UML:Namespace.ownedElement></UML:Model></XMI.content></XMI>';
    }
    
    public function getPackageOpen(PHP_UML_Metamodel_Package $package)
    {
        return '<UML:Package xmi.id="'.$package->uri.
            '" name="'.$package->name.'"><UML:Namespace.ownedElement>';
    }

    public function getPackageClose()
    {
        return '</UML:Namespace.ownedElement></UML:Package>';
    }

    public function getSubsystemOpen(PHP_UML_Metamodel_Package $package)
    {
        return '<UML:Subsystem name="'.$package->name.'" xmi.id="'.
            $package->uri.'" isInstantiable="false"><UML:Namespace.ownedElement>';
    }

    public function getSubsystemClose()
    {
        return '</UML:Namespace.ownedElement></UML:Subsystem>';
    }
    
    public function getDatatype(PHP_UML_Metamodel_Type $type)
    {
        return '<UML:DataType xmi.id="'.self::generateID('datatype', $type->name).
            '" name="'.$type->name.'" visibility="public" isRoot="false" '.
            ' isLeaf="false" isAbstract="false"/>';
    }

    public function getClass(PHP_UML_Metamodel_Class &$class)
    {
        $cn = $class->name;
        $nn = $class->package->uri;
 
        $str = '<UML:Class name="'.$cn.'" xmi.id="'.
            self::generateID('class', $nn.'#'.$cn).'" visibility="package"
            isAbstract="'.($class->isAbstract?'true':'false').'">';

        list($generalizable, $generalization) = $this->getGeneralizations($class);

        $str .= $generalizable;
        $str .= '<UML:Classifier.feature>';

        foreach ($class->ownedAttribute as &$property) {
            $str .= $this->getProperty($property);
        }

        foreach ($class->ownedOperation as &$operation) {
            $str .= $this->getOperation($operation);
        }

        $str .= '</UML:Classifier.feature>';
        $str .= '</UML:Class>';

        return $str.$generalization.$this->getRealizations($class);
    }

    public function getInterface(PHP_UML_Metamodel_Interface &$interface)
    {
        $in = $interface->name; 
        $nn = $interface->package->uri;

        $str = '<UML:Interface name="'.$in.'"'.
            ' xmi.id="'.self::generateID('class', $nn.'#'.$in).'"'.
            ' visibility="package" isAbstract="true">';

        list($generalizable, $generalization) = $this->getGeneralizations($interface);

        $str .= $generalizable;
        $str .= '<UML:Classifier.feature>';
 
        foreach ($interface->ownedOperation as &$operation)
            $str .= $this->getOperation($operation, $nn, $in);

        $str .= '</UML:Classifier.feature>';
        $str .= '</UML:Interface>';
        
        return $str.$generalization;
    }

    public function getGeneralizations(PHP_UML_Metamodel_Type &$client)
    {
        $str = '';
        $set = $client->superClass;
        $cn  = $client->name;
        $nn  = $client->package->uri;

        $generalizable  = '';
        $generalization = '';
 
        foreach ($set as &$gclass) {
            if (!empty($gclass)) {
                $gcn = $gclass->name;
                $gnn = $gclass->package->uri;
                $id  = self::generateID();

                $generalizable .= '<UML:GeneralizableElement.generalization>
                    <UML:Generalization xmi.idref="'.$id.'"/>
                    </UML:GeneralizableElement.generalization>';

                $generalization .= '<UML:Generalization xmi.id="'.$id.'">
                    <UML:Generalization.child><UML:Class xmi.idref="'.
                    self::generateID('class', $nn.'#'.$cn).
                    '" /></UML:Generalization.child>
                    <UML:Generalization.parent><UML:Class xmi.idref="'.
                    self::generateID('class', $gnn.'#'.$gcn).'"/>
                    </UML:Generalization.parent></UML:Generalization>';
            }
        }
        return array($generalizable, $generalization);
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
                $str .= '<UML:Abstraction '.
                    'xmi.id="'.self::generateID().'" isSpecification="false">'.
                    '<UML:ModelElement.stereotype><UML:Stereotype xmi.idref="'.
                    self::generateID('stereotype', 'realize').'"/>'.
                    '</UML:ModelElement.stereotype>'.
                    '<UML:Dependency.client><UML:Class xmi.idref="'.
                    self::generateID('class', $nn.'#'.$cn).
                    '"/></UML:Dependency.client>'.
                    '<UML:Dependency.supplier><UML:Interface xmi.idref="'.
                    self::generateID('class', $rnn.'#'.$rcn).'"/>'.
                    '</UML:Dependency.supplier></UML:Abstraction>';
            }
        }
        return $str;
    }

    public function getProperty(PHP_UML_Metamodel_Property &$property)
    {
        $str = '<UML:Attribute xmi.id="'.self::generateID().'"'.
            ' name="'.$property->name.'" '.
            ' visibility="'.$property->visibility.'" ';

        if (!$property->isInstantiable) {
            $str .= ' isStatic="true" ownerScope="classifier"';
        } else {
            $str .= ' ownerScope="instance"';
        }

        if ($property->isReadOnly)
            $str .= ' changeability="frozen" isReadOnly="true" ';

        $str .= '>';
        $str .= self::_getStructuralFeatureType($property);

        $str .= '</UML:Attribute>';
        return $str;
    }
    
    public function getOperation(PHP_UML_Metamodel_Operation &$operation)
    {
        $str = '<UML:Operation xmi.id="'.self::generateID().'"'.
            ' name="'.$operation->name.'"'.
            ' visibility="'.$operation->visibility.'"';
        if (!$operation->isInstantiable)
            $str .= ' isStatic="true"';
        if ($operation->isAbstract)
            $str .= ' isAbstract="true"';

        $str .= ' isQuery="false" concurrency="sequential">'.
            '<UML:BehavioralFeature.parameter>';

        foreach ($operation->ownedParameter as &$parameter) {
            $str .= $this->getParameter($parameter);
        }

        $str .= '</UML:BehavioralFeature.parameter></UML:Operation>';

        return $str;
    }

    public function getParameter(PHP_UML_Metamodel_Parameter &$parameter)
    {
        return '<UML:Parameter xmi.id="'.self::generateID().'" '.
            ' name="'.$parameter->name.'"'.
            ' kind="'.$parameter->direction.'">'.
            $this->getParameterType($parameter).
            '</UML:Parameter>';
    }
 
    public function getParameterType(PHP_UML_Metamodel_TypedElement &$parameter)
    {
        $str = '';
        $id  = self::generateID();
        // Exception to MOF : a PHP class can have the name of a datatype

        if (get_class($parameter->type)=='PHP_UML_Metamodel_Class') {

            $cn   = $parameter->type->name;
            $nn   = $parameter->type->package->uri;
            $str .= '<UML:Parameter.type>'.
                '<UML:DataType xmi.idref="'.self::generateID('class', $nn.'#'.$cn).
                '"/></UML:Parameter.type>';

        } elseif (get_class($parameter->type)=='PHP_UML_Metamodel_Type') {

            $cn   = $parameter->type->name;
            $str .= '<UML:Parameter.type>'.
                '<UML:DataType xmi.idref="'.self::generateID('datatype', $cn).
                '"/></UML:Parameter.type>';
        }

        if ($parameter->default!='') {
            $str .= '<UML:Parameter.defaultValue>'.
                '<UML:Expression xmi.id="'.$id.'"'.
                ' body="'.htmlentities($parameter->default, ENT_QUOTES, "UTF-8").'" />'.
                '</UML:Parameter.defaultValue>';
        }
        return $str;
    }

    public function getArtifact(PHP_UML_Metamodel_File $file, &$mf = array())
    {
        $file = htmlspecialchars($file->name);
        $id   = self::generateID('artifact', $file);
        $name = basename($file);

        return '<UML:Artifact '.
            ' xmi.id="'.$id.'"'.
            ' name="'.$name.'">'.
            '<UML:ModelElement.stereotype>'.
            '<UML:Stereotype xmi.idref="'.self::generateID('stereotype', self::PHP_FILE).'"/>'.
            '</UML:ModelElement.stereotype>'.
            '</UML:Artifact>';
    }

    public function getComponent(PHP_UML_Metamodel_File $file, $stereotype = '')
    {
        $file = htmlspecialchars($file->name);
        $id   = self::generateID('component', $file);
        $name = basename($file);
        
        return '<UML:Component '.
            ' xmi.id="'.$id.'" '.
            ' name="'.$name.'" '.
            self::DEFAULT_CLASSIFIER_ATT.' stereotype="'.$stereotype.'">'.
            '</UML:Component>';
    }
        
    public function getProfile()
    {
    }
    
    /**
     * Splits a parameter into its type, name and default value. This is a special
     * version of getParameterType (XMI 1.x) used with class properties only.
     *
     * @param PHP_UML_TypedElement &$parameter Parameter to split
     * 
     * @return string XMI code
     */
    static private function _getStructuralFeatureType(PHP_UML_Metamodel_TypedElement &$parameter)
    {
        $str = '';
        $id  = self::generateID();

        if (get_class($parameter->type)=='PHP_UML_Metamodel_Class') {

            $cn   = $parameter->type->name;
            $nn   = $parameter->type->package->uri;
            $str .= '<UML:StructuralFeature.type>'.
                '<UML:DataType xmi.idref="'.self::generateID('class', $nn.'#'.$cn).
                '"/></UML:StructuralFeature.type>';

        } elseif (get_class($parameter->type)=='PHP_UML_Metamodel_Type') {

            $cn   = $parameter->type->name;
            $str .= '<UML:StructuralFeature.type>'.
                '<UML:DataType xmi.idref="'.self::generateID('datatype', $cn).
                '"/></UML:StructuralFeature.type>';
        }

        if ($parameter->default!='') {
            $str .= '<UML:Attribute.initialValue>'.
                '<UML:Expression xmi.id="'.$id.'"'.
                ' body="'.htmlentities($parameter->default, ENT_QUOTES, "UTF-8").'" />'.
                '</UML:Attribute.initialValue>';
        }
        return $str;
    }

    /*
    static private function _getTaggedValue($id, $value, $id_type)
    {
        return '<UML:ModelElement.taggedValue><UML:TaggedValue xmi.id="'.$id.'">'.
            '<UML:TaggedValue.dataValue>'.$value.'</UML:TaggedValue.dataValue>'.
            '<UML:TaggedValue.type><UML:TagDefinition xmi.idref="'.$id_type.'" />'.
            '</UML:TaggedValue.type>'.
            '</UML:TaggedValue></UML:ModelElement.taggedValue>';
    }
    */
}

?>
