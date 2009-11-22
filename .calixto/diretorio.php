<?php
/**
* Classe de reprentação de diretorio
* Esta classe encapsula as formas de acesso a um diretório
* @package FrameCalixto
* @subpackage utilitários
*/
class diretorio extends Directory {
   protected $currentPath;
   protected $slash;
   protected $rootPath;
   protected $recursiveTree;  

   function __construct($rootPath,$win=false)
   {
      switch($win)
      {
         case true:
            $this->slash = '\\';
            break;
         default:
            $this->slash = '/';
      }
      $this->rootPath = $rootPath;
      $this->currentPath = $rootPath;
      $this->recursiveTree = array(dir($this->rootPath));
      $this->rewind();
   }

   function __destruct()
   {
      $this->close();
   }

   public function close()
   {
      while(true === ($d = array_pop($this->recursiveTree)))
      {
         $d->close();
      }
   }

   public function closeChildren()
   {
      while(count($this->recursiveTree)>1 && false !== ($d = array_pop($this->recursiveTree)))
      {
         $d->close();
         return true;
      }
      return false;
   }

   public function getRootPath()
   {
      if(isset($this->rootPath))
      {
         return $this->rootPath;
      }
      return false;
   }

   public function getCurrentPath()
   {
      if(isset($this->currentPath))
      {
         return $this->currentPath;
      }
      return false;
   }
  
   public function read()
   {
      while(count($this->recursiveTree)>0)
      {
         $d = end($this->recursiveTree);
         if((false !== ($entry = $d->read())))
         {
            if($entry!='.' && $entry!='..')
            {
               $path = $d->path.$entry;
              
               if(is_file($path))
               {
                  return $path;
               }
               elseif(is_dir($path.$this->slash))
               {
                  $this->currentPath = $path.$this->slash;
                  if($child = @dir($path.$this->slash))
                  {
                     $this->recursiveTree[] = $child;
                  }
               }
            }
         }
         else
         {
            array_pop($this->recursiveTree)->close();
         }
      }
      return false;
   }

   public function rewind()
   {
      $this->closeChildren();
      $this->rewindCurrent();
   }

   public function rewindCurrent()
   {
      return end($this->recursiveTree)->rewind();
   }
	/**
	* Método de verificação da legibilidade do arquivo
	* @param string caminho do arquivo a ser verificado
	* @return boolean
	*/
	static function legivel($caminhoDiretorio){
		try{
			switch(true){
				case !(is_dir($caminhoDiretorio)):
					throw new erroInclusao("Diretório [$caminhoDiretorio] inexistente!");
				break;
				case !(is_readable($caminhoDiretorio)):
					throw new erroInclusao("Diretório [$caminhoDiretorio] sem permissão de leitura!");
				break;
			}
			return true;
		}
		catch(erro $e){
			throw $e;
		}
	}
	/**
	* Método de verificação da escrita do diretório
	* @param string caminho do arquivo a ser verificado
	* @return boolean
	*/
	static function gravavel($caminhoDiretorio){
		try{
			if(!is_writable($caminhoDiretorio)) 
				throw new erroEscrita("Diretório [$caminhoDiretorio] sem permissão de escrita!");
			return true;
		}
		catch(erro $e){
			throw $e;
		}
	}
}
?>
