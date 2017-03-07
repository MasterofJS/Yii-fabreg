import {API} from "../../modules/config";
import validateFields from "../../services/validateFields";
import Fileupload from "../sign_up/fileupload.jsx";
import ProfileComponent from "../profile";
import AutoForm from "react-auto-form";
import {FacebookButton, GoogleButton} from "../../components/signWithSocial.react";
//import dateValidate from '../../modules/datebuilder';
import {showTotalAlertBox} from '../../modules/alertBox.react';

const CheckBox = React.createClass({

    getInitialState () {
        return {
            value: !!(+this.props.user[this.props.name])
        };
    },

    render () {
        return (
            <div className="columns">
                <input
                    id={`${this.props.name}_`}
                    name={`${this.props.name}`}
                    checked={this.state.value}
                    defaultChecked={this.state.value}
                    onChange={this._onChangeCheck}
                    type="checkbox"/>
                <label htmlFor={`${this.props.name}_`}>{this.props.labelText}</label>
            </div>
        );
    },

    _onChangeCheck(){
        this.setState({
            value: !this.state.value
        })
    }

});


export default React.createClass({

    getInitialState () {
        return {
            avatar: this.props.user._links ? this.props.user._links.avatar.href : null,
            data: null,
            first_name: this.props.user.first_name,
            last_name: this.props.user.last_name,
            'notify_post_upvote': null,
            'notify_post_comment': null,
            'notify_post_share': null,
            'notify_comment_upvote': null,
            'notify_comment_reply': null
        }
    },

    //componentDidMount() {
    //    //var date = new dateValidate(['#day_sign_up_', '#month_sign_up_', '#year_sign_up_']);
    //
    //    //$(document).foundation({
    //    //    abide: {
    //    //        patterns: {
    //    //            password: /^(.){6,}$/
    //    //        },
    //    //        error_labels: false,
    //    //        validators: {
    //    //            isDay: date.isDay
    //    //        }
    //    //    }
    //    //});
    //    //$(document).foundation('abide', 'reflow');
    //},

    wraps: {},

    render () {
        return (
            <div>
                <h1 className="page_title_">Configurações de perfil</h1>
                <hr/>
                <AutoForm
                    data-abide="ajax"
                    ref="form"
                    className="profile_form_"
                    onSubmit={this._submit}>

                    <div className="row">
                        <div
                            ref={(c) => { this.wraps['first_name'] = c}}
                            className="medium-6 columns">
                            <input
                                onChange={this._onChange.bind(this,'first_name')}
                                value={ this.state.first_name }
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
                                onChange={this._onChange.bind(this,'last_name')}
                                value={ this.state.last_name }
                                type="text"
                                name="last_name"
                                placeholder="Sobrenome"
                            />
                            <small className="error">Erro</small>
                        </div>
                    </div>

                    <Fileupload
                        avatar={ this.state.avatar || (this.props.user._links && this.props.user._links.avatar.href) }
                        gender={ this.props.user.gender }
                        cbChangeAvatar={this.cbChangeAvatar}/>

                    <ProfileComponent
                        requared={false}
                        cbInit={function(elm) {
                                this.wraps['gender'] = elm.refs.gender;
                                this.wraps['birthday'] = elm.refs.birthday;
                                this.wraps['country'] = elm.refs.counties;
                                this.wraps['about'] = elm.refs.about;
                            }.bind(this)}
                        user={this.props.user}
                        onChangeGender={this._handleChangeGender}
                        defaultValueAbout={this.props.user.about}
                    />

                    <div className="row">
                        <div className="columns">
                            <label className="bl_">Notificações</label>
                        </div>
                        <CheckBox
                            name="notify_post_upvote"
                            user={this.props.user}
                            labelText="Alguém deu like no meu post"
                        />
                        <CheckBox
                            name="notify_post_comment"
                            user={this.props.user}
                            labelText="Alguém comentou no meu post"
                        />
                        <CheckBox
                            name="notify_post_share"
                            user={this.props.user}
                            labelText="Alguém compartilhou meu post"
                        />
                        <CheckBox
                            name="notify_comment_upvote"
                            user={this.props.user}
                            labelText="Alguém deu like no meu comentário"
                        />
                        <CheckBox
                            name="notify_comment_reply"
                            user={this.props.user}
                            labelText="Alguém respondeu meu comentário"
                        />
                    </div>

                    <ConnectSocial {...this.props.user}/>

                    <div className="row">
                        <div className="large-12 columns">
                            <input className="button" type="submit" value="Salvar alterações"/>
                            <Link className="del_account_" to={'/settings/delete'}>Deletar Conta</Link>
                        </div>
                    </div>
                </AutoForm>
            </div>
        );
    },

    _submit(e, data) {
        var res;
        e.preventDefault();
        //if ($(e.target).is('[data-invalid]')) {
        //    return;
        //}

        res = {
            'first_name': data.first_name,
            'last_name': data.last_name,
            'gender': data.gender,
            'country': data.country,
            'birthday': (() => {
                if (!data.day_birthday || !data.month_birthday || !data.year_birthday) {
                    return;
                }
                let mm = (data.month_birthday).toString();
                let dd = data.day_birthday.toString();
                return `${data.year_birthday}-${(mm[1] ? mm : "0" + mm[0])}-${(dd[1] ? dd : "0" + dd[0])}`;
            })(),
            'about': data.about,
            notify_post_upvote: onToNumber(data.notify_post_upvote),
            notify_post_comment: onToNumber(data.notify_post_comment),
            notify_post_share: onToNumber(data.notify_post_share),
            notify_comment_upvote: onToNumber(data.notify_comment_upvote),
            notify_comment_reply: onToNumber(data.notify_comment_reply)
        };

        if (!!data.avatar) {
            res.avatar = data.avatar;
        }


        $.ajax({
            url: `${ API }/users/me/profile`,
            type: 'put',
            data: res,
            success: function (respond) {

                if (Array.isArray(respond)) {

                    validateFields.call(this, respond, 'wraps');
                    showTotalAlertBox(null);

                    $('body,html').animate({scrollTop: $($('.error .error').get(0).closest('div.error')).offset().top - 100}, 300);

                    return;
                }

                showTotalAlertBox('Salvo', 'success');

                $('body,html').animate({
                    scrollTop: 0
                }, 300);

                auth.setUser(respond);

            }.bind(this),
            error: function (error) {
                console.warn(error);
                showTotalAlertBox(error.responseText, 'alert');

                // $('body,html').animate({
                //     scrollTop: 0
                // }, 300);
            }
        });
    },



    
    cbChangeAvatar(newAvatar, token){

        this.setState({
            avatar: newAvatar,
            token: `default${token}`
        })
    },

    _handleChangeGender() {

    },

    _onChange(t, e){
        this.setState({
            [t]: e.target.value
        })
    },

    _onChangeCheck(e){
        this.setState({
            [e.target.name]: e.target.value
        });
    }

});


function onToNumber(value) {
    return value === 'on' ? 1 : 0;
}

const ConnectSocial = React.createClass({

    getInitialState () {
        return {
            facebook: this.props._links.social_links && this.props._links.social_links.facebook,
            google: this.props._links.social_links && this.props._links.social_links.google
        };
    },


    render () {
        return (
            <div className="row">
                <div className="column connect_">
                    <div className="column large-9">
                        <h5 className="title_">Facebook</h5>
                        {
                            ( this.state.facebook) ?

                                <a
                                    href="#"
                                    onClick={this._disconnect.bind(this,'facebook')}
                                >
                                        <span>Desconectar</span>
                                    </a>
                                :
                                <FacebookButton
                                    responder={APP.props.location.pathname}
                                    className="split_ button facebook"
                                >
                                        <span>
                                            <i className="icon-facebook"></i>
                                        </span>
                                        <span>Conectar</span>
                                    </FacebookButton>
                        }
                    </div>
                </div>
                <div className="column connect_ end">
                    <div className="column large-9 end">

                        <h5 className="title_">Google</h5>
                        {

                            ( this.state.google ) ?
                                <a
                                    href="#"
                                    onClick={this._disconnect.bind(this,'google')}
                                >
                                        <span>Desconectar</span>
                                    </a>
                                :
                                <GoogleButton
                                    responder={APP.props.location.pathname}
                                    className="split_ button gplus"
                                >
                                        <span>
                                            <i className="icon-gplus"></i>
                                        </span>
                                        <span>Conectar</span>
                                    </GoogleButton>
                        }
                        </div>
                    </div>
            </div>
        );
    },

    _disconnect(client, e){
        e.preventDefault();
        $.ajax({
            url: `${ API }/oauth/${client}`,
            method: 'delete',
            success: function (respond) {
                this.setState({
                    [client]: false
                })
            }.bind(this),
            error: function (error) {
                console.warn(error);
            }
        });

    }

});


/*asdfasdf*/