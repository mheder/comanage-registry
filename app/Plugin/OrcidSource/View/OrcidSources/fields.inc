<!--
/**
 * COmanage Registry ORCID Source Plugin Fields
 *
 * Copyright (C) 2016 SCG
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
 * @copyright     Copyright (C) 2016 SCG
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry-plugin
 * @since         COmanage Registry v1.1.0
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version       $Id$
 */
-->
<?php
  // Determine if fields are editable
  $e = false;
  
  if(($this->action == "add" && $permissions['add']) || ($this->action == "edit" && $permissions['edit']))
    $e = true;
    
  // We shouldn't get here if we don't have at least read permission, but check just in case
  
  if(!$e && !$permissions['view'])
    return false;
  
  print $this->Form->hidden('org_identity_source_id', array('default' => $vv_oisid)) . "\n";
  
  $l = 1;
?>
<table id="<?php print $this->action; ?>_orcid_source" class="ui-widget">
  <tbody>
    <tr class="line<?php print ($l % 2); $l++; ?>">
      <td>
        <strong class="fieldTitle"><?php print _txt('pl.orcidsource.redirect_url'); ?>
      </td>
      <td>
        <input type="text" readonly="true" size="60" value="<?php print $this->Html->url($vv_orcid_redirect_url, true); ?>" />
      </td>
    </tr>
    <tr class="line<?php print ($l % 2); $l++; ?>">
      <td>
        <strong class="fieldTitle"><?php print _txt('pl.orcidsource.clientid'); ?></strong><font class="required">*</font><br />
        <font class="desc"><?php print _txt('pl.orcidsource.clientid.desc'); ?></font>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('clientid', array('size' => 50)) : Sanitize::html($orcid_sources[0]['OrcidSource']['clientid'])); ?>
      </td>
    </tr>
    <tr class="line<?php print ($l % 2); $l++; ?>">
      <td>
        <strong class="fieldTitle"><?php print _txt('pl.orcidsource.secret'); ?></strong><font class="required">*</font><br />
        <font class="desc"><?php print _txt('pl.orcidsource.secret.desc'); ?></font>
      </td>
      <td>
        <?php print ($e ? $this->Form->input('client_secret', array('size' => 50)) : Sanitize::html($orcid_sources[0]['OrcidSource']['client_secret'])); ?>
      </td>
    </tr>
    <tr>
      <td>
        <i><font class="required"><?php print _txt('fd.req'); ?></font></i><br />
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
