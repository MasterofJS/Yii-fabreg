import React from 'react';
import { FB_ID} from '../modules/config'
export default React.createClass({

    checkLoginState(e) {
        const that = this;
        e.preventDefault();

        FB.login((response) => {
            if (response.authResponse) {

                that.props.callback(response);
            } else {

            }
        }, {
            scope: 'public_profile,email,user_birthday'
        });
    },

    componentDidMount() {

        global.fbAsyncInit = function () {
            FB.init({
                appId: FB_ID,
                cookie: true,
                xfbml: true,
                version: 'v2.5' // use version 2.2
            });

        };
    },


    render () {
        return (
            <a
                href="/oauth/facebook"
                // onClick={this.checkLoginState}
                className={this.props.className}>
                {this.props.children}
            </a>
        );
    }

});
//<a href="https://twitter.com/share" class="twitter-share-button" data-via="paverbool">Tweet</a>
//<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>