NP_order_edit_INIT = function () {
    SenderCityText_INIT();
};

SenderCityText_INIT = function(){
    var $ = jQuery;
    var anchor = $('<div />', {id:'SenderCityTextConteyner',calss:'conteynerUiAc',});
    var $T = $( '[name="novaposhta[SenderCityText]"]' );
    var p = $T.parent() ;
    p.append(anchor);

    $T.autocomplete({
        minLength : 1 ,
        appendTo : '#SenderCityTextConteyner' ,
        html : true ,
        source : sourceNovaposhtaCityText ,
        select : function(event, ui){

            console.log( ui.item )
            console.log( ui.item.DeliveryCity )

            $('[name="novaposhta[CitySender]"]').val( ui.item.DeliveryCity );
            loadWarehouses( $('[name="novaposhta[SenderAddress]"]') , ui.item.Ref  );

            console.log(ui.item.Ref)
        },
        // autocompleteselect : selectNovaposhtaCityText ,
        autocompletechange : changeNovaposhtaCityText ,
    })


};


NP_order_edit_save = function(event){
    event.preventDefault();
    var NovaPoshtaData = Joomla.getOptions('NpSettingPlg');


    var $ = jQuery ;
    var gnz11 = new GNZ11();

    /*var b = $('#cartModalBody')
    b.wrap('form');
    var fdata = b.parent().serialize();*/

    var fdata = $("#cartModalBody").find("select, textarea, input").serialize();


    var data = {
        'option': (NovaPoshtaData.administrator?'com_virtuemart':'com_ajax'),
        'group' : 'vmshipment',
        'plugin': 'nova_pochta',
        'format': 'json',
        'virtuemart_shipmentmethod_id': NovaPoshtaData.virtuemart_shipmentmethod_id ,
        'virtuemart_order_id': NovaPoshtaData.virtuemart_order_id ,
        'opt'   : {
            'task': 'adminSaveOrder',
            'cityRef': $('#cityRef').val() ,
        }
    };
    if (NovaPoshtaData.administrator){
        data.view = 'nova_pochta_extended' ;
        data.task = 'adminSaveOrder' ;
        data.form = fdata;
    }

    AjaxSetting={};
    gnz11.getModul('Ajax' , AjaxSetting ).then(function(Ajax) {
        Ajax.send(data).then(function (res) {
            if (res.success){
                Ajax.renderNoty(res.messages.message[0],'success')
            }else{
                Ajax.renderNoty(res.message,'error')
            }
        })
    });
};




document.addEventListener("DOMContentLoaded", function () {
    NP_order_edit_INIT();
})























