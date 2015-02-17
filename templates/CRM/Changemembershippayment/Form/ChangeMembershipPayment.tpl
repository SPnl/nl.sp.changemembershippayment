<div class="crm-block crm-form-block">
  {if ($isChangeable)}
    <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="top"}
    </div>
    {foreach from=$elementNames item=elementName}
      <div class="crm-section">
        <div class="label">{$form.$elementName.label}</div>
        <div class="content">{$form.$elementName.html}</div>
        <div class="clear"></div>
      </div>
    {/foreach}
    <div class="crm-submit-buttons">
      {include file="CRM/common/formButtons.tpl" location="bottom"}
    </div>
  {else}
    <div class="messages status no-popup">
      You cannot change membership payment because there are no future contributions for this membership. You should use the renewal functionality of the membership.
    </div>
  {/if}

</div>
