/**
 * Created by vagenas on 13/11/2014.
 */
(function($){
    $input = $('input[data-key="'+carrierId+',"]');
    $tr = $input.closest('tr');
    $ndp = $tr.find('.delivery_option_logo').next();
    $ndp.html(
        '<strong>ACS Courier</strong><br>Παράδοση σε 1-3 εργάσιμες ημέρες<br>' +
        ' <strong>Παραλαβή από το πλησιέστερο κατάστημα:</strong> <br><a href="http://maps.google.com/?q='+encodeURIComponent(googleQ)+'" target="_blank">'+storeInfo.station_description+
        ', ' + storeInfo.station_address + '</a>, τηλ: '+storeInfo.station_phone
    );

})(jQuery);
