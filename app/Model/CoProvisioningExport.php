<?php
/**
 * COmanage Registry CO Provisioning Export Model
 *
 * Copyright (C) 2013 University Corporation for Advanced Internet Development, Inc.
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
 * @copyright     Copyright (C) 2013 University Corporation for Advanced Internet Development, Inc.
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry-plugin
 * @since         COmanage Registry v0.8.2
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version       $Id$
 */

class CoProvisioningExport extends AppModel {
  // Define class name for cake
  public $name = "CoProvisioningExport";
  
  // Add behaviors
  public $actsAs = array('Containable');
  
  // Association rules from this model to other models
  public $belongsTo = array(
    "CoGroup",
    "CoPerson",
    "CoProvisioningTarget"
  );
  
  // Default display field for cake generated views
  public $displayField = "exporttime";
  
  // Validation rules for table elements
  public $validate = array(
    'co_provisioning_target_id' => array(
      'rule' => 'numeric',
      'required' => true,
      'message' => 'A CO Provisioning Target ID must be provided'
    ),
    'co_person_id' => array(
      'rule' => 'numeric',
      'required' => false,
      'allowEmpty' => true
    ),
    'co_group_id' => array(
      'rule' => 'numeric',
      'required' => false,
      'allowEmpty' => true
    ),
    'exporttime' => array(
      'rule' => 'notBlank'
    )
  );
  
  /**
   * Record that a given provisioning target was executed for a CO Person or CO Group.
   *
   * @since  COmanage Registry v0.8.2
   * @param  Integer CO Provisioning Target ID
   * @param  Integer CO Person ID (null if CO Group ID is specified)
   * @param  Integer CO Group ID (null if CO Person ID is specified)
   * @throws RuntimeException For other errors
   */
  
  public function record($coProvisioningTargetId, $coPersonId, $coGroupId) {
    $data = array();
    $data['CoProvisioningExport']['co_provisioning_target_id'] = $coProvisioningTargetId;
    $data['CoProvisioningExport']['co_person_id'] = $coPersonId;
    $data['CoProvisioningExport']['co_group_id'] = $coGroupId;
    $data['CoProvisioningExport']['exporttime'] = date('Y-m-d H:i:s');
    
    // See if we already have a row to update
    $args = array();
    $args['conditions']['CoProvisioningExport.co_provisioning_target_id'] = $coProvisioningTargetId;
    if($coPersonId) {
      $args['conditions']['CoProvisioningExport.co_person_id'] = $coPersonId;
    } else {
      $args['conditions']['CoProvisioningExport.co_group_id'] = $coGroupId;
    }
    $args['contain'] = false;
    $export = $this->find('first', $args);
    
    if(!empty($export)) {
      $data['CoProvisioningExport']['id'] = $export['CoProvisioningExport']['id'];
    }
    
    // Reset the model state in case we're called more than once
    $this->create($data);
    
    if(!$this->save($data)) {
      throw new RuntimeException(_txt('er.db.save'));
    }
  }
}
