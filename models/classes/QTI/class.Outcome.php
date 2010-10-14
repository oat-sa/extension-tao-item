<?php

error_reporting(E_ALL);

class taoItems_models_classes_QTI_Outcome
    extends taoItems_models_classes_QTI_Data
{
    protected $defaultValue = null;
    
    public function getDefaultValue()
    {
        $returnValue = null;

        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002418 begin
        
        $returnValue = $this->defaultValue;
        
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:0000000000002418 end

        return $returnValue;
    }

    /**
     * Short description of method setDefaultValue
     *
     * @access public
     * @author firstname and lastname of author, <author@example.org>
     * @param  string value
     * @return mixed
     */
    public function setDefaultValue($outcome)
    {
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241A begin
        
    	if( ! $outcome instanceof taoItems_models_classes_QTI_Variable){
    		throw new InvalidArgumentException("wrong entry for outcome parameter");
    	}
    	
    	$this->defaultValue = $outcome;
    	
        // section 127-0-1-1-5ae00f6b:12a36da0066:-8000:000000000000241A end
    }

} /* end of class taoItems_models_classes_QTI_Outcome */

?>