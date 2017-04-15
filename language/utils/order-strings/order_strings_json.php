<?php

/*
 * Ejecutar con "php -n order_strings_json.php" sino llena /tmp con la depuración
 * 
 * DEBE EJECUTARSE DESDE LA PROPIA CARPETA DONDE ESTÁ
 * 
 * Lee, ordena y elimina posibles cadenas duplicadas.
 * Es mejor para controlar los cambios en los commits en git.
 * 
 * */

echo "####################################\n";
echo "#    Ordenando archivos JSON...    #\n";
echo "####################################\n";

$path = '../../';
$allFiles = scandir($path);
$jsonFiles = array();

foreach ($allFiles as $file) {
   if (strpos($file, '.json') !== false) {
      $jsonFiles[] = $file;
   }
}

foreach ($jsonFiles as $file) {
   $jsonFileOrig = $path . $file;

   $readedFileOrig = file_get_contents($jsonFileOrig);
   $arrayFileOrig = array_unique(json_decode($readedFileOrig, true));

   ksort($arrayFileOrig);
   file_put_contents($jsonFileOrig, json_encode($arrayFileOrig, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
   echo "Archivo " . $file . " procesado.\n";
}

echo "\nYa tienes todos los archivos JSON ordendos por tag!\n";
?>
