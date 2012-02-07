<?php
/**
 * COmanage Registry User Model
 *
 * Copyright (C) 2011-12 University Corporation for Advanced Internet Development, Inc.
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
 * @copyright     Copyright (C) 2011-12 University Corporation for Advanced Internet Development, Inc.
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry
 * @since         COmanage Registry v0.1
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version       $Id$
 */

class User extends AppModel {
  // The User class is a special Cake class which we extend a bit.  The cm_users table is implemented
  // as a view, so items included here are generally for read purposes only.
  public $name = 'User';
  
  // Association rules from this model to other models
  // XXX User belongsTo ApiUser, but that isn't a formal model yet
    
  // Default display field for cake generated views
  var $displayField = "username";
  
  // Default ordering for find operations
  var $order = array("username");
}
