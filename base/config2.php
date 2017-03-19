<?php
/*
 * This file is part of FacturaScripts
 * Copyright (C) 2013-2016  Carlos Garcia Gomez  neorazorx@gmail.com
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

if( !defined('FS_TMP_NAME') )
{
   define('FS_TMP_NAME', '');
}

if( !defined('FS_PATH') )
{
   define('FS_PATH', '');
}

if( !defined('FS_MYDOCS') )
{
   define('FS_MYDOCS', '');
}

if(FS_TMP_NAME != '' AND !file_exists('tmp/'.FS_TMP_NAME) )
{
   if( !file_exists('tmp') )
   {
      if( mkdir('tmp') )
      {
         file_put_contents('tmp/index.php', "<?php\necho \L::config2__blank_index';");
      }
   }
   
   mkdir('tmp/'.FS_TMP_NAME);
}

if( !defined('FS_COMMUNITY_URL') )
{
   define('FS_COMMUNITY_URL', 'https://www.facturascripts.com/comm3');
}

if( file_exists('tmp/'.FS_TMP_NAME.'config2.ini') )
{
   $GLOBALS['config2'] = parse_ini_file('tmp/'.FS_TMP_NAME.'config2.ini');
   
   if( !isset($GLOBALS['config2']['cost_is_average']) )
   {
      $GLOBALS['config2']['cost_is_average'] = 1;
   }
   
   if( !isset($GLOBALS['config2']['nf0']) )
   {
      $GLOBALS['config2']['nf0'] = 2;
      $GLOBALS['config2']['nf1'] = '.';
      $GLOBALS['config2']['nf2'] = ' ';
      $GLOBALS['config2']['pos_divisa'] = 'right';
   }
   
   if( !isset($GLOBALS['config2']['nf0_art']) )
   {
      $GLOBALS['config2']['nf0_art'] = 4;
   }
   
   if( !isset($GLOBALS['config2']['homepage']) )
   {
      $GLOBALS['config2']['homepage'] = 'admin_home';
   }
   
   if( !isset($GLOBALS['config2']['check_db_types']) )
   {
      $GLOBALS['config2']['check_db_types'] = 0;
   }
   
   if( !isset($GLOBALS['config2']['stock_negativo']) )
   {
      $GLOBALS['config2']['stock_negativo'] = 0;
      $GLOBALS['config2']['ventas_sin_stock'] = 1;
   }
   
   if( !isset($GLOBALS['config2']['precio_compra']) )
   {
      $GLOBALS['config2']['precio_compra'] = \L::config2__precio_compra;
      $GLOBALS['config2']['ip_whitelist'] = '*';
   }
   
   if( !isset($GLOBALS['config2']['iva']) )
   {
      $GLOBALS['config2']['iva'] = \L::config2__iva;
      $GLOBALS['config2']['irpf'] = \L::config2__irpf;
   }
   
   if( !isset($GLOBALS['config2']['libros_contables']) )
   {
      $GLOBALS['config2']['libros_contables'] = 1;
      $GLOBALS['config2']['foreign_keys'] = 1;
   }
   
   if( !isset($GLOBALS['config2']['new_codigo']) )
   {
      $GLOBALS['config2']['new_codigo'] = 'eneboo';
   }
   
   if( !isset($GLOBALS['config2']['factura']) )
   {
      $GLOBALS['config2']['factura'] = \L::config2__factura;
      $GLOBALS['config2']['facturas'] = \L::config2__facturas;
      $GLOBALS['config2']['numero2'] = \L::config2__numero_2;
   }
   
   if( !isset($GLOBALS['config2']['factura_simplificada']) )
   {
      $GLOBALS['config2']['factura_simplificada'] = \L::config2__factura_simplificada;
   }
   
   if( !isset($GLOBALS['config2']['factura_rectificativa']) )
   {
      $GLOBALS['config2']['factura_rectificativa'] = \L::config2__factura_rectificativa;
   }
   
   if( !isset($GLOBALS['config2']['db_integer']) )
   {
      $GLOBALS['config2']['db_integer'] = 'INTEGER';
   }
   
   if( !isset($GLOBALS['config2']['serie']) )
   {
      $GLOBALS['config2']['serie'] = \L::config2__serie;
      $GLOBALS['config2']['series'] = \L::config2__series;
   }
}
else
{
   $GLOBALS['config2'] = array(
       'zona_horaria' => 'Europe/Madrid',
       'nf0' => 2,
       'nf0_art' => 2,
       'nf1' => '.',
       'nf2' => ' ',
       'pos_divisa' => 'right',
       'factura' => \L::config2__factura,
       'facturas' => \L::config2__facturas,
       'factura_simplificada' => \L::config2__factura_simplificada,
       'factura_rectificativa' => \L::config2__factura_rectificativa,
       'albaran' => \L::config2__albaran,
       'albaranes' => \L::config2__albaranes,
       'pedido' => \L::config2__pedido,
       'pedidos' => \L::config2__pedidos,
       'presupuesto' => \L::config2__presupuesto,
       'presupuestos' => \L::config2__presupuestos,
       'provincia' => \L::config2__provincia,
       'apartado' => \L::config2__apartado,
       'cifnif' => \L::config2__cif_nif,
       'iva' => \L::config2__iva,
       'irpf' => \L::config2__irpf,
       'numero2' => \L::config2__numero_2,
       'serie' => \L::config2__serie,
       'series' => \L::config2__series,
       'cost_is_average' => 1,
       'precio_compra' => \L::config2__precio_compra,
       'homepage' => 'admin_home',
       'check_db_types' => 0,
       'stock_negativo' => 1,
       'ventas_sin_stock' => 1,
       'ip_whitelist' => '*',
       'libros_contables' => 1,
       'foreign_keys' => 1,
       'new_codigo' => 'new',
       'db_integer' => 'INTEGER'
   );
}

foreach($GLOBALS['config2'] as $i => $value)
{
   if($i == 'zona_horaria')
   {
      date_default_timezone_set($value);
   }
   else
   {
      define('FS_'.strtoupper($i), $value);
   }
}

if( !file_exists('plugins') )
{
   mkdir('plugins');
   chmod('plugins', octdec(777));
}

/// Cargamos la lista de plugins activos
$GLOBALS['plugins'] = array();
if( file_exists('tmp/enabled_plugins.list') )
{
   rename('tmp/enabled_plugins.list', 'tmp/'.FS_TMP_NAME.'enabled_plugins.list');
}

if( file_exists('tmp/'.FS_TMP_NAME.'enabled_plugins.list') )
{
   $list = explode(',', file_get_contents('tmp/'.FS_TMP_NAME.'enabled_plugins.list'));
   if($list)
   {
      foreach($list as $f)
      {
         if( file_exists('plugins/'.$f) )
         {
            $GLOBALS['plugins'][] = $f;
         }
      }
   }
}

/// cargamos las funciones de los plugins
foreach($GLOBALS['plugins'] as $plug)
{
   if( file_exists('plugins/'.$plug.'/functions.php') )
   {
      require_once 'plugins/'.$plug.'/functions.php';
   }
}
