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

require_model('fs_rol.php');

/**
 * Controlador de admin -> users.
 * @author Carlos García Gómez <neorazorx@gmail.com>
 */
class admin_users extends fs_controller
{
   public $agente;
   public $historial;
   public $rol;
   
   public function __construct()
   {
      parent::__construct(__CLASS__, \L::common__users, 'admin', TRUE, TRUE);
   }
   
   protected function private_core()
   {
      $this->agente = new agente();
      $this->rol = new fs_rol();
      
      if( isset($_POST['nnick']) )
      {
         $this->add_user();
      }
      else if( isset($_GET['delete']) )
      {
         $this->delete_user();
      }
      else if( isset($_POST['nrol']) )
      {
         $this->add_rol();
      }
      else if( isset($_GET['delete_rol']) )
      {
         $this->delete_rol();
      }
      
      /// cargamos el historial
      $fslog = new fs_log();
      $this->historial = $fslog->all_by('login');
   }
   
   private function add_user()
   {
      $nu = $this->user->get($_POST['nnick']);
      if($nu)
      {
         $this->new_error_msg(\L::admin_users__msg_user_already_exists( $_POST['nnick'], $nu->url() ));
      }
      else if(!$this->user->admin)
      {
         $this->new_error_msg(\L::admin_users__msg_admin_only_create_users, TRUE, 'login', TRUE);
      }
      else
      {
         $nu = new fs_user();
         $nu->nick = $_POST['nnick'];
         $nu->email = strtolower($_POST['nemail']);
         
         if( $nu->set_password($_POST['npassword']) )
         {
            $nu->admin = isset($_POST['nadmin']);
            if( isset($_POST['ncodagente']) )
            {
               if($_POST['ncodagente'] != '')
               {
                  $nu->codagente = $_POST['ncodagente'];
               }
            }
            
            if( $nu->save() )
            {
               $this->new_message(\L::admin_users__msg_user_created( $nu->nick ), TRUE, 'login', TRUE);
               
               /// algún rol marcado
               if(!$nu->admin AND isset($_POST['roles']))
               {
                  foreach($_POST['roles'] as $codrol)
                  {
                     $rol = $this->rol->get($codrol);
                     if($rol)
                     {
                        $fru = new fs_rol_user();
                        $fru->codrol = $codrol;
                        $fru->fs_user = $nu->nick;
                        
                        if( $fru->save() )
                        {
                           foreach($rol->get_accesses() as $p)
                           {
                              $a = new fs_access();
                              $a->fs_page = $p->fs_page;
                              $a->fs_user = $nu->nick;
                              $a->allow_delete = $p->allow_delete;
                              $a->save();
                           }
                        }
                     }
                  }
               }
               
               Header('location: index.php?page=admin_user&snick=' . $nu->nick);
            }
            else
            {
               $this->new_error_msg(\L::admin_users__msg_error_cant_save_user);
            }
         }
      }
   }
   
   private function delete_user()
   {
      $nu = $this->user->get($_GET['delete']);
      if($nu)
      {
         if(FS_DEMO)
         {
            $this->new_error_msg(\L::admin_users__msg_demo_cant_delete_user);
         }
         else if(!$this->user->admin)
         {
            $this->new_error_msg(\L::admin_users__msg_only_admin_can_delete_users, 'login', TRUE);
         }
         else if( $nu->delete() )
         {
            $this->new_message(\L::admin_users__msg_user_deleted( $nu->nick ), TRUE, 'login', TRUE);
         }
         else
         {
            $this->new_error_msg(\L::admin_users__msg_user_cant_be_deleted( $nu->nick ));
         }
      }
      else
      {
         $this->new_error_msg(\L::admin_users__msg_user_not_found( $_GET['delete'] ));
      }
   }
   
   private function add_rol()
   {
      $this->rol->codrol = $_POST['nrol'];
      $this->rol->descripcion = $_POST['descripcion'];
      
      if( $this->rol->save() )
      {
         $this->new_message(\L::common__msg_data_saved);
         header('Location: '.$this->rol->url());
      }
      else
      {
         $this->new_error_msg(\L::admin_users__msg_error_creating_rol( $_POST['nrol'] ));
      }
   }
   
   private function delete_rol()
   {
      $rol = $this->rol->get($_GET['delete_rol']);
      if($rol)
      {
         if( $rol->delete() )
         {
            $this->new_message(\L::admin_users__msg_rol_deleted( $rol->id ));
         }
         else
         {
            $this->new_error_msg(\L::admin_users__msg_error_deleting_rol( $rol->id ));
         }
      }
      else
      {
         $this->new_error_msg(\L::admin_users__msg_error_rol_not_found( $_GET['delete_rol'] ));
      }
   }
   
   public function all_pages()
   {
      $returnlist = array();
      
      /// Obtenemos la lista de páginas. Todas
      foreach($this->menu as $m)
      {
         $m->enabled = FALSE;
         $m->allow_delete = FALSE;
         $m->users = array();
         $returnlist[] = $m;
      }
      
      $users = $this->user->all();
      /// colocamos a los administradores primero
      usort($users, function($a, $b) {
         if($a->admin)
         {
            return -1;
         }
         else if($b->admin)
         {
            return 1;
         }
         else
         {
            return 0;
         }
      });
      
      /// completamos con los permisos de los usuarios
      foreach($users as $user)
      {
         if($user->admin)
         {
            foreach($returnlist as $i => $value)
            {
               $returnlist[$i]->users[$user->nick] = array(
                   'modify' => TRUE,
                   'delete' => TRUE,
               );
            }
         }
         else
         {
            foreach($returnlist as $i => $value)
            {
               $returnlist[$i]->users[$user->nick] = array(
                   'modify' => FALSE,
                   'delete' => FALSE,
               );
            }
            
            foreach($user->get_accesses() as $a)
            {
               foreach($returnlist as $i => $value)
               {
                  if($a->fs_page == $value->name)
                  {
                     $returnlist[$i]->users[$user->nick]['modify'] = TRUE;
                     $returnlist[$i]->users[$user->nick]['delete'] = $a->allow_delete;
                     break;
                  }
               }
            }
         }
      }
      
      /// ordenamos por nombre
      usort($returnlist, function($a, $b) {
         return strcmp($a->name, $b->name);
      });
      
      return $returnlist;
   }
}
