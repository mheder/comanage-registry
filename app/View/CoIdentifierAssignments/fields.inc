<?php
/**
 * COmanage Registry CO Identifier Assignment Fields
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
 * @package       registry
 * @since         COmanage Registry v0.6
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version       $Id$
 */

  // Determine if fields are editable
  $e = false;
  
  if(($this->action == "add" && $permissions['add']) || ($this->action == "edit" && $permissions['edit']))
    $e = true;
    
  // We shouldn't get here if we don't have at least read permission, but check just in case
  
  if(!$e && !$permissions['view'])
    return(false);

  // Add breadcrumbs
  print $this->element("coCrumb");
  $args = array();
  $args['plugin'] = null;
  $args['controller'] = 'co_identifier_assignments';
  $args['action'] = 'index';
  $args['co'] = $cur_co['Co']['id'];
  $this->Html->addCrumb(_txt('ct.co_identifier_assignments.pl'), $args);
  $crumbTxt = _txt('op.' . $this->action . '-a', array(_txt('ct.co_identifier_assignments.1')));
  $this->Html->addCrumb($crumbTxt);
  
  print $this->Form->hidden('co_id', array('default' => $cur_co['Co']['id'])) . "\n";
?>
<script type="text/javascript">
  <!-- JS specific to these fields -->
  
  function fields_update_gadgets() {
    // Enable or disable gadgets according to current state
    
    document.getElementById('CoIdentifierAssignmentMaximum').disabled =
      (document.getElementById('CoIdentifierAssignmentAlgorithm').value != 'R');
    
    if(document.getElementById('CoIdentifierAssignmentIdentifierType').value != 'mail') {
      document.getElementById('CoIdentifierAssignmentEmailType').value = "";
      document.getElementById('CoIdentifierAssignmentEmailType').disabled = true;
    } else {
      document.getElementById('CoIdentifierAssignmentEmailType').disabled = false;
    }
  }
  
  function js_local_onload() {
    fields_update_gadgets();
  }
  
  function on_submit()
  {
    // There appears to be an issue with Cake blackholing the request if text
    // fields are disabled (but not, strangely, if checkboxes or selects are
    // disabled). Renable these fields (in case they were disabled) to allow
    // the form to submit. See CO-350.
    
    document.getElementById('CoIdentifierAssignmentMaximum').disabled = false;
    document.getElementById('CoIdentifierAssignmentMinimum').disabled = false;
    document.getElementById('CoIdentifierAssignmentEmailType').disabled = false;
  }
</script>
<table id="<?php print $this->action; ?>_co_identifier_assignment" class="ui-widget">
  <tbody>
    <tr class="line1">
      <td>
        <strong class="fieldTitle">
          <?php print ($e ? $this->Form->label('description', _txt('fd.desc')) : _txt('fd.desc')); ?>
        </strong><br />
      </td>
      <td>
        <?php print ($e
                     ? $this->Form->input('description', array('class' => 'focusFirst'))
                     : filter_var($co_identifier_assignments[0]['CoIdentifierAssignment']['description'],FILTER_SANITIZE_SPECIAL_CHARS)); ?>
      </td>
    </tr>
    <tr class="line2">
      <td>
        <strong class="fieldTitle">
          <?php print ($e ? $this->Form->label('identifier_type', _txt('fd.type')) :  _txt('fd.type')); ?>
        </strong>
      </td>
      <td>
        <?php
          $attrs = array();
          $attrs['value'] = (isset($co_identifier_assignments) ? $co_identifier_assignments[0]['CoIdentifierAssignment']['identifier_type'] : "");
          $attrs['empty'] = false;
          $attrs['onChange'] = 'fields_update_gadgets()';
          
          if($e) {
            print $this->Form->select('identifier_type',
                                      $identifier_types,
                                      $attrs);
            
            if($this->Form->isFieldError('identifier_type')) {
              print $this->Form->error('identifier_type');
            }
          } else {
            print filter_var($co_identifier_assignments[0]['CoIdentifierAssignment']['identifier_type'],FILTER_SANITIZE_SPECIAL_CHARS);
          }
        ?>
      </td>
    </tr>
    <tr class="line1">
      <td>
        <strong class="fieldTitle">
          <?php print ($e ? $this->Form->label('email_type', _txt('fd.ia.type.email')) : _txt('fd.ia.type.email')); ?>
        </strong><br />
        <span class="descr"><?php print _txt('fd.ia.type.email.desc'); ?></span>
      </td>
      <td>
        <?php
          global $cm_lang, $cm_texts;
          $attrs['value'] = (isset($co_identifier_assignments)
                             ? $co_identifier_assignments[0]['CoIdentifierAssignment']['email_type']
                             : "");
          $attrs['empty'] = true;
          
          if($e) {
            // XXX There is some inconsistency here and in EmailAddress model/view as to which email types are actually permitted.
            // Probably the best solution is to just rewrite emailaddress to support extended types. See also CO-106.
            print $this->Form->select('email_type',
                                      $cm_texts[ $cm_lang ]['en.contact'],
                                      $attrs);
            
            if($this->Form->isFieldError('email_type')) {
              print $this->Form->error('email_type');
            }
          } else {
            print _txt('en.cpntact', null, $co_identifier_assignments[0]['CoIdentifierAssignment']['email_type']);
          }
        ?>
      </td>
    </tr>
    <tr class="line2">
      <td>
        <strong class="fieldTitle">
          <?php print _txt('fd.identifier.login'); ?>
        </strong><br />
        <span class="descr"><?php print _txt('fd.identifier.login.desc'); ?></span>
      </td>
      <td>
        <?php print ($e
                     ? $this->Form->input('login') . ' ' . $this->Form->label('login', _txt('fd.identifier.login'))
                     : filter_var($co_identifier_assignments[0]['CoIdentifierAssignment']['login'],FILTER_SANITIZE_SPECIAL_CHARS)); ?>
      </td>
    </tr>
    <tr class="line1">
      <td>
        <strong class="fieldTitle">
          <?php print ($e ? $this->Form->label('algorithm', _txt('fd.ia.algorithm')) : _txt('fd.ia.algorithm')); ?>
        </strong><br />
        <span class="descr"><?php print _txt('fd.ia.algorithm.desc'); ?></span>
      </td>
      <td>
        <?php
          global $cm_lang, $cm_texts;
          $attrs['value'] = (isset($co_identifier_assignments)
                              ? $co_identifier_assignments[0]['CoIdentifierAssignment']['algorithm']
                              : IdentifierAssignmentEnum::Sequential);
          $attrs['empty'] = false;
          $attrs['onChange'] = 'fields_update_gadgets()';
          
          if($e) {
            print $this->Form->select('algorithm',
                                      $cm_texts[ $cm_lang ]['en.ia.algorithm'],
                                      $attrs);
            
            if($this->Form->isFieldError('algorithm')) {
              print $this->Form->error('algorithm');
            }
          } else {
            print _txt('en.ia.algorithm', null, $co_identifier_assignments[0]['CoIdentifierAssignment']['algorithm']);
          }
        ?>
      </td>
    </tr>
    <tr class="line2">
      <td>
        <strong class="fieldTitle">
          <?php print ($e ? $this->Form->label('format', _txt('fd.ia.format')) : _txt('fd.ia.format')); ?>
        </strong><br />
        <span class="descr"><?php print _txt('fd.ia.format.desc'); ?></span>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('format') : filter_var($co_identifier_assignments[0]['CoIdentifierAssignment']['format'],FILTER_SANITIZE_SPECIAL_CHARS)); ?>
        <?php if($e): ?>
        <br/><label for="CoIdentifierAssignmentPrefabricatedFormats"><?php print  _txt('fd.ia.format.pattern') . ': '; ?></label>
        <select
          id="CoIdentifierAssignmentPrefabricatedFormats"
          onchange="javascript:document.getElementById('CoIdentifierAssignmentFormat').value=this.value">
          <option value=""><?php print _txt('fd.ia.format.prefab'); ?></option>
          <option value=""><?php print _txt('fd.ia.format.p0'); ?></option>
          <option value="(g).(f)[1:.(#)]@myvo.org"><?php print _txt('fd.ia.format.p1'); ?></option>
          <option value="(g)[1:.(m:1)].(f)[2:.(#)]@myvo.org"><?php print _txt('fd.ia.format.p2'); ?></option>
          <option value="(g:1)(m:1)(f:1)(#)@myvo.org"><?php print _txt('fd.ia.format.p3'); ?></option>
        </select>
        <?php endif; ?>
      </td>
    </tr>
    <tr class="line1">
      <td>
        <strong class="fieldTitle">
          <?php print ($e ? $this->Form->label('permitted', _txt('fd.ia.permitted')) : _txt('fd.ia.permitted')); ?>
        </strong><br />
        <span class="descr"><?php print _txt('fd.ia.permitted.desc'); ?></span>
      </td>
      <td>
        <?php
          global $cm_lang, $cm_texts;
          $attrs['value'] = (isset($co_identifier_assignments)
                              ? $co_identifier_assignments[0]['CoIdentifierAssignment']['permitted']
                              : PermittedCharacterEnum::AlphaNumeric);
          $attrs['empty'] = false;
          
          if($e) {
            print $this->Form->select('permitted',
                                      $cm_texts[ $cm_lang ]['en.chars.permitted'],
                                      $attrs);
            
            if($this->Form->isFieldError('permitted')) {
              print $this->Form->error('permitted');
            }
          } else {
            print _txt('en.chars.permitted', null, $co_identifier_assignments[0]['CoIdentifierAssignment']['permitted']);
          }
        ?>
      </td>
    </tr>
    <tr class="line2">
      <td>
        <strong class="fieldTitle">
          <?php print ($e ? $this->Form->label('minimum', _txt('fd.ia.minimum')) : _txt('fd.ia.minimum')); ?>
        </strong><br />
        <span class="descr"><?php print _txt('fd.ia.minimum.desc'); ?></span>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('minimum') : filter_var($co_identifier_assignments[0]['CoIdentifierAssignment']['minimum'],FILTER_SANITIZE_SPECIAL_CHARS)); ?>
      </td>
    </tr>
    <tr class="line1">
      <td>
        <strong class="fieldTitle">
          <?php print ($e ? $this->Form->label('maximum', _txt('fd.ia.maximum')) : _txt('fd.ia.maximum')); ?>
        </strong><br />
        <span class="descr"><?php print _txt('fd.ia.maximum.desc'); ?></span>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('maximum') : filter_var($co_identifier_assignments[0]['CoIdentifierAssignment']['maximum'],FILTER_SANITIZE_SPECIAL_CHARS)); ?>
      </td>
    </tr>
    <tr class="line2">
      <td>
        <strong class="fieldTitle">
          <?php print _txt('fd.ia.exclusions'); ?>
        </strong><br />
        <span class="descr"><?php print _txt('fd.ia.exclusions.desc'); ?></span>
      </td>
      <td></td>
    </tr>

    <?php
    if($e) {
      print '<tr><td></td><td>';
      print $this->Form->submit($submit_label, array('onClick' => 'on_submit()'));
      print $this->Form->button(_txt('op.reset'), array('type'=>'reset'));
      print '</td></tr>';
    }
    ?>

  </tbody>
</table>
