<?php

/**
 * CwsDump.
 *
 * PHP class to replace var_dump(), print_r() based on the XDebug style.
 *
 * @author Cr@zy
 * @copyright 2013-2016, Cr@zy
 * @license GNU LESSER GENERAL PUBLIC LICENSE
 *
 * @link https://github.com/crazy-max/CwsDump
 */
namespace Cws;

class CwsDump
{
    const SEP_OBJECT = '___';

    /**
     * Colors of the variable type.
     */
    const COLOR_NULL = '#3465A4';
    const COLOR_BOOL = '#75507B';
    const COLOR_STRING = '#CC0000';
    const COLOR_INT = '#4E9A06';
    const COLOR_FLOAT = '#F57900';
    const COLOR_ARRAY_EMPTY = '#888A85';

    /**
     * Names of the variable type.
     */
    const NAME_NULL = 'null';
    const NAME_BOOL = 'boolean';
    const NAME_STRING = 'string';
    const NAME_INT = 'int';
    const NAME_FLOAT = 'float';
    const NAME_ARRAY = 'array';
    const NAME_OBJECT = 'object';
    const NAME_RESOURCE = 'resource';

    /**
     * Control how are displayed several values.
     */
    const DISPLAY_NULL = 'null';
    const DISPLAY_TRUE = 'true';
    const DISPLAY_FALSE = 'false';
    const DISPLAY_ARRAY_EMPTY = 'empty';
    const DISPLAY_MAX_DEPTH = 'recursion depth limit reached...';
    const DISPLAY_MAX_CHILDREN = 'more elements...';

    /**
     * Controls how many nested levels of array elements
     * and object properties are when variables are displayed.
     *
     * @var int
     */
    private $maxDepth = 3;

    /**
     * Controls the maximum string length that is shown when
     * variables are displayed.
     *
     * @var int
     */
    private $maxData = 512;

    /**
     * Controls the amount of array children and object's
     * properties are shown when variables are displayed.
     *
     * @var int
     */
    private $maxChildren = 128;

    /**
     * The font family.
     *
     * @var string
     */
    private $fontFamily = 'Monospace';

    /**
     * Variable to control the recurse depth.
     *
     * @var int
     */
    private $recurseDepth = 0;

    /**
     * Variable to detect the recurse.
     * default array().
     *
     * @var array
     */
    private $recurseDetection = array();

    public function __construct()
    {
    }

    /**
     * Dump variable into formatted XHTML string.
     *
     * @param mixed $var
     * @param bool  $or
     *
     * @return string - Formatted XHTML
     */
    public function dump($var, $or = true)
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
            if ($this->recurseDepth >= $this->maxDepth) {
                $type = is_object($var) ? self::NAME_OBJECT : self::NAME_ARRAY;
                $maxDepthReached = $this->writeStart(false);
                $maxDepthReached .= $this->writeRow('<strong>'.$type.'</strong>');
                $maxDepthReached .= $this->writeRow('<span style="padding-left:10pt;font-style:italic;"><i>'
                    .self::DISPLAY_MAX_DEPTH.'</span>');
                $maxDepthReached .= $this->writeEnd();

                return $maxDepthReached;
            }
            $this->recurseDepth++;
            if (is_array($var)) {
                $result .= $this->dumpArray($var, $or);
            } else {
                $result .= $this->dumpObject($var, $or);
            }
            $this->recurseDepth--;
        } elseif (is_resource($var)) {
            $result .= $this->dumpRes($var, $or);
        } else {
            $result .= '???';
        }

        return $result;
    }

    private function dumpNull($or)
    {
        return $this->processVar(
            self::DISPLAY_NULL,
            self::NAME_NULL,
            self::COLOR_NULL,
            $or
        );
    }

    private function dumpBool($var, $or)
    {
        return $this->processVar(
            $var ? self::DISPLAY_TRUE : self::DISPLAY_FALSE,
            self::NAME_BOOL,
            self::COLOR_BOOL,
            $or
        );
    }

    private function dumpString($var, $or)
    {
        return $this->processVar(
            var_export($var, true),
            self::NAME_STRING,
            self::COLOR_STRING,
            $or
        );
    }

    private function dumpInt($var, $or)
    {
        return $this->processVar(
            var_export($var, true),
            self::NAME_INT,
            self::COLOR_INT,
            $or
        );
    }

    private function dumpFloat($var, $or)
    {
        return $this->processVar(
            var_export($var, true),
            self::NAME_FLOAT,
            self::COLOR_FLOAT,
            $or
        );
    }

    private function dumpArray(&$var, $or)
    {
        $len = count($var);

        $result = $this->writeStart($or);
        $type = self::NAME_ARRAY;

        if ($len === 0 && $var === array()) {
            $result .= $this->writeRow('<strong>'.$type.'</strong>');
            $result .= $this->writeRow($this->dumpArrayEmpty());
        } else {
            $result .= $this->writeRow('<strong>'.$type.'</strong> <i>(length='.$len.')</i>');
            $result .= $this->writeRow($this->processArray($var, false));
        }

        $result .= $this->writeEnd();

        return $this->writePre($result, $or);
    }

    private function dumpArrayEmpty()
    {
        $result = '<span style="font-style:italic;font-family:'
            .$this->fontFamily.';';
        $result .= 'color:'.self::COLOR_ARRAY_EMPTY.';">';
        $result .= self::DISPLAY_ARRAY_EMPTY.'</span>';

        return '<span style="padding-left:10pt">'.$result.'</span>';
    }

    private function dumpObject(&$var, $or)
    {
        $props = array();
        $object = get_class($var);
        $array = (array) $var;

        foreach ($array as $key => $value) {
            $explKey = explode("\0", $key);
            if (count($explKey) == 1) {
                $props[self::SEP_OBJECT.'public'.self::SEP_OBJECT.$key] = $value;
            } elseif ($explKey[1] == '*') {
                $props[self::SEP_OBJECT.'protected'.self::SEP_OBJECT.$explKey[2]] = $value;
            } else {
                $props[self::SEP_OBJECT.'protected'.self::SEP_OBJECT.$explKey[2]] = $value;
            }
        }

        $row = '<span style="font-weight:bold;font-family:'.$this->fontFamily.';">';
        $row .= self::NAME_OBJECT.'</span> ';
        $row .= '<span style="font-style:italic;font-family:'.$this->fontFamily.';">('.$object.')</i>';

        $result = $this->writeStart($or);
        $result .= $this->writeRow($row);
        $result .= $this->writeRow($this->processArray($props, false));
        $result .= $this->writeEnd();

        return $this->writePre($result, $or);
    }

    private function dumpRes(&$var, $or)
    {
        $result = '<span style="font-weight:bold;font-family:'.$this->fontFamily.';">';
        $result .= self::NAME_RESOURCE.'</span> ';
        $result .= '<span style="font-style:italic;font-family:'.$this->fontFamily.';">';
        $result .= '('.intval($var).', '.get_resource_type($var).')</i>';

        return $this->writePre($result, $or);
    }

    private function processVar($content, $type, $color, $or)
    {
        $len = strlen($content);
        if ($len > $this->maxData && $type == self::NAME_STRING) {
            $content = substr($content, 0, $this->maxData - 3).'...';
        }

        $result = $type != self::NAME_NULL ? '<small>'.$type.'</small> ' : '';
        $result .= '<span style="font-family:'.$this->fontFamily.';color:'.$color.';">';
        $result .= str_replace(array("\n", ' '), array('<br/>', '&#160;'), htmlspecialchars($content, ENT_IGNORE));
        $result .= '</span>';
        $result .= $type == self::NAME_STRING ? ' <i>(length='.$len.')</i>' : '';

        return $this->writePre($result, $or);
    }

    private function processArray(&$var, $or)
    {
        if (false !== array_search($var, $this->recurseDetection, true)) {
            return 'Recursive dependency detected';
        }

        array_push($this->recurseDetection, $var);

        $result = $this->writeStart($or);

        $children = 0;
        foreach ($var as $key => $value) {
            if ($children >= $this->maxChildren) {
                $result .= $this->writeRow('<i>'.self::DISPLAY_MAX_CHILDREN.'</i>');
                break;
            }
            if (substr($key, 0, 3) === self::SEP_OBJECT) {
                $expKey = explode(self::SEP_OBJECT, $key);
                $result .= $this->writeRow('<i>'.$expKey[1].'</i> \''.$expKey[2].'\' => '.$this->dump($value, false));
            } else {
                $result .= $this->writeRow((is_numeric($key) ? $key : '\''.$key.'\'').' => '.$this->dump($value, false));
            }
            $children++;
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
            $result = '<table style="margin:7pt 0 7pt 0;padding:0;font-family:'.$this->fontFamily.';';
            $result .= 'border-collapse:collapse;valign:top;">';
            $result .= $this->writeRow($elt).$this->writeEnd();

            return $result;
        } else {
            return $elt;
        }
    }

    private function writeStart($or = true)
    {
        $result = '<table style="margin:0 0 0 '.($or ? '0' : '10pt').';';
        $result .= 'padding:0 0 0 '.($or ? '0' : '3pt').';';
        $result .= 'font-family:'.$this->fontFamily.';border-collapse:collapse;valign:top;">';

        return $result;
    }

    private function writeRow($elt)
    {
        $result = '<tr><td style="font-family:'.$this->fontFamily.';';
        $result .= 'white-space:nowrap;vertical-align:top">'.$elt.'</td></tr>';

        return $result;
    }

    private function writeEnd()
    {
        return "</table>\r\n";
    }
}
