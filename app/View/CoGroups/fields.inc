<?php
/**
 * COmanage Registry CO Group Fields
 *
 * Copyright (C) 2010-16 University Corporation for Advanced Internet Development, Inc.
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
 * @copyright     Copyright (C) 2010-16 University Corporation for Advanced Internet Development, Inc.
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry
 * @since         COmanage Registry v0.1
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version       $Id$
 */

  // Determine if fields are editable or viewable
  $dok = false;
  $e = false;
  $v = false;
  
  if(($this->action == "add" && $permissions['add'])
     || ($this->action == "edit" && $permissions['edit'])
     || (($this->action == 'add' || $this->action == 'edit')
         && isset($co_groups[0]['CoGroup']['id'])
         && !empty($permissions['owner'])
         && in_array($co_groups[0]['CoGroup']['id'], $permissions['owner'])))
    $e = true;

  if(($permissions['delete'])
     || (isset($co_groups[0]['CoGroup']['id'])
         && !empty($permissions['owner'])
         && in_array($co_groups[0]['CoGroup']['id'], $permissions['owner'])))
    $dok = true;

  if(($this->action == "view" && $permissions['view'])
     || (isset($co_groups[0]['CoGroup']['id'])
         && !empty($permissions['member'])
         && in_array($co_groups[0]['CoGroup']['id'], $permissions['member']))
     || (isset($co_groups[0]['CoGroup']['open']) && $co_groups[0]['CoGroup']['open']))
    $v = true;

  // We shouldn't get here if we don't have at least read permission, but check just in case
  
  if(!$e && !$v)
    return(false);

  // Add breadcrumbs
  print $this->element("coCrumb");
  $args = array();
  $args['plugin'] = null;
  $args['controller'] = 'co_groups';
  $args['action'] = 'index';
  $args['co'] = $cur_co['Co']['id'];
  $this->Html->addCrumb(_txt('ct.co_groups.pl'), $args);
  $crumbTxt = _txt('op.' . $this->action . '-a', array(_txt('ct.co_groups.1')));
  $this->Html->addCrumb($crumbTxt);

  // Populate the reference
  print $this->Form->hidden('co_id', array('default' => $cur_co['Co']['id'])). "\n";

?>
<table id="<?php print $this->action; ?>_co_group" class="ui-widget">
  <tbody>
    <tr class="line1">
      <td>
        <?php print ($e ? $this->Form->label('name', _txt('fd.name')) : _txt('fd.name')); ?><span class="required">*</span>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('name', array('class' => 'focusFirst')) : filter_var($co_groups[0]['CoGroup']['name'],FILTER_SANITIZE_SPECIAL_CHARS)); ?>
      </td>
    </tr>
    <tr class="line2">
      <td>
        <?php print ($e ? $this->Form->label('description', _txt('fd.desc')) : _txt('fd.desc')); ?>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('description') : filter_var($co_groups[0]['CoGroup']['description'],FILTER_SANITIZE_SPECIAL_CHARS)); ?>
      </td>
    </tr>
    <tr class="line1">
      <td>
        <?php print _txt('fd.open'); ?>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('open') : ($co_groups[0]['CoGroup']['open'] ? _txt('fd.yes') : _txt('fd.no'))); ?>
        <?php
          if ($e) {
            print $this->Form->label('open',_txt('fd.open'));
          }
        ?>
      </td>
    </tr>
    <tr class="line2">
      <td>
        <?php print ($e ? $this->Form->label('status', _txt('fd.status')) : _txt('fd.status')); ?>
      </td>
      <td>
        <?php
          global $cm_lang, $cm_texts;
          $attrs['value'] = (isset($co_groups) ? $co_groups[0]['CoGroup']['status'] : 'A');
          $attrs['empty'] = false;

          if($e) {
            print $this->Form->select('status',
                                      $cm_texts[ $cm_lang ]['en.status.susp'],
                                      $attrs);
            
            if($this->Form->isFieldError('status')) {
              print $this->Form->error('status');
            }
          } else {
            print _txt('en.status', null, $co_groups[0]['CoGroup']['status']);
          }
        ?>
      </td>
    </tr>
    <tr>
      <td>
        <em><span class="required"><?php print _txt('fd.req'); ?></span></em><br />
      </td>
      <td>
        <?php
          if($e) {
            print $this->Form->submit($submit_label);
            print $this->Form->button(_txt('op.reset'), 
                                      array('type'=>'reset'));
          }
        ?>
      </td>

    </tr>
  </tbody>
</table>

<?php if($this->action != 'add'): ?>
<h3><?php print _txt('ct.co_group_members.pl'); ?></h3>
<table id="groupMembers">
  <thead>
    <tr class="ui-widget-header">
      <th><?php print _txt('fd.name'); ?></th>
      <th><?php print _txt('fd.status'); ?></th>
      <th><?php print _txt('fd.roles'); ?></th>
      <th class="actionButtons"><?php print _txt('fd.actions'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php
      foreach($vv_co_group_members as $g) {
        print '<tr>';
        
        // Member name
        print '<td>';
        if($permissions['admin']) {
          print $this->Html->link((!empty($g['CoPerson']['PrimaryName'])
                                   ? generateCn($g['CoPerson']['PrimaryName'])
                                   : "(?)"),
                                  array('controller' => 'co_people',
                                        'action' => 'canvas',
                                        $g['CoPerson']['id']));
        } else {
          print filter_var(generateCn($g['CoPerson']['PrimaryName']),FILTER_SANITIZE_SPECIAL_CHARS);
        }
        print '</td>';
        
        // Member's CO Person status
        print '<td>';
        if(!empty($g['CoPerson']['status'])) {
          print _txt('en.status', null, $g['CoPerson']['status']);
        }
        print '</td>';
        
        // Group role (owner vs member)
        print '<td>';
        if($g['CoGroupMember']['owner']) {
          if($g['CoGroupMember']['member']) {
            print _txt('fd.group.grmemown');
          } else {
            print _txt('fd.group.own.only');
          }
        } elseif($g['CoGroupMember']['member']) {
          print _txt('fd.group.mem');
        }
        print '</td>';
        
        print '<td class="actions">';
        if($e) {
          // Do not show edit or delete buttons if this is a members group.
          if(!$isMembersGroup) {
            print $this->Html->link(
              _txt('op.edit'),
              array(
                'controller' => 'co_group_members',
                'action' => 'edit',
                $g['CoGroupMember']['id']
              ),
              array('class' => 'editbutton')
            );
            
            if($dok) {
              print '<a class="deletebutton" title="' . _txt('op.delete')
                . '" onclick="javascript:js_confirm_generic(\''
                . _txt('js.remove.member') . '\',\''    // dialog body text
                . $this->Html->url(              // dialog confirm URL
                  array(
                    'controller' => 'co_group_members',
                    'action' => 'delete',
                    $g['CoGroupMember']['id'],
                    'copersonid' => $g['CoGroupMember']['co_person_id'],
                    'return' => 'group'
                  )
                ) . '\',\''
                . _txt('op.remove') . '\',\''    // dialog confirm button
                . _txt('op.cancel') . '\',\''    // dialog cancel button
                . _txt('op.remove') . '\',[\''   // dialog title
                . filter_var(_jtxt($co_groups[0]['CoGroup']['name']),FILTER_SANITIZE_STRING)  // dialog body text replacement strings
                . '\']);">'
                . _txt('op.delete')
                . '</a>';
            }
          }
        }
        print '</td>';
        
        print '</tr>';
      }
    ?>
  </tbody>
</table>
<?php endif; // add ?>
<?php
  print $this->element("changelog");
