<?php
  $subject = "<p>je suis un paragraphe</p>";
  $pattern = "<(p|/p)>";
  $replace = '<(div|/div)>';

  preg_match_all( $pattern, $replace, $subject);
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_FILES['file'])){
    	$fichier = $_FILES['file'];   //-> permet d'afficher un tableau qui présente l'upload
      $fichier['name'] = 'The Walking Dead '.rand ( 1 , 10000000000 ).' .str';
  		$path='./file/'.$fichier['name'];
			if(!@move_uploaded_file($fichier['tmp_name'], $path)){
				die('un problème s‘est posé lors de la création du fichier sur le serveur');
			}
      else{
        $monFicher = file($path);
        $i=0;
        foreach($monFicher as $line){
          $pattern = "/((\d{2}(:|,)){3}\d{3}) --> ((\d{2}(:|,)){3}\d{3})/";
          //$pattern = "/^((\d{2}(:|,)){3}\d{3})/";
          preg_match($pattern, $line, $match);
          if(!empty($match[$i])){
            $times = explode(' --> ', $match[0]);
            $increaseTheTime = function($time) {
              $string = explode(',', $time);
              $otherString = explode(':', $string[0]);
              $aTimes = [];
              $aTimes['hours']=$otherString[0];
              $aTimes['minutes']=$otherString[1];
              $aTimes['secondes']=$otherString[2];
              $aTimes['milisecondes']=$string[1];
              $fullTime = ($aTimes['hours']*(60*60*1000))+($aTimes['minutes']*(60*1000))+($aTimes['secondes']*1000)+($aTimes['milisecondes']) + $_POST['time'];
              $newTimes = [];
              $tmpTime = explode('.', $fullTime/(60*60*1000));
              if ($fullTime < 60*60*1000) {
                $tmpTime[0] = 0;
              };
              $tmpTime[0] < 10 ? $newTimes['hours'] = '0'.$tmpTime[0] : $newTimes['hours'] = $tmpTime[0];
              $tmpTime = explode('.', ($fullTime/(60*1000))%(60));
              $tmpTime[0] < 10 ? $newTimes['minutes'] = '0'.$tmpTime[0] : $newTimes['minutes'] = $tmpTime[0];
              $tmpTime = explode('.', ($fullTime/1000)%60);
              $tmpTime[0] < 10 ? $newTimes['secondes'] = '0'.$tmpTime[0] : $newTimes['secondes'] = $tmpTime[0];
              $tmpTime = explode('.', ($fullTime)%1000);
              $tmpTime[0] < 10 ? $newTimes['milisecondes'] = '0'.$tmpTime[0] : $newTimes['milisecondes'] = $tmpTime[0];
              $newTimes['milisecondes'] < 100 ? $newTimes['milisecondes'] = '0'.$newTimes['milisecondes'] : $newTimes['milisecondes'];
              return ($newTimes['hours'].':'.$newTimes['minutes'].':'.$newTimes['secondes'].','.$newTimes['milisecondes']);
            };
            $firstTimeIncreased = $increaseTheTime($times[0]);
            $secondeTimeIncreased = $increaseTheTime($times[1]);
            $fullTime = $firstTimeIncreased.' --> '.$secondeTimeIncreased;
          }
          if(isset($fullTime)){
            $newFile[0]='1';
            $newFile[$i] = preg_replace($pattern, $fullTime, $line);
          }
          $i++;
        }
      }
    }
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>Super formulaire pour du regexp surper fun</title>
    <style media="screen">
      body{
        width: 960px;
        margin: 0 auto;
        margin-top: 128px
      }
      div{
        margin: 12px 0
      }
      p{
        min-height: 1em;
      }
    </style>
  </head>
  <body>
    <?php if($_SERVER['REQUEST_METHOD'] === 'GET'): ?>
      <form enctype="multipart/form-data" class="blabla" action="index.php" method="post">
        <div class="">
          <input type="hidden" name="MAX_FILE_SIZE" value="30000000" />
          <input type="file" name="file" value="Choisir un fichier">
        </div>
        <div class="">
          <label for="time">Temps de décalage (ms)</label><input type="number" name="time" value="0">
        </div>
        <div class="">
          <input type="submit" name="submit" value="Décaler">
        </div>
      </form>
    <?php endif; ?>
    <?php if($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
      <?php foreach ($newFile as $line) : ?>
        <pre>
          <?php print($line); ?>
        </pre>
      <?php endforeach; ?>
    <?php endif; ?>
  </body>
</html>
