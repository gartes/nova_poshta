document.addEventListener("DOMContentLoaded", function () {

    Joomla.loadOptions({
        GNZ11:{
            gnzlib_path_modules: "/libraries/GNZ11/assets/js/modules",
            Ajax:{
                'siteUrl' : Joomla.getOptions('siteUrl'), //php=> $doc->addScriptOptions('siteUrl',JUri::root());
                'csrf.token' : Joomla.getOptions('csrf.token'),
                'isClient' : false, // если отправка на администратора
            }
        }
    });

    var NovaPoshtaData = Joomla.getOptions('NovaPoshta-cart');
    var shipmentmethod_id = NovaPoshtaData.virtuemart_shipmentmethod_id;
    function cart_shipmentInit(){
        addListener()
    }

    function addListener() {
        var $ = jQuery ;
        $('body').on('change' , '#shipment_id_'+shipmentmethod_id , loadModal )
    }

    function loadWarehouses(evt) {
        var $ = jQuery ;
        var gnz11 = new GNZ11();
        var $sel =  $(this).find('option:selected') ;
        var AjaxSetting = {} ;
        var WarehousesText = $sel.text();
        var cityRef = $sel.val();
        var $warehouses_ind = $('[name*="warehouses_ind"]') ;
        if (cityRef.length){
            $warehouses_ind.val(1)
        }else{
            $warehouses_ind.val(0)
        }
        $warehouses_ind.trigger('change');
        $('[name="cityRef"]').val(cityRef);

        var data = {
            'option': 'com_ajax',
            'group' : 'vmshipment',
            'plugin': 'nova_pochta',
            'format': 'raw',
            'virtuemart_shipmentmethod_id': shipmentmethod_id ,
            'opt'   : {
                'task': 'loadWarehouses',
                'cityRef': cityRef ,
            }
        };
        // loadWarehouses
        gnz11.getModul('Ajax' , AjaxSetting ).then(function(Ajax) {
            Ajax.send(data).then(function (res) {
                if (!res.success){
                    alert('Error Load Warehouses Nova pochta');
                    return
                }
                var WarehousesEl = $('#novaposhta_warehouses') ;
                WarehousesEl.empty();
                $.each(res.data.WarehousesList,function (i,a) {


                    $('<option />', {
                        value : i,
                        text  : a ,
                    }).appendTo(WarehousesEl);





                });
                WarehousesEl.trigger('chosen:updated');


                 //  $('#novaposhta_warehouses').empty().append($(html)) ;




            }, function (err) {
                console.error(err)
            })
        })

    }
    /**
     * Загрузка модалки с выбором метода доставки
     */
    function loadModal() {
        var $ = jQuery ;
        var gnz11 = new GNZ11();
        var AjaxSetting = {} ;

        var data = {
            'option': 'com_ajax',
            'group' : 'vmshipment',
            'plugin': 'nova_pochta',
            'format': 'raw',
            'virtuemart_shipmentmethod_id': shipmentmethod_id ,
            'opt'   : {
                'task': 'getModalBody'
            }
        };

        gnz11.getModul('Ajax' , AjaxSetting ).then(function(Ajax){
            Ajax.send(data).then(function (res) {
                if (!res.success){
                    alert('Error form Nova pochta');
                    return
                }



                gnz11.__loadModul.Fancybox().then(function(a){
                    a.open(res.data , {

                        baseClass: 'nova_pochtaModalBody' ,
                        touch:!1 ,

                        beforeShow: function(){

                        },
                        afterShow: function(){
                            gnz11.checkBoxRadioInit();
                            gnz11.SHOWON.Init();
                            setTimeout(function () {
                                $('form#form-cartModalBody').addClass('is-init')
                            },200);

                            gnz11.__loadModul.Chosen().then(function (a) {
                                $('#cityText').on('change' , {event} , loadWarehouses ).chosen();
                                $('#novaposhta_warehouses').on('change' , {event} , function (){}).chosen();
                            });

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



    cart_shipmentInit();


});