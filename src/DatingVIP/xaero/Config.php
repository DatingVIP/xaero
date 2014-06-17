<?php
namespace DatingVIP\xaero;

class Config implements \ArrayAccess {
    
    public function __construct($argv) {
        $config         = $this->config;
        $flags          = $this->flags;

        $config["exec"] = array_shift($argv);        

        while (($key = array_shift($argv))) {
            switch (substr($key, 0, 1)) {
                case '-': switch(substr($key, 1, 1)) {
                    case '-': {
                        if (($idx = strpos($key, '=')) !== false) {
                            $value = substr($key, $idx + 1);
                            $key   = substr($key, 2, $idx - 2);
                        } else {
                            $key   = substr($key, 2);
                            $value = array_shift($argv);
                        }
                        
                        $key = trim($key);
                        
                        if ($value === null) {
                            throw new \RuntimeException(
                                "malformed command line at {$key}, no value specified");
                        }
                        
                        $value = trim($value);
                        
                        /* use sensible values for some specific strings */
                        switch (($lv = strtolower($value))) {
                            case "yes":
                            case "true":
                            case "enabled":
                            case "on":
                                $value = true;
                            break;
                            
                            case "no":
                            case "false":
                            case "disabled":
                            case "off":
                               $value = false;
                            break;
                            
                            /* deal with streams in configuration parameters */
                            default: if (($si = strpos($lv, "://")) !== false) {
                                switch (substr($lv, 0, $si)) {
                                    /* virtual php stream */
                                    case "php":
                                        $section = @include(substr($value, $si+3));
                                    break;
                                    
                                    default:
                                        $section = @file_get_contents($value);
                                }
                                
                                if (!$section) {
                                    throw new \RuntimeException(
                                        "malformed configuration stream in command line at {$key} {$value}");
                                }
                                
                                $value = $section;
                            }
                        }
                        
                        if (strpos($key, '.') !== false) {
                            /* generate code for handling arrays */
                            if (substr($key, 0, 1) == "." || 
                                substr($key, -1, 1) == ".") {
                                throw new \RuntimeException(
                                    "malformed array key at {$key}, cannot begin or end with a period");
                            }
                            
                            $kpart = sprintf(
                                '"%s"',
                                str_replace('.', '"]["', $key));
                            
                            if (eval("return isset(\$config[$kpart]);")) {
                                eval("\$config[$kpart] = (array) \$config[$kpart];");
                                eval("\$config[$kpart][] = \$value;");
                            } else eval("\$config[$kpart] = \$value;");
                        } else {
                            if (isset($config[$key])) {
                                $config[$key] = (array) $config[$key];
                                $config[$key][] = $value;
                            } else $config[$key] = $value;
                        }
                    } break;
                    
                    default:
                        $flags[substr($key, 1)] = true;
                } break;
                
                default:
                    throw new \RuntimeException(
                        "failed to read configuration from command line, unexpected parameter {$key}");
            }
        }
        
        $this->config = $config;
        $this->flags  = $flags;
        
        if ($this->hasFlag('help')) {
            $this->printUsage();
            exit(0);
        }
    }
    
    public function hasFlag($flag)              { return isset($this->flags[$flag]); }

    protected function printUsage() {
        $separate = function() {
            echo str_repeat('-', 80);
            echo("\n");
        };
        
        $separate();
        echo("Welcome to xaero :)\n");
        $separate();
        echo("usage: {$this["exec"]} [options] [flags]\n");
        $separate();
        echo("Options:\n");
        echo("[server]\n");
        echo("    [hostname]   irc.datingvip.com\n");
        echo("    [port]       9867\n");
        echo("    [ssl]        on\n");
        echo("[threads]        4\n");
        echo("[nick]           xaero2\n");
        echo("[channel]        #devs\n");
        $separate();
        echo("Flags:\n");
        echo("[quiet]          false\n");
        $separate();
        echo("Example:\n");
        echo("{$this["exec"]} --server.hostname=localhost --server.port=6667 --server.ssl=off -quiet\n");
        $separate();
    }
    
    public function offsetGet($offset)          { return $this->config[$offset];  }
    public function offsetSet($offset, $data)   { $this->config[$offset] = $data; }
    public function offsetUnset($offset)        { unset($this->config[$offset]);  }
    public function offsetExists($offset)       { return isset($this->config[$offset]); }
    
    protected $flags =  [];
    protected $config = [
        "server" => [
            "hostname" => "irc.datingvip.com",
            "port"     => 9867,
            "ssl"      => true
        ],
        "threads"      => 4,
        "nick"         => "xaero2",
        "channel"      => "#devs"
    ];
}
?>
