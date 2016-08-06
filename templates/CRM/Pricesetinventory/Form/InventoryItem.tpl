{* HEADER *}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

{* FIELD EXAMPLE: OPTION 2 (MANUAL LAYOUT)

  <div>
    <span>{$form.favorite_color.label}</span>
    <span>{$form.favorite_color.html}</span>
  </div>

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>


<script type="text/javascript">
{literal}

CRM.$(function($) {

    $("#field_value_id").closest(".crm-section").prop("id", "field_value_section");

    $("#field_id").change(function () {
        $("#field_value_id option").remove();
        if (Object.keys(CRM.Inventory.priceFieldValues[$(this).val()]).length > 1) {
            for(var v in CRM.Inventory.priceFieldValues[$(this).val()]) {
                $("#field_value_id").append("<option value='" + v + "'>" + CRM.Inventory.priceFieldValues[cj(this).val()][v] + "</option>");
            }
            $("#field_value_section").slideDown();
        } else {
            $("#field_value_section").slideUp();
        }
    });
    if ($("#field_value_id option").length < 1) {
        $("#field_value_section").hide();
    }



    //Change up the Image functionality
    $("#image_path").after("<img id='ImagePreview' /><br />");
    $("#image_path").hide();
    $("#ImagePreview").after("<br /><button id='ChangeImage'>Select Image</button>");
    $("#ImagePreview").attr('src', CRM.Inventory.ImagePath + $("#image_path").val());


    //This is functionality for the Image File Browser window
    $("#ChangeImage").click(function() {
        window.KCFinder = {
            callBack: function(url) {
                window.KCFinder = null;
                var purl = url.replace(CRM.Inventory.ImagePath, "");
                $("#ImagePreview").attr('src', CRM.Inventory.ImagePath + purl);
                $("#image_path").val(purl)
                //alert(url);
            }
        };

        window.open(CRM.config.resourceBase + 'packages/kcfinder/browse.php?cms=civicrm&type=images&langCode=en',
                'kcfinder_image', 'status=0, toolbar=0, location=0, menubar=0, ' +
                'directories=0, resizable=1, scrollbars=0, width=800, height=600');
        return false;
    });
});



{/literal}
</script>