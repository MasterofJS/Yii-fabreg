global.jQuery = require('jquery');
global.$ = jQuery;
import {API, ROOT, PROD, TITLE} from './modules/config';
import './app';
import React from 'react';
import ReactDOM from 'react-dom';
global.React = React;
global.ReactDOM = ReactDOM;
import {Router, Route, Link, IndexRoute, IndexLink, RouteHandler} from 'react-router';
import {createHistory, useBasename, History} from 'history';
global.Link = Link;
global.IndexLink = IndexLink;
global.RouteHandler = RouteHandler;
import classNames from 'classnames';
global.classNames = classNames;
import hasValue from './services/hasValue';
global.hasValue = hasValue;
import auth from './services/auth';
global.auth = auth;



const history = useBasename(createHistory)();

import total from './modules/total';

import ConfirmedEmail from './components/confirmedEmail.react'
import SignUp from './pages/sign_up';
import Settings from './pages/settings';
import MainNav from './components/main_nav';
import Footer from './components/footer';
import Login from './pages/LoginPage';
import FeedbackPage from './pages/feedback';
import PageNotFound from './pages/pagenotfound';
import Profile from './pages/profile';
import SearchResult from './pages/searchresult';
import Posts from './components/home/posts';
import Single from './pages/singlepost';
import {ForgotPassword, ResetPassword, ConfirmEmail} from './pages/resetpassword';
import NotificationList from './pages/notificationlist';
import './modules/facebook.sdk';
import * as Pages from './pages/other';
import {TotalAlertBox} from './modules/alertBox.react';
import {showTotalAlertBox} from './modules/alertBox.react';

const App = React.createClass({

    getInitialState() {
        return {
            user: null,
            pageNotFound: null
        }
    },

    componentDidMount () {
        total();
        var message = $('meta[name^="alert"]');
        var statusMatch = /:(.*)/g;
        if(message.length){
            var name = message.attr('name');
            var status = name.match(statusMatch)[0].replace(':', '');
            var messageContent = message.attr('content');
            showTotalAlertBox(messageContent, status);
        }
    },

    componentWillMount () {
        var metaUser;
        global.APP = this;
        metaUser = JSON.parse($('meta[name=user]').attr('content'));
        if (metaUser) {
            auth.setUser(metaUser);
        } else {
            this.setState({user: 'guest'});
        }

        if (($('meta[name="response:status"]').attr('content') === '404') && !metaUser) {
            this.setState({
                pageNotFound: true
            })
        }
    },

    shouldComponentUpdate(){
        if (this.state.pageNotFound) {
            this.setState({
                pageNotFound: null
            });
        }

        return true;
    },

    render() {
        const has_user = !(this.state.user === null);

        //if (this.state.user === null) {
        //    return null;
        //}
		console.log('s');

        return (
            <div>

                {
                    has_user &&
                    <MainNav user={this.state.user}/>
                }

                {
                    has_user &&
                    <ConfirmedEmail { ...this.state }/>
                }

                <TotalAlertBox />

                {
                    has_user &&
                    <div className="total_wrap_">
                        {
                            this.state.pageNotFound &&
                            <PageNotFound />
                        }

                        {
                            !this.state.pageNotFound && this.props.children && React.cloneElement(this.props.children, {
                                user: this.state.user
                            })
                        }
                        <Footer/>
                    </div>
                }

            </div>
        )
    }

});

function requireAuth(nextState, replaceState) {
    //if (!auth.loggedIn()) {
    //    replaceState({nextPathname: nextState.location.pathname}, '/login')
    //}
}


function redirectAuth(nextState, replaceState) {
    //if (auth.loggedIn()) {
    //    replaceState({nextPathname: nextState.location.pathname}, '/')
    //}
}

////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
////////////////////////////////////////////////////////
//////////////////////  /////  ////////////////////////
/////////////////////         ////////////////////////
///////////////////////   ///////////////////////////
////////////////////////////////////////////////////
//////////////////  /////  ////////////////////////
/////////////////         ////////////////////////
/////////////////////////////////////////////////
///////////////  /////  ////////////////////////
//////////////         ////////////////////////
/////////////  /////  ////////////////////////
/////////////////////////////////////////////
//////////////   ///////////////////////////
//////////         ////////////////////////
//////////////////////////////////////////
/////////////////////////////////////////
////////////////////////////////////////
///////////////////////////////////////
//////////////////////////////////////
/////////////////////////////////////
////////////////////////////////////
///////////////////////////////////
//////////////////////////////////
/////////////////////////////////
////////////////////////////////
///////////////////////////////
//////////////////////////////
/////////////////////////////
////////////////////////////
///////////////////////////
//////////////////////////
/////////////////////////
////////////////////////
///////////////////////
//////////////////////
/////////////////////
////////////////////
///////////////////
//////////////////
/////////////////
////////////////
///////////////
//////////////
/////////////
////////////
///////////
//////////
/////////
////////
///////
//////
/////
////
///
//


ReactDOM.render(
    <Router history={history}>
        <Route path="/" component={App} >
            <IndexRoute component={Posts} onEnter={onEnterRoot}/>
            <Route path="home" component={Posts} onEnter={onEnterRoute()}/>
            <Route path="trending" component={Posts} onEnter={onEnterRoute()}/>
            <Route path="fresh" component={Posts} onEnter={onEnterRoute()}/>
            <Route path="posts/:id" component={Single} onEnter={onEnterRoute()}/>
            <Route path="search" component={SearchResult} onEnter={onEnterRoute()}/>
            <Route path="signup" component={SignUp} onEnter={onEnterRoute()}/>
            <Route path="login" component={Login} onEnter={onEnterRoute(redirectAuth)}/>
            <Route path="forgot" component={ForgotPassword} onEnter={onEnterRoute()}/>
            <Route path="reset-password" component={ResetPassword} onEnter={onEnterRoute()}/>
            <Route path="confirm-email" component={ConfirmEmail} onEnter={onEnterRoute()}/>
            <Route path='user/:name(/:tab)' component={Profile} onEnter={onEnterRoute()}/>
            <Route path="notifications" component={NotificationList} onEnter={onEnterRoute(requireAuth)}/>
            <Route path="settings" component={Settings.Settings} onEnter={onEnterRoute(requireAuth)}>
                <IndexRoute component={Settings.Account}/>
                <Route path="account" component={Settings.Account} onEnter={auth.updateUser(requireAuth)}/>
                <Route path="password" component={Settings.Password} onEnter={auth.updateUser(requireAuth)}/>
                <Route path="profile" component={Settings.Profile} onEnter={auth.updateUser(requireAuth)}/>
            </Route>
            <Route path="settings/delete" component={Settings.Delete} onEnter={onEnterRoute(requireAuth)}/>
            <Route path="terms" component={Pages.Terms} onEnter={onEnterRoute()}/>
            <Route path="privacy" component={Pages.Privacy} onEnter={onEnterRoute()}/>
            <Route path="contact" component={FeedbackPage} onEnter={onEnterRoute()}/>
            <Route path="404" component={PageNotFound} onEnter={onEnterRoute()}/>
            <Route path="*" component={PageNotFound} onEnter={onEnterRoute()}/>
        </Route>
    </Router>,
    document.getElementById('app_'));

function onEnterRoute(cb) {


    return function (...arg) {
        let title = TITLE;
        switch (arg[0].routes[1].path) {
            // case 'home':
            //     title = `${title}`;
            //     break;
            case 'trending':
                title = `Em Alta - ${title}`;
                break;
            case 'fresh':
                title = `Novos - ${title}`;
                break;
            // case 'search':
            //     title = `home | ${title}`;
            //     break;
            case 'signup':
                title = `Registre-se | ${title}`;
                break;
            // case 'login':
            //     title = `home | ${title}`;
            //     break;
            // case 'forgot':
            //     title = `home | ${title}`;
            //     break;
            case 'reset-password':
                title = `Resetar Senha | ${title}`;
                break;
            case 'notifications':
                title = `Notificações | ${title}`;
                break;
            case 'settings':
                title = `Configurações | ${title}`;
                break;
            case 'terms':
                title = `Termos de Serviço | ${title}`;
                break;
            case 'privacy':
                title = `Termos de Privacidade | ${title}`;
                break;
            case 'contact':
                title = `Sugestões | ${title}`;
                break;
            case '404':
                title = `404 | ${title}`;
                break;
            case 'user':
            case 'posts':
                title = null;
                break;
        }

        if (title) {
            document.title = title;
        }
        if (typeof cb === 'function') {
            cb.apply(this, arg)
        }
    };
}

function onEnterRoot () {
    document.title = TITLE;
}