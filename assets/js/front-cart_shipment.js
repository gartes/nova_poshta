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

    function loadModal() {
        var gnz11 = new GNZ11();
        var AjaxSetting = {} ;

        var data = {
            'option': 'com_ajax',
            'group' : 'vmshipment',
            'plugin': 'nova_pochta',
            'format': 'raw',
            'opt'   : {
                'task': 'getModalBody'
            }
        };

        gnz11.getModul('Ajax' , AjaxSetting ).then(function(Ajax){
            Ajax.send(data).then(function (res) {
                if (!res.success){
                    alert('Error form Nova pochta')
                    return
                }

                console.log(res) ;

                gnz11.__loadModul.Fancybox().then(function(a){
                    a.open(res.data)
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