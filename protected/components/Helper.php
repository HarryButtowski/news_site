<?php

class Helper
{
    private static $_output  = '';
    private  static $_objects = array();
    private  static $_depth   = null;
    
    public static function dump($var,$depth=10,$highlight=true, $echo=true){
        
        if ($echo === true) echo self::dumpAsString($var,$depth,$highlight);
        else return $this->dumpAsString($var,$depth,$highlight);
    }
    
    public function dumpAsString($var, $depth = 10, $highlight = false) {
        self::$_output  = '';
        self::$_objects = array();
        self::$_depth   = $depth;
        self::dumpInternal($var, 0);
        
        if ($highlight) {
            $result = highlight_string("<?php\n" . self::$_output, true);
            self::$_output = preg_replace('/&lt;\\?php<br \\/>/', '', $result, 1);
        }
        return self::$_output;
    }
    
    private function dumpInternal($var, $level) {
        switch (gettype($var)) {
            case 'boolean':
                self::$_output .= $var ? 'true' : 'false';
                break;
            case 'integer':
                    self::$_output .= "$var";
                    break;
            case 'double':
                    self::$_output .= "$var";
                    break;
            case 'string':
                    self::$_output .= "'$var'";
                    break;
            case 'resource':
                    self::$_output .= '{resource}';
                    break;
            case 'NULL':
                    self::$_output .= "null";
                    break;
            case 'unknown type':
                    self::$_output .= '{unknown}';
                    break;
            case 'array':
                    if (self::$_depth <= $level) {
                            self::$_output .= 'array(...)';
                    }
                    else if (empty($var)) {
                            self::$_output .= '{ }';
                    }
                    else {
                            $keys           = array_keys($var);
                            $spaces         = str_repeat(' ', $level * 2);
                            self::$_output .= $spaces . '';

                            foreach($keys as $key) {
                                    self::$_output .= ($level == 0 ? '' : "\n") . $spaces . "  $key: ";
                                    self::$_output .= self::dumpInternal($var[$key], $level + 1);
                                    self::$_output .= ($level == 0 ? "\n" : '');
                            }

                            self::$_output .= "";
                    }
                    break;
            case 'object':
                    if (($id = array_search($var, self::$_objects, true)) !== false) {
                            self::$_output .= get_class($var) . '#' . ($id + 1) . '(...)';
                    }
                    else if (self::$_depth <= $level) {
                            self::$_output .= get_class($var) . '(...)';
                    }
                    else {
                            $id        = array_push(self::$_objects, $var);
                            $className = get_class($var);
                            $members   = (array)$var;
                            $keys      = array_keys($members);
                            $spaces    = str_repeat(' ', $level * 2);

                            self::$_output .= "$className ID:#$id";//\n".$spaces.'(';

                            foreach ($keys as $key) {
                                    $keyDisplay     = strtr(trim($key), array("\0" => '->'));
                                    self::$_output .= "\n" . $spaces . "  $keyDisplay: ";
                                    self::$_output .= self::dumpInternal($members[$key], $level + 1);
                            }

                            self::$_output .= "\n" . $spaces . ')';
                    }

                    break;
            default:
                    self::$_output .= "\n" . $spaces . '~' . $var;
        }
    }
}
