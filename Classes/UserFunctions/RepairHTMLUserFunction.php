<?php

namespace Subhh\Libconnect\UserFunctions;

/**
 * Class 'RepairHTMLUserFunction' for the 'libconnect' extension.
 *
 * @author    Torsten Witt
 */
class RepairHTMLUserFunction
{

    /**
     * repairs broken HTML
     *
     * @param array list
     * @return array
     */
    public function RepairHTMLUserFunction($list)
    {
        $return = [];

        foreach ($list as $key => $detail) {
            if (!empty($detail)) {
                if (!is_array($detail)) {
                    //list
                    $match = preg_match('/<li>/', $detail);
                    if ($match == 1) {
                        $pos2 = strpos($detail, '<ul>');

                        if ($pos2 == false) {
                            $string = preg_replace('/<li>/', '<ul><li>', $detail, 1);

                            $search = '</li>';
                            $replace = '</li></ul>';

                            $detail=substr($string, 0, strripos($string, $search)) . $replace . substr($string, strripos($string, $search)+strlen($search));
                        }
                    }

                    $detail = $this->filter($detail);
                } else {
                    $temp = [];
                    foreach ($detail as $subkey => $row) {
                        if (!is_array($row)) {
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

    private function filter($string)
    {
        //new lines
        $string = preg_replace('/\n/', '', $string);
        $string = preg_replace('/\r/', '', $string);

        //no spaces before and at end content
        $string = trim($string);

        return $string;
    }
}
