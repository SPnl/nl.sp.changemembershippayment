<script type="text/javascript">
    {literal}
    cj(function() {
        cj('#memberships tr.crm-membership').each(function(index) {
            var td = cj('#'+this.id+' td:last-child .btn-slide ul');
            var mid = this.id.replace('crm-membership_', '');
            var url = CRM.url('civicrm/contact/view/membershippayment', 'reset=1&cid={/literal}{$cid}{literal}&id='+mid);
            td.prepend('<li><a class="action-item" title="Change payment" href="'+url+'">Change payment</a></li>');
        });
    });
    {/literal}
</script>