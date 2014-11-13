/**
 * Created by vagenas on 13/11/2014.
 */
(function($){
    $input = $('input[data-key="'+dpCarrierId+',"]');
    $tr = $input.closest('tr');
    $dp = $tr.find('.delivery_option_logo').next();

    $input = $('input[data-key="'+carrierId+',"]');
    $tr = $input.closest('tr');
    $ndp = $tr.find('.delivery_option_logo').next();
    $ndp.html(
        '<strong>ACS Courier</strong>' +
        ' Παραλαβή από το κατάστημα: <br><a href="http://maps.google.com/?q='+encodeURIComponent(googleQ)+'" target="_blank">'+storeInfo.station_description+
        ', ' + storeInfo.station_address + '</a>, τηλ: '+storeInfo.station_phone
    );
    console.log(storeInfo);
})(jQuery);