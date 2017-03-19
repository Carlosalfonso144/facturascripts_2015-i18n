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

$lang = substr(\filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE'), 0, 2);
$language = ($lang and file_exists('language/lang_' . $lang . '.ini')) ? $lang : 'es';
$i18n = new fs_i18n();
$i18n->setForcedLang($language);
$i18n->init();

/// cargamos las constantes de configuración
require_once 'config.php';
require_once 'base/config2.php';

require_once 'base/fs_db2.php';
$db = new fs_db2();

require_once 'base/fs_model.php';
require_model('fs_extension.php');

if( $db->connect() )
{
   if( !isset($_REQUEST['v']) )
   {
      echo \L::api__no_api_version;
   }
   else if($_REQUEST['v'] == '2')
   {
      if( isset($_REQUEST['f']) )
      {
         $ejecutada = FALSE;
         $fsext = new fs_extension();
         foreach($fsext->all_4_type('api') as $ext)
         {
            if($ext->text == $_REQUEST['f'])
            {
               try
               {
                  $_REQUEST['f']();
               }
               catch(Exception $e)
               {
                  echo \L::api__error.$e->getMessage();
               }
               
               $ejecutada = TRUE;
               break;
            }
         }
         
         if(!$ejecutada)
         {
            echo \L::api__no_api_function_executed;
         }
      }
      else
         echo \L::api__no_function_executed;
   }
   else
   {
      echo \L::api__update_facturascripts;
   }
}
else
   echo \L::api__database_connection_error;
