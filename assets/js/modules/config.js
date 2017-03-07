import React from "react";
let PROD = !($('meta[name=environment]').attr('content') === 'development');
export default {
    TITLE: 'Unicorno',
    ROOT: PROD ? '/' : 'http://unicorno.bigdropinc.net/',
    ORIGIN: window.location.origin,
    API: PROD ? '/api/v1' : 'http://unicorno.bigdropinc.net/api/v1',
    // GOOGLE_ID: '1024910959328-61vmstk4jtu35b42ts9224b1h9bseoqh.apps.googleusercontent.com',
    GOOGLE_ID: $('meta[property="google:client_id"]').attr('content'),
    FB_ID: $('meta[property="fb:app_id"]').attr('content'),
    PROD: PROD,
    viaTwitter: 'UnicornoBR',
    afterTitle: ' | Unicorno',
    GOOGLE_AD: {
        client: "ca-pub-1665577023063217",
        slot: "7960662881"


        // client: 'ca-pub-1665577023063217',
        // slot: '9822563683'
    }
}


//182439365451957 - old facebook id



