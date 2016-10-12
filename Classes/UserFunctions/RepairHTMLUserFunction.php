<?php
namespace Sub\Libconnect\UserFunctions;
/**
 * Class 'RepairHTMLUserFunction' for the 'libconnect' extension.
 *
 * @author    Torsten Witt
 * @package    TYPO3
 * @subpackage    tx_libconnect
 */

class RepairHTMLUserFunction{

    /**
     * repairs broken HTML
     *
     * @param liste array
     */
    public function RepairHTMLUserFunction($list){

        $return = array();
        foreach($list as $key => $detail){
            if(!empty($detail)){

                if(!is_array($detail)){
                    //list
                    $match = preg_match('/<li>/', $detail);
                    if($match == 1){
                        $pos2 = strpos($detail, "<ul>");

                        if($pos2 == FALSE){
                            $string = preg_replace('/<li>/', '<ul><li>', $detail, 1); 

                            $search = '</li>';
                            $replace = '</li></ul>'; 

                            $detail=substr($string,0,strripos ($string,$search)).$replace.substr($string,strripos ($string,$search)+strlen($search));                        
                        }
                    }

                    $detail = $this->filter($detail);

                }else{
                    $temp = array();
                    foreach($detail as $subkey => $row){

                        if(!is_array($row)){
                            $row = $this->filter($row);
                        }

                        $temp[$subkey] = $row;
                    }

                    $detail = $temp;
                }
            }

            $return[$key] = $detail;
        }

        return $return;
    }

    private function filter($String){
        //line break
        $String = preg_replace('/<br>/', '<br/>', $String);

        //new lines
        $String = preg_replace('/\n/', '', $String); 
        $String = preg_replace('/\r/', '', $String); 

        //no spaces before and at end content
        $String = trim($String);

        return $String;
    }
}
?>