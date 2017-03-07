import { FB_ID} from '../modules/config'

$.ajaxSetup({cache: true});
$.getScript('//connect.facebook.net/pt_BR/sdk.js', function () {
    FB.init({
        appId: FB_ID,
        version: 'v2.5', // or v2.0, v2.1, v2.2, v2.3,
        xfbml: true
    });
});


