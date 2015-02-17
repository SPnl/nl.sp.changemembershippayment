<?php

class CRM_Changemembershippayment_Utils {

    public static function isMembershipChangeable($mid) {
        $sql = "SELECT count(c.id) as total from `civicrm_contribution` `c`
            INNER JOIN `civicrm_membership_payment` `mp` ON `c`.`id` = `mp`.`contribution_id`
            WHERE `mp`.`membership_id` = %1
            and date(`c`.`receive_date`) > NOW()";
        $params = array();
        $params[1] = array($mid, 'Integer');
        $dao = CRM_Core_DAO::executeQuery($sql, $params);
        if ($dao->fetch() && $dao->total > 0) {
            return true;
        }
        return false;
    }

}