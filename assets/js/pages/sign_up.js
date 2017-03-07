import React from 'react';
import {ROOT, API} from '../modules/config';

//import dateValidate from '../modules/datebuilder';
// import Aside from '../components/aside.jsx';
import Fileupload from '../components/sign_up/fileupload.jsx';
import Profile from '../components/profile';
import AutoForm from 'react-auto-form';
import { FacebookButton, GoogleButton } from '../components/signWithSocial.react';
import * as router from 'react-router';

export default React.createClass({

    mixins: [router.History],

    getInitialState () {
        return {
            avatar: this.props.avatar,
            gender: null,
            data: null,
            loaded: true
        }
    },
    
    componentWillMount () {

        if (auth.loggedIn()) {
            this.setState({
                loaded: false
            }, function () {
                APP.props.history.replaceState(null, '/');
            });
        }

    },

    componentWillUnmount () {
        if (this.request) {
            this.request.abort();
        }
    },

    componentDidMount () {

        //var date;
        if (auth.loggedIn()) {
            return;
        }

        //date = new dateValidate(['#day_sign_up_', '#month_sign_up_', '#year_sign_up_']);

        $(this.refs['sign_up']).foundation({
            abide: {
                live_validate: false, // validate the form as you go
                validate_on_blur: false, // validate whenever you focus/blur on an input field
                focus_on_invalid: false // automatically bring
            }
        });
        $(document).foundation('abide', 'reflow');

        //        patterns: {
        //            password: /^(.){6,}$/
        //        },
        //        error_labels: false,
        //        live_validate: true, // validate the form as you go
        //        validate_on_blur: true, // validate whenever you focus/blur on an input field
        //        focus_on_invalid: true, // automatically bring the focus to an invalid input field
        //        //error_labels: true, // labels with a for="inputId" will recieve an `error` class
        //        // the amount of time Abide will take before it validates the form (in ms).
        //        // smaller time will result in faster validation
        //        timeout: 500,
        //        validators: {
        //            isDay: date.isDay
        //        }
        //    }
        //});


    },

    render () {
        if (!this.state.loaded) {
            return null;
        }
        return (
            <div className="row">
                <div className="sign_up_ column" ref="sign_up">
                    
                    <h1 className="page_title_">Registre-se</h1>
                    <hr/>
                    
                    <div>
                        <ul className="inline-list">
                            <li>
                                <FacebookButton className="split_ button facebook">
                                    <span>
                                        <i className="icon-facebook"></i>
                                    </span>
                                    <span>Login Com Facebook</span>
                                </FacebookButton>
                            </li>
                            <li>
                                <GoogleButton className="split_ button gplus">
                                    <span>
                                        <i className="icon-gplus"></i>
                                    </span>
                                    <span>Login Com Google</span>
                                </GoogleButton>
                            </li>
                        </ul>
                    </div>
                    
                    <p>
                        Ou entre com email
                    </p>
                    
                    <AutoForm
                        id="form_sign_up_"
                        data-abide="ajax"
                        onSubmit={this._onSubmit}
                        className="profile_form_"
                    >
                        
                        <div className="row">
                            <div
                                ref={(c) => { this.wraps['first_name'] = c}}
                                className="medium-6 columns">
                                <input
                                    type="text"
                                    name="first_name"
                                    placeholder="Nome"
                                />
                                <small className="error">Erro</small>
                            </div>
                            <div
                                ref={(c) => { this.wraps['last_name'] = c}}
                                className="medium-6 columns">
                                <input
                                    type="text"
                                    name="last_name"
                                    placeholder="Sobrenome"
                                />
                                <small className="error">Erro</small>
                            </div>
                        </div>
                        
                        <div className="row">
                            <div className="medium-6 columns end" ref={(c) => { this.wraps['email'] = c}}>
                                <input
                                    type="email"
                                    name="email"
                                    placeholder="Email"
                                />
                                <small className="error">Erro</small>
                            </div>
                        </div>
                        
                        <div className="row">
                            <div className="medium-6 columns password-field" ref={(c) => { this.wraps['password'] = c}}>
                                <input
                                    type="password"
                                    name="password"
                                    id="password_sign_up"
                                    placeholder="Senha"/>
                                <small
                                    className="error">Sua senha precisa ter letras e números, mínimo de 6 caracteres</small>
                            </div>
                            <div className="medium-6 columns password-confirmation-field"
                                 ref={(c) => { this.wraps['confirm_password'] = c}}>
                                <input
                                    type="password"
                                    name="confirm_password"
                                    data-equalto="password_sign_up"
                                    placeholder="Confirmar Senha"/>
                                <small className="error">As senhas devem ser iguais</small>
                            </div>
                        </div>

                        {/* Fileupload section */}
                        <Fileupload
                            avatar={this.state.avatar}
                            gender={this.state.gender}
                            cbChangeAvatar={this.cbChangeAvatar}/>

                        {/* Profile section */}
                        <Profile
                            requared={false}
                            cbInit={function(elm) {
                                this.wraps['gender'] = elm.refs.gender;
                                this.wraps['birthday'] = elm.refs.birthday;
                                this.wraps['country'] = elm.refs.counties;
                                this.wraps['about'] = elm.refs.about;
                            }.bind(this)}
                            user="guest"
                            onChangeGender={this._handleChangeGender}/>
                        
                        
                        <div className="row">
                            <div className="large-12 columns">
                                <button className="button" type="submit">Registrar</button>
                            </div>
                        </div>
                    
                    </AutoForm>
                
                </div>
            </div>
        );
    },
    
    cbChangeAvatar(newAvatar, token){

        this.setState({
            avatar: newAvatar,
            token: token
        })
    },

    wraps: {},

    _onSubmit(e, data){
        e.preventDefault();
        var res;
        //debugger;
        //if ($(e.target).is('[data-invalid]')) {
        //    return;
        //}
        //if (data.password.length < 6 || (data.password !== data.confirm_password)) {
        //    return;
        //}

        //if ($(this.wraps.birthday).hasClass('error')) {
        //    return;
        //}

        res = {
            'first_name': data.first_name,
            'last_name': data.last_name,
            'gender': data.gender,
            'email': data.email,
            'password': data.password,
            'confirm_password': data.confirm_password,
            'country': data.country,
            'avatar': data.avatar,
            'birthday': (() => {
                if (!data.day_birthday || !data.month_birthday || !data.year_birthday) {
                    return;
                }
                let mm = (data.month_birthday).toString();
                let dd = data.day_birthday.toString();
                return `${data.year_birthday}-${(mm[1] ? mm : "0" + mm[0])}-${(dd[1] ? dd : "0" + dd[0])}`;
            })(),
            'about': data.about

        };


        this.request = $.ajax({
            url: `${API}/auth/sign-up`,
            type: 'post',
            data: res,
            dataType: 'json',
            success: function (respond) {
                if (Array.isArray(respond)) {

                    respond.forEach((e) => {
                        $(this.wraps[e.field])
                            .addClass('error')
                            .find('.error')
                            .text(e.message);
                    });
                    //console.log($('.error .error').get(0).closest('div.error'),
                    //    $($('.error .error').get(0).closest('div.error')).offset());
                    $('body,html').animate({scrollTop: $($('.error .error').get(0).closest('div.error')).offset().top - 100}, 300);

                    return;
                }

                auth.setUser(respond, APP.props.location.query.ref || '/');

            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });

    },

    _handleChangeGender (e) {
        
        var value = e.target.value;
        
        if (value === 'male' || 'female') {
            this.setState({
                gender: value
            })
        }
        
    }

});















