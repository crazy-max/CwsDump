<?php

require_once __DIR__.'/../vendor/autoload.php';

class DebugTestClass
{
    public $pubNull = null;
    public $pubStr = 'aString';
    protected $proBool = true;
    protected $proInt = 10;
    protected $proFloat = 1.5;
    private $priArray = array('aString', 10, 1.5, false);
    private $priArray2 = array('key1' => 'value1', 'key2' => 'value2');
    public $pubArray3 = array('key1' => 'value1', 'key2' => array('key1' => 'value1', 'key2' => 'value2'));
    public $pubArray4 = array('key1' => 'value1', 'key2' => array('key1' => 'value1', 'key2' => array('key1' => 'value1', 'key2' => array('key1' => 'value1', 'key2' => array('key1' => 'value1', 'key2' => array('key1' => 'value1', 'key2' => array('key1' => 'value1', 'key2' => array('key1' => 'value1', 'key2' => array('key1' => 'value1', 'key2' => array('key1' => 'value1', 'key2' => 'value2'))))))))));
    public $pubArrayEmpty = array();
    public $pubObject;
    public $pubObject2;
    public $pubRes;

    public function __construct()
    {
        $this->pubObject = new DateTime();
        $this->pubObject2 = new Exception();
        $this->pubRes = mysqli_connect();
    }
}

$null = null;
$str = 'aString';
$longstr = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur magni dolores eos qui ratione voluptatem sequi nesciunt. Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur? Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur? At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat.';
$bool = false;
$int = 10;
$float = 1.5;
$array = array('aString', $longstr, 10, 1.5, true);
$array_keys = array('key1' => 'aString', 'key2' => $longstr, 'key3' => 10, 'key4' => 1.5, 'key5' => true);
$array_empty = array();
$class = new DebugTestClass();
$obj = new DateTime();
$res = mysqli_connect();

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
cwsDump($res);
