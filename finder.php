<?php
  echo '<h1>Text finder</h1>';
$files_size = 0;
/***********************************************************/
/***********************************************************/
function microtime_float() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
/***********************************************************/ 
function str_search($path, $extension, $str)
{
        $file_arr = array();
        foreach (glob(rtrim($path, '/')."/*.".$extension) as $filename)
        {
        }
        return $file_arr;
}


       function FileListinfile($directory, $outputfile) {
          if ($handle = opendir($directory)) {
            while (false !== ($file = readdir($handle))) {
              if (is_file($directory.$file)) {
                  if ((strpos($file, '.css') > 0 ) or
                     (strpos($file, '.php') > 0 ) or
                     (strpos($file, '.html') > 0 ) or
                     (strpos($file, '.txt') > 0 )) {
                         file_put_contents($outputfile, $directory.$file."\n", FILE_APPEND);
                  }
              } elseif ($file != '.' and $file != '..' and is_dir($directory.$file)) {
                FileListinfile($directory.$file.'/', $outputfile);
              }
            }
          }
          closedir($handle);
        }

/***********************************************************/ 
function redirect($were, $timer = 10 ) {
      echo 'Wait <span id="counter">'.$timer.'</span> second<br />';

      echo '<p><a href="'.$were.'">GO.</a></p>';
      echo '<script type="text/javascript">
      function TimeOut () {
      var timec = parseInt(document.getElementById("counter").innerHTML, 10);
       timec--;
       document.getElementById("counter").innerHTML = timec;
       if (timec <= 0){
         location.replace("'.$were.'");
         clearInterval(idtimer);
        }
      }
      var idtimer = setInterval("TimeOut()", 1000);
      </script>';

}

if ($_GET['step'] == 'start') {
  if ($_POST['folder'] != '') {
    $_POST['folder'] = $_SERVER['DOCUMENT_ROOT'].trim($_POST['folder'], '/').'/';
    @unlink('finder_temp.txt');
    @unlink('finder_result.txt');
    FileListinfile($_POST['folder'], 'finder_temp.txt');
    file_put_contents('finder_result.txt', $_POST['search']."\n", FILE_APPEND);

    redirect('?step=go&n=0&search='.$_POST['search'], 0);
  } else {
    echo '<p>Error: Folder not set</p>';
  }
} else if ($_GET['step'] == 'go') {

  $starttime = microtime_float();
  $n=$_GET['n']+0;
  $str = $_GET['search'];
  $files = file('finder_temp.txt');
  $curtime=microtime_float();
  $runtime=$curtime-$starttime ;  
  $curfiles = 0;

  while (($runtime < 5) and ($n < count($files)) and ($curfiles < 1000)) {
    $filename = trim($files[$n]);
    if (preg_match($str, file_get_contents($filename)) != false) {
      file_put_contents('finder_result.txt', $filename."\n", FILE_APPEND);
    };
    $n ++;
    $curfiles ++;
  };
  
  echo  'Current session worktime '.$runtime.'sec. Last file is '.$n.'/'.count($files).' '.$files[$n - 1].'';
  if ($n < count($files)) {
      $were = '?step=go&n='.$n.
             '&search='.$str.
             '';
      redirect($were, 3);
  } else {
      echo '<p><a href="/finder_result.txt">finder_result.txt</a><p>';
  }
} else {

  echo '<form action="?step=start" method="POST">';
  echo 'Folder:<input type="text" name="folder" value="/">';
  echo '<br/>';
  echo 'Search Preg:<input type="text" name="search" value="/search_text/">';
  echo '<br/>';
  echo '<input type="submit" value="Search">';
  echo '<br/>';
  echo '</form>';
}
?>