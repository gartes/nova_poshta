window.np_element_city = {
    init : function ( inputText ) {
        var $ = jQuery;
        var inpText = $(inputText) ;
        var id = inpText.attr('id') ;
        var parent = inpText.parent() ;
        var id_Conteyner = id +'Conteyner' ;
        var id_REF = id +'_ref' ;

        var anchor = $('<div />', {id: id_Conteyner ,calss:'conteynerUiAc',});
        parent.append(anchor);

        var gnz11 = new GNZ11();
        gnz11.__loadModul.Ui().then(function (a) {
            inpText.autocomplete({
                minLength : 1 ,
                appendTo : '#' + id_Conteyner ,
                html : true ,

                source : np_element_city.source ,

                select : function(event, ui){
                    inpText.val(ui.item.DeliveryCity );
                    $('#'+id_REF ).val(ui.item.Ref );
                   //  loadWarehouses();
                    console.log(ui.item.Ref)
                },
               //  autocompletechange : changeNovaposhtaCityText ,
            })
        });
    } ,
    source : function (request, response) {
        var $ = jQuery;
        var params ={
            "modelName": "Address",
            "calledMethod": "searchSettlements",
            "methodProperties": {
                "CityName": request.term,
                "Limit": 20
            },
        };
        queryNovaposhta(params).done(function(sdata){
            var obj = [];
            $.each(sdata.data, function  (i,arr){
                $.each(arr.Addresses , function  (ind,punct){
                    var Region = punct.Region+' р-н ';
                    if( punct.SettlementTypeCode === 'м.' ){
                        Region = '';
                    } // end if
                    var countWarehouses = '<div attr-name="'+punct.MainDescription+'" class="countWarehouses">'+punct.Warehouses+' отд.</div>';
                    if(punct.Warehouses===0){
                        countWarehouses = ' <div class="countWarehouses">(курьерская доставка)</div>'
                    } // end if
                    var area = ' - '+punct.Area+' обл.,';
                    if( !punct.Area ){
                        area='';
                    } // end if
                    obj[ind] = {
                        cityName :punct.MainDescription ,
                        label : punct.MainDescription + area + Region /*+ countWarehouses*/ ,
                        value : punct.MainDescription + area ,
                        DeliveryCity: punct.DeliveryCity ,
                        SettlementRef:punct.Ref,
                        Ref:punct.Ref ,
                        countWarehouses:punct.Warehouses
                    };
                })// end function
            });// end function


            console.log( obj ) ;
            response( obj );
        }).fail(function () {alert('error');});
    }
};
document.addEventListener("DOMContentLoaded", function () {

})

