<?php

// Turn off all error reporting
error_reporting(0);

$dir = getcwd();
chdir($dir);
chdir('..');

$fail = false;

// Does the info service even work?
if(`php index.php service=info` !== 'null') {
    # Info service works, let's test it
    h1("Testing info service");
    $info = json_decode(`php index.php service=info`, 1);
    foreach($info['patterns'] as $ns => $list) {
        h2("Testing $ns patterns");
        foreach($list as $id => $title) {
            p($title);
            if(exec("php index.php service=info pattern=$id 2>/dev/null")) ok();
            else {
                ko();
                $fail = true;
            }
        }
    }
    // Do we have a draft service?
    if(in_array('draft', $info['services'])) {
        // We do, let's test it
        h1("Testing draft service");
        foreach($info['patterns'] as $pns => $patterns) {
            h2("Testing $pns patterns");
            foreach($patterns as $pid => $ptitle) {
                foreach($info['themes'] as $tns => $theme) {
                    foreach($theme as $tid => $tname) {
                        p("$ptitle & $tname theme");
                        $cmd = "php index.php service=draft pattern=$pid theme=$tname";
                        exec($cmd." 2>$dir/test.log", $output, $result);
                        $errors = file_get_contents("$dir/test.log");
                        if($result === 0 && strlen($errors) === 0) ok();
                        else if ($result === 0) {
                            warn();
                            p("Error output:\n\n$errors");
                            p("Please fix these.");
                            p();
                        } else {
                            ko();
                            p("Error output:\n\n$errors");
                            p("Reproduce with this command:\n    $cmd");
                            p();
                            $fail = true;
                        }
                    }
                }
            }
        }
    }
} else {
    echo "\n\nCould not get results from info service\n\n";
    $fail = true;
}

echo "\n\n";
if($fail) exit(1);
else exit(0);








function h1($string)
{
    echo "\n\n\033[33m ".$string."\033[0m";
    echo "\n\n\033[33m ".str_pad('-', 72, '-')."\033[0m";
}

function h2($string)
{
    echo "\n\n\033[33m ".$string."\033[0m";
}

function p($string)
{
    echo "\n".str_pad('    '.$string, 69, ' ');
}

function ok()
{
    echo "\033[32m OK \033[0m";
}

function ko()
{
    echo "\033[31m Problem! \033[0m";
}

function warn()
{
    echo "\033[33m Warning \033[0m";
}
