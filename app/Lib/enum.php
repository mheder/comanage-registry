<?php
/**
 * COmanage Registry Enumerations
 *
 * Copyright (C) 2010-17 University Corporation for Advanced Internet Development, Inc.
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
 * @copyright     Copyright (C) 2010-17 University Corporation for Advanced Internet Development, Inc.
 * @link          http://www.internet2.edu/comanage COmanage Project
 * @package       registry
 * @since         COmanage Registry v0.1
 * @license       Apache License, Version 2.0 (http://www.apache.org/licenses/LICENSE-2.0)
 * @version       $Id$
 */

class ActionEnum
{
  // Codes beginning with 'X' (eg: 'XABC') are reserved for local use
  const CoGroupAdded                    = 'ACGR';
  const CoGroupDeleted                  = 'DCGR';
  const CoGroupEdited                   = 'ECGR';
  const CoGroupMemberAdded              = 'ACGM';
  const CoGroupMemberAddedPipeline      = 'ACGL';
  const CoGroupMemberDeleted            = 'DCGM';
  const CoGroupMemberDeletedPipeline    = 'DCGL';
  const CoGroupMemberEdited             = 'ECGM';
  const CoPersonAddedManual             = 'ACPM';
  const CoPersonAddedPetition           = 'ACPP';
  const CoPersonAddedPipeline           = 'ACPL';
  const CoPersonDeletedManual           = 'DCPM';
  const CoPersonDeletedPetition         = 'DCPP';
  const CoPersonEditedManual            = 'ECPM';
  const CoPersonEditedPetition          = 'ECPP';
  const CoPersonEditedPipeline          = 'ECPL';
  const CoPersonManuallyProvisioned     = 'PCPM';
  const CoPersonMatchedPetition         = 'MCPP';
  const CoPersonMatchedPipelne          = 'MCPL';
  const CoPersonProvisioned             = 'PCPA';
  const CoPersonStatusRecalculated      = 'RCPS';
  const CoPersonRoleAddedManual         = 'ACRM';
  const CoPersonRoleAddedPetition       = 'ACRP';
  const CoPersonRoleAddedPipeline       = 'ACRL';
  const CoPersonRoleDeletedManual       = 'DCRM';
  const CoPersonRoleEditedExpiration    = 'ECRX';
  const CoPersonRoleEditedManual        = 'ECRM';
  const CoPersonRoleEditedPetition      = 'ECRP';
  const CoPersonRoleEditedPipeline      = 'ECRL';
  const CoPersonRoleRelinked            = 'LCRM';
  const CoPersonOrgIdLinked             = 'LOCP';
  const CoPersonOrgIdUnlinked           = 'UOCP';
  const CoPetitionCreated               = 'CPPC';
  const CoPetitionUpdated               = 'CPUP';
  const CoTAndCAgreement                = 'TCAG';
  const CoTAndCAgreementBehalf          = 'TCAB';
  const CommentAdded                    = 'CMNT';
  const EmailAddressVerified            = 'EMLV';
  const EmailAddressVerifyCanceled      = 'EMLC';
  const EmailAddressVerifyReqSent       = 'EMLS';
  const ExpirationPolicyMatched         = 'EXPM';
  const HistoryRecordActorExpunged      = 'HRAE';
  const IdentifierAutoAssigned          = 'AIDA';
  const InvitationConfirmed             = 'INVC';
  const InvitationDeclined              = 'INVD';
  const InvitationExpired               = 'INVE';
  const InvitationSent                  = 'INVS';
  const NameAdded                       = 'ANAM';
  const NameDeleted                     = 'DNAM';
  const NameEdited                      = 'ENAM';
  const NamePrimary                     = 'PNAM';
  const NotificationAcknowledged        = 'NOTA';
  const NotificationCanceled            = 'NOTX';
  const NotificationDelivered           = 'NOTD';
  const NotificationParticipantExpunged = 'NOTE';
  const NotificationResolved            = 'NOTR';
  const OrgIdAddedManual                = 'AOIM';
  const OrgIdAddedPetition              = 'AOIP';
  const OrgIdAddedSource                = 'AOIS';
  const OrgIdDeletedManual              = 'DOIM';
  const OrgIdDeletedPetition            = 'DOIP';
  const OrgIdEditedLoginEnv             = 'EOIE';
  const OrgIdEditedManual               = 'EOIM';
  const OrgIdEditedPetition             = 'EOIP';
  const OrgIdEditedSource               = 'EOIS';
  const OrgIdRemovedSource              = 'ROIS';
  const ProvisionerAction               = 'PRVA';
  const ProvisionerFailed               = 'PRVX';
  const SshKeyAdded                     = 'SSHA';
  const SshKeyDeleted                   = 'SSHD';
  const SshKeyEdited                    = 'SSHE';
  const SshKeyUploaded                  = 'SSHU';
}

class AdministratorEnum
{
  const NoAdmin       = 'N';
  const CoOrCouAdmin  = 'C';
  const CoAdmin       = 'O';
}

class AffiliationEnum
{
  const Faculty       = 'faculty';
  const Student       = 'student';
  const Staff         = 'staff';
  const Alum          = 'alum';
  const Member        = 'member';
  const Affiliate     = 'affiliate';
  const Employee      = 'employee';
  const LibraryWalkIn = 'librarywalkin';
  
  // Mapping to the controlled vocabulary. Suitable for use (eg) writing to LDAP.
  public static $eduPersonAffiliation = array(
    AffiliationEnum::Faculty       => 'Faculty',
    AffiliationEnum::Student       => 'Student',
    AffiliationEnum::Staff         => 'Staff',
    AffiliationEnum::Alum          => 'Alum',
    AffiliationEnum::Member        => 'Member',
    AffiliationEnum::Affiliate     => 'Affiliate',
    AffiliationEnum::Employee      => 'Employee',
    AffiliationEnum::LibraryWalkIn => 'Library Walk-In'
  );
  
  public static $fromEduPersonAffiliation = array(
    'Faculty'         => AffiliationEnum::Faculty,
    'Student'         => AffiliationEnum::Student,
    'Staff'           => AffiliationEnum::Staff,
    'Alum'            => AffiliationEnum::Alum,
    'Member'          => AffiliationEnum::Member,
    'Affiliate'       => AffiliationEnum::Affiliate,
    'Employee'        => AffiliationEnum::Employee,
    'Library Walk-In' => AffiliationEnum::LibraryWalkIn
  );
}

class AuthenticationEventEnum
{
  const ApiLogin               = 'AI';
  const RegistryLogin          = 'IN';
}

class ComparisonEnum
{
  const Contains               = 'CTS'; // Substr
  const ContainsInsensitive    = 'CTI';
  const Equals                 = 'EQS';
  const EqualsInsensitive      = 'EQI';
  const NotContains            = 'NCT';
  const NotContainsInsensitive = 'NCTI';
  const NotEquals              = 'NEQ';
  const NotEqualsInsensitive   = 'NEQI';
  const Regex                  = 'REGX';
}

class ContactEnum
{
  const Campus      = 'campus';
  const Fax         = 'fax';
  const Forwarding  = 'forwarding';
  const Home        = 'home';
  const Mobile      = 'mobile';
  const Office      = 'office';
  const Postal      = 'postal';
}

class ElectStrategyEnum {
  const FIFO        = 'FI';
  const Manual      = 'M';
}

class EmailAddressEnum {
  const Delivery      = 'delivery';
  const Forwarding    = 'forwarding';
  const Official      = 'official';
  const Personal      = 'personal';
  const Preferred     = 'preferred';
  const Recovery      = 'recovery';
}

class EnrollmentAuthzEnum {
  const AuthUser      = 'AU';
  const CoAdmin       = 'CA';
  const CoGroupMember = 'CG';
  const CoOrCouAdmin  = 'A';
  const CoPerson      = 'CP';
  const CouAdmin      = 'UA';
  const CouPerson     = 'UP';
  const None          = 'N';
}

class EnrollmentDupeModeEnum
{
  const Duplicate       = 'D';
  const Merge           = 'M';
  const NewRole         = 'R';
  const NewRoleCouCheck = 'C';
}

class EnrollmentFlowStatusEnum
{
  const Active              = 'A';
  const Suspended           = 'S';
  const Template            = 'T';
}

class EnrollmentMatchPolicyEnum {
  const Advisory  = "A";
  const Automatic = "M";
  const None      = "N";
  const Select    = "P";
  const Self      = "S";
}

class EnrollmentOrgIdentityModeEnum {
  const OISAuthenticate   = "OA";
  const OISClaim          = "OC";
  const OISSearch         = "OS";
  const OISSearchRequired = "SR";
  const OISSelect         = "SL";
  const None              = "N";
}

class EnrollmentRole
{
  const Approver   = 'A';
  const Enrollee   = 'E';
  const Petitioner = 'P';
}

class ExtendedAttributeEnum {
  const Integer   = 'INTEGER';
  const Timestamp = 'TIMESTAMP';
  const Varchar32 = 'VARCHAR(32)';
}

class IdentifierAssignmentEnum
{
  const Random     = 'R';
  const Sequential = 'S';
}

class IdentifierAssignmentExclusionEnum
{
  const Confusing     = 'C';
  const Offensive     = 'O';
  const Superstitious = 'S';
}

class IdentifierEnum
{
  const Badge      = 'badge';
  const Enterprise = 'enterprise';
  const ePPN       = 'eppn';
  const ePTID      = 'eptid';
  const Mail       = 'mail';
  const National   = 'national';
  const Network    = 'network';
  const OpenID     = 'openid';
  const ORCID      = 'orcid';
  const Reference  = 'reference';
  const SORID      = 'sorid';
  const UID        = 'uid';
}

class JobStatusEnum
{
  const Complete   = 'OK';
  const Failed     = 'X';
  const InProgress = 'GO';
  const Queued     = 'Q';
}

class JobTypeEnum
{
  const Expiration      = 'EX';
  const OrgIdentitySync = 'OS';
}

class LinkLocationEnum
{
  const topBar  = 'topbar';
}

class MatchStrategyEnum
{
  const EmailAddress = 'EA';
  const External     = 'EX';
  const Identifier   = 'ID';
  const NoMatching   = 'NO';
}

class MessageTemplateEnum
{
  const EnrollmentApproval     = 'EA';
  const EnrollmentFinalization = 'EF';
  const EnrollmentVerification = 'EV';
  const ExpirationNotification = 'XN';
}

class NameEnum
{
  const Alternate = 'alternate';
  const Author    = 'author';
  const FKA       = 'fka';
  const Official  = 'official';
  const Preferred = 'preferred';
}

class NotificationStatusEnum
{
  const Acknowledged          = 'A';
  const Canceled              = 'X';
  const Deleted               = 'D';
  const PendingAcknowledgment = 'PA';
  const PendingResolution     = 'PR';
  const Resolved              = 'R';
}

class NSFCitizenshipEnum
{
  const USCitizen            = 'US';
  const USPermanentResident  = 'P';
  const Other                = 'O';
}

class NSFDisabilityEnum
{
  const Hearing     = 'H';
  const Visual      = 'V';
  const Mobility    = 'M';
  const Other       = 'O';

}

class NSFEthnicityEnum
{
  const Hispanic    = 'H';
  const NotHispanic = 'N';
}

class NSFGenderEnum
{
  const Male        = 'M';
  const Female      = 'F';
}

class NSFRaceEnum
{
  const Asian            = 'A';
  const AmericanIndian   = 'I';
  const Black            = 'B';
  const NativeHawaiian   = 'N';
  const White            = 'W';
}

class OrgIdentityStatusEnum
{
  const Removed          = 'RM';
  const Synced           = 'SY';
}

class PermissionEnum
{
  const None      = 'N';
  const ReadOnly  = 'RO';
  const ReadWrite = 'RW';
}

class PermittedCharacterEnum
{
  const AlphaNumeric       = 'AN';
  const AlphaNumDotDashUS  = 'AD';
  const AlphaNumDDUSQuote  = 'AQ';
  const Any                = 'AL';
}

// We use the actual field names here to simplify form rendering
class PermittedNameFieldsEnum
{
//  const Given       = "given";  Not currently allowed due to potential conflict with RequiredNameFieldsEnum
  const GF    = "given,family";
  const GMF   = "given,middle,family";
  const GFS   = "given,family,suffix";
  const GMFS  = "given,middle,family,suffix";
  const HGF   = "honorific,given,family";
  const HGMF  = "honorific,given,middle,family";
  const HGFS  = "honorific,given,family,suffix";
  const HGMFS = "honorific,given,middle,family,suffix";
}

class PetitionActionEnum
{
  const Approved                = 'PY';
  const AttributesUpdated       = 'AU';
  const CommentAdded            = 'CM';
  const Created                 = 'PC';
  const Declined                = 'PX';
  const Denied                  = 'PN';
  const EligibilityFailed       = 'EX';
  const Finalized               = 'PF';
  const FlaggedDuplicate        = 'FD';
  const IdentifierAuthenticated = 'ID';
  const IdentifiersAssigned     = 'IA';
  const IdentityLinked          = 'IL';
  const IdentityRelinked        = 'IR';
  const InviteConfirmed         = 'IC';
  const InviteSent              = 'IS';
  const NotificationSent        = 'NS';
  const StatusUpdated           = 'SU';
  const TCExplicitAgreement     = 'TE';
  const TCImpliedAgreement      = 'TI';
}

class PetitionStatusEnum
{
  const Active              = 'A';
  const Approved            = 'Y';
  const Confirmed           = 'C';
  const Created             = 'CR';
  const Declined            = 'X';
  const Denied              = 'N';
  const Duplicate           = 'D2';
  const Finalized           = 'F';
  const PendingApproval     = 'PA';
  const PendingConfirmation = 'PC';
}

// The status of a provisioning plugin
class ProvisionerStatusEnum
{
  const AutomaticMode       = 'A';
  const Disabled            = 'X';
  const ManualMode          = 'M';
}

// The action for which a plugin may want to act on
class ProvisioningActionEnum
{
  const CoGroupAdded                  = 'GA';
  const CoGroupDeleted                = 'GD';
  const CoGroupReprovisionRequested   = 'GR';
  const CoGroupUpdated                = 'GU';
  const CoPersonAdded                 = 'PA';
  const CoPersonDeleted               = 'PD';
  const CoPersonEnteredGracePeriod    = 'PG';
  const CoPersonExpired               = 'PX';
  const CoPersonPetitionProvisioned   = 'PP';  // Triggered after a petition is finalized
  const CoPersonPipelineProvisioned   = 'PL';  // Triggered after a pipeline is executed
  const CoPersonReprovisionRequested  = 'PR';
  const CoPersonUnexpired             = 'PY';
  const CoPersonUpdated               = 'PU';
}

// The status of a provisioned target
class ProvisioningStatusEnum
{
  const NotProvisioned      = 'N';
  const Provisioned         = 'P';
  const Queued              = 'Q';
  const Unknown             = 'X';
}

class RequiredEnum
{
  const Required      = 1;
  const Optional      = 0;
  const NotPermitted  = -1;
}

// We use the actual field names here to simplify form rendering
class RequiredAddressFieldsEnum
{
  const Street                       = "street";
  const StreetCityStatePostal        = "street,locality,state,postal_code";
  const StreetCityStatePostalCountry = "street,locality,state,postal_code,country";
}

class RequiredNameFieldsEnum
{
  const Given       = "given";
  const GivenFamily = "given,family";
}

class SponsorEligibilityEnum {
  const CoAdmin       = 'CA';
  const CoGroupMember = 'CG';
  const CoOrCouAdmin  = 'A';
  const CoPerson      = 'CP';
  const None          = 'N';
}

class SshKeyTypeEnum
{
  // Protocol v2
  const DSA         = 'DSA';
  const RSA         = 'RSA';
  // Protocol v1
  const RSA1        = 'RSA1';
}

class StatusEnum
{
  const Active              = 'A';
  const Approved            = 'Y';
  const Confirmed           = 'C';
  const Deleted             = 'D';
  const Denied              = 'N';
  const Duplicate           = 'D2';
  const Expired             = 'XP';
  const GracePeriod         = 'GP';
  const Invited             = 'I';
  const Pending             = 'P';
  const PendingApproval     = 'PA';
  const PendingConfirmation = 'PC';
  const Suspended           = 'S';
  const Declined            = 'X';

  public static $from_api = array(
    'Active'              => StatusEnum::Active,
    'Approved'            => StatusEnum::Approved,
    'Confirmed'           => StatusEnum::Confirmed,
    'Deleted'             => StatusEnum::Deleted,
    'Denied'              => StatusEnum::Denied,
    'Duplicate'           => StatusEnum::Duplicate,
    'Expired'             => StatusEnum::Expired,
    'GracePeriod'         => StatusEnum::GracePeriod,
    'Invited'             => StatusEnum::Invited,
    'Pending'             => StatusEnum::Pending,
    'PendingApproval'     => StatusEnum::PendingApproval,
    'PendingConfirmation' => StatusEnum::PendingConfirmation,
    'Suspended'           => StatusEnum::Suspended,
    'Declined'            => StatusEnum::Declined
  );
  
  public static $to_api = array(
    StatusEnum::Active              => 'Active',
    StatusEnum::Approved            => 'Approved',
    StatusEnum::Confirmed           => 'Confirmed',
    StatusEnum::Deleted             => 'Deleted',
    StatusEnum::Denied              => 'Denied',
    StatusEnum::Duplicate           => 'Duplicate',
    StatusEnum::Expired             => 'Expired',
    StatusEnum::GracePeriod         => 'GracePeriod',
    StatusEnum::Invited             => 'Invited',
    StatusEnum::Pending             => 'Pending',
    StatusEnum::PendingApproval     => 'PendingApproval',
    StatusEnum::PendingConfirmation => 'PendingConfirmation',
    StatusEnum::Suspended           => 'Suspended',
    StatusEnum::Declined            => 'Declined'
  );
}

class SuspendableStatusEnum
{
  const Active              = 'A';
  const Suspended           = 'S';

  public static $from_api = array(
    'Active'    => SuspendableStatusEnum::Active,
    'Suspended' => SuspendableStatusEnum::Suspended
  );
  
  public static $to_api = array(
    SuspendableStatusEnum::Active    => 'Active',
    SuspendableStatusEnum::Suspended => 'Suspended'
  );
}

class SyncActionEnum
{
  const Add    = 'A';
  const Delete = 'D';
  const Update = 'U';
}

class SyncModeEnum
{
  const Full   = 'F';
  const Manual = 'M';
  const Query  = 'Q';
  const Update = 'U';
}

class TAndCEnrollmentModeEnum
{
  const ExplicitConsent = 'EC';
  const ImpliedConsent  = 'IC';
  const SplashPage      = 'S';
  const Ignore          = 'X';
}

class TAndCLoginModeEnum
{
  const NotEnforced        = 'X';
  const RegistryLogin      = 'R';
  const DisableAllServices = 'D';
}

class VerificationModeEnum
{
  const Automatic = 'A';
  const Review    = 'R';
  const None      = 'X';
}

class VisibilityEnum
{
  const CoAdmin         = 'CA';
  const CoGroupMember   = 'CG';
  const CoMember        = 'CP';
  const Unauthenticated = 'P';
}

// Old style enums below, deprecated
// We're mostly ready to drop these, but there are still a few places that
// reference them and need to be cleaned up.
// See also new use of Model::validEnumsForSelect.

global $affil_t, $affil_ti;
global $contact_t, $contact_ti;
global $extattr_t, $extattr_ti;
global $identifier_t, $identifier_ti;
global $name_t, $name_ti;
global $ssh_ti;  // Used for ldap and github provisioner
global $status_t, $status_ti;

$affil_t = array(
  'faculty' => 'Faculty',
  'student' => 'Student',
  'staff' => 'Staff',
  'alum' => 'Alum',
  'member' => 'Member',
  'affiliate' => 'Affiliate',
  'employee' => 'Employee',
  'librarywalkin' => 'Library Walk-In'    
);

$affil_ti = array(
  'Faculty' => 'faculty',
  'Student' => 'student',
  'Staff' => 'staff',
  'Alum' => 'alum',
  'Member' => 'member',
  'Affiliate' => 'affiliate',
  'Employee' => 'employee',
  'Library Walk-In' => 'librarywalkin'    
);

$contact_t = array(
  'fax' => 'Fax',
  'home' => 'Home',
  'mobile' => 'Mobile',
  'office' => 'Office',
  'postal' => 'Postal',
  'forwarding' => 'Forwarding'
);

$contact_ti = array(
  'Fax' => 'fax',
  'Home' => 'home',
  'Mobile' => 'mobile',
  'Office' => 'office',
  'Postal' => 'postal',
  'Forwarding' => 'forwarding'
);


$identifier_t = array(
  'eppn' => 'ePPN',
  'eptid' => 'ePTID',
  'mail' => 'Mail',
  'openid' => 'OpenID',
  'uid' => 'UID'
);

$identifier_ti = array(
  'ePPN' => 'eppn',
  'ePTID' => 'eptid',
  'Mail' => 'mail',
  'OpenID' => 'openid',
  'UID' => 'uid'
);

$name_t = array(
  'alternate' => 'Alternate',
  'author' => 'Author',
  'fka' => 'FKA',
  'official' => 'Official',
  'preferred' => 'Preferred'
);

$name_ti = array(
  'Alternate' => 'alternate',
  'Author' => 'author',
  'FKA' => 'fka',
  'Official' => 'official',
  'Preferred' => 'preferred'
);

$ssh_ti = array(
  'DSA'  => 'ssh-dss',
  'RSA'  => 'ssh-rsa',
  'RSA1' => 'ssh-rsa1'
);

$status_t = array(
  'A'  => 'Active',
  'D'  => 'Deleted',
  'D2' => 'Duplicate',
  'I'  => 'Invited',
  'N'  => 'Denied',
  'P'  => 'Pending',
  'PA' => 'PendingApproval',
  'S'  => 'Suspended',
  'X'  => 'Declined',
  'Y'  => 'Approved'
);

$status_ti = array(
  'Active'          => 'A',
  'Deleted'         => 'D',
  'Duplicate'       => 'D2',
  'Invited'         => 'I',
  'Denied'          => 'N',
  'Pending'         => 'P',
  'PendingApproval' => 'PA',
  'Suspended'       => 'S',
  'Declined'        => 'X',
  'Approved'        => 'Y'
);

/**
 * Bootstrap plugin enums
 *
 * @since  COmanage Registry v1.1.0
 */
 
function _bootstrap_plugin_enum()
{
  $plugins = App::objects('plugin');
  
  foreach($plugins as $plugin) {
    // Plugin lang files could be under APP or LOCAL
    foreach(array(APP, LOCAL) as $dir) {
      $enumfile = $dir . '/Plugin/' . $plugin . '/Lib/enum.php';
      
      if(is_readable($enumfile)) {
        // Include the file
        include $enumfile;        
        break;
      }
    }
  }
}