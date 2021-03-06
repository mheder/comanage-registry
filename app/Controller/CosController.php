<?php
/**
 * COmanage Registry CO Controller
 *
 * Copyright (C) 2010-15 University Corporation for Advanced Internet Development, Inc.
 * 
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not use this file except in compliance with
 * the License. You may obtain a copy of the License at
 * 
 * http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software distributed under
 * the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied. See the License for the specific language governing
 * permissions and limitations under the License.
 *
 * @copyright     Copyright (C) 2010-15 University Corporation for Advanced Internet Development, Inc.
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry
 * @since         COmanage Registry v0.1
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version       $Id$
 */

App::uses("StandardController", "Controller");

class CosController extends StandardController {
  // Class name, used by Cake
  public $name = "Cos";
    
  // Establish pagination parameters for HTML views
  public $paginate = array(
    'limit' => 25,
    'order' => array(
      'name' => 'asc'
    )
  );
  
  // In order to delete a CO, we need to always use hard delete, since soft
  // deleting records will result in foreign key dependencies sticking around
  public $useHardDelete = true;
  
  /**
   * Perform any dependency checks required prior to a delete operation.
   * - postcondition: Session flash message updated (HTML) or HTTP status returned (REST)
   *
   * @since  COmanage Registry v0.1
   * @param  Array Current data
   * @return boolean true if dependency checks succeed, false otherwise.
   */
  
  function checkDeleteDependencies($curdata) {
    // Make sure this request isn't trying to delete the COmanage CO

    $name = $this->Co->field('name');

    if($name == "COmanage") {
      if($this->request->is('restful')) {
        $this->Api->restResultHeader(403, "Cannot Remove COmanage CO");
      } else {
        $this->Flash->set(_txt('er.co.cm.rm'), array('key' => 'error'));
      }
      
      return false;
    }
    
    return true;
  }
  
  /**
   * Perform any dependency checks required prior to a write (add/edit) operation.
   * - postcondition: Session flash message updated (HTML) or HTTP status returned (REST)
   *
   * @since  COmanage Registry v0.1
   * @param  Array Request data
   * @param  Array Current data
   * @return boolean true if dependency checks succeed, false otherwise.
   */
  
  function checkWriteDependencies($reqdata, $curdata = null) {
    if(isset($curdata)) {
      // Changes to COmanage CO are not permitted
      
      if($curdata['Co']['name'] == "COmanage") {
        if($this->request->is('restful')) {
          $this->Api->restResultHeader(403, "Cannot Edit COmanage CO");
        } else {
          $this->Flash->set(_txt('er.co.cm.edit'), array('key' => 'error'));
        }
        
        return false;
      }
    }
    
    if(!isset($curdata)
       || ($curdata['Co']['name'] != $reqdata['Co']['name'])) {
      // Make sure name doesn't exist
      $x = $this->Co->findByName($reqdata['Co']['name']);
      
      if(!empty($x)) {
        if($this->request->is('restful')) {
          $this->Api->restResultHeader(403, "Name In Use");
        } else {
          $this->Flash->set(_txt('er.co.exists', array($reqdata['Co']['name'])), array('key' => 'error')); 
        }
        
        return false;
      }
    }
    
    return true;
  }
   
  /**
   * Perform any followups following a write operation.  Note that if this
   * method fails, it must return a warning or REST response, but that the
   * overall transaction is still considered a success (add/edit is not
   * rolled back).
   * - postcondition: Session flash message updated (HTML) or HTTP status returned (REST)
   *
   * @since  COmanage Registry v0.1
   * @param  Array Request data
   * @param  Array Current data
   * @param  Array Original request data (unmodified by callbacks)
   * @return boolean true if dependency checks succeed, false otherwise.
   */
  
  function checkWriteFollowups($reqdata, $curdata = null, $origdata = null) {
    // Create an admin and member Group for the new CO. As of now, we don't try to populate
    // them with the current user, since it may not be desirable for the current
    // user (say, the CMP admin) to be a member of the new CO. See also CO-84.
    
    // Only do this via HTTP.
    
    if(!$this->request->is('restful') && $this->action == 'add')
    {
      if(isset($this->Co->id))
      {
        $a['CoGroup'] = array(
          'co_id' => $this->Co->id,
          'name' => 'admin',
          'description' => _txt('fd.group.desc.adm', array($reqdata['Co']['name'])),
          'open' => false,
          'status' => 'A'
        );
        
        $admin_create = $this->Co->CoGroup->save($a);
        
        $this->Co->CoGroup->clear();
        
        $a['CoGroup'] = array(
          'co_id' => $this->Co->id,
          'name' => 'members',
          'description' => _txt('fd.group.desc.mem', array($reqdata['Co']['name'])),
          'open' => false,
          'status' => 'A'
        );
        
        $members_create = $this->Co->CoGroup->save($a);
        
        if(!$admin_create and !$members_create) {
          $this->Flash->set(_txt('er.co.gr.adminmembers'), array('key' => 'information'));
          return(false);
        } elseif (!$admin_create) {
          $this->Flash->set(_txt('er.co.gr.admin'), array('key' => 'information'));
          return(false);
        } elseif(!$members_create) {
          $this->Flash->set(_txt('er.co.gr.members'), array('key' => 'information'));
          return(false);
        }
      }
    }

    return(true);
  }

  /**
   * Authorization for this Controller, called by Auth component
   * - precondition: Session.Auth holds data used for authz decisions
   * - postcondition: $permissions set with calculated permissions
   *
   * @since  COmanage Registry v0.1
   * @return Array Permissions
   */
  
  function isAuthorized() {
    $roles = $this->Role->calculateCMRoles();
    
    // Construct the permission set for this user, which will also be passed to the view.
    $p = array();
    
    // Determine what operations this user can perform
    
    // Add a new CO?
    $p['add'] = $roles['cmadmin'];
    
    // Delete an existing CO?
    $p['delete'] = $roles['cmadmin'];
    
    // Edit an existing CO?
    $p['edit'] = $roles['cmadmin'];
    
    // View all existing COs?
    $p['index'] = $roles['cmadmin'];
    
    // View an existing CO?
    $p['view'] = $roles['cmadmin'];
    
    $this->set('permissions', $p);
    return $p[$this->action];
  }
  
  /**
   * Select the CO for the current session.
   * - precondition: $this->request->data holds CO to select (optional)
   * - precondition: Session.Auth holds data used for authz decisions
   * - postcondition: If no CO is selected and no COs exist, the 'COmanage' CO is created and a redirect issued
   * - postcondition: If no CO is selected and the user is a member of exactly one CO, that CO is selected and a redirect issued
   * - postcondition: If no CO is selected and the user is a member of more than one CO, $cos is set and the view rendered
   *
   * @since  COmanage Registry v0.1
   */
  
  function select() {
    if(empty($this->request->data)) {
      // Set page title
      $this->set('title_for_layout', _txt('op.select-a', array(_txt('ct.cos.1'))));

      if($this->Session->check('Auth.User.cos')) {
        // Retrieve the list of the user's COs, but for admins we want all COs
        
        if(isset($this->viewVars['permissions']['select-all']) && $this->viewVars['permissions']['select-all'])
          $ucos = $this->Co->find('all');
        else {
          // Grab the COs from the session. We can't just use the session variable
          // because it's not a complete retrieval of CO data.
          
          $cos = $this->Session->read('Auth.User.cos');
          $coIds = array();
          
          foreach($cos as $co) {
            $coIds[] = $co['co_id'];
          }
          
          $args['conditions']['id'] = $coIds;
          $ucos = $this->Co->find('all', $args);
        }
        
        if(count($ucos) == 0) {
          // No memberships... could be because there are no COs
          
          $cos = $this->Co->find('all');
          
          if(count($cos) == 0) {
            $this->Flash->set(_txt('er.co.none'), array('key' => 'error'));
            $this->redirect(array('controller' => 'pages', 'action' => 'menu'));
          } else {
            $this->Flash->set(_txt('co.nomember'), array('key' => 'error'));
            $this->redirect(array('controller' => 'pages', 'action' => 'menu'));
          }
        }
        elseif(count($ucos) == 1) {
          // Exactly one CO found

          $r = array('controller' => $this->Session->read('co-select.controller'),
                     'action' => $this->Session->read('co-select.action'),
                     'co' => $ucos[0]['Co']['id']);
          
          if($this->Session->check('co-select.args'))
            $this->redirect(array_merge($r, $this->Session->read('co-select.args')));
          else
            $this->redirect($r);
        } else {
          // Multiple COs found
          
          $this->set('cos', $ucos);
        }
      }
    } else {
      // Return from form to select CO

      $r = array('controller' => $this->Session->read('co-select.controller'),
                 'action' => $this->Session->read('co-select.action'),
                 'co' => $this->data['Co']['co']);

      $this->redirect(array_merge($r, $this->Session->read('co-select.args')));
    }
  }
}
