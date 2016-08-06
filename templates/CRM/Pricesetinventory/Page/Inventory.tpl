<!--
<div id="help">
    {ts}{/ts}
</div>
//-->


{if $inventorySets}
    <div id="inventory-set-list">
        {strip}
            {* handle enable/disable actions *}
            {include file="CRM/common/enableDisableApi.tpl"}
            {include file="CRM/common/jsortable.tpl"}
            <table id="options" class="display">
                <thead>
                <tr>
                    <th id="sortable">{ts}Id{/ts}</th>
                    <th>{ts}Name{/ts}</th>
                    <th>{ts}Price Set{/ts}</th>
                    <th>{ts}Active?{/ts}</th>
                    <th></th>
                </tr>
                </thead>
                {foreach from=$inventorySets item=row}
                    <tr id="Inventroy_Set-{$row.id}" class="crm-entity crm-inventory_{$row.id} {cycle values="even-row,odd-row"} {if NOT $row.is_active}disabled{/if}">
                        <td class="crm-workflow">{$row.sid}</td>
                        <td>{$row.name}</td>
                        <td>{$row.priceSetName} ({$row.price_set_id})</td>
                        <td>{if NOT $row.is_active}No{else}Yes{/if}</td>
                        <td>
                            <a href="{crmURL p='civicrm/admin/inventory/set' q="reset=1&sid=`$row.sid`"}">edit</a> | <a href="{crmURL p='civicrm/admin/inventory/items' q="reset=1&sid=`$row.sid`"}">items</a>
                        </td>
                    </tr>
                {/foreach}
            </table>
        {/strip}

    </div>

{else}
    <div class="messages status no-popup">
        <div class="icon inform-icon"></div>
        {ts}There are no Inventory Sets yet.{/ts}
    </div>
{/if}


<p></p>
<a href="{crmURL p='civicrm/admin/inventory/set' q="reset=1"}" class="button"><span><div class="icon add-icon"></div>{ts}Add Inventory Set{/ts}</span></a>
