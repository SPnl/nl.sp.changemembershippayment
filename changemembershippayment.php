<?php

require_once 'changemembershippayment.civix.php';

function changemembershippayment_civicrm_alterContent(  &$content, $context, $tplName, &$object ) {
  if ($context == 'page' && $object instanceof CRM_Member_Page_Tab) {
    $template = CRM_Core_Smarty::singleton();
    $template->assign('cid', $object->getVar('_contactId'));
    $content .= $template->fetch('CRM/changemebershippayment_js.tpl');
  }
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function changemembershippayment_civicrm_config(&$config) {
  _changemembershippayment_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function changemembershippayment_civicrm_xmlMenu(&$files) {
  _changemembershippayment_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function changemembershippayment_civicrm_install() {
  _changemembershippayment_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function changemembershippayment_civicrm_uninstall() {
  _changemembershippayment_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function changemembershippayment_civicrm_enable() {
  _changemembershippayment_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function changemembershippayment_civicrm_disable() {
  _changemembershippayment_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function changemembershippayment_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _changemembershippayment_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function changemembershippayment_civicrm_managed(&$entities) {
  _changemembershippayment_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function changemembershippayment_civicrm_caseTypes(&$caseTypes) {
  _changemembershippayment_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function changemembershippayment_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _changemembershippayment_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
