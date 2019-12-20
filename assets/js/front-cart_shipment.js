document.addEventListener("DOMContentLoaded", function () {



    var NovaPoshtaData = Joomla.getOptions('NpSettingPlg');
    var shipmentmethod_id = NovaPoshtaData.virtuemart_shipmentmethod_id;

    Joomla.loadOptions({
        GNZ11:{
            gnzlib_path_modules: "/libraries/GNZ11/assets/js/modules",
            Ajax:{
                'siteUrl' : Joomla.getOptions('siteUrl'), //php=> $doc->addScriptOptions('siteUrl',JUri::root());
                'csrf.token' : Joomla.getOptions('csrf.token'),
                'isClient' : (!!NovaPoshtaData.administrator), // если отправка на администратора
            }
        }
    });


    function cart_shipmentInit(){
        var $ = jQuery ;
        addListener();

        if (NovaPoshtaData.administrator){
            document.addEventListener("GNZ11Loaded", function () {
                cityText_init();
            });
        }else{
            // Проверить отмеченный чекбокс и снять если отмеченно
            check_checked()
        }



    }

    function addListener() {
        var $ = jQuery ;
        var $b = $('body');

        $b.on('change', '[name="virtuemart_shipmentmethod_id"]' , function () {
            var v = $(this).val();
            if (v !== shipmentmethod_id ){
                $('#cartModalBody').remove();
            }
        });

        $b.on('change' , '#shipment_id_'+shipmentmethod_id , loadModal )
            .on('change' , '[name="novaposhta[serviceType]"]' , stritAutocomplite );




        // Включить поддержку атозаполнения улиц на стороне админа
        if (NovaPoshtaData.administrator){
            var type = +$('[name="novaposhta[serviceType]"]:checked').val();
            if (!type){
                document.addEventListener("GNZ11Loaded", function () {
                    stritAutocomplite();
                });
            }
        }
    }

    /**
     * Проверить отмеченный чекбокс и снять если отмеченно
     */
    function check_checked() {
        var $ = jQuery ;
        var np_el = $('#shipment_id_'+shipmentmethod_id) ;
        if (!np_el[0]){
            var I = setInterval(function () {
                np_el = $('#shipment_id_'+shipmentmethod_id) ;
                if (np_el[0]){
                    clearInterval(I);
                    if (np_el.prop('checked')){
                        np_el.prop('checked' , false )
                    }
                }
            },500);
        }
    }

    /*
	* Поиск Улицы
	*/
    function stritAutocomplite() {
        var $ = jQuery;
        var gnz11 = new GNZ11();
        gnz11.__loadModul.Ui().then(function (a) {
            var boxData = $('#cartModalBody');
            var autocompleteInp = $(boxData).find('#novaposhta_recipientAddressName');
            var anchor = $('<div />', {id:'streetConteynerAutocomp',calss:'conteynerUiAc',});
            $(autocompleteInp).after(anchor);

            $( autocompleteInp ).autocomplete({
                minLength: 1,
                appendTo: "#streetConteynerAutocomp",
                html:true,
                create: function( event, ui ) {$(autocompleteInp).after(
                    $('<i />',{
                        class:'auto_control clean icon-cancel',
                        click:function(){
                            $(this).prev('input').val('');

                        }
                    })
                );
                },
                source: function( request, response ) {
                    var params ={
                        "modelName": "Address",
                        "calledMethod": "getStreet",
                        "methodProperties": {
                            "CityRef": boxData.find('[name="cityRef"]').val(),
                            "FindByString": request.term
                        }
                    };
                    queryNovaposhta(params).done(function (sdata) {
                        var obj = [];
                        $.each(sdata.data, function  (i,street){

                            obj[i] = {
                                label : street.StreetsType+' '+street.Description,
                                value : street.StreetsType+' '+street.Description ,
                                streetRef:street.Ref ,
                                streetStreetsType:street.StreetsType ,
                                streetStreetsTypeRef:street.StreetsTypeRef
                            };
                        });
                        response( obj );
                    }).fail(function () {
                        alert('error');
                    });
                },
                select: function( event, ui ) {
                    $('#novaposhta_streetRef').val(ui.item.streetRef );
                    $('#novaposhta_streetsType').val(ui.item.streetStreetsType );
                    $('#novaposhta_streetsTypeRef').val(ui.item.streetStreetsTypeRef );
                },
                change: function(event, ui) {
                    console.log(ui)
                    },
            });
        })
    }

    var VMUPS = { Novaposhta :{} };





    /**
     * Загрузка модалки с выбором метода доставки
     */
    function loadModal() {
        var $ = jQuery ;
        var gnz11 = new GNZ11();
        var AjaxSetting = {} ;
        var NpSettingPlg = Joomla.getOptions('NpSettingPlg');
        var data = {
            'option': 'com_ajax',
            'group' : 'vmshipment',
            'plugin': 'nova_pochta',
            'format': 'raw',
            'virtuemart_shipmentmethod_id': shipmentmethod_id ,
            'city_celect_style': NpSettingPlg.city_celect_style ,
            'opt'   : {
                'task': 'getModalBody'
            }
        };

        $('#cartModalBody').remove();


        gnz11.getModul('Ajax' , AjaxSetting ).then(function(Ajax){
            Ajax.send(data).then(function (res) {
                if (!res.success){
                    alert('Error form Nova pochta');
                    return
                }

                if (!+NpSettingPlg.form_style){
                    $('#shipment_id_'+shipmentmethod_id ).parent().addClass('np_Ext').append(res.data)
                    gnz11.checkBoxRadioInit();
                    gnz11.SHOWON.Init();
                    setChosen();
                    $('[name="novaposhta[serviceType]"]').on('change' , getCost );
                    $('body').on('click' , 'i.auto_control.clean' , function () {
                        jQuery('[name="cityRef"]').val('');
                    });


                    return;
                }
                gnz11.__loadModul.Fancybox().then(function(a){
                    a.open(res.data , {
                        baseClass: 'nova_pochtaModalBody' ,
                        touch:!1 ,
                        beforeShow: function(){ },
                        afterShow: function(){
                            gnz11.checkBoxRadioInit();
                            gnz11.SHOWON.Init();

                            setTimeout(function () {
                                $('form#form-cartModalBody').addClass('is-init')
                            },200);

                            setChosen();
                        },
                        beforeClose : function( instance, current , e ) {}
                    })
                });
            },function (err) {
                console.log(err)
            });
            console.log(Ajax)
        },function(err){
            console.error(err)
        });
    }


    function setChosen() {
        var $ = jQuery ;
        var gnz11 = new GNZ11();
        var NpSettingPlg = Joomla.getOptions('NpSettingPlg') ;

        console.log(NpSettingPlg )
        gnz11.__loadModul.Chosen().then(function (a) {
            if ( +NpSettingPlg.city_celect_style ) {
                $('#cityText').on('change' , {event} , loadWarehouses ).chosen();
            }else {
                cityText_init();
            }
            $('[name="novaposhta[warehouses]"]').on('change' , {event} , function (){}).chosen();
        });
    }


    function OpenModal() {}

    function cityText_init() {
        var $ = jQuery ;
        var gnz11 = new GNZ11();
        gnz11.__loadModul.Ui().then(function (a) {
            VMUPS.Novaposhta.searchSettlements();
        });


    }

    cart_shipmentInit();


    VMUPS.keyup = function (ElAutocomplite) {
        var $ = jQuery;
        var Box = $(ElAutocomplite).closest('.VMUPS_PLG');
        var li = $(Box).find('.ui-autocomplete li');
        if (li.length === 1) {
            var attr_name = $(li).find('div[attr-name]').attr('attr-name');
            if (attr_name.toUpperCase() === $(ElAutocomplite).val().toUpperCase()) {
                $(li).find('a').click();
                $(ElAutocomplite).blur();
            }
        } // end if

    }; // end function


    VMUPS.Novaposhta.searchSettlements = function  ( ){
        var $ = jQuery;
        var anchor = $('<div />', {id:'tlementsConteyner',calss:'conteynerUiAc',});

        var $T = $( '#cityText' );
        var p = $T.parent() ;
        p.append(anchor);

        $T.autocomplete({
            minLength : 1 ,
            appendTo : '#tlementsConteyner' ,
            html : true ,
            source : sourceNovaposhtaCityText ,
            select : function(event, ui){
                $('#cityRef').val(ui.item.DeliveryCity );
                $('#novaposhta_Ref').val(ui.item.Ref );
                loadWarehouses();

            },

            autocompletechange : changeNovaposhtaCityText ,
        })

    };// end function

});


/**
 * Загрузка складов в городе.
 * @param Elem - Елемент в которы загружить склады
 */
function loadWarehouses(  Elem   ) {
    var $ = jQuery ;
    var gnz11 = new GNZ11();
    var NovaPoshtaData = Joomla.getOptions('NpSettingPlg');
    var cityRef;
    var WarehousesEl ;

    // Если стиль выбора города "TEXT - Autocomplite"
    if (NovaPoshtaData.city_celect_style){
        switch ( $(Elem).attr('id')  ) {
            case 'novaposhtaSenderAddress':
                cityRef = $('[name="novaposhta[CitySender]"]').val();
                WarehousesEl = $(Elem) ;
                break ;

            default:
                cityRef = $('#cityRef').val();
                WarehousesEl = $('[name="novaposhta[warehouses]"]') ;
                var $warehouses_ind = $('[name*="warehouses_ind"]') ;
        }

        console.log( Elem )
    }else {
        cityRef =  $(this).find('option:selected').val() ;
        WarehousesEl = $('[name="novaposhta[warehouses]"]') ;
        $('[name="cityRef"]').val(cityRef);

    }

    var AjaxSetting = {} ;

    var data = {
        'option': (NovaPoshtaData.administrator?'com_virtuemart':'com_ajax'),
        'group' : 'vmshipment',
        'plugin': 'nova_pochta',
        'format': 'json',
        'virtuemart_shipmentmethod_id': NovaPoshtaData.virtuemart_shipmentmethod_id ,
        'opt'   : {
            'task': 'loadWarehouses',
            'cityRef': cityRef ,
        }
    };
    if (NovaPoshtaData.administrator){
        data.view = 'nova_pochta_extended' ;
        data.task = 'loadWarehouses' ;
    }

    // loadWarehouses
    gnz11.getModul('Ajax' , AjaxSetting ).then(function(Ajax) {
        Ajax.send(data).then(function (res) {
            if (!res.success){
                alert('Error Load Warehouses Nova pochta');
                return
            }
            WarehousesEl.empty();
            $.each(res.data.WarehousesList,function (i,a) {
                $('<option />', { value : i, text  : a , }).appendTo(WarehousesEl);
            });


            WarehousesEl.trigger('chosen:updated').trigger("liszt:updated");
            getCost();
        }, function (err) {
            console.error(err)
        })
    })
}

/**
 * Расчет стоимости доставки
 */
getCost = function(){
    var $ = jQuery ;
    var gnz11 = new GNZ11();
    var NovaPoshtaData = Joomla.getOptions('NpSettingPlg');
    var cityRef =  $('[name="cityRef"]').val() ;
    var serviceType =  $('[name="novaposhta[serviceType]"]:checked').val() ;

    $('#documentPrice').remove();
    if (!cityRef) {

        return
    }

    var data = {
        'option': (NovaPoshtaData.administrator?'com_virtuemart':'com_ajax'),
        'group' : 'vmshipment',
        'plugin': 'nova_pochta',
        'format': 'json',
        'virtuemart_shipmentmethod_id': NovaPoshtaData.virtuemart_shipmentmethod_id ,
        'opt'   : {
            'task': 'getCost',
            'cityRef': cityRef ,
            'serviceType': serviceType ,
        }
    };

    var AjaxSetting = {} ;
    gnz11.getModul('Ajax' , AjaxSetting ).then(function(Ajax) {
        Ajax.send(data).then(function (res) {
            $('#cartModalBody').append(res.data);
            console.log(res.data)
        })
    })


};


/*
 *Отправить запрос к API новой почты
 *
 */
queryNovaposhta = function  (params ){
    var $ = jQuery;
    return $.ajax({
        url: 'https://api.novaposhta.ua/v2.0/json/?' + $.param(params),
        beforeSend:  function (xhrObj) {
            xhrObj.setRequestHeader('Content-Type', 'application/json');
            return Number;
        },
        type: 'POST',
        dataType: 'jsonp',
        data: '{body}'
    })
};

/**
 * Создание запроса для автозаполнителя городов
 * @param request
 * @param response
 */
function sourceNovaposhtaCityText (request, response){
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
        response( obj );
    }).fail(function () {alert('error');});
}// end function

/**
 * событие автокомплита
 * @param event
 * @param ui
 */
function	changeNovaposhtaCityText( event, ui ){
    console.log( ui.item )

    // VMUPS.SaveShipmentmethod();
}// end function
















