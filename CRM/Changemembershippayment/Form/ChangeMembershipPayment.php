<?php

require_once 'CRM/Core/Form.php';

/**
 * Form controller class
 *
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC43/QuickForm+Reference
 */
class CRM_Changemembershippayment_Form_ChangeMembershipPayment extends CRM_Core_Form {

  protected $mid;

  protected $cid;

  protected $isChangeable = false;

  function preProcess() {
    $this->mid = CRM_Utils_Request::retrieve('id', 'Integer', CRM_Core_DAO::$_nullObject, true);
    $this->cid = CRM_Utils_Request::retrieve('cid', 'Integer', CRM_Core_DAO::$_nullObject, true);

    $this->add('hidden', 'cid', $this->cid);
    $this->add('hidden', 'id', $this->mid);

    $this->isChangeable = CRM_Changemembershippayment_Utils::isMembershipChangeable($this->mid);
    $this->assign('isChangeable', $this->isChangeable);
  }


  function buildQuickForm() {

    // add form elements
    $this->add(
      'select', // field type
      'payment_instrument_id', // field name
      'Payment instrument', // field label
      array('' => ts('- select -')) + CRM_Contribute_PseudoConstant::paymentInstrument(),
      true // is required
    );

    $this->add(
        'text',
        'total_amount',
        'Total amount'
    );
    $this->addRule('total_amount', ts('Please enter a valid amount.'), 'money');

    $this->add(
      'select',
      'iban',
      'IBAN',
      $this->getIbanOptions()
    );

    $this->add(
        'select',
        'mandaat_id',
        'SEPA Mandaat',
        $this->getMandaatOptions()
    );

    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => ts('Submit'),
        'isDefault' => TRUE,
      ),
    ));

    // export form elements
    $this->assign('elementNames', $this->getRenderableElementNames());
    parent::buildQuickForm();
  }

  function setDefaultValues() {
    $sql = "SELECT c.* from `civicrm_contribution` `c`
            INNER JOIN `civicrm_membership_payment` `mp` ON `c`.`id` = `mp`.`contribution_id`
            WHERE `mp`.`membership_id` = %1 and c.receive_date > now()
            ORDER BY `c`.`receive_date`
            LIMIT 1";
    $params = array();
    $params[1] = array($this->mid, 'Integer');
    $dao = CRM_Core_DAO::executeQuery($sql, $params);
    $defaults = array();
    if ($dao->fetch()) {
      $defaults['payment_instrument_id'] = $dao->payment_instrument_id;
      $defaults['total_amount'] = $dao->total_amount;

      $iban = $this->getCurrentIbanAccount($this->cid, $this->mid);
      if ($iban) {
        $defaults['iban'] = $iban;
      }

      $mandaat_id = $this->getCurrentSepamandaat($this->cid, $this->mid);
      if ($mandaat_id) {
        $defaults['mandaat_id'] = $mandaat_id;
      }
    }
    return $defaults;
  }

  function postProcess() {
    $values = $this->exportValues();

    if ($this->isChangeable) {
      $sql = "SELECT c.id from `civicrm_contribution` `c`
            INNER JOIN `civicrm_membership_payment` `mp` ON `c`.`id` = `mp`.`contribution_id`
            WHERE `mp`.`membership_id` = %1
            and date(`c`.`receive_date`) > NOW()";
      $params = array();
      $params[1] = array($this->mid, 'Integer');
      $dao = CRM_Core_DAO::executeQuery($sql, $params);
      $contributionCount = 0;
      while ($dao->fetch()) {
        $this->updateContribution($dao->id, $values);
        $contributionCount++;
      }

      $params['id'] = $this->mid;
      $iban_config = CRM_Ibanaccounts_Config::singleton();
      $sepa_config = CRM_Sepamandaat_Config_MembershipSepaMandaat::singleton();
      if (!empty($values['iban'])) {
        $params['custom_'.$iban_config->getIbanMembershipCustomFieldValue('id')] = $values['iban'];
      } else {
        $params['custom_'.$iban_config->getIbanMembershipCustomFieldValue('id')] = '';
      }
      if (!empty($values['mandaat_id'])) {
        $params['custom_'.$sepa_config->getCustomField('mandaat_id', 'id')] = $values['mandaat_id'];
      } else {
        $params['custom_'.$sepa_config->getCustomField('mandaat_id', 'id')] = '';
      }
      civicrm_api3('Membership', 'create', $params);

      CRM_Core_Session::setStatus('Updated ' . $contributionCount . ' contributions', '', 'success');
    }

    parent::postProcess();
  }

  protected function updateContribution($contribution_id, $values) {
    $params['id'] = $contribution_id;
    $params['payment_instrument_id'] = $values['payment_instrument_id'];
    $params['total_amount'] = $values['total_amount'];
    $iban_config = CRM_Ibanaccounts_Config::singleton();
    $sepa_config = CRM_Sepamandaat_Config_ContributionSepaMandaat::singleton();
    if (!empty($values['iban'])) {
      $params['custom_'.$iban_config->getIbanContributionCustomFieldValue('id')] = $values['iban'];
    } else {
      $params['custom_'.$iban_config->getIbanContributionCustomFieldValue('id')] = '';
    }
    if (!empty($values['mandaat_id'])) {
      $params['custom_'.$sepa_config->getCustomField('mandaat_id', 'id')] = $values['mandaat_id'];
    } else {
      $params['custom_'.$sepa_config->getCustomField('mandaat_id', 'id')] = '';
    }
    civicrm_api3('Contribution', 'create', $params);
  }

  protected function getCurrentSepamandaat($contactId, $mid) {
    $config = CRM_Sepamandaat_Config_MembershipSepaMandaat::singleton();
    $table = $config->getCustomGroupInfo('table_name');
    $mandaat_id_field = $config->getCustomField('mandaat_id', 'column_name');

    //set default value
    $sql = "SELECT * FROM `" . $table . "` WHERE `entity_id` = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array(1 => array($mid, 'Integer')));
    if ($dao->fetch()) {
      $mandaat_id = $dao->$mandaat_id_field;
      if (CRM_Sepamandaat_SepaMandaat::isExistingMandaat($mandaat_id, $contactId)) {
        return $mandaat_id;
      }
    }
    return false;;
  }

  protected function getCurrentIbanAccount($contactId, $mid) {
    $config = CRM_Ibanaccounts_Config::singleton();
    $table = $config->getIbanMembershipCustomGroupValue('table_name');
    $iban_field = $config->getIbanMembershipCustomFieldValue('column_name');

    //set default value
    $sql = "SELECT * FROM `" . $table . "` WHERE `entity_id` = %1";
    $dao = CRM_Core_DAO::executeQuery($sql, array(1 => array($mid, 'Integer')));
    if ($dao->fetch()) {
      $iban = $dao->$iban_field;
      $account_id = CRM_Ibanaccounts_Ibanaccounts::getIdByIBANAndContactId($iban, $contactId);
      if ($account_id) {
        return $iban;
      }
    }
    return false;
  }

  protected function getIbanOptions() {
    $iban = array(ts(' -- Select IBAN account --'));
    $ibans = CRM_Ibanaccounts_Ibanaccounts::IBANForContact($this->cid);
    foreach($ibans as $i) {
      $iban[$i['iban']] = $i['iban_human'];
    }
    return $iban;
  }

  protected function getMandaatOptions() {
    $mandaat_ids = array(ts(' -- Select Mandaat ID --'));
    $mandaats = CRM_Sepamandaat_SepaMandaat::getMandatesByContact($this->cid);
    foreach($mandaats as $id => $mandaat) {
      $mandaat_ids[$mandaat['mandaat_nr']] = $mandaat['mandaat_nr'];
    }
    return $mandaat_ids;
  }

  /**
   * Get the fields/elements defined in this form.
   *
   * @return array (string)
   */
  function getRenderableElementNames() {
    // The _elements list includes some items which should not be
    // auto-rendered in the loop -- such as "qfKey" and "buttons".  These
    // items don't have labels.  We'll identify renderable by filtering on
    // the 'label'.
    $elementNames = array();
    foreach ($this->_elements as $element) {
      /** @var HTML_QuickForm_Element $element */
      $label = $element->getLabel();
      if (!empty($label)) {
        $elementNames[] = $element->getName();
      }
    }
    return $elementNames;
  }
}
