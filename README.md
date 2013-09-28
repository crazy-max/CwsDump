# CwsDump

CwsDump is a PHP class to replace var_dump(), print_r() based on the Xdebug style.

### Installation

* Copy the ``class.cws.dump.php`` and ``cws.dump.ini`` files in a same folder on your server.
* You can use the ``index.php`` file sample to help you.

### Configuration

You can change the configuration of CwsDump in the ``cws.dump.ini`` file :

```ini

;=====================================================================================
;   This file is an integral part of the CwsDump class.
;=====================================================================================

[cwsdump_general]
cwsdump_max_depth = 3 ; Controls how many nested levels of array elements and object properties are when variables are displayed.
cwsdump_max_data = 512 ; Controls the maximum string length that is shown when variables are displayed.
cwsdump_max_children = 128 ; Controls the amount of array children and object's properties are shown when variables are displayed.
cwsdump_font_family = "Monospace"

; The names of the variable type
[cwsdump_names]
cwsdump_null = "null"
cwsdump_bool = "boolean"
cwsdump_string = "string"
cwsdump_int = "int"
cwsdump_float = "float"
cwsdump_array = "array"
cwsdump_object = "object"
cwsdump_resource = "resource"

; The colors of the variable type
[cwsdump_colors]
cwsdump_null = "#3465A4"
cwsdump_bool = "#75507B"
cwsdump_string = "#CC0000"
cwsdump_int = "#4E9A06"
cwsdump_float = "#F57900"
cwsdump_array_empty = "#888A85"

; Control how are displayed several values
[cwsdump_display_values]
cwsdump_null = "null"
cwsdump_true = "true"
cwsdump_false = "false"
cwsdump_array_empty = "empty"
cwsdump_max_depth = "recursion depth limit reached..."
cwsdump_max_children = "more elements..."

```

## Getting started

```php
<?php

require_once 'class.cws.dump.php';

class DebugTestClass
{
    public $pubNull = null;
    public $pubStr = "aString";
    protected $proBool = true;
    protected $proInt = 10;
    protected $proFloat = 1.5;
    private $priArray = array("aString", 10, 1.5, false);
    private $priArray2 = array("key1" => "value1", "key2" => "value2");
    public $pubArray3 = array("key1" => "value1", "key2" => array("key1" => "value1", "key2" => "value2"));
    public $pubArray4 = array("key1" => "value1", "key2" => array("key1" => "value1", "key2" => array("key1" => "value1", "key2" => array("key1" => "value1", "key2" => array("key1" => "value1", "key2" => array("key1" => "value1", "key2" => array("key1" => "value1", "key2" => array("key1" => "value1", "key2" => array("key1" => "value1", "key2" => array("key1" => "value1", "key2" => "value2"))))))))));
    public $pubArrayEmpty = array();
    public $pubObject;
    public $pubObject2;
    
    public function __construct()
    {
        $this->pubObject = new DateTime();
        $this->pubObject2 = new Exception();
    }
}

$null = null;
$str = "aString";
$longstr = "Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.";
$bool = false;
$int = 10;
$float = 1.5;
$array = array("aString", $longstr, 10, 1.5, true);
$array_keys = array("key1" => "aString", "key2" => $longstr, "key3" => 10, "key4" => 1.5, "key5" => true);
$array_empty = array();
$class = new DebugTestClass();
$obj = new DateTime();

cwsDump($null);
cwsDump($str);
cwsDump($longstr);
cwsDump($bool);
cwsDump($int);
cwsDump($float);
cwsDump($array);
cwsDump($array_keys);
cwsDump($array_empty);
cwsDump($class);
cwsDump($obj);

?>
```

## Example

An example is available in ``index.php`` file :

![](http://static.crazyws.fr/resources/blog/2013/09/cwsdump-var-dump-xdebug-full3.png)

## Method

Just call **cwsDump()** !

## License

LGPL. See ``LICENSE`` for more details.

## More infos

http://www.crazyws.fr/dev/classes-php/cwsdump-alternative-aux-fonctions-var-dump-et-print-S6ZPT.html
