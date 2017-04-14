<?php
/*
 * This file is part of FacturaScripts
 * Copyright (C) 2013-2017  Carlos Garcia Gomez  neorazorx@gmail.com
 * Copyright (C) 2017  Francesc Pineda Segarra  shawe.ewahs@gmail.com
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'base/fs_i18n.php';

/* El idioma se lee de los lenguajes que soporta el navegador */
$lang = substr(\filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'), 0, 2);
/* 
 * En caso de que quieras probar como se ven las traducciones, indicalo aquí
 */
$lang = 'ba';

$language = ($lang and file_exists('language/lang_' . $lang . '.json')) ? $lang : 'es';
$i18n = new fs_i18n();
$i18n->setForcedLang($language);
$i18n->init();

$nombre_archivo = "config.php";
error_reporting(E_ALL);
$errors = array();
$errors2 = array();
$db_type = 'MYSQL';
$db_host = 'localhost';
$db_port = '3306';
$db_name = 'facturascripts';
$db_user = '';

function random_string($length = 20) {
   return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function guarda_config($nombre_archivo) {
   $archivo = fopen($nombre_archivo, "w");
   if ($archivo) {
      fwrite($archivo, "<?php\n");
      fwrite($archivo, "/*\n");
      fwrite($archivo, " * Configuración de la base de datos.\n");
      fwrite($archivo, " * type: postgresql o mysql (mysql está en fase experimental).\n");
      fwrite($archivo, " * host: la ip del ordenador donde está la base de datos.\n");
      fwrite($archivo, " * port: el puerto de la base de datos.\n");
      fwrite($archivo, " * name: el nombre de la base de datos.\n");
      fwrite($archivo, " * user: el usuario para conectar a la base de datos\n");
      fwrite($archivo, " * pass: la contraseña del usuario.\n");
      fwrite($archivo, " * history: TRUE si quieres ver todas las consultas que se hacen en cada página.\n");
      fwrite($archivo, " */\n");
      fwrite($archivo, "define('FS_DB_TYPE', '" . $_REQUEST['db_type'] . "'); /// MYSQL o POSTGRESQL\n");
      fwrite($archivo, "define('FS_DB_HOST', '" . $_REQUEST['db_host'] . "');\n");
      fwrite($archivo, "define('FS_DB_PORT', '" . $_REQUEST['db_port'] . "'); /// MYSQL -> 3306, POSTGRESQL -> 5432\n");
      fwrite($archivo, "define('FS_DB_NAME', '" . $_REQUEST['db_name'] . "');\n");
      fwrite($archivo, "define('FS_DB_USER', '" . $_REQUEST['db_user'] . "'); /// MYSQL -> root, POSTGRESQL -> postgres\n");
      fwrite($archivo, "define('FS_DB_PASS', '" . $_REQUEST['db_pass'] . "');\n");

      if ($_REQUEST['db_type'] == 'MYSQL' AND $_POST['mysql_socket'] != '') {
         fwrite($archivo, "ini_set('mysqli.default_socket', '" . $_POST['mysql_socket'] . "');\n");
      }

      fwrite($archivo, "\n");
      fwrite($archivo, "/*\n");
      fwrite($archivo, " * Un directorio de nombre aleatorio para mejorar la seguridad del directorio temporal.\n");
      fwrite($archivo, " */\n");
      fwrite($archivo, "define('FS_TMP_NAME', '" . random_string(20) . "/');\n");
      fwrite($archivo, "\n");
      fwrite($archivo, "/*\n");
      fwrite($archivo, " * En cada ejecución muestra todas las sentencias SQL utilizadas.\n");
      fwrite($archivo, " */\n");
      fwrite($archivo, "define('FS_DB_HISTORY', FALSE);\n");
      fwrite($archivo, "\n");
      fwrite($archivo, "/*\n");
      fwrite($archivo, " * Habilita el modo demo, para pruebas.\n");
      fwrite($archivo, " * Este modo permite hacer login con cualquier usuario y la contraseña demo,\n");
      fwrite($archivo, " * además deshabilita el límite de una conexión por usuario.\n");
      fwrite($archivo, " */\n");
      fwrite($archivo, "define('FS_DEMO', FALSE);\n");
      fwrite($archivo, "\n");
      fwrite($archivo, "/*\n");
      fwrite($archivo, " * Configuración de memcache.\n");
      fwrite($archivo, " * Host: la ip del servidor donde está memcached.\n");
      fwrite($archivo, " * port: el puerto en el que se ejecuta memcached.\n");
      fwrite($archivo, " * prefix: prefijo para las claves, por si tienes varias instancias de\n");
      fwrite($archivo, " * FacturaScripts conectadas al mismo servidor memcache.\n");
      fwrite($archivo, " */\n");
      fwrite($archivo, "\n");
      fwrite($archivo, "define('FS_CACHE_HOST', '" . $_REQUEST['cache_host'] . "');\n");
      fwrite($archivo, "define('FS_CACHE_PORT', '" . $_REQUEST['cache_port'] . "');\n");
      fwrite($archivo, "define('FS_CACHE_PREFIX', '" . $_REQUEST['cache_prefix'] . "');\n");
      fwrite($archivo, "\n");
      fwrite($archivo, "/// caducidad (en segundos) de todas las cookies\n");
      fwrite($archivo, "define('FS_COOKIES_EXPIRE', 7776000);\n");
      fwrite($archivo, "\n");
      fwrite($archivo, "/// el número de elementos a mostrar en pantalla\n");
      fwrite($archivo, "define('FS_ITEM_LIMIT', 50);\n");
      fwrite($archivo, "\n");
      fwrite($archivo, "/// desactiva el poder modificar plugins (añadir, descargar y eliminar)\n");
      fwrite($archivo, "define('FS_DISABLE_MOD_PLUGINS', FALSE);\n");
      fwrite($archivo, "\n");
      fwrite($archivo, "/// desactiva el poder añadir plugins manualmente\n");
      fwrite($archivo, "define('FS_DISABLE_ADD_PLUGINS', FALSE);\n");
      fwrite($archivo, "\n");
      fwrite($archivo, "/// desactiva el poder eliminar plugins manualmente\n");
      fwrite($archivo, "define('FS_DISABLE_RM_PLUGINS', FALSE);\n");

      if ($_REQUEST['proxy_type']) {
         fwrite($archivo, "\n");
         fwrite($archivo, "define('FS_PROXY_TYPE', '" . $_REQUEST['proxy_type'] . "');\n");
         fwrite($archivo, "define('FS_PROXY_HOST', '" . $_REQUEST['proxy_host'] . "');\n");
         fwrite($archivo, "define('FS_PROXY_PORT', '" . $_REQUEST['proxy_port'] . "');\n");
      }

      fclose($archivo);

      header("Location: index.php");
      exit();
   } else {
      $errors[] = "permisos";
   }
}

if (file_exists('config.php')) {
   header('Location: index.php');
} else if (floatval(substr(phpversion(), 0, 3)) < 5.3) {
   $errors[] = 'php';
} else if (floatval('3,1') >= floatval('3.1')) {
   $errors[] = "floatval";
   $errors2[] = \L::install__point_isnt_the_default_decimal_separator;
} else if (!function_exists('mb_substr')) {
   $errors[] = "mb_substr";
} else if (!extension_loaded('simplexml')) {
   $errors[] = "simplexml";
   $errors2[] = \L::install__simplexml_not_found;
   $errors2[] = \L::install__install_phpxml;
} else if (!extension_loaded('openssl')) {
   $errors[] = "openssl";
} else if (!extension_loaded('zip')) {
   $errors[] = "ziparchive";
} else if (!is_writable(getcwd())) {
   $errors[] = "permisos";
} else if (isset($_REQUEST['db_type'])) {
   if ($_REQUEST['db_type'] == 'MYSQL') {
      if (class_exists('mysqli')) {
         if ($_POST['mysql_socket'] != '') {
            ini_set('mysqli.default_socket', $_POST['mysql_socket']);
         }

         // Omitimos el valor del nombre de la BD porque lo comprobaremos más tarde
         $connection = @new mysqli($_REQUEST['db_host'], $_REQUEST['db_user'], $_REQUEST['db_pass'], "", intval($_REQUEST['db_port']));
         if ($connection->connect_error) {
            $errors[] = "db_mysql";
            $errors2[] = $connection->connect_error;
         } else {
            // Comprobamos que la BD exista, de lo contrario la creamos
            $db_selected = mysqli_select_db($connection, $_REQUEST['db_name']);
            if ($db_selected) {
               guarda_config($nombre_archivo);
            } else {
               $sqlCrearBD = "CREATE DATABASE `" . $_REQUEST['db_name'] . "`;";
               if (mysqli_query($connection, $sqlCrearBD)) {
                  guarda_config($nombre_archivo);
               } else {
                  $errors[] = "db_mysql";
                  $errors2[] = mysqli_error($connection);
               }
            }
         }
      } else {
         $errors[] = "db_mysql";
         $errors2[] = \L::install__php_mysql_not_found;
      }
   } else if ($_REQUEST['db_type'] == 'POSTGRESQL') {
      if (function_exists('pg_connect')) {
         $connection = @pg_connect('host=' . $_REQUEST['db_host'] . ' port=' . $_REQUEST['db_port'] . ' user=' . $_REQUEST['db_user'] . ' password=' . $_REQUEST['db_pass']);
         if ($connection) {
            // Comprobamos que la BD exista, de lo contrario la creamos
            $connection2 = @pg_connect('host=' . $_REQUEST['db_host'] . ' port=' . $_REQUEST['db_port'] . ' dbname=' . $_REQUEST['db_name']
                            . ' user=' . $_REQUEST['db_user'] . ' password=' . $_REQUEST['db_pass']);

            if ($connection2) {
               guarda_config($nombre_archivo);
            } else {
               $sqlCrearBD = 'CREATE DATABASE "' . $_REQUEST['db_name'] . '";';
               if (pg_query($connection, $sqlCrearBD)) {
                  guarda_config($nombre_archivo);
               } else {
                  $errors[] = "db_postgresql";
                  $errors2[] = \L::install__error_creating_database;
               }
            }
         } else {
            $errors[] = "db_postgresql";
            $errors2[] = \L::install__error_connecting_database;
         }
      } else {
         $errors[] = "db_postgresql";
         $errors2[] = \L::install__php_pgsql_not_found;
      }
   }

   $db_type = $_REQUEST['db_type'];
   $db_host = $_REQUEST['db_host'];
   $db_port = $_REQUEST['db_port'];
   $db_name = $_REQUEST['db_name'];
   $db_user = $_REQUEST['db_user'];
}

$system_info = 'facturascripts: ' . file_get_contents('VERSION') . "\n";
$system_info .= 'os: ' . php_uname() . "\n";
$system_info .= 'php: ' . phpversion() . "\n";

if (isset($_SERVER['REQUEST_URI'])) {
   $system_info .= 'url: ' . $_SERVER['REQUEST_URI'] . "\n------";
}
foreach ($errors as $e) {
   $system_info .= "\n" . $e;
}

$system_info = str_replace('"', "'", $system_info);
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es" xml:lang="es" >
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <title>FacturaScripts</title>
      <meta name="description" content="<?php echo \L::header__meta_description; ?>" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta name="generator" content="<?php echo \L::common__facturascripts; ?>" />
      <link rel="shortcut icon" href="view/img/favicon.ico" />
      <link rel="stylesheet" href="view/css/bootstrap-yeti.min.css" />
      <link rel="stylesheet" href="view/css/font-awesome.min.css" />
      <link rel="stylesheet" href="view/css/datepicker.css" />
      <link rel="stylesheet" href="view/css/custom.css" />
      <script type="text/javascript" src="view/js/jquery.min.js"></script>
      <script type="text/javascript" src="view/js/bootstrap.min.js"></script>
      <script type="text/javascript" src="view/js/bootstrap-datepicker.js" charset="UTF-8"></script>
      <script type="text/javascript" src="view/js/jquery.autocomplete.min.js"></script>
      <script type="text/javascript" src="view/js/base.js"></script>
      <script type="text/javascript" src="view/js/jquery.validate.min.js"></script>
   </head>
   <body>
      <nav class="navbar navbar-default" role="navigation" style="margin: 0px;">
         <div class="container-fluid">
            <div class="navbar-header">
               <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                  <span class="sr-only"><?php echo \L::common__menu; ?></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
                  <span class="icon-bar"></span>
               </button>
               <a class="navbar-brand" href="index.php"><?php echo \L::common__facturascripts; ?></a>
            </div>
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
               <ul class="nav navbar-nav navbar-right">
                  <li>
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" title="<?php echo \L::common__help; ?>">
                        <span class="glyphicon glyphicon-question-sign hidden-xs"></span>
                        <span class="visible-xs"><?php echo \L::common__help; ?></span>
                     </a>
                     <ul class="dropdown-menu">
                        <li>
                           <a href="https://www.facturascripts.com/documentacion" target="_blank">
                              <i class="fa fa-book" aria-hidden="true"></i>&nbsp;<?php echo \L::header__documentation; ?>
                           </a>
                        </li>
                        <li>
                           <a href="https://www.facturascripts.com/noticias" target="_blank">
                              <i class="fa fa-newspaper-o" aria-hidden="true"></i>&nbsp;<?php echo \L::header__news; ?>
                           </a>
                        </li>
                        <li>
                           <a href="https://www.facturascripts.com/preguntas" target="_blank">
                              <i class="fa fa-question-circle" aria-hidden="true"></i>&nbsp;<?php echo \L::header__questions; ?>
                           </a>
                        </li>
                        <li>
                           <a href="https://www.facturascripts.com/errores" target="_blank">
                              <i class="fa fa-bug" aria-hidden="true"></i>&nbsp;<?php echo \L::header__errors; ?>
                           </a>
                        </li>
                        <li>
                           <a href="https://www.facturascripts.com/ideas" target="_blank">
                              <i class="fa fa-lightbulb-o" aria-hidden="true"></i>&nbsp;<?php echo \L::header__ideas; ?>
                           </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                           <a href="#" id="b_feedback">
                              <i class="fa fa-send" aria-hidden="true"></i>&nbsp;<?php echo \L::header__report; ?>
                           </a>
                        </li>
                     </ul>
                  </li>
               </ul>
            </div>
         </div>
      </nav>

      <form name="f_feedback" action="https://www.facturascripts.com/comm3/index.php?page=community_feedback" method="post" target="_blank" class="form" role="form">
         <input type="hidden" name="feedback_info" value="<?php echo $system_info; ?>"/>
         <input type="hidden" name="feedback_type" value="error"/>
         <div class="modal" id="modal_feedback">
            <div class="modal-dialog">
               <div class="modal-content">
                  <div class="modal-header">
                     <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                     <h4 class="modal-title"><?php echo \L::feedback__needs_help; ?></h4>
                     <p class="help-block">
                        <?php echo \L::install__help_use_this_form_for_help; ?>
                     </p>
                  </div>
                  <div class="modal-body">
                     <div class="form-group">
                        <label for="feedback_textarea"><?php echo \L::install__describe_your_problem; ?></label>
                        <textarea id="feedback_textarea" class="form-control" name="feedback_text" rows="6"></textarea>
                     </div>
                     <div class="form-group">
                        <div class="input-group">
                           <span class="input-group-addon">
                              <span class="glyphicon glyphicon-envelope"></span>
                           </span>
                           <input type="email" class="form-control" name="feedback_email" placeholder="<?php echo \L::header__placeholder_your_email; ?>"/>
                        </div>
                     </div>
                  </div>
                  <div class="modal-footer">
                     <button type="submit" class="btn btn-sm btn-primary">
                        <span class="glyphicon glyphicon-send"></span>&nbsp;<?php echo \L::common__send; ?>
                     </button>
                  </div>
               </div>
            </div>
         </div>
      </form>

      <script type="text/javascript">
         function change_db_type() {
            if (document.f_configuracion_inicial.db_type.value == 'POSTGRESQL')
            {
               document.f_configuracion_inicial.db_port.value = '5432';
               if (document.f_configuracion_inicial.db_user.value == '')
               {
                  document.f_configuracion_inicial.db_user.value = 'postgres';
               }
               $("#mysql_socket").hide();
            } else
            {
               document.f_configuracion_inicial.db_port.value = '3306';
               $("#mysql_socket").show();
            }
         }
         $(document).ready(function () {
            $("#f_configuracion_inicial").validate({
               rules: {
                  db_type: {required: false},
                  db_host: {required: true, minlength: 2},
                  db_port: {required: true, minlength: 2},
                  db_name: {required: true, minlength: 2},
                  db_user: {required: true, minlength: 2},
                  db_pass: {required: false},
                  cache_host: {required: true, minlength: 2},
                  cache_port: {required: true, minlength: 2},
                  cache_prefix: {required: false, minlength: 2}
               },
               messages: {
                  db_host: {
                     required: "<?php echo \L::install__required_field; ?>",
                     minlength: $.validator.format("<?php echo \L::install__minimum_required; ?> {0} <?php echo \L::install__characters; ?>")
                                 },
                                 db_port: {
                                    required: "<?php echo \L::install__required_field; ?>",
                                    minlength: $.validator.format("<?php echo \L::install__minimum_required; ?> {0} <?php echo \L::install__characters; ?>")
                                                },
                                                db_name: {
                                                   required: "<?php echo \L::install__required_field; ?>",
                                                   minlength: $.validator.format("<?php echo \L::install__minimum_required; ?> {0} <?php echo \L::install__characters; ?>")
                                                               },
                                                               db_user: {
                                                                  required: "<?php echo \L::install__required_field; ?>",
                                                                  minlength: $.validator.format("<?php echo \L::install__minimum_required; ?> {0} <?php echo \L::install__characters; ?>")
                                                                              },
                                                                              cache_host: {
                                                                                 required: "<?php echo \L::install__required_field; ?>",
                                                                                 minlength: $.validator.format("<?php echo \L::install__minimum_required; ?> {0} <?php echo \L::install__characters; ?>")
                                                                                             },
                                                                                             cache_port: {
                                                                                                required: "<?php echo \L::install__required_field; ?>",
                                                                                                minlength: $.validator.format("<?php echo \L::install__minimum_required; ?> {0} <?php echo \L::install__characters; ?>")
                                                                                                            },
                                                                                                         }
                                                                                                      });
                                                                                                   });
      </script>

      <div class="container">
         <div class="row">
            <div class="col-sm-12">
               <div class="page-header">
                  <h1>
                     <i class="fa fa-cloud-upload" aria-hidden="true"></i>
                     <?php echo \L::install__welcome_to_facturascripts_installer; ?>
                     <small><?php echo file_get_contents('VERSION'); ?></small>
                  </h1>
               </div>
            </div>
         </div>

         <div class="row">
            <div class="col-sm-12">
               <?php
               foreach ($errors as $err) {
                  if ($err == 'permisos') {
                     ?>
                     <div class="panel panel-danger">
                        <div class="panel-heading">
                           <?php echo \L::install__heading_write_permissions; ?>
                        </div>
                        <div class="panel-body">
                           <p>
                              <?php echo \L::install__cant_write_on_facturascripts_folder; ?>
                           </p>
                           <h3>
                              <i class="fa fa-linux" aria-hidden="true"></i><?php echo \L::install__linux; ?>
                           </h3>
                           <pre>sudo chmod -R o+w <?php echo dirname(__FILE__); ?></pre>
                           <p class="help-block">
                              <?php echo \L::install__help_linux; ?>
                           </p>
                           <h3>
                              <i class="fa fa-lock" aria-hidden="true"></i><?php echo \L::install__distributions; ?>
                           </h3>
                           <p class="help-block">
                              <?php echo \L::install__help_distributions; ?>
                           </p>
                           <h3>
                              <i class="fa fa-globe" aria-hidden="true"></i><?php echo \L::install__hosting; ?>
                           </h3>
                           <p class="help-block">
                              <?php echo \L::install__help_hosting; ?>
                           </p>
                        </div>
                     </div>
                     <?php
                  } else if ($err == 'php') {
                     ?>
                     <div class="panel panel-danger">
                        <div class="panel-heading">
                           <?php echo \L::install__heading_obsolete_php; ?>
                        </div>
                        <div class="panel-body">
                           <p>
                              <?php echo \L::install__heading_obsolete_php; ?>
                              <?php echo \L::install__obsolete_php; ?> <b><?php echo phpversion() ?></b>.
                           </p>
                           <h3><?php echo \L::install__solutions; ?></h3>
                           <ul>
                              <li>
                                 <p class="help-block">
                                    <?php echo \L::install__help_php_version_1; ?>
                                 </p>
                              </li>
                              <li>
                                 <p class="help-block">
                                    <?php echo \L::install__help_php_version_2; ?>
                                    <a href="https://www.facturascripts.com/descargar?nube=TRUE" target="_blank"><?php echo \L::install__recommended_hostings; ?></a>.
                                 </p>
                              </li>
                           </ul>
                        </div>
                     </div>
                     <?php
                  } else if ($err == 'mb_substr') {
                     ?>
                     <div class="panel panel-danger">
                        <div class="panel-heading">
                           <?php echo \L::install__php_mb_substr_not_found; ?>
                        </div>
                        <div class="panel-body">
                           <p>
                              <?php echo \L::install__required_mbstring; ?>
                           </p>
                           <h3>
                              <i class="fa fa-linux" aria-hidden="true"></i><?php echo \L::install__linux; ?>
                           </h3>
                           <p class="help-block">
                              <?php echo \L::install__required_mbstring_instructions; ?>
                           </p>
                           <h3>
                              <i class="fa fa-globe" aria-hidden="true"></i><?php echo \L::install__hosting; ?>
                           </h3>
                           <p class="help-block">
                              <?php echo \L::install__help_mbstring_hosting; ?>
                              <a href="https://www.facturascripts.com/descargar?nube=TRUE" target="_blank"><?php echo \L::install__recommended_hostings; ?></a>.
                           </p>
                        </div>
                     </div>
                     <?php
                  } else if ($err == 'openssl') {
                     ?>
                     <div class="panel panel-danger">
                        <div class="panel-heading">
                           <?php echo \L::install__php_openssl_not_found; ?>
                        </div>
                        <div class="panel-body">
                           <p>
                              <?php echo \L::install__help_openssl; ?>
                           </p>
                           <h3>
                              <i class="fa fa-globe" aria-hidden="true"></i><?php echo \L::install__hosting; ?>
                           </h3>
                           <p class="help-block">
                              <?php echo \L::install__help_openssl_hosting; ?>
                              <a href="https://www.facturascripts.com/descargar?nube=TRUE" target="_blank"><?php echo \L::install__recommended_hostings; ?></a>.
                           </p>
                           <h3>
                              <i class="fa fa-windows" aria-hidden="true"></i><?php echo \L::install__windows; ?>
                           </h3>
                           <p class="help-block">
                              <?php echo \L::install__official_package_for_windows; ?>
                              <a href="https://www.facturascripts.com/descargar?windows=TRUE" target="_blank"><?php echo \L::install__downloads; ?></a>.
                              <?php echo \L::install__unofficial_package_for_windows; ?>
                           </p>
                           <h3>
                              <i class="fa fa-linux" aria-hidden="true"></i><?php echo \L::install__linux; ?>
                           </h3>
                           <p class="help-block">
                              <?php echo \L::install__help_openssl_linux; ?>
                           </p>
                           <h3>
                              <i class="fa fa-apple" aria-hidden="true"></i><?php echo \L::install__mac; ?>
                           </h3>
                           <p class="help-block">
                              <?php echo \L::install__help_openssl_mac; ?>
                              <a href="https://www.facturascripts.com/descargar?mac=TRUE" target="_blank"><?php echo \L::install__downloads; ?></a>.
                           </p>
                        </div>
                     </div>
                     <?php
                  } else if ($err == 'ziparchive') {
                     ?>
                     <div class="panel panel-danger">
                        <div class="panel-heading">
                           <?php echo \L::install__php_ziparchive_not_found; ?>
                        </div>
                        <div class="panel-body">
                           <p>
                              <?php echo \L::install__help_ziparchive; ?>
                           </p>
                           <h3>
                              <i class="fa fa-linux" aria-hidden="true"></i><?php echo \L::install__linux; ?>
                           </h3>
                           <p class="help-block">
                              <?php echo \L::install__help_ziparchive_linux; ?>
                           </p>
                           <h3>
                              <i class="fa fa-globe" aria-hidden="true"></i><?php echo \L::install__hosting; ?>
                           </h3>
                           <p class="help-block">
                              <?php echo \L::install__help_ziparchive_hosting; ?>
                              <a href="https://www.facturascripts.com/descargar?nube=TRUE" target="_blank"><?php echo \L::install__recommended_hostings; ?></a>.
                           </p>
                        </div>
                     </div>
                     <?php
                  } else if ($err == 'db_mysql') {
                     ?>
                     <div class="panel panel-danger">
                        <div class="panel-heading">
                           <?php echo \L::install__database_access_mysql; ?>
                        </div>
                        <div class="panel-body">
                           <ul>
                              <?php
                              foreach ($errors2 as $err2)
                                 echo "<li>" . $err2 . "</li>";
                              ?>
                           </ul>
                        </div>
                     </div>
                     <?php
                  } else if ($err == 'db_postgresql') {
                     ?>
                     <div class="panel panel-danger">
                        <div class="panel-heading">
                           <?php echo \L::install__database_access_psql; ?>
                        </div>
                        <div class="panel-body">
                           <ul>
                              <?php
                              foreach ($errors2 as $err2)
                                 echo "<li>" . $err2 . "</li>";
                              ?>
                           </ul>
                        </div>
                     </div>
                     <?php
                  } else {
                     ?>
                     <div class="panel panel-danger">
                        <div class="panel-heading">
                           <?php echo \L::install__error; ?>
                        </div>
                        <div class="panel-body">
                           <ul>
                              <?php
                              if ($errors2) {
                                 foreach ($errors2 as $err2) {
                                    echo "<li>" . $err2 . "</li>";
                                 }
                              } else {
                                 echo "<li>" . \L::install__unknow_error . "</li>";
                              }
                              ?>
                           </ul>
                        </div>
                     </div>
                     <?php
                  }
               }
               ?>
            </div>
         </div>

         <div class="row">
            <div class="col-sm-10">
               <b><?php echo \L::install__before_start; ?></b>
               <p class="help-block">
                  <?php echo \L::install__help_us; ?>
               </p>
               <a href="https://www.youtube.com/watch?v=JWzGkOBP_d4" target="_blank" class="btn btn-sm btn-danger">
                  <span class="glyphicon glyphicon-facetime-video"></span> &nbsp;<?php echo \L::install__facturascripts_on_youtube; ?>
               </a>
            </div>
            <div class="col-sm-2">
               <div class="thumbnail">
                  <img src="view/img/help-menu.png" alt="ayuda"/>
               </div>
            </div>
         </div>

         <form name="f_configuracion_inicial" id="f_configuracion_inicial" action="install.php" class="form" role="form" method="post">
            <div class="row">
               <div class="col-sm-12">
                  <ul class="nav nav-tabs" role="tablist">
                     <li role="presentation" class="active">
                        <a href="#db" aria-controls="db" role="tab" data-toggle="tab">
                           <i class="fa fa-database"></i>&nbsp;
                           <?php echo \L::install__database; ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a href="#cache" aria-controls="cache" role="tab" data-toggle="tab">
                           <i class="fa fa-wrench"></i>&nbsp;
                           <?php echo \L::install__advanced; ?>
                        </a>
                     </li>
                     <li role="presentation">
                        <a href="#licencia" aria-controls="licencia" role="tab" data-toggle="tab">
                           <i class="fa fa-file-text-o"></i>&nbsp;
                           <?php echo \L::install__license; ?>
                        </a>
                     </li>
                  </ul>
                  <br/>
               </div>
            </div>
            <div class="tab-content">
               <div role="tabpanel" class="tab-pane active" id="db">
                  <div class="row">
                     <div class="col-sm-4">
                        <div class="form-group">
                           <?php echo \L::install__type_database_server; ?>
                           <select name="db_type" class="form-control" onchange="change_db_type()">
                              <option value="MYSQL"<?php
                              if ($db_type == 'MYSQL') {
                                 echo ' selected=""';
                              }
                              ?>><?php echo \L::install__mysql; ?></option>
                              <option value="POSTGRESQL"<?php
                              if ($db_type == 'POSTGRESQL') {
                                 echo ' selected=""';
                              }
                              ?>><?php echo \L::install__postgresql; ?></option>
                           </select>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <?php echo \L::install__server; ?>
                           <input class="form-control" type="text" name="db_host" value="<?php echo $db_host; ?>" autocomplete="off"/>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <?php echo \L::install__port; ?>
                           <input class="form-control" type="number" name="db_port" value="<?php echo $db_port; ?>" autocomplete="off"/>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-4">
                        <div class="form-group">
                           <?php echo \L::install__database_name; ?>
                           <input class="form-control" type="text" name="db_name" value="<?php echo $db_name; ?>" autocomplete="off"/>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <?php echo \L::install__database_username; ?>
                           <input class="form-control" type="text" name="db_user" value="<?php echo $db_user; ?>" autocomplete="off"/>
                        </div>
                     </div>
                     <div class="col-sm-4">
                        <div class="form-group">
                           <?php echo \L::install__database_password; ?>
                           <input class="form-control" type="password" name="db_pass" value="" autocomplete="off"/>
                        </div>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-sm-4">
                        <div id="mysql_socket" class="form-group">
                           <?php echo \L::install__database_socket; ?>
                           <input class="form-control" type="text" name="mysql_socket" value="" placeholder="<?php echo \L::install__optional; ?>" autocomplete="off"/>
                           <p class="help-block">
                              <?php echo \L::install__help_database_socket; ?>
                           </p>
                        </div>
                     </div>
                  </div>
               </div>
               <div role="tabpanel" class="tab-pane" id="cache">
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="panel panel-default">
                           <div class="panel-heading">
                              <h3 class="panel-title"><?php echo \L::install__memcached; ?></h3>
                           </div>
                           <div class="panel-body">
                              <p class="help-block">
                                 <?php echo \L::install__help_memcached; ?>
                              </p>
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <?php echo \L::install__server; ?>
                                       <input class="form-control" type="text" name="cache_host" value="localhost" autocomplete="off"/>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <?php echo \L::install__port; ?>
                                       <input class="form-control" type="number" name="cache_port" value="11211" autocomplete="off"/>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <?php echo \L::install__prefix; ?>
                                       <input class="form-control" type="text" name="cache_prefix" value="<?php echo random_string(8); ?>_" autocomplete="off"/>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                        <div class="panel panel-default">
                           <div class="panel-heading">
                              <h3 class="panel-title"><?php echo \L::install__proxy; ?></h3>
                           </div>
                           <div class="panel-body">
                              <div class="row">
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <?php echo \L::install__type_proxy; ?>
                                       <select class='form-control' name="proxy_type">
                                          <option value=""><?php echo \L::install__without_proxy; ?></option>
                                          <option value="">------</option>
                                          <option value="HTTP"><?php echo \L::install__http; ?></option>
                                          <option value="HTTPS"><?php echo \L::install__https; ?></option>
                                          <option value="SOCKS5"><?php echo \L::install__socks5; ?></option>
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <?php echo \L::install__server; ?>
                                       <input class="form-control" type="text" name="proxy_host" placeholder="192.168.1.1" autocomplete="off"/>
                                    </div>
                                 </div>
                                 <div class="col-sm-4">
                                    <div class="form-group">
                                       <?php echo \L::install__port; ?>
                                       <input class="form-control" type="number" name="proxy_port" placeholder="8080" autocomplete="off"/>
                                    </div>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div role="tabpanel" class="tab-pane" id="licencia">
                  <div class="row">
                     <div class="col-sm-12">
                        <div class="form-group">
                           <pre><?php echo file_get_contents('COPYING'); ?></pre>
                           <p>
                              <?php echo \L::install__help_license_1_10; ?><a href="https://github.com/feulf/raintpl/tree/65493157073ff0f313a67fe2ee42139b3eaa7f5a"><?php echo \L::install__raintpl; ?></a><?php echo \L::install__help_license_2_10; ?><a href="raintpl/LICENSE.txt"><?php echo \L::install__lgpl; ?></a><?php echo \L::install__help_license_3_10; ?><a href="https://github.com/PHPMailer/PHPMailer/"><?php echo \L::install__phpmailer; ?></a><?php echo \L::install__help_license_4_10; ?><a href="extras/phpmailer/LICENSE"><?php echo \L::install__lgpl; ?></a><?php echo \L::install__help_license_5_10; ?><a href="http://getbootstrap.com"><?php echo \L::install__bootstrap; ?></a><?php echo \L::install__help_license_6_10; ?><a href="https://github.com/twbs/bootstrap/blob/master/LICENSE"><?php echo \L::install__mit; ?></a><?php echo \L::install__help_license_7_10; ?><a href="http://fontawesome.io"><?php echo \L::install__fontawesome; ?></a><?php echo \L::install__help_license_8_10; ?><a href="http://fontawesome.io/license"><?php echo \L::install__mit; ?></a><?php echo \L::install__help_license_9_10; ?><a href="https://github.com/jquery/jquery"><?php echo \L::install__jquery; ?></a><?php echo \L::install__help_license_10_10; ?><a href="https://github.com/jquery/jquery/blob/master/LICENSE.txt"><?php echo \L::install__mit; ?></a>.
                           </p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="row">
               <div class="col-sm-12 text-right">
                  <button id="submit_button" class="btn btn-sm btn-primary" type="submit">
                     <span class="glyphicon glyphicon-ok"></span>&nbsp;<?php echo \L::install__accept; ?>
                  </button>
               </div>
            </div>
         </form>

         <div class="row" style="margin-bottom: 20px;">
            <div class="col-sm-12 text-center">
               <hr/>
               <small>
                  &COPY; 2013-2017 <a target="_blank" href="https://www.facturascripts.com"><?php echo \L::common__facturascripts; ?></a>
               </small>
            </div>
         </div>
      </div>
   </body>
</html>