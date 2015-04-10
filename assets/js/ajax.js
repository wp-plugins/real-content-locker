function jsTweet() {

    var urlTW = "https://twitter.com/intent/tweet?text=Text&url=http://my_url.com";
    window.open(urlTW, "", "toolbar=0, status=0, width=650, height=360");

}


jQuery(document).ready(function($){

    //from the href="#"
    jQuery('.facebook-realcontentlocker a').on('click',function(event){
        //do something
        //prevent the click as is passed to the function as an event
        event.preventDefault();
        
        return false;
    });
    /*
    $(".ilenvideolock").on('click',function(){

        $(this).find('.ilenboxshare').css("display","block");
        $(this).find('.ilenboxshare').css("cursor","default");

    });*/


    // ilenvideolock twitter
    if (typeof twttr !== 'undefined') {
        twttr.ready(function(twttr) {
            twttr.events.bind('tweet', function(event) {
                console.log(new Date(), "Sweett, tweet callback: ", event);
                var id_twitter_content = $("#"+event.target.parentElement.className);
                //alert($(id_twitter_content).attr("class"));
                //alert($(id_twitter_content).attr("id"));
                var twitter_id_post = $(id_twitter_content).attr("data-id-post");
                var twitter_url = $(id_twitter_content).attr("data-url");
                var twitter_hash = $(id_twitter_content).attr("data-hash");

                //alert('tweet!!! id post:'+ twitter_id_post+' url: '+twitter_url+' hash: '+twitter_hash);
                ajax_shared_complete_content( twitter_id_post, twitter_url, twitter_hash, 'twitter' );
            });
        });
    }


    // hover google plus trigger events
    $('.google-plus-ilenvideolock').on('hover',function(){
        $(this).children("#igp-post-id").val( $(this).attr("data-id-post") );
        $(this).children("#igp-post-url").val( $(this).attr("data-url") );
        $(this).children("#igp-post-hash").val( $(this).attr("data-hash") );
    });

    // hover linkedin plus trigger events
    $('.linkedin-plus-ilenvideolock').on('hover',function(){
        $(this).children("#ile-post-id").val( $(this).attr("data-id-post") );
        $(this).children("#ile-post-url").val( $(this).attr("data-url") );
        $(this).children("#ile-post-hash").val( $(this).attr("data-hash") );
    });

 
});



function unlocker_fb( post_id, url_path, id_hash, app_id ){
    var $ = jQuery;
 

    console.log( url_path );

    var string_app_id = "";
    
    if( app_id ){
        string_app_id = "&app_id="+app_id;
    }
    /*if( isMobile() && AjaxContent.facebook_api ){
        alert('entro:'+AjaxContent.facebook_api);
        window.fbAsyncInit = function() {
            FB.init({
              appId      : '674156459272075',
              xfbml      : true,
              version    : 'v2.2'
            });
        };
    }*/
    //https://m.facebook.com/dialog/feed?app_id&display=touch&e2e={}&image=http://farandulaecuatoriana.com/wp-content/uploads/2014/11/Efrain-Ruales-y-Michela-Pincay-estarian-juntos-300x177.jpg&link=http://farandulaecuatoriana.com/efrain-ruales-y-michela-pincay-estan-juntos&locale=en_US&next=http://static.ak.facebook.com/connect/xd_arbiter/QjK2hWv6uak.js?version=41#cb=f14fce2aa4&domain=farandulaecuatoriana.com&origin=http%3A%2F%2Ffarandulaecuatoriana.com%2Ff1b09d2528&relation=opener&frame=f2c80e21a&result=%22xxRESULTTOKENxx%22&sdk=joey&pnref=story
    FB.ui({ 
        //method: isMobile() ? 'feed' : '../sharer/sharer.php?u=' +encodeURIComponent(document.URL)+ '&t=&pass=',
        //method: isMobile() ? 'share' : '../sharer/sharer.php?u=' +encodeURIComponent(document.URL)+ '&t=&pass=app_id=261667905712',
        method: isMobile()? 'feed': '../../sharer/sharer.php?u=' +encodeURIComponent(document.URL)+ '&t=&pass='+string_app_id,
        link : document.URL/*,
        image : $('.ilenvideolock_img_'+id_hash).attr('src')*/
    }, function (response) {
        if( response ){
            ajax_shared_complete_content( post_id, url_path, id_hash, 'facebook' );
        }else{
            null; // upts!
        }
    });

    
}

function unlocker_go(params) {
    //console.log(new Date(), " google go: ", params.state);
    var $ = jQuery;
    if( "on" == params.state )
    {
        //alert( $("#igp-post-id").val() + " " + $("#igp-post-url").val() + " " + $("#igp-post-hash").val()  );
        ajax_shared_complete_content( $("#igp-post-id").val(),  $("#igp-post-url").val() , $("#igp-post-hash").val() , 'googleplus');
    }
}

function unlocker_go_close(params) {
    //console.log(new Date(), " google go close: ", params,+ " tt:"+tt);
    var $ = jQuery;
    if( "confirm" == params.type )
    {
        //alert( $("#igp-post-id").val() + " " + $("#igp-post-url").val() + " " + $("#igp-post-hash").val()  );
        ajax_shared_complete_content( $("#igp-post-id").val(),  $("#igp-post-url").val() , $("#igp-post-hash").val(), 'googleplus' );
    }
}


function ilenvideolock_linkedin( post_id, url_path, id_hash ) {

    var $ = jQuery;
    ajax_shared_complete_content( $("#igp-post-id").val(),  $("#igp-post-url").val() , $("#igp-post-hash").val(), 'linkedin' );

}

function ajax_shared_complete_content( post_id, url_path, id_hash, social ){

    var $ = jQuery;
 
    $('.realcontentlocker_id_'+id_hash).find(".realcontentlocker__unlock").remove();
    $('.realcontentlocker_id_'+id_hash).find(".realcontentlocker__content").css("display","block");
    $('.realcontentlocker_id_'+id_hash).removeClass("realcontentlocker");
    
    jQuery.ajax({
        type : "post",
        dataType: "json",
        url : AjaxContent.ajaxurl,
        data : {
                'action': "ajax-content", 
                'post_id' : post_id, 
                'social':social,
                'nonce': AjaxContent.nonce
               },
        success: function(response) {
            console.log("success ok "+response.success+" - post id: "+post_id+"  -  nonce "+AjaxContent.nonce+" - time: "+response.times);
        },
        error: function (jqXHR, textStatus, errorThrown, responseText){
            console.log("error: "+ responseText + " errorThrown:"+errorThrown+ " jqXHR:"+jqXHR+ " url:"+AjaxContent.ajaxurl+ "data [ post_id:"+post_id+", social:"+social+"]");
        }
    });

}