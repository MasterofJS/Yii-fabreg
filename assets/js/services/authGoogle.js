import React from 'react';
import {GOOGLE_ID} from '../modules/config'
import * as router from 'react-router';
import auth from '../services/auth';
let auth2;
let flag = null;

let scope = {};

var startApp = function () {
    gapi.load('auth2', function () {
        // Retrieve the singleton for the GoogleAuth library and set up the client.
        auth2 = gapi.auth2.init({
            client_id: GOOGLE_ID,
            cookiepolicy: 'single_host_origin',
            scope: 'profile email'
        });

        flag = true;
        for (var key in scope) {
            attachSignin.call(scope[key], scope[key].refs.googleBtn);
        }

    });
};
startApp();

function attachSignin(element) {
    const that = this;

    $(element)
        .off('click')
        .on('click', function () {
            auth2.signIn()
                .then(function (a) {
                    that.props.onSubmit(a)
                }, function (error) {
                    alert(JSON.stringify(error, undefined, 2));
                });
        });

    //auth2.attachClickHandler(
    //    element,
    //    {
    //        client_id: GOOGLE_ID,
    //        cookiepolicy: 'single_host_origin',
    //        scope: 'profile email'
    //    },
    //    that.props.onSubmit,
    //    function (error) {
    //    });
}


const Google = React.createClass({

    mixins: [router.History],

    componentWillUnmount () {
        if (!flag) {
            delete scope[this.key];
        }
    },
    key: null,
    componentDidMount () {
        this.key = `id${Date.now() * Math.random()}`;

        scope[this.key] = this;
        if (flag) {
            attachSignin.call(this, this.refs.googleBtn);
        }

        //}
        //let that = this;
        //var googleUser = {};
        //if (!flag) {
        //    startApp(this, this.refs.googleBtn);
        //} else {

        ///
        attachSignin.call(this, this.refs.googleBtn);
        //}


        /*     var startApp = function () {
         gapi.load('auth2', function () {
         // Retrieve the singleton for the GoogleAuth library and set up the client.
         auth2 = gapi.auth2.init({
         client_id: GOOGLE_ID,
         cookiepolicy: 'single_host_origin',
         scope: 'profile email'
         });
         attachSignin(that.refs.googleBtn);
         });
         };

         function attachSignin(element) {
         auth2.attachClickHandler(element, {},
         function (googleUser) {
         // SUBMIT User
         that.props.onSubmit(googleUser);

         }, function (error) {
         alert(JSON.stringify(error, undefined, 2));
         });
         }

         startApp();*/

    },

    render () {
        return (
            <a
                // ref='googleBtn'
                href="/oauth/google"
                className={this.props.className}
                // onClick={(e) => {e.preventDefault();}}
            >
                {this.props.children}
            </a>
        );
    },
    /*
     <div id="gSignInWrapper">
     <span className="label">Sign in with:</span>
     <div id="customBtn" className="customGPlusSignIn">
     <span className="icon"></span>
     <span classNade="buttonText">Google</span>

     </div>
     </div>
     <div id="name"></div>
     * */
    onSignIn(a, b, c){
        //console.dir(a, b, c);
    }


});

export default Google;
