//const ReactScriptLoaderMixin = require('../services/ReactScriptLoader.js').ReactScriptLoaderMixin;
import { FB_ID } from '../modules/config'
import PostList from './asideAdsList';

/*
 window.fbAsyncInit = function() {
 FB.init({
 appId      : 'your-app-id',
 xfbml      : true,
 version    : 'v2.5'
 });
 };

 (function(d, s, id){
 var js, fjs = d.getElementsByTagName(s)[0];
 if (d.getElementById(id)) {return;}
 js = d.createElement(s); js.id = id;
 js.src = "//connect.facebook.net/en_US/sdk.js";
 fjs.parentNode.insertBefore(js, fjs);
 }(document, 'script', 'facebook-jssdk'));*/

//$.ajaxSetup({cache: true});
//$.getScript('//connect.facebook.net/en_US/sdk.js', function () {
//    FB.init({
//        appId: '182439365451957',
//        version: 'v2.5' // or v2.0, v2.1, v2.2, v2.3
//    });
//    //$('#loginbutton,#feedbutton').removeAttr('disabled');
//    //FB.getLoginStatus(function (a,b,c) {
//    //    console.log(a,b,c);
//    //});
//});

export default React.createClass({

    //mixins: [ReactScriptLoaderMixin],
    //
    //getInitialState() {
    //    return {
    //        scriptLoading: true,
    //        scriptLoadError: false
    //    };
    //},
    //
    //getScriptURL() {
    //    return '//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.5&appId=182439365451957';
    //},
    //
    //onScriptLoaded() {
    //
    //    this.setState({scriptLoading: false});
    //},

    // ReactScriptLoaderMixin calls this function when the script has failed to load.
    //onScriptError() {
    //    this.setState({scriptLoading: false, scriptLoadError: true});
    //},

    componentDidMount(){
        var FacebookLoadInterval = setInterval(function () {
            if (window.FB) {
                FB.init({
                    appId: FB_ID,
                    version: 'v2.5', // or v2.0, v2.1, v2.2, v2.3,
                    xfbml: true
                });

                clearInterval(FacebookLoadInterval);
            }
        }, 100);

        /*
         (function (d, s, id) {
         //if ($(id).length) {
         //    $(id).remove();
         //    //return;
         //}
         var js, fjs = d.getElementsByTagName(s)[0];
         if (d.getElementById(id)) return;
         js = d.createElement(s);
         js.id = id;
         //js.src = "//connect.facebook.net/pt_BR/sdk.js#xfbml=1&version=v2.5&appId=182439365451957";
         js.src = "//connect.facebook.net/en_US/sdk/debug.js#xfbml=1&version=v2.5&appId=182439365451957";
         fjs.parentNode.insertBefore(js, fjs);
         }(document, 'script', 'facebook-jssdk'));*/

        if (window.twttr) {
            twttr.widgets.load();
        }

        !function (d, s, id) {
            if ($(id).lenght) {
                return;
            }
            var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
            if (!d.getElementById(id)) {
                js = d.createElement(s);
                js.id = id;
                js.src = p + '://platform.twitter.com/widgets.js';
                fjs.parentNode.insertBefore(js, fjs);
            }
        }(document, 'script', 'twitter-wjs');

        /*   (function (d, t) {
         var g, s;
         if ($('#instagramfollowbutton').length > 0) {
         $('#instagramfollowbutton').remove();
         }
         g = d.createElement(t), s = d.getElementsByTagName(t)[0];
         g.src = "//x.instagramfollowbutton.com/follow.js";
         g.id = 'instagramfollowbutton';
         s.parentNode.insertBefore(g, s);
         }(document, "script"));*/


        //(function (d, t) {
        //    var g = d.createElement(t), s = d.getElementsByTagName(t)[0];
        //    g.src = "//x.instagramfollowbutton.com/follow.js";
        //    s.parentNode.insertBefore(g, s);
        //}(document, "script"));

    },

    //componentWillUnmount(){
    //$('#twitter-wjs').remove();
    //},

    render () {
        return (
            <aside className="large-3 medium-4 hidden-for-small-only column aside_full_" id="aside_">
                <div className="row">
                    <div className="column content_">

                        {
                            <PostList />
                        }

                        {<ul className="no-bullet social_box_">

                            <li>
                                <div className="fb-like"
                                     data-href="https://www.facebook.com/Unicorno-1616356291963803/"
                                     data-layout="button_count"
                                     data-action="like"
                                     data-show-faces="true"
                                     data-share="false">
                                </div>
                            </li>
                            <li>
                                <a href="https://twitter.com/UnicornoBR"
                                   className="twitter-follow-button"
                                   data-show-count="false"
                                   data-lang="pt"
                                   data-show-screen-name="false">Seguir</a>
                            </li>
                            <li>
                                <div className="ig-background">
                                    <a
                                        className="ig-follow"
                                        href="https://www.instagram.com/instacorno/"
                                        target="_blank">
                                        <i className="icon-instagram"></i>
                                        Seguir
                                    </a>
                                </div>

                            </li>
                        </ul>}
                    </div>
                </div>
            </aside>
        )
    }
});

//global.Aside = Aside;
