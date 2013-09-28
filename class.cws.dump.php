<?php

/**
 * CwsDump
 * 
 * CwsDump is a PHP class to replace var_dump(), print_r() based on the Xdebug style.
 *
 * CwsDump is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or (at your option)
 * or (at your option) any later version.
 *
 * CwsDump is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE. See the GNU Lesser General Public License
 * for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see http://www.gnu.org/licenses/.
 * 
 * Related post : http://goo.gl/pRwJti
 * 
 * @package CwsDump
 * @author Cr@zy
 * @copyright 2013, Cr@zy
 * @license GNU LESSER GENERAL PUBLIC LICENSE
 * @version 1.0
 *
 */

define('CWSDUMP_INI_FILE',          'cws.dump.ini');

define('CWSDUMP_VAR_NULL',          'null');
define('CWSDUMP_VAR_BOOL',          'boolean');
define('CWSDUMP_VAR_STRING',        'string');
define('CWSDUMP_VAR_INT',           'int');
define('CWSDUMP_VAR_FLOAT',         'float');
define('CWSDUMP_VAR_ARRAY',         'array');
define('CWSDUMP_VAR_OBJECT',        'object');

define('CWSDUMP_INICAT_1',          'cwsdump_general');
define('CWSDUMP_INI1_MAXDEPTH',     'cwsdump_max_depth');
define('CWSDUMP_INI1_MAXDATA',      'cwsdump_max_data');
define('CWSDUMP_INI1_SEPOBJECT',    'cwsdump_sep_object');
define('CWSDUMP_INI1_FONTFAMILY',   'cwsdump_font_family');

define('CWSDUMP_INICAT_2',          'cwsdump_colors');
define('CWSDUMP_INI2_NULL',         'cwsdump_null');
define('CWSDUMP_INI2_BOOL',         'cwsdump_bool');
define('CWSDUMP_INI2_STRING',       'cwsdump_string');
define('CWSDUMP_INI2_INT',          'cwsdump_int');
define('CWSDUMP_INI2_FLOAT',        'cwsdump_float');
define('CWSDUMP_INI2_AREMPTY',      'cwsdump_array_empty');

define('CWSDUMP_INICAT_3',          'cwsdump_display_values');
define('CWSDUMP_INI3_NULL',         'cwsdump_null');
define('CWSDUMP_INI3_TRUE',         'cwsdump_true');
define('CWSDUMP_INI3_FALSE',        'cwsdump_false');
define('CWSDUMP_INI3_AREMPTY',      'cwsdump_array_empty');

class CwsDump
{
    /**
     * The properties from the INI file CWSDUMP_INI_FILE
     * @var array
     */
    private $iniProps;
    
    /**
     * Variable to control the recurse depth.
     * default 0
     * @var int
     */
    private $recurseDepth = 0;
    
    /**
     * Variable to detect the recurse.
     * default array()
     * @var array
     */
    private $recurseDetection = array();
    
    public function __construct()
    {
        $this->retrieveIniProps(CWSDUMP_INI_FILE);
    }
    
    /**
     * Retrieve the properties from the INI file
     * @param string $iniFilePath
     */
    private function retrieveIniProps($iniFilePath)
    {
        if (file_exists($iniFilePath)) {
            $this->iniProps = parse_ini_file($iniFilePath, true);
            if ($this->iniProps !== false) {
                $this->checkIniProps();
            } else {
                die('CwsDump: An error occurred while parsing ' . CWSDUMP_INI_FILE . ' file...');
            }
        } else {
            die('CwsDump: Ini file ' . CWSDUMP_INI_FILE . ' not found...');
        }
    }
    
    /**
     * Check the properties from the INI file.
     */
    private function checkIniProps()
    {
        // Cat GENERAL
        $cat = $this->checkIniCat(CWSDUMP_INICAT_1);
        $this->checkIniKey(CWSDUMP_INICAT_1, CWSDUMP_INI1_MAXDEPTH, '8');
        $this->checkIniKey(CWSDUMP_INICAT_1, CWSDUMP_INI1_MAXDATA, '1024');
        $this->checkIniKey(CWSDUMP_INICAT_1, CWSDUMP_INI1_SEPOBJECT, '___');
        $this->checkIniKey(CWSDUMP_INICAT_1, CWSDUMP_INI1_FONTFAMILY, 'Monospace');
        
        // Cat COLORS
        $cat = $this->checkIniCat(CWSDUMP_INICAT_2);
        $this->checkIniKey(CWSDUMP_INICAT_2, CWSDUMP_INI2_NULL, '#3465A4');
        $this->checkIniKey(CWSDUMP_INICAT_2, CWSDUMP_INI2_BOOL, '#75507B');
        $this->checkIniKey(CWSDUMP_INICAT_2, CWSDUMP_INI2_STRING, '#CC0000');
        $this->checkIniKey(CWSDUMP_INICAT_2, CWSDUMP_INI2_INT, '#4E9A06');
        $this->checkIniKey(CWSDUMP_INICAT_2, CWSDUMP_INI2_FLOAT, '#F57900');
        $this->checkIniKey(CWSDUMP_INICAT_2, CWSDUMP_INI2_AREMPTY, '#888A85');
        
        // Cat DISPLAY_VALUES
        $cat = $this->checkIniCat(CWSDUMP_INICAT_3);
        $this->checkIniKey(CWSDUMP_INICAT_3, CWSDUMP_INI3_NULL, 'null');
        $this->checkIniKey(CWSDUMP_INICAT_3, CWSDUMP_INI3_TRUE, 'true');
        $this->checkIniKey(CWSDUMP_INICAT_3, CWSDUMP_INI3_FALSE, 'false');
        $this->checkIniKey(CWSDUMP_INICAT_3, CWSDUMP_INI3_AREMPTY, 'empty');
        
        // Change some types
        $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_MAXDEPTH] = intval($this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_MAXDEPTH]);
        $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_MAXDATA] = intval($this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_MAXDATA]);
    }
    
    /**
     * Check a category from the INI file
     * @param string $cat
     */
    private function checkIniCat($cat)
    {
        if (!isset($this->iniProps[CWSDUMP_INICAT_1])) {
            $this->iniProps[CWSDUMP_INICAT_1] = array();
        }
    }
    
    /**
     * Check a key from the INI file
     * @param string $cat
     * @param string $key
     * @param string $default
     */
    private function checkIniKey($cat, $key, $default)
    {
        if (!isset($this->iniProps[$cat][$key])) {
            $this->iniProps[$cat][$key] = $default;
        }
    }
    
    /**
     * Dump variable into formatted XHTML string
     * @param mixed $var
     * @param boolean $or
     * @return string - Formatted XHTML
     */
    public function dump($var, $or=true)
    {
        $result = '';
        
        if ($var === null) {
            $result .= $this->dumpNull($or);
        } elseif (is_bool($var)) {
            $result .= $this->dumpBool($var, $or);
        } elseif (is_string($var)) {
            $result .= $this->dumpString($var, $or);
        } elseif (is_int($var)) {
            $result .= $this->dumpInt($var, $or);
        } elseif (is_float($var)) {
            $result .= $this->dumpFloat($var, $or);
        } elseif (is_array($var) || is_object($var)) {
            if ($this->recurseDepth >= $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_MAXDEPTH]) {
                return 'Recursion depth limit reached';
            }
            $this->recurseDepth++;
            if (is_array($var)) {
                $result .= $this->dumpArray($var, $or);
            } else {
                $result .= $this->dumpObject($var, $or);
            }
            $this->recurseDepth--;
        } elseif(is_resource($var)) {
            $result .= 'resource';
        } else {
            $result .= '???';
        }
        
        return $result;
    }
    
    private function dumpNull($or)
    {
        return $this->processVar(
            $this->iniProps[CWSDUMP_INICAT_3][CWSDUMP_INI3_NULL],
            CWSDUMP_VAR_NULL,
            $this->iniProps[CWSDUMP_INICAT_2][CWSDUMP_INI2_NULL],
            $or
        );
    }
    
    private function dumpBool($var, $or)
    {
        return $this->processVar(
            $var ? $this->iniProps[CWSDUMP_INICAT_3][CWSDUMP_INI3_TRUE] : $this->iniProps[CWSDUMP_INICAT_3][CWSDUMP_INI3_FALSE],
            CWSDUMP_VAR_BOOL,
            $this->iniProps[CWSDUMP_INICAT_2][CWSDUMP_INI2_BOOL],
            $or
        );
    }
    
    private function dumpString($var, $or)
    {
        return $this->processVar(
            var_export($var, true),
            CWSDUMP_VAR_STRING,
            $this->iniProps[CWSDUMP_INICAT_2][CWSDUMP_INI2_STRING],
            $or
        );
    }
    
    private function dumpInt($var, $or)
    {
        return $this->processVar(
            var_export($var, true),
            CWSDUMP_VAR_INT,
            $this->iniProps[CWSDUMP_INICAT_2][CWSDUMP_INI2_INT],
            $or
        );
    }
    
    private function dumpFloat($var, $or)
    {
        return $this->processVar(
            var_export($var, true),
            CWSDUMP_VAR_FLOAT,
            $this->iniProps[CWSDUMP_INICAT_2][CWSDUMP_INI2_FLOAT],
            $or
        );
    }
    
    private function dumpArray(&$var, $or)
    {
        $len = count($var);
    
        $result = $this->writeStart($or);
        
        if ($len === 0 && $var === array()) {
            $result .= $this->writeRow('<strong>' . CWSDUMP_VAR_ARRAY . '</strong>');
            $result .= $this->writeRow($this->dumpArrayEmpty());
        } else {
            $result .= $this->writeRow('<strong>' . CWSDUMP_VAR_ARRAY . '</strong> <i>(length=' . $len . ')</i>');
            $result .= $this->writeRow($this->processArray($var, false));
        }
        
        $result .= $this->writeEnd();
        
        return $this->writePre($result, $or);
    }
    
    private function dumpArrayEmpty()
    {
        $result = '<span style="font-style:italic;font-family:' . $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_FONTFAMILY] . ';';
        $result .= 'color:' . $this->iniProps[CWSDUMP_INICAT_2][CWSDUMP_INI2_AREMPTY] . ';">';
        $result .= $this->iniProps[CWSDUMP_INICAT_3][CWSDUMP_INI3_AREMPTY] . '</span>';
        return $this->writeFull($result, false);
    }
    
    private function dumpObject(&$var, $or)
    {
        $props = array();
        $object = get_class($var);
        $array = (array)$var;
        
        foreach ($array as $key => $value) {
            $explKey = explode("\0", $key);
            if (count($explKey) == 1) {
                $propkey = $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_SEPOBJECT] . 'public';
                $propkey .= $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_SEPOBJECT] . $key;
                $props[$propkey] = $value;
            } elseif ($explKey[1] == '*') {
                $propkey = $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_SEPOBJECT] . 'protected';
                $propkey .= $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_SEPOBJECT] . $explKey[2];
                $props[$propkey] = $value;
            } else {
                $propkey = $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_SEPOBJECT] . 'protected';
                $propkey .= $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_SEPOBJECT] . $explKey[2];
                $props[$propkey] = $value;
            }
        }
        
        $row = '<span style="font-weight:bold;font-family:' . $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_FONTFAMILY] . ';">';
        $row .= CWSDUMP_VAR_OBJECT . '</span> ';
        $row .= '<span style="font-style:italic;font-family:' . $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_FONTFAMILY] . ';">';
        $row .= $object . '</i>';
        
        $result = $this->writeStart($or);
        $result .= $this->writeRow($row);
        $result .= $this->writeRow($this->processArray($props, false));
        $result .= $this->writeEnd();
        
        return $this->writePre($result, $or);
    }
    
    private function processVar($content, $type, $color, $or)
    {
        $len = strlen($content);
        if ($len > $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_MAXDATA] && $type == CWSDUMP_VAR_STRING) {
            $content = substr($content, 0, $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_MAXDATA] - 3) . '...';
        }
        
        $result = $type != CWSDUMP_VAR_NULL ? '<small>' . $type . '</small> ' : '';
        $result .= '<span style="font-family:' . $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_FONTFAMILY] . ';color:' . $color . ';">';
        $result .= str_replace(array("\n", ' '), array('<br/>', '&#160;'), htmlspecialchars($content));
        $result .= '</span>';
        $result .= $type == CWSDUMP_VAR_STRING ? ' <i>(length=' . $len . ')</i>' : '';
        
        return $this->writePre($result, $or);
    }
    
    private function processArray(&$var, $or)
    {
        if (false !== array_search($var, $this->recurseDetection, true)) {
            return 'Recursive dependency detected';
        }
    
        array_push($this->recurseDetection, $var);
    
        $result = $this->writeStart($or);
    
        foreach($var as $key => $value) {
            if (substr($key, 0, 3) === $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_SEPOBJECT]) {
                $expKey = explode($this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_SEPOBJECT], $key);
                $result .= $this->writeRow('<i>' . $expKey[1] . '</i> \'' . $expKey[2] . '\' => ' . $this->dump($value, false));
            } else {
                $result .= $this->writeRow((is_numeric($key) ? $key : '\'' . $key . '\'') . ' => ' . $this->dump($value, false));
            }
        }
    
        if ($var !== array_pop($this->recurseDetection)) {
            throw new RuntimeException('Inconsistent state');
        }
    
        $result .= $this->writeEnd();
        
        return $this->writePre($result, $or);
    }
    
    private function writePre($elt, $or)
    {
        if ($or) {
            $result = '<table style="margin:7pt 0 7pt 0;padding:0;';
            $result .= 'font-family:' . $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_FONTFAMILY] . ';';
            $result .= 'border-collapse:collapse;valign:top;">';
            $result .= $this->writeRow($elt) . $this->writeEnd();
            return $result;
        } else {
            return $elt;
        }
    }
    
    private function writeStart($or=true)
    {
        $result = '<table style="margin:0 0 0 ' . ($or ? '0' : '10pt') . ';';
        $result .= 'padding:0 0 0 ' . ($or ? '0' : '3pt') . ';';
        $result .= 'font-family:' . $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_FONTFAMILY] . ';';
        $result .= 'border-collapse:collapse;valign:top;">';
        return $result;
    }
    
    private function writeRow($elt)
    {
        $result = '<tr><td style="font-family:' . $this->iniProps[CWSDUMP_INICAT_1][CWSDUMP_INI1_FONTFAMILY] . ';';
        $result .= 'white-space:nowrap;vertical-align:top">' . $elt . '</td></tr>';
        return $result;
    }
    
    private function writeEnd()
    {
        return "</table>\r\n";
    }
    
    private function writeFull($elt, $or)
    {
        return $this->writeStart($or) . $this->writeRow($elt) . $this->writeEnd();
    }
}

function cwsDump($var) {
    echo call_user_func(array(new CwsDump(), 'dump'), $var);
}