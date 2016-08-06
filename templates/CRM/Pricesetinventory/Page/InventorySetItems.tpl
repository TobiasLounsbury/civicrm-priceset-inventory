<!--
<div id="help">
    {ts}{/ts}
</div>
//-->


{if $inventoryItems}
    <div id="inventory-set-list">
        {strip}
            {* handle enable/disable actions *}
            {include file="CRM/common/enableDisableApi.tpl"}
            {include file="CRM/common/jsortable.tpl"}
            <table id="options" class="display">
                <thead>
                <tr>
                    <th id="sortable">{ts}Id{/ts}</th>
                    <th>{ts}Title{/ts}</th>
                    <th>{ts}Price Field{/ts}</th>
                    <th>{ts}Quantity{/ts}</th>
                    <th>{ts}Active?{/ts}</th>
                    <th></th>
                </tr>
                </thead>
                {foreach from=$inventoryItems item=row}
                    <tr id="Inventroy_Set-{$row.id}" class="crm-entity crm-inventory_{$row.id} {cycle values="even-row,odd-row"} {if NOT $row.is_active}disabled{/if}">
                        <td class="crm-workflow">{$row.id}</td>
                        <td>{$row.title}</td>
                        <td>{$row.priceFieldName}</td>
                        <td>{$row.quantity}</td>
                        <td>{if NOT $row.is_active}No{else}Yes{/if}</td>
                        <td>
                            <a href="{crmURL p='civicrm/admin/inventory/item' q="reset=1&sid=`$sid`&id=`$row.id`"}">edit</a> | <a href="{crmURL p='#' q="reset=1&id=`$row.sid`"}">delete</a>
                        </td>
                    </tr>
                {/foreach}
            </table>
        {/strip}

    </div>

{else}
    <div class="messages status no-popup">
        <div class="icon inform-icon"></div>
        {ts}There are no Inventory Items yet.{/ts}
    </div>
{/if}


<p></p>
<a href="{crmURL p='civicrm/admin/inventory/item' q="reset=1&sid=`$sid`"}" class="button"><span><div class="icon add-icon"></div>{ts}Add Inventory Item{/ts}</span></a>
