/**
 * Created by tobias on 3/9/15.
 */
CRM.$(function ($) {

  function isZeroQuantity(qty) {
    return (qty === 0 || qty === "0");
  }

  function getInformationPane(itemId, item) {
    var itemContent = $("<div id='inventory_" + itemId + "' class='inventory-section inventory-section-default  inventory-" + CRM.Inventory.Items[item].type.toLowerCase() + "-section'></div>");
    itemContent.append("<h3>" + CRM.Inventory.Items[item].title + "</h3>");
    var img = "";
    if (CRM.Inventory.Items[item].image_data) {
      //alert("Image Data");
    }
    if (CRM.Inventory.Items[item].image_path) {
      img = "<img class='inventory-image' src='" + CRM.Inventory.ImagePath + CRM.Inventory.Items[item].image_path + "' />";
    }
    itemContent.append("<div class='inventory-content'>" + img + CRM.Inventory.Items[item].description + "<div class='clear'></div></div>");
    return itemContent;
  }

  function doForSelect(item) {
    var button = doForAll(item, $("#price_" + CRM.Inventory.Items[item].field_id).closest(".content"), "append");
    //Check for and mark sold out items
    if(isZeroQuantity(CRM.Inventory.Items[item].quantity)) {
      $("#price_" + CRM.Inventory.Items[item].field_id + " option[value='" + CRM.Inventory.Items[item].field_value_id + "']").prop('disabled',true).append("<span class='inventory-sold-out'> - " + ts('SOLD OUT')  + "</span>");
    }
  }

  function doForCheckbox(item) {
    var button = doForAll(item, $("#price_" + CRM.Inventory.Items[item].field_id + "_" + CRM.Inventory.Items[item].field_value_id).parent(), "after");
    $("#price_" + CRM.Inventory.Items[item].field_id + "_" + CRM.Inventory.Items[item].field_value_id).parent().append(button);
    //Check for and mark sold out items
    if(isZeroQuantity(CRM.Inventory.Items[item].quantity)) {
      $("#price_" + CRM.Inventory.Items[item].field_id + "_" + CRM.Inventory.Items[item].field_value_id).prop('disabled',true).next().addClass("disabled").append("<span class='inventory-sold-out'> - " + ts('SOLD OUT')  + "</span>");
    }
  }
  function doForRadio(item) {
    var button = doForAll(item, $("[name='price_" + CRM.Inventory.Items[item].field_id + "'][value=" + CRM.Inventory.Items[item].field_value_id + "]").parent(), "after");
    $("#price_" + CRM.Inventory.Items[item].field_id + "_" + CRM.Inventory.Items[item].field_value_id).parent().append(button);
    //Check for and mark sold out items
    if(isZeroQuantity(CRM.Inventory.Items[item].quantity)) {
      $("[name='price_" + CRM.Inventory.Items[item].field_id + "'][value=" + CRM.Inventory.Items[item].field_value_id + "]").prop('disabled',true).next().addClass("disabled").append("<span class='inventory-sold-out'> - " + ts('SOLD OUT')  + "</span>");
    }
  }

  function doForText(item) {
    var button = doForAll(item, $("#price_" + CRM.Inventory.Items[item].field_id).closest(".content"), "append");
    $("#inventory_" + CRM.Inventory.Items[item].field_id).before(button);
    //Check for and mark sold out items
    if(isZeroQuantity(CRM.Inventory.Items[item].quantity)) {
      $("#price_" + CRM.Inventory.Items[item].field_id).prop('disabled',true).after("<span class='inventory-sold-out'> - " + ts('SOLD OUT')  + "</span>");
    }
  }

  function doForAll(item, place, method) {
    var itemId = CRM.Inventory.Items[item].field_id;
    if(CRM.Inventory.Items[item].field_value_id) {
      itemId = itemId + "_"  + CRM.Inventory.Items[item].field_value_id;
    }

    var itemContent;
    if(typeof CRM.Inventory.CustomInformationPane == "function") {
      itemContent = CRM.Inventory.CustomInformationPane(itemId, item);
    } else {
      itemContent = getInformationPane(itemId, item);
    }

    switch(method) {
      case 'before':
        $(place).before(itemContent);
        break;
      case 'append':
        $(place).append(itemContent);
        break;
      case 'after':
        $(place).after(itemContent);
        break;
      case 'prepend':
        $(place).prepend(itemContent);
        break;

    }
    if (CRM.Inventory.Items[item].default_open != 1) {
      itemContent.hide();
    }
    return "<a href='#' class='button ToggleInventory' data-target='"+itemId+"' title='Info'><span><div class='icon inform-icon'></div></span><div class='inventoryMoreInfo'><div class='inventoryMoreInfoText'>" + ts('More Information') + "</div></div></a>";
  }

  function inv_sort(itms) {
    var n = {},r=[];
    for(var i = 0; i < itms.length; i++)
    {
      if (!n[itms[i]])
      {
        n[itms[i]] = true;
        r.push(itms[i]);
      }
    }
    return r;
  }

  if (CRM.Inventory.Items.length > 0) {
    var inv_selects = [],inv_checks = [],inv_radios = [];
    for (var i in CRM.Inventory.Items) {
      switch (CRM.Inventory.Items[i].type) {
        case "Select":
          doForSelect(i);
          inv_selects.push(i);
          break;
        case "Radio":
          doForRadio(i);
          inv_radios.push(i);
          break;
        case "checkbox":
        case "Checkbox":
        case "CheckBox":
          doForCheckbox(i);
          inv_checks.push(i);
          break;
        case "Text":
          doForText(i);
          break;
      }
    }


    inv_selects = inv_sort(inv_selects);
    inv_radios = inv_sort(inv_radios);
    inv_checks = inv_sort(inv_checks);


    $.each(inv_selects, function(i, v) {
      $("#price_" + CRM.Inventory.Items[v].field_id).change(function() {
        var trgt = $("#inventory_" + CRM.Inventory.Items[v].field_id + "_" + $(this).val());
        $(this).closest(".crm-section").find(".inventory-section").not(trgt).slideUp();
        trgt.slideDown()
      }).closest(".crm-section").find(".inventory-section").hide();
      $("#inventory_" + CRM.Inventory.Items[v].field_id + "_" + $("#price_" + CRM.Inventory.Items[v].field_id).val()).show();
    });
    $.each(inv_radios, function(i, v) {
      //Is there anything we need to do with inventory when a different option is selected
    });
    $.each(inv_checks, function(i, v) {
      //Is there anything we need to do with inventory when a different option is selected
    });


    //Add click functionality to info buttons
    $("a.ToggleInventory").click(function () {
      $("#inventory_" + $(this).attr("data-target")).slideToggle();
      return false;
    }).mouseover(function () {
      var obj = $(this).find(".inventoryMoreInfoText");
      $(this).find(".inventoryMoreInfo").stop().animate({width: obj.outerWidth()}, "slow");
    }).mouseout(function() {
      $(this).find(".inventoryMoreInfo").stop().animate({width: 0}, "slow");
    });

    //$(".content .price-set-row:even").css("backgroundColor", "RGBA(128,128,128,0.2)");

    $("body").append("<div id='inventory-dialog' style='display: none;'><img id='inventory-fullImage' src='' /></div>");
    $("img.inventory-image").click(function() {
      $("#inventory-fullImage").attr("src", $(this).attr("src"));
      $("#inventory-dialog").dialog({
        width: 'auto',
        closeOnEscape: true,
        modal: true,
        hide: {
          effect: "drop",
          direction: "up"
        },
        show: {
          effect: "drop",
          direction: "up"
        },
        resizable: false,
        open: function() {
          $(".ui-widget-overlay").click(function() {
            $("#inventory-dialog").dialog("close");
          });
        }
      });
      $(".ui-dialog-titlebar").hide();
    });

    $("#inventory-dialog").click(function() {
      $("#inventory-dialog").dialog("close");
    });
  }
});