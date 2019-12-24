window.adminMethodEdit = {
   init : function () {
       np_element_city.init( '#params_city_sender' ,  window.adminMethodEdit.onSelectCity );
    },
    /**
     * Событие вабо города из списка
     * @param RefCity - Ref Выбранного города
     */
    onSelectCity : function ( RefCity ) {
        Joomla.submitbutton('apply');
    }
};

document.addEventListener("GNZ11Loaded", function () {
    adminMethodEdit.init();
});

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

