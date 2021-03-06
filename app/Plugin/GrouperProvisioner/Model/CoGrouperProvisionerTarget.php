<?php
/**
 * COmanage Registry CO Grouper Provisioner Target Model
 *
 * Copyright (C) 2012-16 University Corporation for Advanced Internet Development, Inc.
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
 * @copyright     Copyright (C) 2012-16 University Corporation for Advanced Internet Development, Inc.
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry-plugin
 * @since         COmanage Registry v0.8.3
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version       $Id$
 */

App::uses("CoProvisionerPluginTarget", "Model", "ConnectionManager");
App::uses('GrouperRestClient', 'GrouperProvisioner.Lib');
App::uses('GrouperRestClientException', 'GrouperProvisioner.Lib');
App::uses('GrouperCouProvisioningStyle', 'GrouperProvisioner.Lib');

class CoGrouperProvisionerTarget extends CoProvisionerPluginTarget {

  // Define class name for cake
  public $name = "CoGrouperProvisionerTarget";
  
  // Add behaviors
  public $actsAs = array('Containable');
  
  // Association rules from this model to other models
  public $belongsTo = array("CoProvisioningTarget");
  
  public $hasMany = array(
      "CoGrouperProvisionerGroup" => array(
      'className' => 'GrouperProvisioner.CoGrouperProvisionerGroup',
      'dependent' => true
    ),
  );
  
  // Default display field for cake generated views
  public $displayField = "serverurl";
  
  // Validation rules for table elements
  public $validate = array(
    'co_provisioning_target_id' => array(
      'rule' => 'numeric',
      'required' => true,
      'allowEmpty' => false,
      'on' => null,
      'message' => 'A CO Provisioning Target ID must be provided'
    ),
    'serverurl' => array(
      'rule' => array('custom', '/^https?:\/\/.*/'),
      'required' => true,
      'allowEmpty' => false,
      'on' => null,
      'message' => 'Please enter a valid http or https URL'
    ),
    'contextpath' => array(
      'rule' => array('custom', '/^\/.*/'),
      'required' => true,
      'allowEmpty' => false,
      'on' => null,
      'message' => 'Please enter a valid context path'
    ),
    'login' => array(
      'rule' => 'notBlank',
      'required' => true,
      'on' => null,
      'allowEmpty' => false,
    ),
    'password' => array(
      'rule' => 'notBlank',
      'required' => true,
      'on' => null,
      'allowEmpty' => false,
    ),
    'stem' => array(
      'rule' => 'notBlank',
      'required' => true,
      'allowEmpty' => false,
      'on' => null,
    ),
    'subject_identifier' => array(
      'rule' => 'notBlank',
      'required' => false,
      'allowEmpty' => true,
      'on' => null,
    ),
    'login_identifier' => array(
      'rule' => 'notBlank',
      'required' => true,
      'allowEmpty' => false,
      'on' => null,
    ),
    'email_identifier' => array(
      'rule' => 'notBlank',
      'required' => true,
      'allowEmpty' => false,
      'on' => null,
    ),
    'subject_view' => array(
      'subjectViewRule1' => array(
      'rule' => array('maxLength', 30),
      'required' => false,
      'allowEmpty' => true,
      'on' => null,
    ),
    'subjectViewRule2' => array(
      'rule' => 'isUnique',
      'message' => 'The view name must be unique'
      )
    )
  );
  
  /**
   * Called after each successful save operation. Right now used
   * to create the view(s) for Grouper subjects.
   * 
   * @since COmanage Registry v0.9.3
   * @param bool $created True if this save created a new record
   * @param array $options Options passed from Model::save().
   * @return void
   */
  public function afterSave($created, $options = array()) {
    // Only create the view in legacy mode
    if (!$this->data['CoGrouperProvisionerTarget']['legacy_comanage_subject']) {
      return;
    }

    $prefix = "";
    $db =& ConnectionManager::getDataSource('default');
    $db_driver = split("/", $db->config['datasource'], 2);
      
    if(isset($db->config['prefix'])) {
      $prefix = $db->config['prefix'];
    }        
    
    $view = $this->data['CoGrouperProvisionerTarget']['subject_view'];
    
    $args = array();
    $args['conditions']['CoProvisioningTarget.id'] = $this->data['CoGrouperProvisionerTarget']['co_provisioning_target_id'];
    $args['contain'] = false;
    $target = $this->CoProvisioningTarget->find('first', $args);
    $coId = $target['CoProvisioningTarget']['co_id'];
    
    $sqlTemplate = "
CREATE OR REPLACE VIEW $view (id, name, lfname, description, description_lower, loginid, email) AS
SELECT
  CONCAT('COMANAGE_', '$coId', '_', CAST(cm_co_people.id AS @CAST_TYPE@)),
  CONCAT(COALESCE(cm_names.given,''), ' ', COALESCE(cm_names.family,'')),
  CONCAT(COALESCE(cm_names.family,''), ' ', COALESCE(cm_names.given,'')),
  CONCAT(COALESCE(cm_names.given,''), ' ', COALESCE(cm_names.family,''), ' (', cm_cos.name, ')'),
  LOWER(CONCAT(cm_names.given, ' ', cm_names.family, ' ', COALESCE(cm_email_addresses.mail, ''), ' ', COALESCE(cm_identifiers.identifier,''))),
  cm_identifiers.identifier,
  cm_email_addresses.mail
FROM
  cm_co_people
  LEFT JOIN cm_names ON cm_co_people.id = cm_names.co_person_id AND cm_names.primary_name IS TRUE AND cm_names.name_id IS NULL AND cm_names.deleted IS FALSE
  JOIN cm_cos ON cm_co_people.co_id = cm_cos.id AND cm_cos.id = $coId
  LEFT JOIN cm_identifiers ON cm_co_people.id = cm_identifiers.co_person_id AND cm_identifiers.type = '@IDENTIFIER_TYPE@' AND cm_identifiers.identifier_id IS NULL AND cm_identifiers.deleted IS FALSE AND cm_identifiers.status = 'A'
  LEFT JOIN cm_email_addresses ON cm_co_people.id = cm_email_addresses.co_person_id AND cm_email_addresses.type = '@EMAIL_TYPE@' AND cm_email_addresses.email_address_id IS NULL AND cm_email_addresses.deleted IS FALSE
  WHERE cm_co_people.status = 'A' AND cm_co_people.co_person_id IS NULL AND cm_co_people.deleted IS FALSE
  ";                  
        
    $replacements = array();
    $replacements['cm_'] = $prefix;
    $replacements['@IDENTIFIER_TYPE@'] = $this->data['CoGrouperProvisionerTarget']['login_identifier'];
    $replacements['@EMAIL_TYPE@'] = $this->data['CoGrouperProvisionerTarget']['email_identifier'];  
    
    switch ($db_driver[1]) {
      case 'Mysql':
        $replacements['@CAST_TYPE@'] = 'char';
        break;
      case 'Postgres':
        $replacements['@CAST_TYPE@'] = 'text';
        break;
    }
    
    $sql = strtr($sqlTemplate, $replacements);       
            
    $result = $this->query($sql);
    
  }

  /**
   * Compute the subject used by Grouper for the CoPerson.
   *
   * @since COmanage Registry v1.0.5
   * @param  Array CO Provisioning Target data
   * @param  ProvisioningActionEnum Registry transaction type triggering provisioning
   * @param  Array Provisioning data
   * @param  Integer CoPersonId for the group membership
   * @return String
  */
  public function computeSubject($coProvisioningTargetData, $op, $provisioningData, $coPersonId) {
    // For legacy use of SQL view as the Grouper subject source the subject
    // is a combination of the CO ID and the CoPerson ID prefixed with 'COMANAGE'.
    if ($coProvisioningTargetData['CoGrouperProvisionerTarget']['legacy_comanage_subject']) {
      $coId = $provisioningData['CoGroup']['co_id'];
      return 'COMANAGE_' . $coId . '_' . $coPersonId;
    }

    // Select the identifier from the CoPerson based on the provisioner configuration
    $idType = $coProvisioningTargetData['CoGrouperProvisionerTarget']['subject_identifier'];

    switch($op) { 
      case ProvisioningActionEnum::CoPersonPetitionProvisioned:
      case ProvisioningActionEnum::CoPersonPipelineProvisioned:
        if (array_key_exists('Identifier', $provisioningData)) {
          $identifiers = $provisioningData['Identifier'];
          foreach ($identifiers as $identifier) {
            if ($identifier['type'] == $idType && 
                $identifier['status'] == SuspendableStatusEnum::Active && 
                !$identifier['deleted']) {
                return $identifier['identifier'];
            }
          }
        }

        break;

      case ProvisioningActionEnum::CoGroupUpdated:
        if (isset($provisioningData['CoGroup']['CoPerson'])) {
          $coPersonId = $provisioningData['CoGroup']['CoPerson']['id'];
          $args = array();
          $args['conditions']['Identifier.co_person_id'] = $coPersonId;
          $args['conditions']['Identifier.type'] = $idType;
          $args['conditions']['Identifier.status'] = SuspendableStatusEnum::Active;
          $args['conditions']['Identifier.deleted'] = false;
          $args['contain'] = false;

          $identifier = $this->CoProvisioningTarget->Co->CoPerson->Identifier->find('first', $args);
          if ($identifier) {
            return $identifier['Identifier']['identifier'];
          }
        }
        break;

      case ProvisioningActionEnum::CoPersonDeleted:
        if (array_key_exists('Identifier', $provisioningData)) {
          $identifiers = $provisioningData['Identifier'];
          foreach ($identifiers as $identifier) {
            if ($identifier['type'] == $idType && $identifier['status'] == SuspendableStatusEnum::Active) {
                return $identifier['identifier'];
            }
          }
        }
        break;

      case ProvisioningActionEnum::CoPersonUpdated:
        if (array_key_exists('Identifier', $provisioningData)) {
          $identifiers = $provisioningData['Identifier'];
          foreach ($identifiers as $identifier) {
            if ($identifier['type'] == $idType && 
                $identifier['status'] == SuspendableStatusEnum::Active && 
                !$identifier['deleted']) {
                return $identifier['identifier'];
            }
          }
        }
        break;
    }

    // If we fall through we were not able to find an identifier so return null
    // and expect the caller to handle appropriately.
    return null;
  }

  /**
   * Determine the provisioning status of this target.
   *
   * @since  COmanage Registry v0.8.3
   * @param  Integer CO Provisioning Target ID
   * @param  Integer CO Person ID (null if CO Group ID is specified)
   * @param  Integer CO Group ID (null if CO Person ID is specified)
   * @return Array ProvisioningStatusEnum, Timestamp of last update in epoch seconds, Comment
   * @throws RuntimeException 
   */
  
  public function status($coProvisioningTargetId, $coPersonId, $coGroupId=null) {
    $ret = array(
      'status'    => ProvisioningStatusEnum::Unknown,
      'timestamp' => null,
      'comment'   => ""
    );

    if(!empty($coPersonId)) {
      // For CO people we just return unknown.
      $ret['comment'] = 'see status for individual groups';
      return $ret;
    }

    $args = array();
    $args['conditions']['CoGrouperProvisionerTarget.co_provisioning_target_id'] = $coProvisioningTargetId;
    $args['conditions']['CoGrouperProvisionerGroup.co_group_id'] = $coGroupId;

    $group = $this->CoGrouperProvisionerGroup->find('first', $args);

    if(!empty($group)) {
      $ret['status'] = ProvisioningStatusEnum::Provisioned;
      $ret['timestamp'] = $group['CoGrouperProvisionerGroup']['modified'];
    }

   return $ret;
  }
  
  /**
   * Provision for the specified CO Person or CO Group.
   *
   * @since  COmanage Registry v0.8
   * @param  Array CO Provisioning Target data
   * @param  ProvisioningActionEnum Registry transaction type triggering provisioning
   * @param  Array Provisioning data, populated with ['CoPerson'] or ['CoGroup']
   * @return Boolean True on success
   * @throws InvalidArgumentException If $coPersonId not found
   * @throws RuntimeException For other errors
   */

  public function provision($coProvisioningTargetData, $op, $provisioningData) {
    $serverUrl = $coProvisioningTargetData['CoGrouperProvisionerTarget']['serverurl'];
    $contextPath = $coProvisioningTargetData['CoGrouperProvisionerTarget']['contextpath'];
    $login = $coProvisioningTargetData['CoGrouperProvisionerTarget']['login'];
    $password = $coProvisioningTargetData['CoGrouperProvisionerTarget']['password'];

    switch($op) {
      case ProvisioningActionEnum::CoPersonDeleted:
      case ProvisioningActionEnum::CoPersonUpdated:
      
        // We only process CoPersonUpdated provisioning actions when the status
        // on the CoPerson is deleted to signal an expunge action taking place.
        // We need to find the necessary identifier to be used as the Grouper subject
        // and remove memberships before the identifier is deleted as part of the expunge
        // process.
        if (($op == ProvisioningActionEnum::CoPersonUpdated) && array_key_exists('CoPerson', $provisioningData)) {
          if ($provisioningData['CoPerson']['status'] != StatusEnum::Deleted) {
            break;
          }
        }

        // This is a CoPersonUpdated provisioning action with a deleted CoPerson.
        $coPersonId = $provisioningData['CoPerson']['id'];

        // Find the Grouper subject.
        $subject = $this->computeSubject($coProvisioningTargetData, $op, $provisioningData, $coPersonId);
        if (!isset($subject)) {
          $this->log("GrouperProvisioner is unable to compute the Grouper subject for coPersonId = $coPersonId");
          break;
        }

        $memberships = array();

        if ($op == ProvisioningActionEnum::CoPersonDeleted) {
          foreach ($provisioningData['CoGroupMember'] as $m) {
            $memberships[] = array('CoGroupMember' => $m);
          }
          
        } elseif ($op == ProvisioningActionEnum::CoPersonUpdated) {

          // Find all group memberships for the CoPerson.
          $args = array();
          $args['conditions']['CoGroupMember.co_person_id'] = $coPersonId;
          $args['conditions']['CoGroupMember.member'] = true;
          $args['conditions']['CoGroupMember.deleted'] = false;
          $args['contain'] = false;

          $memberships = $this->CoProvisioningTarget->Co->CoPerson->CoGroupMember->find('all', $args);
        }

        try {
          $grouper = new GrouperRestClient($serverUrl, $contextPath, $login, $password);

          foreach ($memberships as $m) {
            // Find the corresponding CoGroup
            $args = array();
            $args['conditions']['CoGroup.id'] = $m['CoGroupMember']['co_group_id'];
            $args['contain'] = false;

            $group = $this->CoProvisioningTarget->Co->CoGroup->find('first', $args);
            if (!$group) {
              continue;
            }

            // Map from CoGroup to the Grouper group details.
            $provisionerGroup = $this->CoGrouperProvisionerGroup->findProvisionerGroup($coProvisioningTargetData, $group);
            $groupName = $this->CoGrouperProvisionerGroup->getGroupName($provisionerGroup);

            // Ask Grouper to delete the membership.
            $grouper->deleteManyMember($groupName, array($subject));

            // Update provisioner group table to record new modified time.
            $this->CoGrouperProvisionerGroup->updateProvisionerGroup($provisionerGroup, $provisionerGroup);     
          }

        } catch (GrouperRestClientException $e) {
          throw new RuntimeException($e->getMessage());
        }

        break;

      case ProvisioningActionEnum::CoGroupAdded:

        $provisionerGroup = $this->CoGrouperProvisionerGroup->addProvisionerGroup($coProvisioningTargetData, $provisioningData);
        $groupName = $this->CoGrouperProvisionerGroup->getGroupName($provisionerGroup);
        $groupDescription = $this->CoGrouperProvisionerGroup->getGroupDescription($provisionerGroup);
        $groupDisplayExtension = $this->CoGrouperProvisionerGroup->getGroupDisplayExtension($provisionerGroup);
        
        try {
          $grouper = new GrouperRestClient($serverUrl, $contextPath, $login, $password);
          
          // Loop over stems since we may need to create a COU hierarchy. Begin by
          // taking full group name and cutting off the group, and then the
          // first stem since that is the CO base stem which will already exist.
          
          $stemComponents = explode(':', $groupName, -1);
          $baseStem = array_shift($stemComponents);
          
          $stem = $baseStem;
          foreach($stemComponents as $component) {
            $stem = $stem . ':' . $component;
            $exists = $grouper->stemExists($stem);
            if(!$exists) {
              $grouper->stemSave($stem, '', '');
            }
          }
          
          // All stems exists so now save the group.
          $grouper->groupSave($groupName, $groupDescription, $groupDisplayExtension, 'group');
        } catch (GrouperRestClientException $e) {
          throw new RuntimeException($e->getMessage());
        }

        break;

      case ProvisioningActionEnum::CoGroupDeleted:
        $provisionerGroup = $this->CoGrouperProvisionerGroup->findProvisionerGroup($coProvisioningTargetData, $provisioningData);
        $groupName = $this->CoGrouperProvisionerGroup->getGroupName($provisionerGroup);

        try {
          $grouper = new GrouperRestClient($serverUrl, $contextPath, $login, $password);
          $grouper->groupDelete($groupName);
          
          // If this was a COU members or admin group we need to delete the stem in which
          // the group lived since that stem represented the COU and the only way
          // a COU members or admin group is deleted is if the COU is deleted.
          // We cannot, however, delete a stem until it has no child groups and
          // we don't know which of the admin or the members group will be deleted
          // first. Grouper has no easy way to query a stem and decide if it has
          // child groups, so for now just try to delete stem and walk over a 
          // failed delete call.
          if($this->CoGrouperProvisionerGroup->CoGroup->isCouAdminOrMembersGroup($provisioningData)) {
            $stem = $this->CoGrouperProvisionerGroup->getStem($provisionerGroup);
            try {
              $grouper->stemDelete($stem);
            } catch (GrouperRestClientException $e) {
            }
          }
        } catch (GrouperRestClientException $e) {
          throw new RuntimeException($e->getMessage());
        }

        $this->CoGrouperProvisionerGroup->delProvisionerGroup($provisionerGroup);

        break;

      case ProvisioningActionEnum::CoGroupReprovisionRequested:
        $provisionerGroup = $this->CoGrouperProvisionerGroup->findProvisionerGroup($coProvisioningTargetData, $provisioningData);
        $groupName = $this->CoGrouperProvisionerGroup->getGroupName($provisionerGroup);

        // We reprovision in three steps: 
        // (1) Call ourselves recursively with a CoGroupUpdated operation since
        //     that branch of the switch statement has the logic to check for
        //     existence of stems and groups and create any if necessary.
        // (2) Use a transaction and a SELECT FOR UPDATE statement 
        //     with offset and limit to loop over all identifiers
        //     for all members of the group and then ask Grouper
        //     to add those members to the group.
        // (3) Query Grouper for identifiers of all members in
        //     its group instance and query to find any that are not supposed
        //     to be in the group and then ask Grouper to delete those
        //     from its instance of the group.

        // Begin by calling ourselves recursively with a CoGroupUpdated operation.
        $ret = $this->provision($coProvisioningTargetData, ProvisioningActionEnum::CoGroupUpdated, $provisioningData);
        if (!$ret) {
          $this->log("Recursive call of provision by GrouperProvisioner with operation CoGroupUpdated failed");
          return $ret;
        }

        $grouper = new GrouperRestClient($serverUrl, $contextPath, $login, $password);

        // Next find the identifiers for all members of the group.
        
        // Get a handle to the database interface.
        $dbc = $this->getDataSource();

        // Begin a transaction.
        $dbc->begin();

        $args = array();

        // We use Identifier as the primary table.
        $args['table'] = $dbc->fullTableName($this->CoProvisioningTarget->Co->CoPerson->Identifier);
        $args['alias'] = $this->CoProvisioningTarget->Co->CoPerson->Identifier->alias;

        // We join across CoGroupMember and CoPeople.
        $args['joins'][0]['table']         = 'co_group_members';
        $args['joins'][0]['alias']         = 'CoGroupMember';
        $args['joins'][0]['type']          = 'INNER';
        $args['joins'][0]['conditions'][0] = 'Identifier.co_person_id=CoGroupMember.co_person_id';

        $args['joins'][1]['table']         = 'co_people';
        $args['joins'][1]['alias']         = 'CoPerson';
        $args['joins'][1]['type']          = 'INNER';
        $args['joins'][1]['conditions'][0] = 'Identifier.co_person_id=CoPerson.id';

        // We only want identifiers used as the Grouper subject source.
        $args['conditions']['Identifier.type'] = $coProvisioningTargetData['CoGrouperProvisionerTarget']['subject_identifier'];

        // We only want active identifiers.
        $args['conditions']['Identifier.status'] = StatusEnum::Active;

        // We only want memberships in the group being reprovisioned.
        $args['conditions']['CoGroupMember.co_group_id'] = $provisioningData['CoGroup']['id'];

        // We only want people from the corresponding CO.
        $args['conditions']['CoPerson.co_id'] = $provisioningData['CoGroup']['co_id'];

        // We only want CoPeople in the active or approved status.
        $args['conditions']['CoPerson.status'][0] = StatusEnum::Active;
        $args['conditions']['CoPerson.status'][1] = StatusEnum::Approved;

        // Contain the query since we only want the identifiers.
        $args['contain'] = false;

        // We only need to return the identifier itself.
        $args['fields'] = $dbc->fields($this->CoProvisioningTarget->Co->CoPerson->Identifier, null, array('Identifier.identifier'));

        // Order by the identifier.
        $args['order'] = 'Identifier.identifier';

        // Start at the beginning and only consider 100 at a time in order to
        // help with memory scaling.
        $args['limit']  = 100;
        $offset = 0;

        $done = false;

        while (!$done) {
          $args['offset'] = $offset;

          // Appending to the generated query should be fairly portable.
          // We use buildQuery to ensure callbacks (such as ChangelogBehavior) are
          // invoked, then buildStatement to turn it into SQL.

          $sql = $dbc->buildStatement(
                    $this->CoProvisioningTarget->Co->CoPerson->Identifier->buildQuery('all', $args), 
                    $this->CoProvisioningTarget->Co->CoPerson->Identifier);

          $sqlForUpdate = $sql . " FOR UPDATE";

          $identifiers = $dbc->fetchAll($sqlForUpdate, array(), array('cache' => false));

          if ($identifiers) {
            $subjects = array();
            foreach ($identifiers as $i) {
              $subjects[] = $i['Identifier']['identifier'];
            }
            try {
                $grouper->addManyMember($groupName, $subjects);
            } catch (GrouperRestClientException $e) {
              // Log the exception but continue with the next set.
              $this->log("GrouperRestClientException: " . $e->getMessage());
            }

          } else {
            $done = true;
          }

          $offset = $offset + 100;
        }

        // End the transaction to release the read lock held by SELECT FOR UPDATE.
        $dbc->commit();

        // Next query Grouper using pagination to find the identifiers of all the
        // subjects it thinks are members of the group and test against the
        // database to find any that should be deleted.

        $done = false;

        // Request no more than 100 members of a Grouper group.
        $pageSize = 100;

        // Start with the first page of members.
        $pageNumber = 1;

        // Order by the subject Id which will be the configured identifier.
        $sortString = 'subjectId';

        // Order in ascending order.
        $ascending = 'T';

        while (!$done) {
          $memberIdentifiers = array();

          // Query Grouper for a page of members.
          try {
            $ret = $grouper->getMembersManyGroups(array($groupName), $pageSize, $pageNumber, $sortString, $ascending);
          } catch (GrouperRestClientException $e) {
            // Log the exception but continue gracefully.
            $this->log("GrouperRestClientException: " . $e->getMessage());
            $done = true;
            continue;
          }

          if ($ret) {
            if (array_key_exists($groupName, $ret)) {
              $memberIdentifiers = $ret[$groupName];
            }
          }

          if (!$memberIdentifiers) {
            // No more members returned so we are done paging.
            $done = true;
            continue;
          } else {
            // Increment page number for the next query.
            $pageNumber = $pageNumber + 1;
          }

          $coGroupId = $provisioningData['CoGroup']['id'];
          $identifierType = $coProvisioningTargetData['CoGrouperProvisionerTarget']['subject_identifier'];

          $args = array();

          // LEFT join the CoGroupMember table so we can find identifiers of people not in the group.
          $args['joins'][0]['table'] = 'co_group_members';
          $args['joins'][0]['alias'] = 'CoGroupMember';
          $args['joins'][0]['type'] = 'LEFT';
          $args['joins'][0]['conditions'][0] = 'Identifier.co_person_id=CoGroupMember.co_person_id';
          $args['joins'][0]['conditions'][1] = "CoGroupMember.co_group_id=$coGroupId";

          // INNER join the CoPerson table so we only select identifiers for people in our CO.
          $args['joins'][1]['table'] = 'co_people';
          $args['joins'][1]['alias'] = 'CoPerson';
          $args['joins'][1]['type'] = 'INNER';
          $args['joins'][1]['conditions'][0] = 'Identifier.co_person_id=CoPerson.id';

          // Only consider the identifier we are using as the Grouper subject.
          $args['conditions']['Identifier.type'] = $coProvisioningTargetData['CoGrouperProvisionerTarget']['subject_identifier'];

          // Select identifiers of people not in the group by having the CoGroupMember id be null.
          $args['conditions']['CoGroupMember.id'] = null;

          // Only consider CoPeople in our CO.
          $args['conditions']['CoPerson.co_id'] = $provisioningData['CoGroup']['co_id'];

          // Only consider active or approved people.
          $args['conditions']['OR'][0]['CoPerson.status'] = StatusEnum::Active;
          $args['conditions']['OR'][1]['CoPerson.status'] = StatusEnum::Approved;

          // Consider only the group of identifiers Grouper says are in the group.
          foreach ($memberIdentifiers as $identifier) {
            $args['conditions']['Identifier.identifier'][] = $identifier;
          }

          // We only need to return the identifier itself.
          $args['fields'] = $dbc->fields($this->CoProvisioningTarget->Co->CoPerson->Identifier, null, array('Identifier.identifier'));

          $args['contain'] = false;

          $identifiersToDelete = $this->CoProvisioningTarget->Co->CoPerson->Identifier->find('all', $args);

          if ($identifiersToDelete) {
            $subjects = array();
            foreach ($identifiersToDelete as $s) {
              $subjects[] = $s['Identifier']['identifier'];
            }
            try {
                $grouper->deleteManyMember($groupName, $subjects);
            } catch (GrouperRestClientException $e) {
              // Log the exception but continue with the next set.
              $this->log("GrouperRestClientException: " . $e->getMessage());
            }
          }
        }

        // Update provisioner group table to record new modified time.
        $this->CoGrouperProvisionerGroup->updateProvisionerGroup($provisionerGroup, $provisionerGroup);     
        break;

      case ProvisioningActionEnum::CoGroupUpdated:
        // Determine if any details about the group itself changed
        // and update as necessary.

        $currentProvisionerGroup = $this->CoGrouperProvisionerGroup->findProvisionerGroup($coProvisioningTargetData, $provisioningData);
        if(empty($currentProvisionerGroup)) {
          if($op != ProvisioningActionEnum::CoGroupReprovisionRequested) {
            // If we cannot find a provisioner group in our table for this 
            // group and this is not a reprovision then we are being called 
            // after a delete and should do no further processing.
            break;
          } else {
            $currentProvisionerGroup = $this->CoGrouperProvisionerGroup->emptyProvisionerGroup(); 
          }
        } 

        $newProvisionerGroup = $this->CoGrouperProvisionerGroup->computeProvisionerGroup($coProvisioningTargetData, $provisioningData);

        $groupUpdateNeeded = false;
        if($currentProvisionerGroup['CoGrouperProvisionerGroup']['stem'] != $newProvisionerGroup['CoGrouperProvisionerGroup']['stem']) {
          $groupUpdateNeeded = true;
        }
        if($currentProvisionerGroup['CoGrouperProvisionerGroup']['extension'] != $newProvisionerGroup['CoGrouperProvisionerGroup']['extension']) {
          $groupUpdateNeeded = true;
        }
        if($currentProvisionerGroup['CoGrouperProvisionerGroup']['description'] != $newProvisionerGroup['CoGrouperProvisionerGroup']['description']) {
          $groupUpdateNeeded = true;
        }
        
        // If either something about a group other than membership has changed
        // or if this is a reprovision update the table. We update the table
        // on a reprovision to change the modify timestamp.
        if($groupUpdateNeeded or ($op == ProvisioningActionEnum::CoGroupReprovisionRequested)) {
          $this->CoGrouperProvisionerGroup->updateProvisionerGroup($currentProvisionerGroup, $newProvisionerGroup);     
          // If this is a COU members group and its name changed because the COU name changed
          // and if the COU has children we need to change the stems for all of the children also
          // in the mapping table.
          if($this->CoGrouperProvisionerGroup->CoGroup->isCouAdminOrMembersGroup($provisioningData)) {
            if($newProvisionerGroup['CoGrouperProvisionerGroup']['extension'] != $currentProvisionerGroup['CoGrouperProvisionerGroup']['extension']) {
              // Find the COU for this COU admin or members group.
              $coId = $provisioningData['CoGroup']['co_id'];
              $args = array();
              $args['conditions']['Cou.co_id'] = $coId;
              $args['conditions']['Cou.name'] = $this->CoGrouperProvisionerGroup->CoGroup->couNameFromAdminOrMembersGroup($provisioningData);
              $args['contain'] = false;
              $cou = $this->CoProvisioningTarget->Co->Cou->find('first', $args);
                  
              // Find the children, if any of the COU.
              $allChildCous = $this->CoProvisioningTarget->Co->Cou->children($cou['Cou']['id']);
              foreach($allChildCous as $child) {
                $prefixes = array('admin:', 'members:');
                foreach($prefixes as $prefix) {
                  // Find the admin or members group for the COU.
                  $group = $this->CoProvisioningTarget->Co->CoGroup->findByName($coId, $prefix . $child['Cou']['name']);
                  // Find the provisioner group.
                  $args = array();
                  $args['conditions']['CoGrouperProvisionerGroup.co_group_id'] = $group['CoGroup']['id'];
                  $args['contain'] = false;
                  $current = $this->CoGrouperProvisionerGroup->find('first', $args);
                  if(!empty($current)) {
                    $new = $this->CoGrouperProvisionerGroup->computeProvisionerGroup($coProvisioningTargetData, $group);
                    $this->CoGrouperProvisionerGroup->updateProvisionerGroup($current, $new);
                  }
                }
              }
            }
          }
        }

        // If something other than membership has changed about the group than
        // tell Grouper to do the update.
        try {
          $grouper = new GrouperRestClient($serverUrl, $contextPath, $login, $password);

          $currentName = $this->CoGrouperProvisionerGroup->getGroupName($currentProvisionerGroup);
          $newName = $this->CoGrouperProvisionerGroup->getGroupName($newProvisionerGroup);
          $groupDescription = $this->CoGrouperProvisionerGroup->getGroupDescription($newProvisionerGroup);
          $groupDisplayExtension = $this->CoGrouperProvisionerGroup->getGroupDisplayExtension($newProvisionerGroup);

          // Test for existence here because it is mostly cheap and if
          // the group does not exist we can just create it. This makes
          // manual reprovision nicer.
          $exists = $grouper->groupExists($currentName);
          if($exists && $groupUpdateNeeded) {
            // Before updating the group see if any stem names might have changed 
            // due to this being a COU admin or members group and COU name change and change those.
            $currentStems = explode(':', $currentName, -1);
            $newStems = explode(':', $newName, -1);
            $stems = array_combine($currentStems, $newStems);
            $base = array_shift($stems);
            if(!empty($stems)) {
              $stem = $base;
              foreach($stems as $cur => $new) {
                    $old = $stem . ':' . $cur;
                    $stem = $stem . ':' . $new;
                  if($new != $cur) {
                    $grouper->stemUpdate($old, $stem, '', '');  
                  }
              }
            }
            
            // Now change the group itself.
            $grouper->groupUpdate($currentName, $newName, $groupDescription, $groupDisplayExtension);
          } elseif(!$exists) {
            // Loop over stems since we may need to create a COU hierarchy. Begin by
            // taking full group name and cutting off the group, and then the
            // first stem since that is the CO base stem which will already exist.
          
            $stemComponents = explode(':', $newName, -1);
            $baseStem = array_shift($stemComponents);
          
            $stem = $baseStem;
            foreach($stemComponents as $component) {
              $stem = $stem . ':' . $component;
              $exists = $grouper->stemExists($stem);
              if(!$exists) {
                $grouper->stemSave($stem, '', '');
              }
            }
            // All stems exists so now save the group.
            try {
              $grouper->groupSave($newName, $groupDescription, $groupDisplayExtension, 'group');
            } catch (GrouperRestClientException $e) {
               // Since it is possible for the group to exist outside of the control
               // of COmanage and then for COmanage to be asked to provision into
               // the group it is not an error here for the group to exist.
               if ($e->getMessage() != 'Group already existed') {
                 throw new RuntimeException($e->getMessage());
               }
            }
          }
        } catch (GrouperRestClientException $e) {
          throw new RuntimeException($e->getMessage());
        }

        // CoGroupMember is only present if a membership change has happened,
        // but we need to make sure that we are not being called because a CoPerson
        // has been deleted.
        if (array_key_exists('CoGroupMember', $provisioningData['CoGroup']) &&
              $provisioningData['CoGroup']['CoPerson']['status'] != StatusEnum::Deleted) {

          $coGroupMember = $provisioningData['CoGroup']['CoGroupMember'];
          $subject = $this->computeSubject($coProvisioningTargetData, $op, $provisioningData, $provisioningData['CoGroup']['CoPerson']['id']);

          if (empty($subject)) {
            $coPersonId = $provisioningData['CoGroup']['CoPerson']['id'];
            $this->log("GrouperProvisioner is unable to compute the Grouper subject for coPersonId = $coPersonId");
            break;
          }

          try {
            $grouper = new GrouperRestClient($serverUrl, $contextPath, $login, $password);
            $groupName = $this->CoGrouperProvisionerGroup->getGroupName($newProvisionerGroup);

            // If CoGroupMember is empty or the member flag is false then it is a membership delete, 
            // otherwise if the member flag is true it is a membership add.
            if (!$coGroupMember) {
              $grouper->deleteManyMember($groupName, array($subject));
            } elseif (!$coGroupMember[0]['member'] && ($coGroupMember[0]['co_group_id'] == $provisioningData['CoGroup']['id'])) {
              $grouper->deleteManyMember($groupName, array($subject));
            } elseif ($coGroupMember[0]['member'] && ($coGroupMember[0]['co_group_id'] == $provisioningData['CoGroup']['id'])) {
              $grouper->addManyMember($groupName, array($subject));
            } else {
              // This should not happen so log if we get here.
              $this->log("GrouperProvisioner is unable to determine membership status");
            }

            // Update provisioner group table to record new modified time.
            $this->CoGrouperProvisionerGroup->updateProvisionerGroup($currentProvisionerGroup, $newProvisionerGroup);     

          } catch (GrouperRestClientException $e) {
            throw new RuntimeException($e->getMessage());
          }

        }

        break;

      // A petition is finalized and so we need to provision any group
      // memberships for the new enrollee. We can assume that the groups
      // already have themselves been provisioned.
      case ProvisioningActionEnum::CoPersonPetitionProvisioned:
      case ProvisioningActionEnum::CoPersonPipelineProvisioned:
        if(isset($provisioningData['CoGroupMember'])) {
          foreach($provisioningData['CoGroupMember'] as $membership) {
            if($membership['member']) {
              $coId = $membership['CoGroup']['co_id'];
              $coPersonId = $membership['co_person_id'];
              $subject = $this->computeSubject($coProvisioningTargetData, $op, $provisioningData, $coPersonId);
              if (empty($subject)) {
                throw new RuntimeException(_txt('er.grouperprovisioner.subject'));
              }

              try {
                $grouper = new GrouperRestClient($serverUrl, $contextPath, $login, $password);
                $provisionerGroup = $this->CoGrouperProvisionerGroup->findProvisionerGroup($coProvisioningTargetData, $membership);
                $groupName = $this->CoGrouperProvisionerGroup->getGroupName($provisionerGroup);
                $grouper->addManyMember($groupName, array($subject));
              } catch (GrouperRestClientException $e) {
                throw new RuntimeException($e->getMessage());
              }
            }
          }
        }

        break;

      default:
        // This provisioner does not fire on any CoPerson related provisioning actions
        // since all changes to groups or group memberships are carried through
        // the group provisioning actions.
        break;
    }
    
    return true;
  }

  /**
   * Test a Grouper server to verify that the connection available is valid.
   *
   * @since  COmanage Registry v0.8.3
   * @param  String Server URL
   * @param  String Context path
   * @param  String Login
   * @param  String Password
   * @param  String Stem name
   * @return Boolean True if parameters are valid
   * @throws RuntimeException
   */
  
  public function verifyGrouperServer($serverUrl, $contextPath, $login, $password, $stemName) {

    // test server access and authentication
    try {
      $grouper = new GrouperRestClient($serverUrl, $contextPath, $login, $password);
      $exists = $grouper->stemExists($stemName);
    } catch (GrouperRestClientException $e) {
      throw new RuntimeException($e->getMessage());
    }

    // create stem if it does not exist
    if(!$exists) {
      try {
        $grouper->stemSave($stemName, "", ""); 
      } catch (GrouperRestClientException $e) {
        throw new RuntimeException($e->getMessage());
      }
    }
    
    return true;
  }
}
